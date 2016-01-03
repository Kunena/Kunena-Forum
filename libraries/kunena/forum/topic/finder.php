<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Forum.Topic
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class KunenaForumTopicFinder
 */
class KunenaForumTopicFinder extends KunenaDatabaseObjectFinder
{
	protected $table = '#__kunena_topics';
	protected $hold = array(0);
	protected $moved = null;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->limit = KunenaConfig::getInstance()->threads_per_page;
	}

	/**
	 * @param $field
	 * @param $operation
	 * @param $value
	 *
	 * @return $this
	 * @deprecated Use where() instead.
	 */
	public function filterBy($field, $operation, $value)
	{
		return $this->where($field, $operation, $value);
	}

	/**
	 * Filter by user access to the categories.
	 *
	 * It is very important to use this or category filter. Otherwise topics from unauthorized categories will be
	 * included to the search results.
	 *
	 * @param KunenaUser $user
	 *
	 * @return $this
	 */
	public function filterByUserAccess(KunenaUser $user)
	{
		$categories = $user->getAllowedCategories();
		$list = implode(',', $categories);
		$this->query->where("a.category_id IN ({$list})");

		return $this;
	}

	/**
	 * Filter by list of categories.
	 *
	 * It is very important to use this or user access filter. Otherwise topics from unauthorized categories will be
	 * included to the search results.
	 *
	 * $topics->filterByCategories($me->getAllowedCategories())->limit(20)->find();
	 *
	 * @param array $categories
	 *
	 * @return $this
	 */
	public function filterByCategories(array $categories)
	{
		$list = array();
		foreach ($categories as $category)
		{
			if ($category instanceof KunenaForumCategory)
			{
				$list[] = (int) $category->id;
			}
			else
			{
				$list[] = (int) $category;
			}
		}

		$list = implode(',', $list);

		// Handle empty list as impossible filter value.
		if (!$list)
		{
			$list = -1;
		}

		$this->query->where("a.category_id IN ({$list})");

		return $this;
	}

	/**
	 * Filter by time, either on first or last post.
	 *
	 * @param JDate $starting  Starting date or null if older than ending date.
	 * @param JDate $ending    Ending date or null if newer than starting date.
	 * @param bool  $lastPost  True = last post, False = first post.
	 *
	 * @return $this
	 */
	public function filterByTime(JDate $starting = null, JDate $ending = null, $lastPost = true)
	{
		$name = $lastPost ? 'last' : 'first';

		if ($starting && $ending)
		{
			$this->query->where("a.{$name}_post_time BETWEEN {$this->db->quote($starting->toUnix())} AND {$this->db->quote($ending->toUnix())}");
		}
		elseif ($starting)
		{
			$this->query->where("a.{$name}_post_time > {$this->db->quote($starting->toUnix())}");
		}
		elseif ($ending)
		{
			$this->query->where("a.{$name}_post_time <= {$this->db->quote($ending->toUnix())}");
		}

		return $this;
	}

	/**
	 * Filter by users role in the topic. For now use only once.
	 *
	 * first_post = User has posted the first post.
	 * last_post = User has posted the last post.
	 * owner = User owns the topic (usually has created it).
	 * posted = User has posted to the topic.
	 * replied = User has written a reply to the topic (but does not own it).
	 * favorited = User has favorited the topic.
	 * subscribed = User has subscribed to the topic.
	 *
	 * @param KunenaUser $user
	 * @param string     $action Action or negation of the action (!action).
	 *
	 * @return $this
	 */
	public function filterByUser(KunenaUser $user, $action = 'owner')
	{
		$this->query->innerJoin('#__kunena_user_topics AS ut ON a.id=ut.topic_id');
		$this->query->where("ut.user_id = {$this->db->quote($user->userid)}");

		switch ($action)
		{
			case 'first_post':
				$this->query->where('a.first_post_userid={$this->db->quote($user->userid)}');
				break;
			case '!first_post':
				$this->query->where('a.first_post_userid!={$this->db->quote($user->userid)}');
				break;
			case 'last_post':
				$this->query->where('a.last_post_userid={$this->db->quote($user->userid)}');
				break;
			case '!last_post':
				$this->query->where('a.last_post_userid!={$this->db->quote($user->userid)}');
				break;
			case 'owner':
				$this->query->where('ut.owner=1');
				break;
			case '!owner':
				$this->query->where('ut.owner!=1');
				break;
			case 'posted':
				$this->query->where('ut.posts>0');
				break;
			case '!posted':
				$this->query->where('ut.posts=0');
				break;
			case 'replied':
				$this->query->where('(ut.owner=0 AND ut.posts>0)');
				break;
			case '!replied':
				$this->query->where('(ut.owner=0 AND ut.posts=0)');
				break;
			case 'favorited':
				$this->query->where('ut.favorite=1');
				break;
			case '!favorited':
				$this->query->where('ut.favorite!=1');
				break;
			case 'subscribed':
				$this->query->where('ut.subscribed=1');
				break;
			case '!subscribed':
				$this->query->where('ut.subscribed!=1');
				break;
			case 'involved':
				$this->query->where('(ut.posts>0 OR ut.favorite=1 OR ut.subscribed=1)');
				break;
			case '!involved':
				$this->query->where('(ut.posts<1 AND ut.favorite=0 AND ut.subscribed=0)');
				break;
		}

		return $this;
	}

	/**
	 * Filter topics where group of people have (not) posted after the topic owner.
	 *
	 * @param array $users
	 * @param bool  $negate
	 *
	 * @return $this
	 */
	public function filterAnsweredBy(array $users, $negate = false)
	{
		$list = array();
		foreach ($users as $user)
		{
			if ($user instanceof KunenaUser)
			{
				$list[] = (int) $user->userid;
			}
			elseif ($user instanceof JUser)
			{
				$list[] = (int) $user->id;
			}
			else
			{
				$list[] = (int) $user;
			}
		}

		if (empty($list))
		{
			$this->query->where('0');
			return $this;
		}

		$userlist = implode(',', $list);

		$subQuery = $this->db->getQuery(true);
		$subQuery->select('st.id, MAX(sut.last_post_id) AS max_post_id')
			->from('#__kunena_topics AS st')
			->leftJoin('#__kunena_user_topics AS sut ON sut.topic_id=st.id')
			->where("sut.user_id IN ({$userlist})")
			->group('st.last_post_id')
			->order('st.last_post_id DESC');

		// Hard limit on sub-query to make derived table faster to sort.
		$this->query->innerJoin("({$subQuery} LIMIT 1000) AS uu ON uu.id=a.id");
		$this->query->innerJoin("#__kunena_user_topics AS ut ON ut.topic_id=a.id AND ut.owner=1");

		if ($negate)
		{
			// Topic owner has posted after $users (or $users haven't replied at all).
			$this->query->where("ut.last_post_id > uu.max_post_id");
		}
		else
		{
			// One of the $users has posted after topic owner.
			$this->query->where("ut.last_post_id < uu.max_post_id");
		}

		return $this;
	}

	/**
	 * Filter by hold (0=published, 1=unapproved, 2=deleted, 3=topic deleted).
	 *
	 * @param array $hold  List of hold states to display.
	 *
	 * @return $this
	 */
	public function filterByHold(array $hold = array(0))
	{
		$this->hold = $hold;

		return $this;
	}

	/**
	 * Filter by moved topics.
	 *
	 * @param bool $value True on moved, false on not moved.
	 *
	 * @return $this
	 */
	public function filterByMoved($value = true)
	{
		$this->moved = (bool) $value;

		return $this;
	}

	/**
	 * Get topics.
	 *
	 * @param  string  $access  Kunena action access control check.
	 * @return array|KunenaForumTopic[]
	 */
	public function find($access = 'read')
	{
		$results = parent::find();

		return KunenaForumTopicHelper::getTopics($results, $access);
	}

	protected function build(JDatabaseQuery $query)
	{
		if (!empty($this->hold))
		{
			JArrayHelper::toInteger($this->hold, 0);
			$hold = implode(',', $this->hold);
			$query->where("a.hold IN ({$hold})");
		}

		if (isset($this->moved))
		{
			$query->where('a.moved_id' . ($this->moved ? '>0' : '=0'));
		}
	}
}
