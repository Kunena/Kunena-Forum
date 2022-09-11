<?php
/**
 * Kunena Component
 * @package         Kunena.Framework
 * @subpackage      Forum.Topic
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\Utilities\ArrayHelper;

/**
 * Class KunenaForumTopicFinder
 * @since Kunena
 */
class KunenaForumTopicFinder extends KunenaDatabaseObjectFinder
{
	/**
	 * @var string
	 * @since Kunena
	 */
	protected $table = '#__kunena_topics';

	/**
	 * @var array
	 * @since Kunena
	 */
	protected $hold = array(0);

	/**
	 * @var null
	 * @since Kunena
	 */
	protected $moved = null;

	/**
	 * Constructor.
	 * @since Kunena
	 * @throws Exception
	 */
	public function __construct()
	{
		parent::__construct();

		$this->limit = KunenaConfig::getInstance()->threads_per_page;
	}

	/**
	 * Filter by user access to the categories.
	 *
	 * It is very important to use this or category filter. Otherwise topics from unauthorized categories will be
	 * included to the search results.
	 *
	 * @param   KunenaUser $user user
	 *
	 * @return $this
	 * @throws Exception
	 * @since Kunena
	 */
	public function filterByUserAccess(KunenaUser $user)
	{
		$categories = $user->getAllowedCategories();
		$list       = implode(',', $categories);
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
	 * @param   array $categories categories
	 *
	 * @return $this
	 * @since Kunena
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
	 * @param   \Joomla\CMS\Date\Date $starting Starting date or null if older than ending date.
	 * @param   \Joomla\CMS\Date\Date $ending   Ending date or null if newer than starting date.
	 * @param   bool                  $lastPost True = last post, False = first post.
	 *
	 * @return $this
	 * @since Kunena
	 */
	public function filterByTime(\Joomla\CMS\Date\Date $starting = null, \Joomla\CMS\Date\Date $ending = null, $lastPost = true)
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
	 * @param   KunenaUser $user   user
	 * @param   string     $action Action or negation of the action (!action).
	 *
	 * @return $this
	 * @since Kunena
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
	 * Filter topics with topics Id given.
	 *
	 * @param   string  $topicsId   The list of ID of topics to filter (the strong should be like that: 1,2,3)
	 *
	 * @return  $this
	 *
	 * @since   Kunena 6.0
	 */
	public function filterTopicNotIn(string $topicsId)
	{
		$this->query->where($this->db->quoteName('a.id') . ' NOT IN (' . $topicsId . ')');

		return $this;
	}

	/**
	 * Filter by hold (0=published, 1=unapproved, 2=deleted, 3=topic deleted).
	 *
	 * @param   array $hold List of hold states to display.
	 *
	 * @return $this
	 * @since Kunena
	 */
	public function filterByHold(array $hold = array(0))
	{
		$this->hold = $hold;

		return $this;
	}

	/**
	 * Filter by moved topics.
	 *
	 * @param   bool $value True on moved, false on not moved.
	 *
	 * @return $this
	 * @since Kunena
	 */
	public function filterByMoved($value = true)
	{
		$this->moved = (bool) $value;

		return $this;
	}

	/**
	 * Get topics.
	 *
	 * @param   string $access Kunena action access control check.
	 *
	 * @return array|KunenaForumTopic[]
	 * @throws Exception
	 * @throws null
	 * @since Kunena
	 */
	public function find($access = 'read')
	{
		$results = parent::find();

		return KunenaForumTopicHelper::getTopics($results, $access);
	}

	/**
	 * Access to the query select
	 *
	 * @param   mixed $columns A string or an array of field names.
	 *
	 * @return $this
	 * @since Kunena
	 */
	public function select($columns)
	{
		$this->query->select($columns);

		return $this;
	}

	/**
	 * @param   JDatabaseQuery $query query
	 *
	 * @since Kunena
	 * @return void
	 */
	protected function build(JDatabaseQuery $query)
	{
		if (!empty($this->hold))
		{
			$this->hold = ArrayHelper::toInteger($this->hold, 0);
			$hold = implode(',', $this->hold);
			$query->where("a.hold IN ({$hold})");
		}

		if (isset($this->moved))
		{
			$query->where('a.moved_id' . ($this->moved ? '>0' : '=0'));
		}
	}
}
