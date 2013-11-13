<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Forum.Topic
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class KunenaForumTopicFinder
 */
class KunenaForumTopicFinder
{
	/**
	 * @var JDatabaseQuery
	 */
	protected $query;
	/**
	 * @var JDatabase
	 */
	protected $db;
	protected $start = 0;
	protected $limit = 20;
	protected $hold = array(0);
	protected $moved = null;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->limit = KunenaConfig::getInstance()->threads_per_page;
		$this->db = JFactory::getDbo();
		$this->query = $this->db->getQuery(true);
		$this->query->from('#__kunena_topics AS t');
	}

	/**
	 * Set limitstart for the query.
	 *
	 * @param int $limitstart
	 *
	 * @return $this
	 */
	public function start($limitstart = 0)
	{
		$this->start = $limitstart;

		return $this;
	}

	/**
	 * Set limit to the query.
	 *
	 * If this function isn't used, Kunena will use threads per page configuration setting.
	 *
	 * @param int $limit
	 *
	 * @return $this
	 */
	public function limit($limit = null)
	{
		if (!isset($limit)) $limit = KunenaConfig::getInstance()->threads_per_page;
		$this->limit = $limit;

		return $this;
	}

	/**
	 * Set order by field and direction.
	 *
	 * This function can be used more than once to chain order by.
	 *
	 * @param  string $by
	 * @param  int $direction
	 * @param  string $alias
	 *
	 * @return $this
	 */
	public function order($by, $direction = 1, $alias = 't')
	{
		$direction = $direction > 0 ? 'ASC' : 'DESC';
		$by = $alias.'.'.$this->db->quoteName($by);
		$this->query->order("{$by} {$direction}");

		return $this;
	}

	public function filterBy($field, $operation, $value)
	{
		$operation = strtoupper($operation);
		switch ($operation)
		{
			case '>':
			case '>=':
			case '<':
			case '<=':
			case '=':
				$this->query->where("{$this->db->quoteName($field)} {$operation} {$this->db->quote($value)}");
				break;
			case 'IN':
			case 'NOT IN':
				$value = (array) $value;
				if (empty($value)) {
					// WHERE field IN (nothing).
					$this->query->where('0');
				} else {
					$list = implode(',', $value);
					$this->query->where("{$this->db->quoteName($field)} {$operation} ({$list})");
				}
				break;
		}

		return $this;
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
		$this->query->where("t.category_id IN ({$list})");

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
			if ($category instanceof KunenaForumCategory) $list[] = (int) $category->id;
			else $list[] = (int) $category;
		}
		$list = implode(',', $list);

		// Handle empty list as impossible filter value.
		if (!$list) $list = -1;

		$this->query->where("t.category_id IN ({$list})");

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

		if ($starting && $ending) {
			$this->query->where("t.{$name}_post_time BETWEEN {$this->db->quote($starting->toUnix())} AND {$this->db->quote($ending->toUnix())}");
		} elseif ($starting) {
			$this->query->where("t.{$name}_post_time > {$this->db->quote($starting->toUnix())}");
		} elseif ($ending) {
			$this->query->where("t.{$name}_post_time <= {$this->db->quote($ending->toUnix())}");
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
		$this->query->innerJoin('#__kunena_user_topics AS ut ON t.id=ut.topic_id');
		$this->query->where("ut.user_id = {$this->db->quote($user->userid)}");

		switch ($action)
		{
			case 'first_post':
				$this->query->where('t.first_post_userid={$this->db->quote($user->userid)}');
				break;
			case '!first_post':
				$this->query->where('t.first_post_userid!={$this->db->quote($user->userid)}');
				break;
			case 'last_post':
				$this->query->where('t.last_post_userid={$this->db->quote($user->userid)}');
				break;
			case '!last_post':
				$this->query->where('t.last_post_userid!={$this->db->quote($user->userid)}');
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
			if ($user instanceof KunenaUser) $list[] = (int) $user->userid;
			elseif ($user instanceof JUser) $list[] = (int) $user->id;
			else $list[] = (int) $user;
		}
		if (empty($list)) {
			$this->query->where('0');
			return;
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
		$this->query->innerJoin("({$subQuery} LIMIT 1000) AS uu ON uu.id=t.id");
		$this->query->innerJoin("#__kunena_user_topics AS ut ON ut.topic_id=t.id AND ut.owner=1");

		if ($negate) {
			// Topic owner has posted after $users (or $users haven't replied at all).
			$this->query->where("ut.last_post_id > uu.max_post_id");
		} else {
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
		$query = clone $this->query;
		$this->build($query);
		$query->select('t.id');
		$this->db->setQuery($query, $this->start, $this->limit);
		$results = (array) $this->db->loadColumn();
		KunenaError::checkDatabaseError();

		return KunenaForumTopicHelper::getTopics($results, $access);
	}

	/**
	 * Count topics.
	 * @return int
	 */
	public function count()
	{
		$query = clone $this->query;
		$this->build($query);
		$query->select('COUNT(*)');
		$query->clear('order');
		$this->db->setQuery($query);
		$count = (int) $this->db->loadResult();
		KunenaError::checkDatabaseError();

		return $count;
	}

	protected function build($query)
	{
		if (!empty($this->hold)) {
			JArrayHelper::toInteger($this->hold, 0);
			$hold = implode(',', $this->hold);
			$query->where("t.hold IN ({$hold})");
		}

		if (isset($this->moved)) {
			$query->where('t.moved_id' . ($this->moved ? '>0' : '=0'));
		}
	}
}
