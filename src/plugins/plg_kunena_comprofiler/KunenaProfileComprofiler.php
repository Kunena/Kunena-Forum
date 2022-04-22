<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Comprofiler
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Integration\KunenaProfile;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\User\KunenaUser;

/**
 * Class KunenaProfileComprofiler
 *
 * @since   Kunena 5.0
 */
class KunenaProfileComprofiler extends KunenaProfile
{
	/**
	 * @var     null
	 * @since   Kunena 5.0
	 */
	protected $params = null;

	/**
	 * KunenaProfileComprofiler constructor.
	 *
	 * @param   object  $params  params
	 *
	 * @since   Kunena 5.0
	 */
	public function __construct(object $params)
	{
		$this->params = $params;
	}

	/**
	 * @param   string  $event   event
	 * @param   object  $params  params
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 *
	 * @since   Kunena 5.0
	 */
	public static function trigger(string $event, object $params): void
	{
		KunenaIntegrationComprofiler::trigger($event, $params);
	}

	/**
	 * @return  void
	 *
	 * @throws  Exception
	 *
	 * @since   Kunena 5.0
	 */
	public function open(): void
	{
		KunenaIntegrationComprofiler::open();
	}

	/**
	 * @return  void
	 *
	 * @throws  Exception
	 *
	 * @since   Kunena 5.0
	 */
	public function close(): void
	{
		KunenaIntegrationComprofiler::close();
	}

	/**
	 * Get the URL for userlist from CB
	 *
	 * @param   string  $action  action
	 * @param   bool    $xhtml   xhtml
	 *
	 * @return string
	 *
	 * @since   Kunena 5.0
	 * @throws \Exception
	 */
	public function getUserListURL(string $action = '', bool $xhtml = true): string
	{
		global $_CB_framework;

		$config = KunenaFactory::getConfig();
		$my     = Factory::getApplication()->getIdentity();

		if ($config->userlistAllowed == 0 && $my->id == 0)
		{
			return false;
		}

		return $_CB_framework->userProfilesListUrl(null, $xhtml);
	}

	/**
	 * Get the profile URL from CB
	 *
	 * @param   int          $userid     userid
	 * @param   string|null  $task       task
	 * @param   bool         $xhtml      xhtml
	 * @param   string       $avatarTab  avatarTab
	 *
	 * @return  boolean|string
	 *
	 * @throws  Exception
	 *
	 * @since   Kunena 5.0
	 */
	public function getProfileURL(int $userid, $task = '', bool $xhtml = true, string $avatarTab = '')
	{
		global $_CB_framework;

		$user = KunenaFactory::getUser($userid);

		if ($user->userid == 0)
		{
			return false;
		}

		// Get CUser object
		$cbUser = CBuser::getInstance($user->userid);

		if ($cbUser === null)
		{
			return false;
		}

		return $_CB_framework->userProfileUrl($user->userid, $xhtml);
	}

	/**
	 * @param   KunenaLayout  $view    view
	 * @param   object        $params  params
	 *
	 * @return  string
	 *
	 * @since   Kunena 5.0
	 */
	public function showProfile(KunenaLayout $view, object $params)
	{
		global $_PLUGINS;

		$_PLUGINS->loadPluginGroup('user');

		return implode(
			' ',
			$_PLUGINS->trigger(
				'forumSideProfile',
				['kunena', $view, $view->profile->userid,
					['config' => &$view->config, 'userprofile' => &$view->profile, 'params' => &$params], ]
			)
		);
	}

	/**
	 * @param   int  $limit  limit
	 *
	 * @return  array
	 *
	 * @throws  Exception
	 *
	 * @since   Kunena 5.0
	 */
	public function getTopHits($limit = 0): array
	{
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->select('cu.user_id AS id, cu.hits AS count');
		$query->from($db->quoteName('#__comprofiler', 'cu'));
		$query->innerJoin($db->quoteName('#__users', 'u') . ' ON u.id = cu.user_id');
		$query->where('cu.hits > 0');
		$query->order('cu.hits DESC');
		$query->setLimit($limit);
		$db->setQuery($query);

		try
		{
			$top = (array) $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			KunenaError::displayDatabaseError($e);
		}

		return $top;
	}

	/**
	 * @param   int   $userid  userid
	 * @param   bool  $xhtml   xhtml
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 5.0
	 */
	public function getEditProfileURL(int $userid, bool $xhtml = true): bool
	{
		global $_CB_framework;

		return $_CB_framework->userProfileEditUrl(null, $xhtml);
	}

	/**
	 * Return name or username of user with community builder settings
	 *
	 * @param   KunenaUser  $user         user
	 * @param   string      $visitorname  name
	 * @param   bool        $escape       escape
	 *
	 * @return string
	 *
	 * @since Kunena 5.2
	 */
	public function getProfileName(KunenaUser $user, string $visitorname = '', bool $escape = true): string
	{
		global $ueConfig;

		if ($user->exists())
		{
			if ($ueConfig['name_format'] == 1)
			{
				return $user->name;
			}
			elseif ($ueConfig['name_format'] == 2)
			{
				return $user->name . ' (' . $user->username . ')';
			}
			elseif ($ueConfig['name_format'] == 3)
			{
				return $user->username;
			}
			elseif ($ueConfig['name_format'] == 4)
			{
				return $user->username . ' (' . $user->name . ')';
			}
		}

		return $visitorname;
	}
}
