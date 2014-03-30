<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Forum.Category
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class KunenaForumCategory
 *
 * @property int $id
 * @property int $parent_id
 * @property string $name
 * @property string $alias
 * @property int $icon_id
 * @property int $locked
 * @property string $accesstype
 * @property int $access
 * @property int $pub_access
 * @property int $pub_recurse
 * @property int $admin_access
 * @property int $admin_recurse
 * @property int $ordering
 * @property int $published
 * @property string $channels
 * @property int $checked_out
 * @property string $checked_out_time
 * @property int $review
 * @property int $allow_anonymous
 * @property int $post_anonymous
 * @property int $hits
 * @property string $description
 * @property string $headerdesc
 * @property string $class_sfx
 * @property int $allow_polls
 * @property string $topic_ordering
 * @property int $numTopics
 * @property int $numPosts
 * @property int $last_topic_id
 * @property int $last_post_id
 * @property int $last_post_time
 * @property string $params
 */
class KunenaForumCategory extends KunenaDatabaseObject {
	public $id = null;
	public $level = 0;
	protected $authorised = array();

	protected $_aliases = null;
	protected $_alias = null;
	/**
	 * @var KunenaForumCategory[]
	 */
	protected $_channels = false;
	protected $_topics = false;
	protected $_posts = false;
	protected $_lastid = false;
	protected $_authcache = array();
	protected $_authfcache = array();
	protected $_new = 0;
	protected $_table = 'KunenaCategories';
	protected static $actions = array(
			'none'=>array(),
			'read'=>array('Read'),
			'subscribe'=>array('Read', 'CatSubscribe', 'NotBanned', 'NotSection'),
			'moderate'=>array('Read', 'NotBanned', 'Moderate'),
			'admin'=>array('NotBanned', 'Admin'),
			'topic.read'=>array('Read'),
			'topic.create'=>array('Read', 'GuestWrite', 'NotBanned', 'NotSection', 'Unlocked', 'Channel'),
			'topic.reply'=>array('Read', 'GuestWrite', 'NotBanned', 'NotSection', 'Unlocked'),
			'topic.edit'=>array('Read', 'NotBanned', 'Unlocked'),
			'topic.move'=>array('Read', 'NotBanned', 'Moderate', 'Channel'),
			'topic.approve'=>array('Read','NotBanned', 'Moderate'),
			'topic.delete'=>array('Read', 'NotBanned', 'Moderate'),
			'topic.undelete'=>array('Read', 'NotBanned', 'Moderate'),
			'topic.permdelete'=>array('Read', 'NotBanned', 'Admin'),
			'topic.favorite'=>array('Read','NotBanned', 'Favorite'),
			'topic.subscribe'=>array('Read','NotBanned', 'Subscribe'),
			'topic.sticky'=>array('Read','NotBanned', 'Moderate'),
			'topic.lock'=>array('Read','NotBanned', 'Moderate'),
			'topic.poll.read'=>array('Read', 'Poll'),
			'topic.poll.create'=>array('Read', 'GuestWrite', 'NotBanned', 'Unlocked', 'Poll'),
			'topic.poll.edit'=>array('Read', 'NotBanned', 'Unlocked', 'Poll'),
			'topic.poll.delete'=>array('Read', 'NotBanned', 'Unlocked', 'Poll'),
			'topic.poll.vote'=>array('Read', 'NotBanned', 'Unlocked', 'Poll'),
			'topic.post.read'=>array('Read'),
			'topic.post.reply'=>array('Read', 'GuestWrite', 'NotBanned', 'NotSection', 'Unlocked'),
			'topic.post.thankyou' =>array('Read', 'NotBanned'),
			'topic.post.unthankyou' =>array('Read', 'NotBanned', 'GlobalModerate'),
			'topic.post.edit'=>array('Read', 'NotBanned', 'Unlocked'),
			'topic.post.move'=>array('Read', 'NotBanned', 'Moderate', 'Channel'),
			'topic.post.approve'=>array('Read', 'NotBanned', 'Moderate'),
			'topic.post.delete'=>array('Read', 'NotBanned', 'Unlocked'),
			'topic.post.undelete'=>array('Read', 'NotBanned', 'Moderate'),
			'topic.post.permdelete'=>array('Read', 'NotBanned', 'Admin'),
			'topic.post.attachment.read'=>array('Read'),
			'topic.post.attachment.create'=>array('Read', 'GuestWrite', 'NotBanned', 'Unlocked', 'Upload'),
			'topic.post.attachment.delete'=>array('NotBanned'), // TODO: In the future we might want to restrict this: array('Read', 'NotBanned', 'Unlocked'),
		);

