<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Integration
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Access;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Database\Exception\ExecutionFailureException;
use Joomla\Utilities\ArrayHelper;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\Category;
use Kunena\Forum\Libraries\Forum\Forum;
use Kunena\Forum\Libraries\Forum\Topic\Helper;
use Kunena\Forum\Libraries\Forum\Topic\Topic;
use Kunena\Forum\Libraries\Profiler\KunenaProfiler;
use Kunena\Forum\Libraries\User\KunenaUser;
use function defined;

/**
 * Class KunenaAccess
 *
 * @since   Kunena 6.0
 */
class Access
{
	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	const CATEGORY_SUBSCRIPTION = 1;

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	const TOPIC_SUBSCRIPTION = 2;

	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected static $instance = null;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected static $cacheKey = 'com_kunena.access.global.v1';

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	protected $accesstypes = ['all' => []];

	/**
	 * @var     array|null
	 * @since   Kunena 6.0
	 */
	protected $adminsByCatid = null;

	/**
	 * @var     array|null
	 * @since   Kunena 6.0
	 */
	protected $adminsByUserid = null;

	/**
	 * @var     array|null
	 * @since   Kunena 6.0
	 */
	protected $moderatorsByCatid = null;

	/**
	 * @var     array|null
	 * @since   Kunena 6.0
	 */
	protected $moderatorsByUserid = null;

	/**
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function __construct()
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
		PluginHelper::importPlugin('kunena');

		$classes = Factory::getApplication()->triggerEvent('onKunenaGetAccessControl');

		foreach ($classes as $class)
		{
			if (!is_object($class))
			{
				continue;
			}

			$types                      = $class->getAccessTypes();
			$this->accesstypes['all'][] = $class;
			unset($types['all']);

			foreach ($types as $type)
			{
				$this->accesstypes[$type][] = $class;
			}
		}

		if (KunenaConfig::getInstance()->get('cache_adm'))
		{
			// Load administrators and moderators from cache
			$cache = Factory::getCache('com_kunena', 'output');
			$data  = $cache->get(self::$cacheKey, 'com_kunena');

			if ($data)
			{
				$data                     = unserialize($data);
				$this->adminsByCatid      = (array) $data['ac'];
				$this->adminsByUserid     = (array) $data['au'];
				$this->moderatorsByCatid  = (array) $data['mc'];
				$this->moderatorsByUserid = (array) $data['mu'];
			}
		}

		// If values were not cached (or users permissions have been changed), force reload
		if (!isset($this->adminsByCatid))
		{
			$this->clearCache();
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
	}

	/**
	 * @return  mixed|void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function clearCache()
	{
		$this->adminsByCatid      = [];
		$this->adminsByUserid     = [];
		$this->moderatorsByCatid  = [];
		$this->moderatorsByUserid = [];

		// Reset read access for the current session
		$me = \Kunena\Forum\Libraries\User\Helper::getMyself();
		Factory::getApplication()->setUserState("com_kunena.user{$me->userid}_read", null);

		// @var KunenaAccess $access

		foreach ($this->accesstypes['all'] as $access)
		{
			if (method_exists($access, 'loadCategoryRoles'))
			{
				$this->storeRoles((array) $access->loadCategoryRoles());
			}
		}

		// Load native category moderators and administrators
		$db    = Factory::getDBO();
		$query = $db->getQuery(true)
			->select($db->quoteName(['user_id', 'category_id', 'role']))
			->from($db->quoteName('#__kunena_user_categories'))
			->where($db->quoteName('role') . ' IN (1,2)');
		$db->setQuery($query);

		try
		{
			$this->storeRoles((array) $db->loadObjectList());
		}
		catch (ExecutionFailureException $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		// FIXME: enable caching after fixing the issues
		if (KunenaConfig::getInstance()->get('cache_adm'))
		{
			// Store new data into cache
			$cache = Factory::getCache('com_kunena', 'output');
			$cache->store(
				serialize(
					[
						'ac' => $this->adminsByCatid,
						'au' => $this->adminsByUserid,
						'mc' => $this->moderatorsByCatid,
						'mu' => $this->moderatorsByUserid,
					]
				), self::$cacheKey, 'com_kunena'
			);
		}
	}

	/**
	 * @param   array  $list  list
	 *
	 * @return  mixed|void
	 *
	 * @since   Kunena 6.0
	 */
	protected function storeRoles(array $list = null)
	{
		if (empty($list))
		{
			return;
		}

		foreach ($list as $item)
		{
			$userid = intval($item->user_id);
			$catid  = intval($item->category_id);

			if (!$userid)
			{
				continue;
			}

			if ($item->role == Forum::ADMINISTRATOR)
			{
				$this->adminsByUserid [$userid] [$catid] = 1;
				$this->adminsByCatid [$catid] [$userid]  = 1;
			}
			elseif ($item->role == Forum::MODERATOR)
			{
				$this->moderatorsByUserid [$userid] [$catid] = 1;
				$this->moderatorsByCatid [$catid] [$userid]  = 1;
			}
		}
	}

