<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Integration
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class KunenaAccess
 */
class KunenaAccess
{
	const CATEGORY_SUBSCRIPTION = 1;
	const TOPIC_SUBSCRIPTION = 2;

	protected static $instance = null;
	protected $accesstypes = array('all'=>array());

	protected $adminsByCatid = null;
	protected $adminsByUserid = null;
	protected $moderatorsByCatid = null;
	protected $moderatorsByUserid = null;

	protected static $cacheKey = 'com_kunena.access.global.v1';

	public function __construct()
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
		JPluginHelper::importPlugin('kunena');
		$dispatcher = JDispatcher::getInstance();
		$classes = $dispatcher->trigger('onKunenaGetAccessControl');

		foreach ($classes as $class)
		{
			if (!is_object($class))
			{
				continue;
			}

			$types = $class->getAccessTypes();
			$this->accesstypes['all'][] = $class;
			unset ($types['all']);

			foreach ($types as $type)
			{
				$this->accesstypes[$type][] = $class;
			}
		}

		if (KunenaConfig::getInstance()->get('cache_adm'))
		{
			// Load administrators and moderators from cache
			$cache = JFactory::getCache('com_kunena', 'output');
			// FIXME: Experimental caching.
			$data = $cache->get(self::$cacheKey, 'com_kunena');

			if ($data)
			{
				$data = unserialize($data);
				$this->adminsByCatid = (array)$data['ac'];
				$this->adminsByUserid = (array)$data['au'];
				$this->moderatorsByCatid = (array)$data['mc'];
				$this->moderatorsByUserid = (array)$data['mu'];
			}
		}

		// If values were not cached (or users permissions have been changed), force reload
		if (!isset($this->adminsByCatid))
		{
			$this->clearCache();
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;
	}

	public static function getInstance()
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

		if (!self::$instance)
		{
			self::$instance = new KunenaAccess();
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

		return self::$instance;
	}

	public function clearCache()
	{
		$this->adminsByCatid = array();
		$this->adminsByUserid = array();
		$this->moderatorsByCatid = array();
		$this->moderatorsByUserid = array();

		// Reset read access for the current session
		$me = KunenaUserHelper::getMyself();
		JFactory::getApplication()->setUserState("com_kunena.user{$me->userid}_read", null);

		/** @var KunenaAccess $access */
		foreach ($this->accesstypes['all'] as $access)
		{
			if (method_exists($access, 'loadCategoryRoles'))
			{
				$this->storeRoles((array) $access->loadCategoryRoles());
			}
		}

		// Load native category moderators and administrators
		$db = JFactory::getDBO ();
		$query = "SELECT user_id, category_id, role FROM #__kunena_user_categories WHERE role IN (1,2)";
		$db->setQuery ( $query );
		$this->storeRoles((array) $db->loadObjectList ());
		KunenaError::checkDatabaseError ();

		// FIXME: enable caching after fixing the issues
		if (KunenaConfig::getInstance()->get('cache_adm'))
		{
			// Store new data into cache
			$cache = JFactory::getCache('com_kunena', 'output');
			$cache->store(serialize(array(
				'ac'=>$this->adminsByCatid,
				'au'=>$this->adminsByUserid,
				'mc'=>$this->moderatorsByCatid,
				'mu'=>$this->moderatorsByUserid,
				)), self::$cacheKey, 'com_kunena');
		}
	}

	/**
	 * @param KunenaForumCategory	$category
	 * @return array
	 */
	public function getAccessOptions($category)
	{
		$list = array();
		/** @var KunenaAccess $access */

		foreach ($this->accesstypes['all'] as $access)
		{
			if (method_exists($access, 'getAccessOptions'))
			{
				$list += $access->getAccessOptions(null, $category);
			}
		}

		// User has disabled access control
		$key = preg_replace('/[^\w\d]/', '-', $category->accesstype);

		if (!isset($list [$key]))
		{
			$list [$key]['access'] = array(
				'title' => JText::_('COM_KUNENA_ACCESS_UNKNOWN'),
				'desc' => JText::sprintf('COM_KUNENA_ACCESS_UNKNOWN_DESC', $category->accesstype),
				'input' => $category->access
			);
		}

		return $list;
	}