    /**
     * @param mixed|array $properties
     */
    public function __construct($properties = null) {
		if ($properties !== null) {
			$this->setProperties($properties);
		}
		$registry = new JRegistry();
		if (!empty($this->params)) $registry->loadString($this->params);
		$this->params = $registry;

		if (!$this->_name) $this->_name = get_class ($this);
		$this->_alias = $this->get('alias', '');
	}

    /**
     * Returns the global KunenaForumCategory object.
     *
     * @param null $identifier  The category id to load.
     * @param bool $reload      Force reload from the database.
     *
     * @return KunenaForumCategory
     */
    static public function getInstance($identifier = null, $reload = false) {
		return KunenaForumCategoryHelper::get($identifier, $reload);
	}

	/**
	 * Returns list of children of this category.
	 *
	 * @param int $levels	How many levels to search.
	 *
	 * @return array	List of KunenaForumCategory objects.
	 */
	public function getChildren($levels = 0) {
		return KunenaForumCategoryHelper::getChildren($this->id, $levels);
	}

	/**
	 * Returns object containing user information from this category.
	 *
	 * @param mixed $user
	 *
	 * @return KunenaForumCategoryUser
	 */
	public function getUserInfo($user = null) {
		return KunenaForumCategoryUserHelper::get($this->id, $user);
	}

	/**
	 * Subscribe / Unsubscribe user to this category.
	 *
	 * @param boolean $value True for subscribe, false for unsubscribe.
	 * @param mixed $user
	 *
	 * @return bool
	 */
	public function subscribe($value=true, $user=null) {
		$usercategory = KunenaForumCategoryUserHelper::get($this->id, $user);
		$usercategory->subscribed = (int)$value;
		if (!$usercategory->save()) {
			$this->setError($usercategory->getError());
		}
		return !$this->getError();
	}

	/**
	 * Returns new topics count from this category for current user.
	 *
	 * @todo Currently new topics needs to be calculated manually, make it automatic.
	 *
	 * @param mixed $count	Internal parameter to set new count.
	 *
	 * @return int	New topics count.
	 */
	public function getNewCount($count = null) {
		if ($count !== null) $this->_new = $count;
		return $this->_new;
	}

	/**
	 * @return bool
	 */
	public function isSection() {
		$this->buildInfo();
		return $this->parent_id == 0 || (!$this->numTopics && $this->locked && empty($this->_channels['none']));
	}

	/**
	 * @param mixed		$category	Fake category (or null).
	 * @param bool 		$xhtml		True if URL needs to be escaped for XHTML.
	 * @param int|null	$action		Limitstart.
	 *
	 * @return string
	 */
	public function getUrl($category = null, $xhtml = true, $action = null) {
		$uri = $this->getUri($category, $action);
		return KunenaRoute::_($uri, $xhtml);
	}

	/**
	 * @param mixed		$category	Fake category (or null).
	 * @param int|null	$action		Limitstart.
	 *
	 * @return JUri
	 */
	public function getUri($category = null, $action = null) {
		if (!$category) {
			$category = $this;
		} else {
			$category = KunenaForumCategoryHelper::get($category);
		}
		$uri = JUri::getInstance("index.php?option=com_kunena&view=category&catid={$category->id}");
		if ((string)$action === (string)(int)$action) {
			$uri->setVar('limitstart', $action);
		}
		return $uri;
	}

	/**
	 * @param string	$field	Field to be displayed.
	 *
	 * @return int|string
	 */
	public function displayField($field) {
		switch ($field) {
			case 'id':
				return intval($this->id);
			case 'name':
				return KunenaHtmlParser::parseText($this->name);
			case 'description':
			case 'headerdesc':
				return KunenaHtmlParser::parseBBCode($this->$field);
		}
		return '';
	}

	/**
	 * @return $this
	 */
	public function getCategory() {
		return $this;
	}

	/**
	 * @return array	Array of Kunena aliases.
	 */
	public function getAliases() {
		if (!isset($this->_aliases)) {
			$db = JFactory::getDbo();
			$query = "SELECT * FROM #__kunena_aliases WHERE type='catid' AND item={$db->Quote($this->id)}";
			$db->setQuery ($query);
			$this->_aliases = (array) $db->loadObjectList('alias');
		}
		return $this->_aliases;
	}

	/**
	 * @param string	$alias
	 *
	 * @return bool|string
	 */
	public function checkAlias($alias) {
		// Check if category is already using the alias.
		if ($this->_alias && $this->_alias == $alias) return true;

		// Check if alias is valid in current configuration.
		if (KunenaRoute::stringURLSafe($alias) != $alias) return false;
		$item = KunenaRoute::resolveAlias($alias);

		// Is alias free to be used?
		if (!$item) return 'insert';

		// Fail if alias is reserved or used by another category.
		if (empty($item['catid']) || $item['catid'] != $this->id) return false;

		return 'update';
	}