	/**
	 * @return  Access|null
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public static function getInstance()
	{
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		if (!self::$instance)
		{
			self::$instance = new Access;
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return self::$instance;
	}

	/**
	 * @param  Category  $category  category
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	public function getAccessOptions($category)
	{
		$list = [];

		// @var KunenaAccess $access

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
			$list [$key]['access'] = [
				'title' => Text::_('COM_KUNENA_ACCESS_UNKNOWN'),
				'desc'  => Text::sprintf('COM_KUNENA_ACCESS_UNKNOWN_DESC', $category->accesstype),
				'input' => $category->access,
			];
		}

		return $list;
	}

	/**
	 * @param  Category  $category  category
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getAccessTypesList($category)
	{
		static $enabled = false;

		if (!$enabled)
		{
			$enabled = true;
			Factory::getApplication()->getDocument()->addScriptDeclaration(
				"function kShowAccessType(htmlclass, el) {
	var selectedvalue = el.find(\":selected\").val();
	
	name = selectedvalue.replace(/[^\\w\\d]+/, '-');

$('.'+htmlclass).each(function() {
  $( this ).hide();
});

$('.'+htmlclass+'-'+name).each(function() {
  $( this ).show();
});
	
}
jQuery(document).ready(function ($) {
	var item = $('#accesstype');
	if (item) {
		kShowAccessType('kaccess', item);
	}
});"
			);
		}

		$exists      = 0;
		$accesstypes = [];

		foreach ($this->accesstypes as $type => $list)
		{
			if ($type == 'all')
			{
				continue;
			}

			foreach ($list as $access)
			{
				if (method_exists($access, 'getAccessOptions'))
				{
					$string                = Text::_('COM_KUNENA_INTEGRATION_TYPE_' . preg_replace('/[^\w\d]/', '_', $type));
					$accesstypes [$string] = HTMLHelper::_('select.option', $type, $string);
					$exists                |= $type == $category->accesstype;

					break;
				}
			}
		}

		ksort($accesstypes);

		// User has disabled access control
		if (!$exists)
		{
			$string                = Text::sprintf('COM_KUNENA_INTEGRATION_UNKNOWN', $category->accesstype);
			$accesstypes [$string] = HTMLHelper::_('select.option', $category->accesstype, $string);
		}

		return HTMLHelper::_('select.genericlist', $accesstypes, 'accesstype', 'class="inputbox form-control" size="' . count($accesstypes) . '" onchange="kShowAccessType(\'kaccess\', $(this))"', 'value', 'text', $category->accesstype);
	}

	/**
	 * Get access groups for the selected category.
	 *
	 * @param  Category  $category  Category
	 *
	 * @return  array|null
	 *
	 * @since   Kunena 6.0
	 */
	public function getCategoryAccess(Category $category)
	{
		$list = [];

		$accesstype = $category->accesstype;

		if (!isset($this->accesstypes[$accesstype]))
		{
			return $list;
		}

		foreach ($this->accesstypes[$accesstype] as $access)
		{
			if (method_exists($access, 'getCategoryAccess'))
			{
				$list += $access->getCategoryAccess($category);
			}
		}

		return $list;
	}