	/**
	 * @param KunenaForumCategory $category
	 *
	 * @return string
	 */
	public function getAccessTypesList($category)
	{
		static $enabled = false;

		if (!$enabled)
		{
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

		$exists = 0;
		$accesstypes = array ();

		foreach ($this->accesstypes as $type=>$list)
		{
			if ($type == 'all')
			{
				continue;
			}

			foreach ($list as $access)
			{
				if (method_exists($access, 'getAccessOptions'))
				{
					$string = JText::_('COM_KUNENA_INTEGRATION_TYPE_'.preg_replace('/[^\w\d]/', '_', $type));
					$accesstypes [$string] = JHtml::_ ( 'select.option', $type, $string );
					$exists |= $type == $category->accesstype;

					break;
				}
			}
		}

		ksort($accesstypes);

		// User has disabled access control
		if (!$exists)
		{
			$string = JText::sprintf('COM_KUNENA_INTEGRATION_UNKNOWN', $category->accesstype);
			$accesstypes [$string] = JHtml::_ ( 'select.option', $category->accesstype, $string );
		}

		return JHtml::_ ( 'select.genericlist', $accesstypes, 'accesstype', 'class="inputbox" size="'.count($accesstypes).'" onchange="kShowAccessType(\'kaccess\', $(this))"', 'value', 'text', $category->accesstype );
	}

	/**
	 * Get access groups for the selected category.
	 *
	 * @param KunenaForumCategory  $category  Category
	 * @return array|null
	 */
	public function getCategoryAccess(KunenaForumCategory $category)
	{
		$list = array();

		$accesstype = $category->accesstype;

		if (!isset($this->accesstypes[$accesstype]))
		{
			return $list;
		}

		/** @var KunenaAccess $access */
		foreach ($this->accesstypes[$accesstype] as $access)
		{
			if (method_exists($access, 'getCategoryAccess'))
			{
				$list += $access->getCategoryAccess($category);
			}
		}

		if (!$list)
		{
			// Legacy support.
			$id = $category->access;
			$name = $this->getGroupName($accesstype, $id);
			$list["{$accesstype}.{$id}"] = array('type'=>'joomla.level', 'id'=>$id,
				'title'=>$name);
		}

		return $list;
	}

	/**
	 * Get group name in selected access type.
	 *
	 * @param string	$accesstype	Access type.
	 * @param mixed		$id			Group id.
	 * @return string|null
	 *
	 * @deprecated 3.0.1
	 */
	public function getGroupName($accesstype, $id)
	{
		if (!isset($this->accesstypes[$accesstype]))
		{
			return JText::sprintf('COM_KUNENA_INTEGRATION_UNKNOWN', $id);
		}

		/** @var KunenaAccess $access */
		foreach ($this->accesstypes[$accesstype] as $access)
		{
			if (method_exists($access, 'getGroupName'))
			{
				return $access->getGroupName($accesstype, $id);
			}
		}

		return null;
	}

	/**
	 * Get category administrators.
	 *
	 * @param int $catid Category Id
	 * @param bool $all
	 *
	 * @return array
	 */
	public function getAdmins($catid = 0, $all = false)
	{
		$list = !empty($this->adminsByCatid[$catid]) ? $this->adminsByCatid[$catid] : array();

		if ($all && !empty($this->adminsByCatid[0]))
		{
			$list += $this->adminsByCatid[0];
		}

		return $list;
	}

	/**
	 * Get category moderators.
	 *
	 * @param int $catid Category Id
	 * @param bool $all
	 *
	 * @return array
	 */
	public function getModerators($catid = 0, $all = false)
	{
		$list = !empty($this->moderatorsByCatid[$catid]) ? $this->moderatorsByCatid[$catid] : array();

		if ($all && !empty($this->moderatorsByCatid[0]))
		{
			$list += $this->moderatorsByCatid[0];
		}

		return $list;
	}

	/**
	 * @param mixed $user
	 *
	 * @return array
	 */
	public function getAdminStatus($user = null)
	{
		if (!($user instanceof KunenaUser))
		{
			$user = KunenaFactory::getUser($user);
		}

		return !empty($this->adminsByUserid[$user->userid]) ? $this->adminsByUserid[$user->userid] : array();
	}

	/**
	 * @param mixed $user
	 *
	 * @return array
	 */
	public function getModeratorStatus($user = null)
	{
		if (!($user instanceof KunenaUser))
		{
			$user = KunenaFactory::getUser($user);
		}

		return !empty($this->moderatorsByUserid[$user->userid]) ? $this->moderatorsByUserid[$user->userid] : array();
	}

	/**
	 * @param mixed $user
	 * @param int  $catid
	 *
	 * @return bool
	 */
	public function isAdmin($user = null, $catid = 0)
	{
		if (!($user instanceof KunenaUser))
		{
			$user = KunenaFactory::getUser($user);
		}

		// Guests and banned users cannot be administrators
		if (!$user->exists() || $user->isBanned())
		{
			return false;
		}

		// In backend every logged in user has global admin rights (for now)
		if (JFactory::getApplication()->isAdmin() && $user->userid == KunenaUserHelper::getMyself()->userid)
		{
			return true;
		}

		// Is user a global administrator?
		if (!empty($this->adminsByUserid[$user->userid][0]))
		{
			return true;
		}

		// Is user a category administrator?
		if (!empty($this->adminsByUserid[$user->userid][$catid]))
		{
			return true;
		}

		return false;
	}

	/**
	 * @param mixed $user
	 * @param int  $catid
	 *
	 * @return bool
	 */
	public function isModerator($user = null, $catid = 0)
	{
		if (!($user instanceof KunenaUser))
		{
			$user = KunenaUserHelper::get($user);
		}

		// Guests and banned users cannot be moderators
		if (!$user->exists() || $user->isBanned())
		{
			return false;
		}

		// Administrators are always moderators
		if ($this->isAdmin($user, $catid))
		{
			return true;
		}

		if (!empty($this->moderatorsByUserid[$user->userid]))
		{
			// Is user a global moderator?
			if (!empty($this->moderatorsByUserid[$user->userid][0]))
			{
				return true;
			}

			// Is user a category moderator?
			if (!empty($this->moderatorsByUserid[$user->userid][$catid]))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Assign user as moderator or resign him.
	 *
	 * @param KunenaForumCategory $category
	 * @param mixed $user
	 * @param bool $status
	 *
	 * @return bool
	 *
	 * @example if ($category->authorise('admin')) $category->setModerator($user, true);
	 */
	public function setModerator($category, $user = null, $status = true)
	{
		// Check if category exists
		if ($category && !$category->exists())
		{
			return false;
		}

		$category_id = $category ? $category->id : 0;
		$status = intval($status);

		// Check if user exists
		if (!($user instanceof KunenaUser))
		{
			$user = KunenaUserHelper::get($user);
		}

		if (!$user->exists()) {
			return false;
		}

		$success = true;
		$usercategory = KunenaForumCategoryUserHelper::get($category_id, $user);

		if (($usercategory->role == 0 && $status) || ($usercategory->role == 1 && !$status))
		{
			$usercategory->role = $status;
			$success = $usercategory->save();

			// Clear role cache
			$this->clearCache();

			// Change user moderator status
			$moderator = $this->getModeratorStatus($user);

			if ($user->moderator != !empty($moderator))
			{
				$user->moderator = intval(!empty($moderator));
				$success = $user->save();
			}
		}

		return $success;
	}

	/**
	 * @param mixed $user
	 *
	 * @return mixed
	 */
	public function getAllowedCategories($user = null)
	{
		static $read = array();

		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

		if (!($user instanceof KunenaUser))
		{
			$user = KunenaFactory::getUser($user);
		}

		if (!isset($read[$user->userid]))
		{
			$id = $user->userid;
			$app = JFactory::getApplication();
			// TODO: handle guests/bots with no userstate
			$read[$id] = $app->getUserState("com_kunena.user{$id}_read");

			if ($read[$id] === null)
			{
				$list = array();
				$categories = KunenaForumCategoryHelper::getCategories(false, false, 'none');

				foreach ($categories as $category)
				{
					// Remove unpublished categories
					if ($category->published != 1)
					{
						unset($categories[$category->id]);
					}

					// Moderators have always access
					if (self::isModerator($user, $category->id))
					{
						$list[$category->id] = $category->id;
						unset($categories[$category->id]);
					}
				}

				// Get external authorization
				if (!empty($categories))
				{
					/** @var KunenaAccess $access */
					foreach ($this->accesstypes['all'] as $access)
					{
						if (method_exists($access, 'authoriseCategories'))
						{
							$list += $access->authoriseCategories($id, $categories);
						}
					}
				}

				// Clean up and filter the resulting list by using only array keys.
				$list = array_keys($list);
				JArrayHelper::toInteger($list);
				$read[$id] = array_combine($list, $list);
				unset($read[$id][0]);
				$app->setUserState("com_kunena.user{$id}_read", $read[$id]);
			}
		}
		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.__CLASS__.'::'.__FUNCTION__.'()') : null;

		return $read[$user->userid];
	}

	/**
	 * Authorise user actions in a category.
	 *
	 * Function returns a list of authorised actions. Missing actions are threaded as inherit.
	 *
	 * @param KunenaForumCategory $category
	 * @param int $userid
	 *
	 * @return array
	 */
	public function authoriseActions(KunenaForumCategory $category, $userid)
	{
		$list = array();

		if (empty($this->accesstypes[$category->accesstype]))
		{
			return $list;
		}

		foreach ($this->accesstypes[$category->accesstype] as $access)
		{
			/** @var KunenaAccess $access */
			if (method_exists($access, 'getAuthoriseActions'))
			{
				$sublist = $access->getAuthoriseActions($category, $userid);

				foreach ($sublist as $key=>$value)
				{
					$list[$key] = !empty($list[$key]) || $value;
				}
			}
		}
		return $list;
	}

	/**
	 * @param mixed $user
	 * @param int $catid
	 * @param bool $string
	 *
	 * @return string|array
	 */
	public function getAllowedHold($user, $catid, $string = true)
	{
		// hold = 0: normal
		// hold = 1: unapproved
		// hold = 2: deleted
		if (!($user instanceof KunenaUser))
		{
			$user = KunenaFactory::getUser($user);
		}

		$config = KunenaFactory::getConfig ();

		$hold [0] = 0;

		if ($this->isModerator($user, $catid)) {
			$hold [1] = 1;
		}

		if (($config->mod_see_deleted == '0' && $this->isAdmin($user, $catid))
			|| ($config->mod_see_deleted == '1' && $this->isModerator($user, $catid)))
		{
			$hold [2] = 2;
			$hold [3] = 3;
		}

		if ($string)
		{
			$hold = implode ( ',', $hold );
		}

		return $hold;
	}

	/**
	 * @param int  $catid
	 * @param mixed $topic
	 * @param mixed $type
	 * @param bool $moderators
	 * @param bool $admins
	 * @param mixed $excludeList
	 *
	 * @return array
	 */
	public function getSubscribers($catid, $topic, $type = false, $moderators = false, $admins = false, $excludeList = null)
	{
		$topic = KunenaForumTopicHelper::get($topic);
		$category = $topic->getCategory();

		if (!$topic->exists())
		{
			return array();
		}

		$modlist = array();

		if (!empty($this->moderatorsByCatid[0]))
		{
			$modlist += $this->moderatorsByCatid[0];
		}

		if (!empty($this->moderatorsByCatid[$catid]))
		{
			$modlist += $this->moderatorsByCatid[$catid];
		}

		$adminlist = array();

		if (!empty($this->adminsByCatid[0]))
		{
			$adminlist += $this->adminsByCatid[0];
		}

		if (!empty($this->adminsByCatid[$catid]))
		{
			$adminlist += $this->adminsByCatid[$catid];
		}

		if ($type)
		{
			$subscribers = $this->loadSubscribers($topic, $type);
			$allow = $deny = array();

			if (!empty($subscribers))
			{
				/** @var KunenaAccess $access */
				foreach ($this->accesstypes[$category->accesstype] as $access)
				{
					if (method_exists($access, 'authoriseUsers'))
					{
						list ($a, $d) = $access->authoriseUsers($topic, $subscribers);

						if (!empty($a))
						{
							$allow += array_combine($a, $a);
						}

						if (!empty($d))
						{
							$deny += array_combine($d, $d);
						}
					}
				}
			}

			$subslist = array_diff($allow, $deny);
			// Category administrators and moderators override ACL
			$subslist += array_intersect_key($adminlist, array_flip($subscribers));
			$subslist += array_intersect_key($modlist, array_flip($subscribers));
		}

		if (!$moderators)
		{
			$modlist = array();
		}
		else
		{
			// If category has no moderators, send email to admins instead
			if (empty($modlist))
			{
				$admins = true;
			}
		}

		if (!$admins)
		{
			$adminlist = array();
		}

		$query = new KunenaDatabaseQuery();
		$query->select('u.id, u.name, u.username, u.email');
		$query->from('#__users AS u');
		$query->where("u.block=0");
		$userlist = array();

		if (!empty($subslist))
		{
			$userlist += $subslist;
			$subslist = implode(',', array_keys($subslist));
			$query->select("IF( u.id IN ({$subslist}), 1, 0 ) AS subscription");
		}
		else
		{
			$query->select("0 AS subscription");
		}

		if (!empty($modlist))
		{
			$userlist += $modlist;
			$modlist = implode(',', array_keys($modlist));
			$query->select("IF( u.id IN ({$modlist}), 1, 0 ) AS moderator");
		}
		else
		{
			$query->select("0 AS moderator");
		}

		if (!empty($adminlist))
		{
			$userlist += $adminlist;
			$adminlist = implode(',', array_keys($adminlist));
			$query->select("IF( u.id IN ({$adminlist}), 1, 0 ) AS admin");
		}
		else
		{
			$query->select("0 AS admin");
		}

		if (empty($excludeList))
		{
			// false, null, '', 0 and array(): get all subscribers
			$excludeList = array();
		}
		elseif (is_array($excludeList))
		{
			// array() needs to be flipped to get userids into keys
			$excludeList = array_flip($excludeList);
		}
		else
		{
			// Others: let's assume that we have comma separated list of values (string)
			$excludeList = array_flip(explode(',', (string) $excludeList));
		}

		$userlist = array_diff_key($userlist, $excludeList);
		$userids = array();

		if (!empty($userlist))
		{
			$userlist = implode(',', array_keys($userlist));
			$query->where("u.id IN ({$userlist})");
			$db = JFactory::getDBO();
			$db->setQuery ( $query );
			$userids = (array) $db->loadObjectList ();
			KunenaError::checkDatabaseError();
		}

		return $userids;
	}

	/**
	 * @param array $list
	 */
	protected function storeRoles(array $list = null)
	{
		if (empty($list))
		{
			return;
		}

		foreach ($list as $item)
		{
			$userid = intval ($item->user_id);
			$catid = intval ($item->category_id);

			if (!$userid)
			{
				continue;
			}

			if ($item->role == KunenaForum::ADMINISTRATOR)
			{
				$this->adminsByUserid [$userid] [$catid] = 1;
				$this->adminsByCatid [$catid] [$userid] = 1;
			}
			elseif ($item->role == KunenaForum::MODERATOR)
			{
				$this->moderatorsByUserid [$userid] [$catid] = 1;
				$this->moderatorsByCatid [$catid] [$userid] = 1;
			}
		}
	}

	/**
	 * @param KunenaForumTopic $topic
	 * @param bool             $type
	 *
	 * @return array
	 */
	public function loadSubscribers(KunenaForumTopic $topic, $type)
	{
		$category = $topic->getCategory();
		$db = JFactory::getDBO ();
		$query = array();

		if ($type & self::TOPIC_SUBSCRIPTION)
		{
			// Get topic subscriptions
			$query[] = "SELECT user_id FROM #__kunena_user_topics WHERE topic_id={$topic->id} AND subscribed=1";
		}

		if ($type & self::CATEGORY_SUBSCRIPTION)
		{
			// Get category subscriptions
			$query[] = "SELECT user_id FROM #__kunena_user_categories WHERE category_id={$category->id} AND subscribed=1";
		}

		$query = implode(' UNION ', $query);
		$db->setQuery ($query);
		$userids = (array) $db->loadColumn();
		KunenaError::checkDatabaseError();

		return $userids;
	}
}