	/**
	 * @param string	$alias
	 *
	 * @return bool
	 */
	public function addAlias($alias) {
		if (!$this->exists()) return false;

		$alias = KunenaRoute::stringURLSafe($alias);
		if (!$alias) {
			$alias = $this->id;
		}
		$check = $this->checkAlias($alias);
		// Cannot add alias?
		if ($check === false) return false;
		// Did alias change?
		if ($check === true) return true;

		$db = JFactory::getDbo();
		$query = "REPLACE INTO #__kunena_aliases (alias, type, item) VALUES ({$db->Quote($alias)},'catid',{$db->Quote($this->id)})";
		$db->setQuery ($query);
		$db->query ();
		return KunenaError::checkDatabaseError ();
	}

	/**
	 * @param string	$alias
	 *
	 * @return bool
	 */
	public function deleteAlias($alias) {
		// Do not delete valid alias.
		if (JString::strtolower($this->alias) == JString::strtolower($alias)) return false;

		$db = JFactory::getDbo();
		$query = "DELETE FROM #__kunena_aliases WHERE type='catid' AND item={$db->Quote($this->id)} AND alias={$db->Quote($alias)}";
		$db->setQuery ($query);
		$db->query ();
		KunenaError::checkDatabaseError ();
		return (bool) $db->getAffectedRows();
	}

	/**
	 * @return int
	 */
	public function getTopics() {
		$this->buildInfo();
		return $this->_topics;
	}

	/**
	 * @return int
	 */
	public function getPosts() {
		$this->buildInfo();
		return $this->_posts;
	}

	/**
	 * @return int
	 */
	public function getReplies() {
		$this->buildInfo();
		return max($this->_posts - $this->_topics, 0);
	}

	/**
	 * @return KunenaForumCategory
	 */
	public function getLastCategory() {
		$this->buildInfo();
		return KunenaForumCategoryHelper::get($this->_lastid);
	}

	/**
	 * @return KunenaForumTopic
	 */
	public function getLastTopic() {
		return KunenaForumTopicHelper::get($this->getLastCategory()->last_topic_id);
	}

	/**
	 * @param string $action
	 *
	 * @return KunenaForumCategory[]
	 */
	public function getChannels($action='read') {
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		if ($this->_channels === false) {
			$this->_channels['none'] = array();
			if (!$this->published || $this->parent_id == 0 || (!$this->numTopics && $this->locked)) {
				// Unpublished categories and sections do not have channels
			} elseif (empty($this->channels) || $this->channels == $this->id) {
				// No channels defined
				$this->_channels['none'][$this->id] = $this;
			} else {
				// Fetch all channels
				$ids = array_flip(explode(',', $this->channels));
				if (isset($ids[0]) || isset($ids['THIS'])) {
					// Handle current category
					$this->_channels['none'][$this->id] = $this;
				}
				if (!empty($ids)) {
					// More category channels
					$this->_channels['none'] += KunenaForumCategoryHelper::getCategories(array_keys($ids), null, 'none');
				}
				if (isset($ids['CHILDREN'])) {
					// Children category channels
					$this->_channels['none'] += KunenaForumCategoryHelper::getChildren($this->id, 1, array($action=>'none'));
				}
			}
		}
		if (!isset($this->_channels[$action])) {
			$this->_channels[$action] = array();
			foreach ($this->_channels['none'] as $channel) {
				/** @var KunenaForumCategory $channel */
				if (($channel->id == $this->id && $action == 'read') || $channel->authorise($action, null, false))
					$this->_channels[$action][$channel->id] = $channel;
			}
		}
		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		return $this->_channels[$action];
	}

	/**
	 * @param mixed $user
	 *
	 * @return KunenaForumCategory
	 */
	public function getNewTopicCategory($user=null) {
		foreach ($this->getChannels() as $category) {
			if ($category->authorise('topic.create', $user, true)) return $category;
		}
		$categories = KunenaForumCategoryHelper::getChildren(intval($this->id), -1, array('action'=>'topic.create'));
		if ($categories) foreach ($categories as $category) {
			if ($category->authorise('topic.create', null, true)) return $category;
		}
		return new KunenaForumCategory();
	}

	/**
	 * @param array  $fields
	 * @param mixed  $user
	 * @param array  $safefields
	 *
	 * @return array
	 */
	public function newTopic(array $fields=null, $user=null, array $safefields=null) {
		$catid = isset($safefields['category_id']) ? $safefields['category_id'] : $this->getNewTopicCategory($user)->id;
		$user = KunenaUserHelper::get($user);
		$message = new KunenaForumMessage();
		$message->catid = $catid;
		$message->name = $user->getName('');
		$message->userid = $user->userid;
		$message->ip = !empty($_SERVER ['REMOTE_ADDR']) ? $_SERVER ['REMOTE_ADDR'] : '';
		$message->hold = $this->review ? (int)!$this->authorise ('moderate', $user, true) : 0;
		if ($safefields) $message->bind($safefields);
		if ($fields) $message->bind($fields, array ('name', 'email', 'subject', 'message'), true);

		$topic = new KunenaForumTopic();
		$topic->category_id = $catid;
		$topic->hold = KunenaForum::TOPIC_CREATION;
		if ($safefields) $topic->bind($safefields);
		if ($fields) $topic->bind($fields, array ('subject','icon_id'), true);

		$message->setTopic($topic);
		return array($topic, $message);
	}

