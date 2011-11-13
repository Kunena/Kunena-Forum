<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Integration
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class KunenaAccess {
	protected static $instance = null;
	protected $accesstypes = array('all'=>array());

	protected $adminsByCatid = null;
	protected $adminsByUserid = null;
	protected $moderatorsByCatid = null;
	protected $moderatorsByUserid = null;

	protected static $cacheKey = 'com_kunena.access.global';

	public function __construct() {
		JPluginHelper::importPlugin('kunena');
		$dispatcher = JDispatcher::getInstance();
		$classes = $dispatcher->trigger('onKunenaGetAccessControl');
		foreach ($classes as $class) {
			if (!is_object($class)) continue;
			$types = $class->getAccessTypes();
			$this->accesstypes['all'][] = $class;
			unset ($types['all']);
			foreach ($types as $type) {
				$this->accesstypes[$type][] = $class;
			}
		}

		// Load administrators and moderators from cache
		$cache = JFactory::getCache('com_kunena', 'output');
		$data = $cache->get(self::$cacheKey, 'com_kunena');
		if ($data) {
			$data = unserialize($data);
			if (isset($data['v']) && $data['v'] == 1) {
				$this->adminsByCatid = (array)$data['ac'];
				$this->adminsByUserid = (array)$data['au'];
				$this->moderatorsByCatid = (array)$data['mc'];
				$this->moderatorsByUserid = (array)$data['mu'];
			}
		}
		//$my = JFactory::getUser();
		// If values were not cached (or users permissions have been changed), force reload
		if (!isset($this->adminsByCatid)) { // || ($my->id && $my->authorize('com_kunena', 'administrator') == empty($this->adminsByUserid[$my->id][0]) )) {
			$this->clearCache();
		}
	}

	static public function getInstance() {
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		if (!self::$instance) {
			self::$instance = new KunenaAccess();
		}
		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		return self::$instance;
	}

	public function clearCache() {
		$this->adminsByCatid = array();
		$this->adminsByUserid = array();
		$this->moderatorsByCatid = array();
		$this->moderatorsByUserid = array();

		$roles = array();
		foreach ($this->accesstypes['all'] as $access) {
			if (method_exists($access, 'loadCategoryRoles')) {
				$this->storeRoles((array) $access->loadCategoryRoles());
			}
		}
		// Load native category moderators and administrators
		$db = JFactory::getDBO ();
		$query = "SELECT user_id, category_id, role FROM #__kunena_user_categories WHERE role IN (1,2)";
		$db->setQuery ( $query );
		$this->storeRoles((array) $db->loadObjectList ());
		KunenaError::checkDatabaseError ();

		// Store new data into cache
		$cache = JFactory::getCache('com_kunena', 'output');
		$cache->store(serialize(array(
			'v'=>1, // version identifier
			'ac'=>$this->adminsByCatid,
			'au'=>$this->adminsByUserid,
			'mc'=>$this->moderatorsByCatid,
			'mu'=>$this->moderatorsByUserid,
			)), self::$cacheKey, 'com_kunena');
	}

	/**
	 * Get HTML list of the available groups
	 *
	 * @param string	Access type.
	 * @param int		Group id.
	 */
	public function getAccessOptions($category) {
		$list = array();
		foreach ($this->accesstypes['all'] as $access) {
			if (method_exists($access, 'getAccessOptions')) {
				$list += $access->getAccessOptions(null, $category);
			}
		}
		return $list;
	}

	public function getAccessTypesList($category) {
		static $enabled = false;
		if (!$enabled) {
			$enabled = true;
			JFactory::getDocument()->addScriptDeclaration("function kShowAccessType(htmlclass, el) {
	var selected = el.getChildren().filter(function(option){ return option.selected; });
	var name = selected[0].value;
	name = name.replace(/[^\\w\\d]+/, '-');
	$$('.'+htmlclass).each(function(e){
		e.setStyle('display', 'none');
	});
	$$('.'+htmlclass+'-'+name).each(function(e){
		e.setStyle('display', '');
	});
}
window.addEvent('domready', function(){
	var item = $('accesstype');
	if (item) {
		kShowAccessType('kaccess', item);
	}
});");
		}

		$accesstypes = array ();
		foreach ($this->accesstypes as $type=>$list) {
			if ($type == 'all') continue;
			foreach ($list as $access) {
				if (method_exists($access, 'getAccessOptions')) {
					// TODO: change none type ->
					$string = JText::_('COM_KUNENA_INTEGRATION_'.preg_replace('/[^\w\d]/', '_', $type=='none' ? 'joomla.group' : $type));
					$accesstypes [$string] = JHTML::_ ( 'select.option', $type, $string );
					break;
				}
			}
		}
		ksort($accesstypes);
		return JHTML::_ ( 'select.genericlist', $accesstypes, 'accesstype', 'class="inputbox" size="2" onchange="javascript:kShowAccessType(\'kaccess\', $(this))"', 'value', 'text', $category->accesstype );
	}


	/**
	 * Get group name in selected access type.
	 *
	 * @param string	Access type.
	 * @param mixed		Group id.
	 */
	public function getGroupName($accesstype, $id) {
		foreach ($this->accesstypes[$accesstype] as $access) {
			if (method_exists($access, 'getGroupName')) {
				return $access->getGroupName($accesstype, $id);
			}
		}
	}

	public function getAdmins($catid = 0) {
		return !empty($this->adminsByCatid[$catid]) ? $this->adminsByCatid[$catid] : array();
	}

	public function getModerators($catid = 0) {
		return !empty($this->moderatorsByCatid[$catid]) ? $this->moderatorsByCatid[$catid] : array();
	}

	public function getAdminStatus($user = null) {
		$user = KunenaFactory::getUser($user);
		return !empty($this->adminsByUserid[$user->userid]) ? $this->adminsByUserid[$user->userid] : array();
	}

	public function getModeratorStatus($user = null) {
		$user = KunenaFactory::getUser($user);
		return !empty($this->moderatorsByUserid[$user->userid]) ? $this->moderatorsByUserid[$user->userid] : array();
	}

	public function isAdmin($user = null, $catid = 0) {
		$user = KunenaFactory::getUser($user);

		// Guests and banned users cannot be administrators
		if (!$user->exists() || $user->isBanned()) return false;

		// In backend every logged in user has global admin rights (for now)
		if (JFactory::getApplication()->isAdmin() && $user->userid == KunenaUserHelper::getMyself()->userid)
			return true;

		// If $catid is not numeric: Is user administrator in ANY category?
		if (!is_numeric($catid)) return !empty($this->adminsByUserid[$user->userid]);

		// Is user a global administrator?
		if (!empty($this->adminsByUserid[$user->userid][0])) return true;
		// Is user a category administrator?
		if (!empty($this->adminsByUserid[$user->userid][$catid])) return true;

		return false;
	}

	public function isModerator($user = null, $catid = 0) {
		$user = KunenaFactory::getUser($user);

		// Guests and banned users cannot be moderators
		if (!$user->exists() || $user->isBanned()) return false;

		// Administrators are always moderators
		if ($this->isAdmin($user, $catid)) return true;

		if (!empty($this->moderatorsByUserid[$user->userid])) {
			// Is user a global moderator?
			if (!empty($this->moderatorsByUserid[$user->userid][0])) return true;
			// Were we looking only for global moderator?
			if (!is_numeric($catid)) return false;
			// Is user moderator in ANY category?
			if ($catid == 0) return true;
			// Is user a category moderator?
			if (!empty($this->moderatorsByUserid[$user->userid][$catid])) return true;
		}
		return false;
	}

	/**
	 * Assign user as moderator or resign him
	 *
	 * @example if ($category->authorise('admin')) $category->setModerator($user, true);
	 **/
	public function setModerator($category, $user = null, $status = true) {
		// Check if category exists
		if ($category && !$category->exists()) return false;
		$category_id = $category ? $category->id : 0;
		$status = intval($status);

		// Check if user exists
		$user = KunenaUserHelper::get($user);
		if (!$user->exists()) {
			return false;
		}
		$success = true;
		$usercategory = KunenaForumCategoryUserHelper::get($category_id, $user);
		if (($usercategory->role == 0 && $status) || ($usercategory->role == 1 && !$status)) {
			$usercategory->role = $status;
			$success = $usercategory->save();

			// Clear role cache
			$this->clearCache();

			// Change user moderator status
			$moderator = $this->getModeratorStatus($user);
			if ($user->moderator != !empty($moderator)) {
				$user->moderator = intval(!empty($moderator));
				$success = $user->save();
			}
		}
		return $success;
	}

	public function getAllowedCategories($user = null) {
		static $read = array();

		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		$user = KunenaFactory::getUser($user);
		$id = $user->userid;

		if (!isset($read[$id])) {
			$app = JFactory::getApplication();
			// TODO: handle guests/bots with no userstate
			$read[$id] = $app->getUserState("com_kunena.user{$id}_read");
			if ($read[$id] === null) {
				$read[$id] = array();
				$categories = KunenaForumCategoryHelper::getCategories(false, false, 'none');
				foreach ( $categories as $category ) {
					// Remove unpublished categories
					if (!$category->published) {
						unset($categories[$category->id]);
					}
					// Moderators have always access
					if (self::isModerator($id, $category->id)) {
						$read[$id][$category->id] = $category->id;
						unset($categories[$category->id]);
					}
				}

				// Get external authorization
				if (!empty($categories)) {
					foreach ($this->accesstypes['all'] as $access) {
						if (method_exists($access, 'authoriseCategories')) {
							$read[$id] += $access->authoriseCategories($id, $categories);
						}
					}
				}
				$app->setUserState("com_kunena.user{$id}_read", $read[$id]);
			}
		}
		$allowed = $read[$id];
		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		return $allowed;
	}

	public function getAllowedHold($user, $catid, $string=true) {
		// hold = 0: normal
		// hold = 1: unapproved
		// hold = 2: deleted
		$user = KunenaFactory::getUser($user);
		$config = KunenaFactory::getConfig ();

		$hold [0] = 0;
		if ($this->isModerator ( $user->userid, $catid )) {
			$hold [1] = 1;
		}
		if (($config->mod_see_deleted == '0' && $this->isAdmin ( $user->userid, $catid ))
			|| ($config->mod_see_deleted == '1' && $this->isModerator( $user->userid, $catid ))) {
			$hold [2] = 2;
			$hold [3] = 3;
	}
		if ($string) $hold = implode ( ',', $hold );
		return $hold;
	}

	public function getSubscribers($catid, $topic, $subscriptions = false, $moderators = false, $admins = false, $excludeList = null) {
		$topic = KunenaForumTopicHelper::get($topic);
		if (!$topic->exists())
			return array();

		if ($subscriptions) {
			$subslist = $this->loadSubscribers($topic, (int)$subscriptions);
		}
		if ($moderators) {
			$modlist = array();
			if (!empty($this->moderatorsByCatid[0])) $modlist += $this->moderatorsByCatid[0];
			if (!empty($this->moderatorsByCatid[$catid])) $modlist += $this->moderatorsByCatid[$catid];

			// If category has no moderators, send email to admins instead
			if (empty($modlist)) $admins = true;
		}
		if ($admins) {
			$adminlist = array();
			if (!empty($this->adminsByCatid[0])) $adminlist += $this->adminsByCatid[0];
			if (!empty($this->adminsByCatid[$catid])) $adminlist += $this->adminsByCatid[$catid];
		}

		$query = new KunenaDatabaseQuery();
		$query->select('u.id, u.name, u.username, u.email');
		$query->from('#__users AS u');
		$query->where("u.block=0");
		$userlist = array();
		if (!empty($subslist)) {
			$userlist = $subslist;
			$subslist = implode(',', array_keys($subslist));
			$query->select("IF( u.id IN ({$subslist}), 1, 0 ) AS subscription");
		} else {
			$query->select("0 AS subscription");
		}
		if (!empty($modlist)) {
			$userlist += $modlist;
			$modlist = implode(',', array_keys($modlist));
			$query->select("IF( u.id IN ({$modlist}), 1, 0 ) AS moderator");
		} else {
			$query->select("0 AS moderator");
		}
		if (!empty($adminlist)) {
			$userlist += $adminlist;
			$adminlist = implode(',', array_keys($adminlist));
			$query->select("IF( u.id IN ({$adminlist}), 1, 0 ) AS admin");
		} else {
			$query->select("0 AS admin");
		}
		if (empty($excludeList)) {
			// false, null, '', 0 and array(): get all subscribers
			$excludeList = array();
		} elseif (is_array($excludeList)) {
			// array() needs to be flipped to get userids into keys
			$excludeList = array_flip($excludeList);
		} else {
			// Others: let's assume that we have comma separated list of values (string)
			$excludeList = array_flip(explode(',', (string) $excludeList));
		}
		$userlist = array_diff_key($userlist, $excludeList);
		$userids = array();
		if (!empty($userlist)) {
			$userlist = implode(',', array_keys($userlist));
			$query->where("u.id IN ({$userlist})");
			$db = JFactory::getDBO();
			$db->setQuery ( $query );
			$userids = (array) $db->loadObjectList ();
			KunenaError::checkDatabaseError();
		}
		return $userids;
	}

	protected function storeRoles(array $list = null) {
		if (empty($list)) return;
		foreach ( $list as $item ) {
			$userid = intval ( $item->user_id );
			$catid = intval ( $item->category_id );
			if (!$userid) continue;

			if ($item->role == KunenaForum::ADMINISTRATOR) {
				$this->adminsByUserid [$userid] [$catid] = 1;
				$this->adminsByCatid [$catid] [$userid] = 1;
			} elseif ($item->role == KunenaForum::MODERATOR) {
				$this->moderatorsByUserid [$userid] [$catid] = 1;
				$this->moderatorsByCatid [$catid] [$userid] = 1;
			}
		}
	}

	protected function &loadSubscribers($topic, $subsriptions) {
		$category = $topic->getCategory();
		$db = JFactory::getDBO ();
		$query = array();
		if ($subsriptions == 1 || $subsriptions == 2) {
			// Get topic subscriptions
			//FIXME: user topics is missing a column
			$once = false; //KunenaFactory::getConfig()->topic_subscriptions == 'first' ? 'AND future1=0' : '';
			$query[] = "SELECT user_id FROM #__kunena_user_topics WHERE topic_id={$topic->id} AND subscribed=1 {$once}";
		}
		if ($subsriptions == 1 || $subsriptions == 3) {
			// Get category subscriptions
			$query[] = "SELECT user_id FROM #__kunena_user_categories WHERE category_id={$category->id} AND subscribed=1";
		}
		$query = implode(' UNION ', $query);
		$db->setQuery ($query);
		$userids = (array) $db->loadResultArray();
		KunenaError::checkDatabaseError();
		$allow = $deny = array();
		if (!empty($userids)) {
			foreach ($this->accesstypes[$category->accesstype] as $access) {
				if (method_exists($access, 'authoriseUsers')) {
					list ($a, $d) = $access->authoriseUsers($topic, $userids);
					$allow = array_combine($allow, $a);
					$deny = array_combine($deny, $d);
				}
			}
		}
		return array_diff($allow, $deny);
	}
}