	/**
	 * Get category administrators.
	 *
	 * @param   int   $catid  Category Id
	 * @param   bool  $all    all
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	public function getAdmins($catid = 0, $all = false)
	{
		$list = !empty($this->adminsByCatid[$catid]) ? $this->adminsByCatid[$catid] : [];

		if ($all && !empty($this->adminsByCatid[0]))
		{
			$list += $this->adminsByCatid[0];
		}

		return $list;
	}

	/**
	 * Get category moderators.
	 *
	 * @param   int   $catid  Category Id
	 * @param   bool  $all    all
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	public function getModerators($catid = 0, $all = false)
	{
		$list = !empty($this->moderatorsByCatid[$catid]) ? $this->moderatorsByCatid[$catid] : [];

		if ($all && !empty($this->moderatorsByCatid[0]))
		{
			$list += $this->moderatorsByCatid[0];
		}

		return $list;
	}

	/**
	 * @param   mixed  $user  user
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getAdminStatus($user = null)
	{
		if (!($user instanceof KunenaUser))
		{
			$user = KunenaFactory::getUser($user);
		}

		return !empty($this->adminsByUserid[$user->userid]) ? $this->adminsByUserid[$user->userid] : [];
	}

	/**
	 * Assign user as moderator or resign him.
	 *
	 * @param   int    $category  category
	 * @param   mixed  $user      user
	 * @param   bool   $status    status
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @example if ($category->isAuthorised('admin')) $category->setModerator($user, true);
	 */
	public function setModerator($category, $user = null, $status = true)
	{
		// Check if category exists
		if ($category && !$category->exists())
		{
			return false;
		}

		$category_id = $category ? $category->id : 0;
		$status      = intval($status);

		// Check if user exists
		if (!($user instanceof KunenaUser))
		{
			$user = \Kunena\Forum\Libraries\User\Helper::get($user);
		}

		if (!$user->exists())
		{
			return false;
		}

		$success      = true;
		$usercategory = \Kunena\Forum\Libraries\Forum\Category\User\Helper::get($category_id, $user);

		if (($usercategory->role == 0 && $status) || ($usercategory->role == 1 && !$status))
		{
			$usercategory->role = $status;

			if (!$usercategory->params)
			{
				$usercategory->params = '';
			}

			$success = $usercategory->save();

			// Clear role cache
			$this->clearCache();

			// Change user moderator status
			$moderator = $this->getModeratorStatus($user);

			if ($user->moderator != !empty($moderator))
			{
				$user->moderator = intval(!empty($moderator));
				$success         = $user->save();
			}
		}

		return $success;
	}