	/**
	 * @return KunenaForumCategory
	 */
	public function getParent() {
		$parent = KunenaForumCategoryHelper::get(intval($this->parent_id));
		if (!$parent->exists()) {
			$parent->name = JText::_ ( 'COM_KUNENA_TOPLEVEL' );
			$parent->_exists = true;
		}
		return $parent;
	}

	/**
	 * @param string $action
	 * @param mixed  $user
	 * @param bool   $silent
	 *
	 * @return bool
	 */
	public function authorise($action='read', $user=null, $silent=false) {
		if ($action == 'none') return true;
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		if ($user === null) {
			$user = KunenaUserHelper::getMyself();
		} elseif (!($user instanceof KunenaUser)) {
			$user = KunenaUserHelper::get($user);
		}
		if (empty($this->_authcache[$user->userid][$action])) {
			if (!isset(self::$actions[$action])) {
				JError::raiseError(500, JText::sprintf ( 'COM_KUNENA_LIB_AUTHORISE_INVALID_ACTION', $action ) );
				KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
				return false;
			}

			if (!isset($this->authorised[$user->userid])) $this->authorised[$user->userid] = KunenaAccess::getInstance()->authoriseActions($this, $user->userid);
			if (isset($this->authorised[$user->userid][$action]) && $this->authorised[$user->userid][$action] === false) {
				$this->_authcache[$user->userid][$action] = JText::_ ( 'COM_KUNENA_NO_ACCESS' );
			} else {
				$this->_authcache[$user->userid][$action] = null;
				foreach (self::$actions[$action] as $function) {
					if (!isset($this->_authfcache[$user->userid][$function])) {
						$authFunction = 'authorise'.$function;
						$this->_authfcache[$user->userid][$function] = $this->$authFunction($user);
					}
					$error = $this->_authfcache[$user->userid][$function];
					if ($error) {
						$this->_authcache[$user->userid][$action] = $error;
						break;
					}
				}
			}
		}
		$error = $this->_authcache[$user->userid][$action];
		if ($silent === false && $error) $this->setError ( $error );

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		if ($silent !== null) $error = !$error;
		return $error;
	}

	/**
	 * Get users, who can administrate this category.
	 *
	 * @param bool $includeGlobal
	 *
	 * @return array
	 */
	public function getAdmins($includeGlobal = true) {
		$access = KunenaAccess::getInstance();
		$userlist = array();
		if (!empty($this->catid)) $userlist = $access->getAdmins($this->catid);
		if ($includeGlobal) $userlist += $access->getAdmins();
		return $userlist;
	}

	/**
	 * Get users, who can moderate this category.
	 *
	 * @param bool $includeGlobal
	 * @param bool $objects
	 *
	 * @return array
	 */
	public function getModerators($includeGlobal = true, $objects = true) {
		$access = KunenaAccess::getInstance();
		$userlist = array();
		if (!empty($this->id)) {
			$userlist += $access->getModerators($this->id);
		}
		if ($includeGlobal) {
			$userlist += $access->getModerators();
		}
		if (empty($userlist)) return $userlist;
		$userlist = array_keys($userlist);
		return $objects ? KunenaUserHelper::loadUsers($userlist) : array_combine($userlist, $userlist);
	}

	/**
	 * Add or remove moderator from this category.
	 *
	 * @param mixed $user
	 * @param bool $value
	 *
	 * @return bool
	 *
	 * @example if ($category->authorise('admin')) $category->setModerator($user, true);
	 */
	public function setModerator($user = null, $value = false) {
		return KunenaAccess::getInstance()->setModerator($this, $user, $value);
	}

	/**
	 * Add moderator to this category.
	 *
	 * @param mixed $user
	 *
	 * @return bool
	 *
	 * @example if ($category->authorise('admin')) $category->addModerator($user);
	 */
	public function addModerator($user = null) {
		return $this->setModerator($user, true);
	}

	/**
	 * Add multiple moderators to this category.
	 *
	 * @param array $users
	 *
	 * @example if ($category->authorise('admin')) $category->addModerators(array($user1, $user2, $user3));
	 */
	public function addModerators($users = array()) {
		if ( empty($users) ) return;

		foreach($users as $user) {
			$user_inst = KunenaUserHelper::get($user);
			$this->setModerator($user_inst, true);
		}
	}

