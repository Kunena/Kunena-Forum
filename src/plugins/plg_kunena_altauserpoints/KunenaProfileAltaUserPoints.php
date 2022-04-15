<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      AltaUserPoints
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Integration\KunenaProfile;
use Kunena\Forum\Libraries\Layout\KunenaLayout;

/**
 * KunenaActivityAltaUserPoints class to handle profile integration with AltaUserPoints
 *
 * @since  5.0
 */
class KunenaProfileAltaUserPoints extends KunenaProfile
{
	/**
	 * @var     null
	 * @since   Kunena 5.0
	 */
	protected $params = null;

	/**
	 * KunenaProfileAltaUserPoints constructor.
	 *
	 * @param   mixed  $params  params
	 *
	 * @since   Kunena 5.0
	 */
	public function __construct($params)
	{
		$this->params = $params;
	}

	/**
	 * @param   string  $action  action
	 * @param   bool    $xhtml   xhtml
	 *
	 * @return  string
	 *
	 * @since   Kunena 5.0
	 *@throws  Exception
	 */
	public function getUserListURL(string $action = '', bool $xhtml = true): string
	{
		$config = KunenaFactory::getConfig();
		$my     = Factory::getApplication()->getIdentity();

		if ($config->userlistAllowed == 0 && $my->id == 0)
		{
			return false;
		}

		return AltaUserPointsHelper::getAupUsersURL();
	}

	/**
	 * @param   int  $limit  limit
	 *
	 * @return array
	 *
	 * @since   Kunena 5.0
	 */
	public function getTopHits(int $limit = 0): array
	{
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true)
			->select($db->quoteName(['u.*', 'ju.username', 'ju.email', 'ju.lastvisitDate'], [null, null, 'last_login']))
			->from($db->quoteName('#__alpha_userpoints', 'a'))
			->innerJoin($db->quoteName('#__users', 'u') . ' ON u.id = a.userid')
			->where('a.profileviews > 0')
			->order('a.profileviews DESC');
		$query->setLimit($limit);
		$db->setQuery($query);

		try
		{
			$top = (array) $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			return false;
		}

		return $top;
	}

	/**
	 * @param   KunenaLayout  $view    view
	 * @param   object        $params  params
	 *
	 * @return  void
	 *
	 * @since   Kunena 5.0
	 */
	public function showProfile(KunenaLayout $view, object $params)
	{
	}

	/**
	 * @param   int   $userid
	 * @param   bool  $xhtml  xhtml
	 *
	 * @return  boolean
	 *
	 * @throws Exception
	 * @since   Kunena 5.0
	 */
	public function getEditProfileURL(int $userid, bool $xhtml = true): bool
	{
		return $this->getProfileURL($userid, '', $xhtml);
	}

	/**
	 * @param   int     $userid
	 * @param   string  $task       task
	 * @param   bool    $xhtml      xhtml
	 * @param   string  $avatarTab  avatartab
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 5.0
	 * @throws \Exception
	 */
	public function getProfileURL(int $userid, string $task = '', bool $xhtml = true, string $avatarTab = '')
	{
		if ($userid == 0)
		{
			return false;
		}

		$user = KunenaFactory::getUser($userid);
		$my   = Factory::getApplication()->getIdentity();

		if ($user === false)
		{
			return false;
		}

		$userid     = $my->id != $user->userid ? '&userid=' . AltaUserPointsHelper::getAnyUserReferreID($user->userid) : '';
		$AUP_itemid = AltaUserPointsHelper::getItemidAupProfil();

		return Route::_('index.php?option=com_altauserpoints&view=account' . $userid . '&Itemid=' . $AUP_itemid, $xhtml);
	}

	/**
	 * Return username of user
	 *
	 * @param   int     $userid       userid
	 * @param   string  $visitorname  name
	 * @param   bool    $escape       escape
	 *
	 * @return  string
	 *
	 * @since   Kunena 5.2
	 */
	public function getProfileName(int $userid, string $visitorname = '', bool $escape = true)
	{
		$referrid = AltaUserPointsHelper::getAnyUserReferreID($userid);
		$userinfo = AltaUserPointsHelper::getUserInfo($referrid);

		return $userinfo->username;
	}
}