	/**
	 * @param   mixed  $user  user
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getModeratorStatus($user = null)
	{
		if (!($user instanceof KunenaUser))
		{
			$user = KunenaFactory::getUser($user);
		}

		return !empty($this->moderatorsByUserid[$user->userid]) ? $this->moderatorsByUserid[$user->userid] : [];
	}

	/**
	 * @param   mixed  $user  user
	 *
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getAllowedCategories($user = null)
	{
		static $read = [];

		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		if (!($user instanceof KunenaUser))
		{
			$user = KunenaFactory::getUser($user);
		}

		if (!isset($read[$user->userid]))
		{
			$id  = $user->userid;
			$app = Factory::getApplication();

			// TODO: handle guests/bots with no userstate
			$read[$id] = $app->getUserState("com_kunena.user{$id}_read");

			if ($read[$id] === null)
			{
				$list       = [];
				$categories = \Kunena\Forum\Libraries\Forum\Category\Helper::getCategories(false, false, 'none');

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
					// @var KunenaAccess $access

					foreach ($this->accesstypes['all'] as $access)
					{
						if (method_exists($access, 'authoriseCategories'))
						{
							$list += $access->authoriseCategories($id, $categories);
						}
					}
				}

				// Clean up and filter the resulting list by using only array keys.
				$list      = array_keys($list);
				$list      = ArrayHelper::toInteger($list);
				$read[$id] = array_combine($list, $list);
				unset($read[$id][0]);
				$app->setUserState("com_kunena.user{$id}_read", $read[$id]);
			}
		}

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return $read[$user->userid];
	}

	/**
	 * @param   mixed  $user   user
	 * @param   int    $catid  catid
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function isModerator($user = null, $catid = 0)
	{
		if (!($user instanceof KunenaUser))
		{
			$user = \Kunena\Forum\Libraries\User\Helper::get($user);
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
	 * @param   mixed  $user   user
	 * @param   int    $catid  catid
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
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
		if (Factory::getApplication()->isClient('administrator') && $user->userid == \Kunena\Forum\Libraries\User\Helper::getMyself()->userid)
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
	 * Authorise user actions in a category.
	 *
	 * Function returns a list of authorised actions. Missing actions are threaded as inherit.
	 *
	 * @param  Category  $category  category
	 * @param   int                  $userid    user id
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	public function authoriseActions(Category $category, $userid)
	{
		$list = [];

		if (empty($this->accesstypes[$category->accesstype]))
		{
			return $list;
		}

		foreach ($this->accesstypes[$category->accesstype] as $access)
		{
			// @var KunenaAccess $access

			if (method_exists($access, 'getAuthoriseActions'))
			{
				$sublist = $access->getAuthoriseActions($category, $userid);

				foreach ($sublist as $key => $value)
				{
					$list[$key] = !empty($list[$key]) || $value;
				}
			}
		}

		return $list;
	}

	/**
	 * @param   mixed  $user    user
	 * @param   int    $catid   catid
	 * @param   bool   $string  string
	 *
	 * @return  string|array
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getAllowedHold($user, $catid, $string = true)
	{
		/**
		 * Hold = 0: normal
		 * Hold = 1: unapproved
		 * Hold = 2: deleted
		 */

		if (!($user instanceof KunenaUser))
		{
			$user = KunenaFactory::getUser($user);
		}

		$config = KunenaFactory::getConfig();

		$hold [0] = 0;

		if ($this->isModerator($user, $catid))
		{
			$hold [1] = 1;
		}

		if (($config->mod_see_deleted == '0' && $this->isAdmin($user, $catid))
			|| ($config->mod_see_deleted == '1' && $this->isModerator($user, $catid))
		)
		{
			$hold [2] = 2;
			$hold [3] = 3;
		}

		if ($string)
		{
			$hold = implode(',', $hold);
		}