	/**
	 * Remove moderator from this category.
	 *
	 * @param mixed $user
	 *
	 * @return bool
	 *
	 * @example if ($category->authorise('admin')) $category->removeModerator($user);
	 */
	public function removeModerator($user = null) {
		return $this->setModerator($user, false);
	}

	/**
	 * @see KunenaDatabaseObject::bind()
	 */
	public function bind(array $src = null, array $fields = null, $include = false) {
		if (isset($src['channels']) && is_array($src['channels'])) $src['channels'] = implode(',', $src['channels']);
		$result = parent::bind($src, $fields, $include);

		if (!($this->params instanceof JRegistry)) {
			$registry = new JRegistry();
			if (is_array($this->params)) {
				$registry->loadArray($this->params);
			} else {
				$registry->loadString($this->params);
			}
			$this->params = $registry;
		}

		return $result;
	}

	/**
	 * @see KunenaDatabaseObject::load()
	 */
	public function load($id = null) {
		$exists = parent::load($id);

		if (!$this->_saving)
			$this->_alias = $this->get('alias', '');

		$registry = new JRegistry();
		if ($this->params) $registry->loadString($this->params);
		$this->params = $registry;

		// Register category if it exists
		if ($exists) KunenaForumCategoryHelper::register($this);
		return $exists;
	}

	/**
	 * @see KunenaDatabaseObject::check()
	 */
	public function check() {
		$this->alias = trim($this->alias);
		if (empty($this->alias)) {
			$this->alias = $this->name;
		}
		if ($this->alias != $this->_alias) {
			$this->alias = KunenaRoute::stringURLSafe($this->alias);
			if ($this->checkAlias($this->alias) === false) {
				$this->setError ( JText::sprintf ( 'COM_KUNENA_LIB_FORUM_CATEGORY_ERROR_ALIAS_RESERVED', $this->alias ) );
				return false;
			}
		}
		return true;
	}

	/**
	 * @see KunenaDatabaseObject::saveInternal()
	 */
	protected function saveInternal() {
		// Reorder categories
		$table = $this->getTable ();
		$table->bind ( $this->getProperties () );
		$table->exists ( $this->_exists );

		// Update alias
		$success = $this->addAlias($this->get('alias'));
		if ($success) $this->_alias = $this->alias;

		// TODO: remove this hack...
		if (!isset($this->_noreorder)) {
			$table->reorder ();
			$this->ordering = $table->ordering;
			unset($this->_noreorder);
		}

		// Clear cache
		$access = KunenaAccess::getInstance();
		$access->clearCache();

		$cache = JFactory::getCache('com_kunena', 'output');
		// FIXME: enable caching after fixing the issues
		//$cache->clean('categories');

		return true;
	}

	/**
	 * Purge old topics from this category. Removes topics from the database.
	 *
	 * @param       $time
	 * @param array $params
	 * @param int   $limit
	 *
	 * @return int	Number of purged topics.
	 */
	public function purge($time, $params=array(), $limit = 1000) {
		// FIXME: why time isn't used?
		if (!$this->exists()) {
			return 0;
		}

		$where = isset($params['where']) ? (string) $params['where'] : '';

		$db = JFactory::getDBO ();
		$query ="SELECT id FROM #__kunena_topics AS tt WHERE tt.category_id={$this->id} {$where} ORDER BY tt.last_post_time ASC";
		$db->setQuery($query, 0, $limit);
		$ids = $db->loadColumn();
		KunenaError::checkDatabaseError ();
		if (empty($ids)) return 0;

		$count = KunenaForumTopicHelper::delete($ids);

		KunenaUserHelper::recount();
		KunenaForumCategoryHelper::recount($this->id);
		KunenaForumMessageAttachmentHelper::cleanup();

		return $count;
	}

	/**
	 * Trash old topics in this category. Changes topic state to deleted.
	 *
	 * @param       $time
	 * @param array $params
	 * @param int   $limit
	 *
	 * @return int	Number of trashed topics.
	 */
	public function trash($time, $params = array(), $limit = 1000) {
		// FIXME: why time isn't used?
		if (!$this->exists()) {
			return 0;
		}

		$where = isset($params['where']) ? (string) $params['where'] : '';

		$db = JFactory::getDBO ();
		$query ="SELECT id FROM #__kunena_topics AS tt WHERE tt.category_id={$this->id} AND tt.hold!=2 {$where} ORDER BY tt.last_post_time ASC";
		$db->setQuery($query, 0, $limit);
		$ids = $db->loadColumn();
		KunenaError::checkDatabaseError ();
		if (empty($ids)) return 0;

		$count = KunenaForumTopicHelper::trash($ids);

		KunenaUserHelper::recount();
		KunenaForumCategoryHelper::recount($this->id);

		return $count;
	}

