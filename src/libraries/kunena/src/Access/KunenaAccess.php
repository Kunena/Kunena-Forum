<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Integration
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Access;

\defined('_JEXEC') or die();

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
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Category\User\KunenaCategoryUserHelper;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Profiler\KunenaProfiler;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * Class KunenaAccess
 *
 * @since   Kunena 6.0
 */
class KunenaAccess
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
	 * @throws  Exception
	 * @since   Kunena 6.0
	 *
	 */
	public function __construct()
	{
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
		PluginHelper::importPlugin('kunena');

		$classes = Factory::getApplication()->triggerEvent('onKunenaGetAccessControl');

		foreach ($classes as $class)
		{
			if (!\is_object($class))
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

		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;
	}

	/**
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 *
	 */
	public function clearCache()
	{
		$this->adminsByCatid      = [];
		$this->adminsByUserid     = [];
		$this->moderatorsByCatid  = [];
		$this->moderatorsByUserid = [];

		// Reset read KunenaAccess for the current session
		$me = KunenaUserHelper::getMyself();
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
		$db    = Factory::getContainer()->get('DatabaseDriver');
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
				),
				self::$cacheKey,
				'com_kunena'
			);
		}
	}

	/**
	 * @param   array|null  $list  list
	 *
	 * @return  void
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
			$userid = \intval($item->user_id);
			$catid  = \intval($item->category_id);

			if (!$userid)
			{
				continue;
			}

			if ($item->role == KunenaForum::ADMINISTRATOR)
			{
				$this->adminsByUserid[$userid][$catid] = 1;
				$this->adminsByCatid[$catid][$userid]  = 1;
			}
			elseif ($item->role == KunenaForum::MODERATOR)
			{
				$this->moderatorsByUserid[$userid][$catid] = 1;
				$this->moderatorsByCatid[$catid][$userid]  = 1;
			}
		}
	}

	/**
	 * @return  KunenaAccess|null
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 *
	 */
	public static function getInstance(): ?KunenaAccess
	{
		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		if (!self::$instance)
		{
			self::$instance = new KunenaAccess;
		}

		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return self::$instance;
	}

	/**
	 * @param   KunenaCategory  $category  category
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	public function getAccessOptions(KunenaCategory $category): array
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

		// User has disabled KunenaAccess control
		$key = preg_replace('/[^\w\d]/', '-', $category->accesstype);

		if (!isset($list[$key]))
		{
			$list[$key]['access'] = [
				'title' => Text::_('COM_KUNENA_ACCESS_UNKNOWN'),
				'desc'  => Text::sprintf('COM_KUNENA_ACCESS_UNKNOWN_DESC', $category->accesstype),
				'input' => $category->access,
			];
		}

		return $list;
	}

	/**
	 * @param   KunenaCategory  $category  category
	 *
	 * @return  string
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public function getAccessTypesList(KunenaCategory $category): string
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
					$string               = Text::_('COM_KUNENA_INTEGRATION_TYPE_' . preg_replace('/[^\w\d]/', '_', $type));
					$accesstypes[$string] = HTMLHelper::_('select.option', $type, $string);
					$exists               |= $type == $category->accesstype;

					break;
				}
			}
		}

		ksort($accesstypes);

		// User has disabled KunenaAccess control
		if (!$exists)
		{
			$string               = Text::sprintf('COM_KUNENA_INTEGRATION_UNKNOWN', $category->accesstype);
			$accesstypes[$string] = HTMLHelper::_('select.option', $category->accesstype, $string);
		}

		return HTMLHelper::_('select.genericlist', $accesstypes, 'accesstype', 'class="inputbox form-control" size="' . \count($accesstypes) . '" onchange="kShowAccessType(\'kaccess\', $(this))"', 'value', 'text', $category->accesstype);
	}

	/**
	 * Get KunenaAccess groups for the selected category.
	 *
	 * @param   KunenaCategory  $category  Category
	 *
	 * @return  array|null
	 *
	 * @since   Kunena 6.0
	 */
	public function getCategoryAccess(KunenaCategory $category): ?array
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
	public function getAdmins($catid = 0, $all = false): array
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
	public function getModerators($catid = 0, $all = false): array
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
	 * @throws  Exception
	 * @since   Kunena 6.0
	 *
	 */
	public function getAdminStatus($user = null): array
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
	 * @param   KunenaCategory|int  $category  KunenaCategory object
	 *
	 * @param   mixed               $user      KunenaUser object
	 * @param   bool                $status    status
	 *
	 * @return  boolean
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 *
	 * @example if ($category->isAuthorised('admin')) $category->setModerator($user, true);
	 *
	 */
	public function setModerator($category, $user = null, $status = true): bool
	{
		// Check if category exists
		if ($category && !$category->exists())
		{
			return false;
		}

		$categoryId = $category ? $category->id : 0;
		$status     = \intval($status);

		// Check if user exists
		if (!($user instanceof KunenaUser))
		{
			$user = KunenaUserHelper::get($user);
		}

		if (!$user->exists())
		{
			return false;
		}

		$success      = true;
		$userCategory = KunenaCategoryUserHelper::get($categoryId, $user);

		if (($userCategory->role == 0 && $status) || ($userCategory->role == 1 && !$status))
		{
			$userCategory->role = $status;

			$success = $userCategory->save();

			// Clear role cache
			$this->clearCache();

			// Change user moderator status
			$moderator = $this->getModeratorStatus($user);

			if ($user->moderator != !empty($moderator))
			{
				$user->moderator = \intval(!empty($moderator));
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
	 * @throws  Exception
	 * @since   Kunena 6.0
	 *
	 */
	public function getModeratorStatus($user = null): array
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
	 * @throws  Exception
	 * @since   Kunena 6.0
	 *
	 */
	public function getAllowedCategories($user = null)
	{
		static $read = [];

		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->start('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

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
				$categories = KunenaCategoryHelper::getCategories(false, false, 'none');

				foreach ($categories as $category)
				{
					// Remove unpublished categories
					if ($category->published != 1)
					{
						unset($categories[$category->id]);
					}

					// Moderators have always KunenaAccess
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

		KunenaProfiler::getInstance() ? KunenaProfiler::instance()->stop('function ' . __CLASS__ . '::' . __FUNCTION__ . '()') : null;

		return $read[$user->userid];
	}

	/**
	 * @param   mixed  $user   user
	 * @param   int    $catid  catid
	 *
	 * @return  boolean
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 *
	 */
	public function isModerator($user = null, $catid = 0): bool
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
	 * @param   mixed  $user   user
	 * @param   int    $catid  catid
	 *
	 * @return  boolean
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 *
	 */
	public function isAdmin($user = null, $catid = 0): bool
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
		if (Factory::getApplication()->isClient('administrator') && $user->userid == KunenaUserHelper::getMyself()->userid)
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
	 * @param   KunenaCategory  $category  category
	 * @param   int             $userid    user id
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	public function authoriseActions(KunenaCategory $category, int $userid): array
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
	 * @throws Exception
	 * @since   Kunena 6.0
	 *
	 */
	public function getAllowedHold($user, int $catid, $string = true)
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

		$hold[0] = 0;

		if ($this->isModerator($user, $catid))
		{
			$hold[1] = 1;
		}

		if (($config->modSeeDeleted == '0' && $this->isAdmin($user, $catid))
			|| ($config->modSeeDeleted == '1' && $this->isModerator($user, $catid))
		)
		{
			$hold[2] = 2;
			$hold[3] = 3;
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
	 * @throws Exception
	 * @since   Kunena 6.0
	 *
	 */
	public function getSubscribers(int $catid, $topic, $type = false, $moderators = false, $admins = false, $excludeList = null): array
	{
		$topic    = KunenaTopicHelper::get($topic);
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

			$subsList = array_diff($allow, $deny);

			// Category administrators and moderators override ACL
			$subsList += array_intersect_key($adminlist, array_flip($subscribers));
			$subsList += array_intersect_key($modlist, array_flip($subscribers));
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

		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->select('u.id, u.name, u.username, u.email')
			->from('#__users AS u')
			->where("u.block=0");
		$userlist = [];

		if (!empty($subsList))
		{
			$userlist += $subsList;
			$subsList = implode(',', array_keys($subsList));
			$query->select("IF( u.id IN ({$subsList}), 1, 0 ) AS subscription");
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
		elseif (\is_array($excludeList))
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
			if (KunenaConfig::getInstance()->get('useSystemEmails'))
			{
				$query->where("u.sendEmail = 1");
			}

			$db = Factory::getContainer()->get('DatabaseDriver');
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
	 * @param   KunenaTopic  $topic  loadSubscribers
	 * @param   bool         $type   type
	 *
	 * @return  array
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 *
	 */
	public function loadSubscribers(KunenaTopic $topic, $type)
	{
		$category = $topic->getCategory();
		$db       = Factory::getContainer()->get('DatabaseDriver');
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