		return $hold;
	}

	/**
	 * @param   int    $catid        catid
	 * @param   mixed  $topic        topic
	 * @param   mixed  $type         type
	 * @param   bool   $moderators   moderators
	 * @param   bool   $admins       admins
	 * @param   mixed  $excludeList  exclude list
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getSubscribers($catid, $topic, $type = false, $moderators = false, $admins = false, $excludeList = null)
	{
		$topic    = Helper::get($topic);
		$category = $topic->getCategory();

		if (!$topic->exists())
		{
			return [];
		}

		$modlist = [];

		if (!empty($this->moderatorsByCatid[0]))
		{
			$modlist += $this->moderatorsByCatid[0];
		}

		if (!empty($this->moderatorsByCatid[$catid]))
		{
			$modlist += $this->moderatorsByCatid[$catid];
		}

		$adminlist = [];

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
			$allow       = $deny = [];

			if (!empty($subscribers))
			{
				// @var KunenaAccess $access

				foreach ($this->accesstypes[$category->accesstype] as $access)
				{
					if (method_exists($access, 'authoriseUsers'))
					{
						list($a, $d) = $access->authoriseUsers($topic, $subscribers);

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
			$modlist = [];
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
			$adminlist = [];
		}

		$db    = Factory::getDBO();
		$query = $db->getQuery(true);
		$query->select('u.id, u.name, u.username, u.email')
			->from('#__users AS u')
			->where("u.block=0");
		$userlist = [];

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
			$modlist  = implode(',', array_keys($modlist));
			$query->select("IF( u.id IN ({$modlist}), 1, 0 ) AS moderator");
		}
		else
		{
			$query->select("0 AS moderator");
		}

		if (!empty($adminlist))
		{
			$userlist  += $adminlist;
			$adminlist = implode(',', array_keys($adminlist));
			$query->select("IF( u.id IN ({$adminlist}), 1, 0 ) AS admin");
		}
		else
		{
			$query->select("0 AS admin");
		}

		if (empty($excludeList))
		{
			// False, null, '', 0 and array(): get all subscribers
			$excludeList = [];
		}
		elseif (is_array($excludeList))
		{
			// Array() needs to be flipped to get userids into keys
			$excludeList = array_flip($excludeList);
		}
		else
		{
			// Others: let's assume that we have comma separated list of values (string)
			$excludeList = array_flip(explode(',', (string) $excludeList));
		}

		$userlist = array_diff_key($userlist, $excludeList);
		$userids  = [];

		if (!empty($userlist))
		{
			$userlist = implode(',', array_keys($userlist));
			$query->where("u.id IN ({$userlist})");

			// Only send to users whose Joomla account is enabled to Receive System Emails
			if (KunenaConfig::getInstance()->get('use_system_emails'))
			{
				$query->where("u.sendEmail = 1");
			}

			$db = Factory::getDBO();
			$db->setQuery($query);

			try
			{
				$userids = (array) $db->loadObjectList();
			}
			catch (ExecutionFailureException $e)
			{
				KunenaError::displayDatabaseError($e);
			}
		}

		return $userids;
	}

	/**
	 * @param   Topic  $topic  loadSubscribers
	 * @param   bool              $type   type
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function loadSubscribers(Topic $topic, $type)
	{
		$category = $topic->getCategory();
		$db       = Factory::getDBO();
		$query    = [];

		if ($type & self::TOPIC_SUBSCRIPTION)
		{
			// Get topic subscriptions
			$querytopic = $db->getQuery(true)
				->select($db->quoteName('user_id'))
				->from($db->quoteName('#__kunena_user_topics', 'ut'))
				->leftJoin($db->quoteName('#__kunena_users', 'ku') . ' ON ' . $db->quoteName('ut.user_id') . ' = ' . $db->quoteName('ku.userid'))
				->where($db->quoteName('ut.topic_id') . ' = ' . $db->quote($topic->id))
				->where($db->quoteName('ut.subscribed') . ' = 1')
				->where($db->quoteName('ku.banned') . ' <> 0')
				->where($db->quoteName('ku.banned') . ' IS NULL')
				->where($db->quoteName('ut.topic_id') . ' = ' . $db->quote($topic->id))
				->where($db->quoteName('ut.subscribed') . ' = 1')
				->group($db->quoteName('user_id'));

			$query[] = $querytopic;
		}

		if ($type & self::CATEGORY_SUBSCRIPTION)
		{
			// Get category subscriptions
			$querycat = $db->getQuery(true)
				->select($db->quoteName('user_id'))
				->from($db->quoteName('#__kunena_user_categories', 'ut'))
				->leftJoin($db->quoteName('#__kunena_users', 'ku') . ' ON ' . $db->quoteName('ut.user_id') . ' = ' . $db->quoteName('ku.userid'))
				->where($db->quoteName('category_id') . ' = ' . $db->quote($category->id))
				->andWhere($db->quoteName('ut.subscribed') . ' = 1')
				->andWhere($db->quoteName('ku.banned') . ' <> 0')
				->orWhere($db->quoteName('ku.banned') . ' IS NULL')
				->andWhere($db->quoteName('category_id') . ' = ' . $db->quote($category->id))
				->andWhere($db->quoteName('ut.subscribed') . ' = 1')
				->group($db->quoteName('user_id'));
			$query[]  = $querycat;
		}

		$query = implode(' UNION ', $query);
		$db->setQuery($query);

		try
		{
			$userids = (array) $db->loadColumn();
		}
		catch (ExecutionFailureException $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		return $userids;
	}
}