	/**
	 * Delete this category and all related information from the database.
	 *
	 * @return bool	True on success
	 */
	public function delete() {
		if (!$this->exists()) {
			return true;
		}

		if (!parent::delete()) {
			return false;
		}

		$access = KunenaAccess::getInstance();
		$access->clearCache();

		$db = JFactory::getDBO ();
		$queries[] = "DELETE FROM #__kunena_aliases WHERE type='catid' AND item={$db->quote($this->id)}";
		// Delete user topics
		$queries[] = "DELETE FROM #__kunena_user_topics WHERE category_id={$db->quote($this->id)}";
		// Delete user categories
		$queries[] = "DELETE FROM #__kunena_user_categories WHERE category_id={$db->quote($this->id)}";
		// Delete user read
		$queries[] = "DELETE FROM #__kunena_user_read WHERE category_id={$db->quote($this->id)}";
		// Delete thank yous
		$queries[] = "DELETE t FROM #__kunena_thankyou AS t INNER JOIN #__kunena_messages AS m ON m.id=t.postid WHERE m.catid={$db->quote($this->id)}";
		// Delete poll users
		$queries[] = "DELETE p FROM #__kunena_polls_users AS p INNER JOIN #__kunena_topics AS tt ON tt.poll_id=p.pollid WHERE tt.category_id={$db->quote($this->id)} AND tt.moved_id=0";
		// Delete poll options
		$queries[] = "DELETE p FROM #__kunena_polls_options AS p INNER JOIN #__kunena_topics AS tt ON tt.poll_id=p.pollid WHERE tt.category_id={$db->quote($this->id)} AND tt.moved_id=0";
		// Delete polls
		$queries[] = "DELETE p FROM #__kunena_polls AS p INNER JOIN #__kunena_topics AS tt ON tt.poll_id=p.id WHERE tt.category_id={$db->quote($this->id)} AND tt.moved_id=0";
		// Delete messages
		$queries[] = "DELETE m, t FROM #__kunena_messages AS m INNER JOIN #__kunena_messages_text AS t ON m.id=t.mesid WHERE m.catid={$db->quote($this->id)}";
		// TODO: delete attachments
		// TODO: delete keywords
		// Delete topics
		$queries[] = "DELETE FROM #__kunena_topics WHERE category_id={$db->quote($this->id)}";

		foreach ($queries as $query) {
			$db->setQuery($query);
			$db->query();
			KunenaError::checkDatabaseError ();
		}

		KunenaUserHelper::recount();
		KunenaForumMessageThankyouHelper::recount();

		$this->id = null;
		KunenaForumCategoryHelper::register($this);
		return true;
	}

	/**
	 * Method to check out the KunenaForumCategory object.
	 *
	 * @param	integer	$who
	 *
	 * @return	boolean	True if checked out by somebody else.
	 *
	 * @since 1.6
	 */
	public function checkout($who) {
		if (!$this->_exists)
			return false;

		// Create the user table object
		$table = $this->getTable ();
		$table->bind ( $this->getProperties () );
		$table->exists ( $this->_exists );
		$result = $table->checkout($who);

		// Assuming all is well at this point lets bind the data
		$params = $this->params;
		$this->setProperties ( $table->getProperties () );
		$this->params = $params;

		return $result;
	}

	/**
	 * Method to check in the KunenaForumCategory object.
	 *
	 * @return	boolean	True on success.
	 *
	 * @since 1.6
	 */
	public function checkin() {
		if (!$this->_exists)
			return true;

		// Create the user table object
		$table = $this->getTable ();
		$table->bind ( $this->getProperties () );
		$table->exists ( $this->_exists );
		$result = $table->checkin();

		// Assuming all is well at this point lets bind the data
		$params = $this->params;
		$this->setProperties ( $table->getProperties () );
		$this->params = $params;

		$cache = JFactory::getCache('com_kunena', 'output');
		// FIXME: enable caching after fixing the issues
		//$cache->clean('categories');

		return $result;
	}

	/**
	 * Method to check if an item is checked out.
	 *
	 * @param string	$with
	 *
	 * @return bool
	 *
	 * @since 1.6
	 */
	public function isCheckedOut($with) {
		if (!$this->_exists)
			return false;

		// Create the user table object
		$table = $this->getTable ();
		$table->bind ( $this->getProperties () );
		$table->exists ( $this->_exists );
		$result = $table->isCheckedOut($with);
		return $result;
	}

	/**
	 * @param KunenaForumTopic $topic
	 * @param int $topicdelta
	 * @param int $postdelta
	 *
	 * @return bool
	 */
	public function update($topic, $topicdelta=0, $postdelta=0) {
		if (!$topic->id) return false;

		$update = false;
		if ($topicdelta || $postdelta) {
			// Update topic and post count
			$this->numTopics += (int)$topicdelta;
			$this->numPosts += (int)$postdelta;
			$update = true;
		}
		if ($topic->exists() && $topic->hold==0 && $topic->moved_id==0 && $topic->category_id==$this->id
			&& ($this->last_post_time<$topic->last_post_time || ($this->last_post_time==$topic->last_post_time && $this->last_post_id <= $topic->last_post_id))) {
			// If topic has new post or last topic changed, we need to update cache
			$this->last_topic_id = $topic->id;
			$this->last_post_id = $topic->last_post_id;
			$this->last_post_time = $topic->last_post_time;
			$update = true;
		} elseif ($this->last_topic_id == $topic->id) {
			// If last topic/post got moved or deleted, we need to find last post
			$db = JFactory::getDBO ();
			$query = "SELECT * FROM #__kunena_topics WHERE category_id={$db->quote($this->id)} AND hold=0 AND moved_id=0 ORDER BY last_post_time DESC, last_post_id DESC";
			$db->setQuery ( $query, 0, 1 );
			$topic = $db->loadObject ();
			KunenaError::checkDatabaseError ();

			if ($topic) {
				$this->last_topic_id = $topic->id;
				$this->last_post_id = $topic->last_post_id;
				$this->last_post_time = $topic->last_post_time;
				$update = true;
			} else {
				$this->numTopics = 0;
				$this->numPosts = 0;
				$this->last_topic_id = 0;
				$this->last_post_id = 0;
				$this->last_post_time = 0;
				$update = true;
			}
		}
		if (!$update) return true;

		// TODO: remove this hack...
		$this->_noreorder = true;
		return $this->save();
	}

	/**
	 * Get if the user has subscribed on this category.
	 *
	 * @param mixed $userid
	 *
	 * @return bool
	 *
	 * @since	2.0.0
	 */
	public function getSubscribed($userid = null ) {
		if (!$this->exists()) {
			return false;
		}

		if (!$userid ) return false;

		$usercategory = KunenaForumCategoryUserHelper::get($this->id,$userid);

		return (bool) $usercategory->subscribed;
	}

	// Internal functions

	protected function buildInfo() {
		if ($this->_topics !== false)
			return;
		$this->_topics = 0;
		$this->_posts = 0;
		$this->_lastid = $this->id;
		/** @var array|KunenaForumCategory[] $categories */
		$categories[$this->id] = $this;
		// TODO: support channels
		//$categories += $this->getChannels();
		$categories += KunenaForumCategoryHelper::getChildren($this->id);
		foreach ($categories as $category) {
			$category->buildInfo();
			$lastCategory = $category->getLastCategory();
			$this->_topics += $category->_topics ? $category->_topics : max($category->numTopics, 0);
			$this->_posts += $category->_posts ? $category->_posts: max($category->numPosts, 0);
			if ($lastCategory->last_post_time && KunenaForumCategoryHelper::get($this->_lastid)->last_post_time < $lastCategory->last_post_time)
				$this->_lastid = $lastCategory->id;
		}
	}

	/**
	 * @param KunenaUser $user
	 *
	 * @return string|null
	 */
	protected function authoriseRead(KunenaUser $user) {
		static $catids = false;
		if ($catids === false) {
			$catids = KunenaAccess::getInstance()->getAllowedCategories ( $user );
		}

		// Checks if user can read category
		if (!$this->exists()) {
			return JText::_ ( 'COM_KUNENA_NO_ACCESS' );
		}
		if (empty($catids[$this->id])) {
			return JText::_ ( 'COM_KUNENA_NO_ACCESS' );
		}
		return null;
	}

	/**
	 * @param KunenaUser $user
	 *
	 * @return string|null
	 */
	protected function authoriseNotBanned(KunenaUser $user) {
		$banned = $user->isBanned();
		if ($banned) {
			$banned = KunenaUserBan::getInstanceByUserid($user->userid, true);
			if (!$banned->isLifetime()) {
				return JText::sprintf ( 'COM_KUNENA_POST_ERROR_USER_BANNED_NOACCESS_EXPIRY', KunenaDate::getInstance($banned->expiration)->toKunena());
			} else {
				return JText::_ ( 'COM_KUNENA_POST_ERROR_USER_BANNED_NOACCESS' );
			}
		}
		return null;
	}

	/**
	 * @param KunenaUser $user
	 *
	 * @return string|null
	 */
	protected function authoriseGuestWrite(KunenaUser $user) {
		// Check if user is guest and they can create or reply topics
		if ($user->userid == 0 && !KunenaFactory::getConfig()->pubwrite) {
			return JText::_ ( 'COM_KUNENA_POST_ERROR_ANONYMOUS_FORBITTEN' );
		}
		return null;
	}

	/**
	 * @param KunenaUser $user
	 *
	 * @return string|null
	 */
	protected function authoriseSubscribe(KunenaUser $user) {
		// Check if user is guest and they can create or reply topics
		$config = KunenaFactory::getConfig();
		if ($user->userid == 0 || !$config->allowsubscriptions || $config->topic_subscriptions == 'disabled') {
			return JText::_ ( 'COM_KUNENA_LIB_CATEGORY_AUTHORISE_FAILED_SUBSCRIPTIONS' );
		}
		return null;
	}

	/**
	 * @param KunenaUser $user
	 *
	 * @return string|null
	 */
	protected function authoriseCatSubscribe(KunenaUser $user) {
		// Check if user is guest and they can create or reply topics
		$config = KunenaFactory::getConfig();
		if ($user->userid == 0 || !$config->allowsubscriptions || $config->category_subscriptions == 'disabled') {
			return JText::_ ( 'COM_KUNENA_LIB_CATEGORY_AUTHORISE_FAILED_SUBSCRIPTIONS' );
		}
		return null;
	}

	/**
	 * @param KunenaUser $user
	 *
	 * @return string|null
	 */
	protected function authoriseFavorite(KunenaUser $user) {
		// Check if user is guest and they can create or reply topics
		if ($user->userid == 0 || !KunenaFactory::getConfig()->allowfavorites) {
			return JText::_ ( 'COM_KUNENA_LIB_CATEGORY_AUTHORISE_FAILED_FAVORITES' );
		}
		return null;
	}

	/**
	 * @param KunenaUser $user
	 *
	 * @return string|null
	 */
	protected function authoriseNotSection(KunenaUser $user) {
		// Check if category is not a section
		if ($this->isSection()) {
			return JText::_ ( 'COM_KUNENA_POST_ERROR_IS_SECTION' );
		}
		return null;
	}

	/**
	 * @param KunenaUser $user
	 *
	 * @return string|null
	 */
	protected function authoriseChannel(KunenaUser $user) {
		// Check if category is alias
		$channels = $this->getChannels('none');
		if (!isset($channels[$this->id])) {
			return JText::_ ( 'COM_KUNENA_POST_ERROR_IS_ALIAS' );
		}
		return null;
	}

	/**
	 * @param KunenaUser $user
	 *
	 * @return string|null
	 */
	protected function authoriseUnlocked(KunenaUser $user) {
		// Check that category is not locked or that user is a moderator
		if ($this->locked && (!$user->userid || !$user->isModerator($this))) {
			return JText::_ ( 'COM_KUNENA_POST_ERROR_CATEGORY_LOCKED' );
		}
		return null;
	}

	/**
	 * @param KunenaUser $user
	 *
	 * @return string|null
	 */
	protected function authoriseModerate(KunenaUser $user) {
		// Check that user is moderator
		if (!$user->userid || !$user->isModerator($this)) {
			return JText::_ ( 'COM_KUNENA_POST_NOT_MODERATOR' );
		}
		return null;
	}

	/**
	 * @param KunenaUser $user
	 *
	 * @return string|null
	 */
	protected function authoriseGlobalModerate(KunenaUser $user) {
		// Check that user is a global moderator
		if (!$user->userid || !$user->isModerator()) {
			return JText::_ ( 'COM_KUNENA_POST_NOT_GLOBAL_MODERATOR' );
		}
		return null;
	}

	/**
	 * @param KunenaUser $user
	 *
	 * @return string|null
	 */
	protected function authoriseAdmin(KunenaUser $user) {
		// Check that user is admin
		if (!$user->userid || !$user->isAdmin($this)) {
			return JText::_ ( 'COM_KUNENA_MODERATION_ERROR_NOT_ADMIN' );
		}
		return null;
	}

	/**
	 * @param KunenaUser $user
	 *
	 * @return string|null
	 */
	protected function authorisePoll(KunenaUser $user) {
		// Check if polls are enabled at all
		if (!KunenaFactory::getConfig()->pollenabled) {
			return JText::_ ( 'COM_KUNENA_LIB_CATEGORY_AUTHORISE_FAILED_POLLS_DISABLED' );
		}
		// Check if polls are not enabled in this category
		if (!$this->allow_polls) {
			return JText::_ ( 'COM_KUNENA_LIB_CATEGORY_AUTHORISE_FAILED_POLLS_NOT_ALLOWED' );
		}
		// Check if user is guest
		if ($user->userid == 0) {
			return JText::_ ( 'COM_KUNENA_POLL_NOT_LOGGED' );
		}
		return null;
	}

	/**
	 * @param KunenaUser $user
	 *
	 * @return string|null
	 */
	protected function authoriseUpload(KunenaUser $user) {
		// Check if attachments are allowed
		if (KunenaForumMessageAttachmentHelper::getExtensions($this, $user) === false) {
			return JText::_ ( 'COM_KUNENA_LIB_CATEGORY_AUTHORISE_FAILED_UPLOAD_NOT_ALLOWED' );
		}
		return null;
	}
}
