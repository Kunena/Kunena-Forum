<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Kunena
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Access\Access;
use Joomla\CMS\Factory;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Integration\KunenaProfile;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * Class KunenaProfile
 *
 * @since   Kunena 5.0
 */
class KunenaProfileKunena extends KunenaProfile
{
	/**
	 * @var     null
	 * @since   Kunena 5.0
	 */
	protected $params = null;

	/**
	 * @param   object  $params  params
	 *
	 * @since   Kunena 5.0
	 */
	public function __construct(object $params)
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
	 *@throws  null
	 *
	 * @throws  Exception
	 */
	public function getUserListURL(string $action = '', bool $xhtml = true): string
	{
		$config = KunenaFactory::getConfig();
		$my     = Factory::getApplication()->getIdentity();

		if ($config->userlistAllowed == 0 && $my->id == 0)
		{
			return false;
		}

		return KunenaRoute::_('index.php?option=com_kunena&view=user&layout=list' . $action, $xhtml);
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
	public function getTopHits(int $limit = 0): array
	{
		$db    = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->select($db->quoteName(['u.id', 'ku.uhits'], [null, 'count']));
		$query->from($db->quoteName(['#__kunena_users'], ['ku']));
		$query->innerJoin($db->quoteName('#__users', 'u') . ' ON ' . $db->quoteName('u.id') . ' = ' . $db->quoteName('ku.userid'));
		$query->where($db->quoteName('ku.uhits') . ' > 0');
		$query->order($db->quoteName('ku.uhits') . ' DESC');

		if (KunenaFactory::getConfig()->superAdminUserlist)
		{
			$filter = Access::getUsersByGroup(8);
			$query->andwhere('u.id NOT IN (' . implode(',', $filter) . ')');
		}

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
	 * @param   int     $view    view
	 * @param   object  $params  params
	 *
	 * @return  void
	 *
	 * @since   Kunena 5.0
	 */
	public function showProfile($view, object $params)
	{
	}

	/**
	 * @param   int   $userid  userid
	 * @param   bool  $xhtml   xhtml
	 *
	 * @return  boolean
	 *
	 * @throws  null
	 * @throws  Exception
	 *
	 * @since   Kunena 5.0
	 */
	public function getEditProfileURL(int $userid, bool $xhtml = true): bool
	{
		$avatartab = '&avatartab=1';

		return $this->getProfileURL($userid, 'edit', $xhtml, $avatartab);
	}

	/**
	 * @param   int     $userid     userid
	 * @param   string  $task       task
	 * @param   bool    $xhtml      xhtml
	 * @param   string  $avatarTab  avatarTab
	 *
	 * @return  boolean
	 *
	 * @throws  null
	 * @throws  Exception
	 *
	 * @since   Kunena 5.0
	 */
	public function getProfileURL(int $userid, $task = '', bool $xhtml = true, string $avatarTab = '')
	{
		if ($userid == 0)
		{
			return false;
		}

		if (!($userid instanceof KunenaUser))
		{
			$user = KunenaUserHelper::get($userid);
		}

		if ($user === false)
		{
			return false;
		}

		$userid = "&userid={$user->userid}";

		if ($task && $task != 'edit')
		{
			throw new \Exception('Sorry, Kunena 6.0 no support url with func in method getProfileURL class KunenaProfileKunena');
		}

		$layout = $task ? '&layout=' . $task : '';

		if ($layout)
		{
			return KunenaRoute::_("index.php?option=com_kunena&view=user{$layout}{$userid}{$avatarTab}", $xhtml);
		}

		return KunenaRoute::getUserUrl($user, $xhtml);
	}

	/**
	 * Get the name of the user from this profile
	 *
	 * @param   KunenaUser  $user         user
	 * @param   string      $visitorname  name
	 * @param   bool        $escape       escape
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 *
	 * @see     KunenaProfile::getProfileName()
	 * @since   Kunena 5.2
	 */
	public function getProfileName(KunenaUser $user, string $visitorname = '', bool $escape = true): string
	{
		$config = KunenaFactory::getConfig();

		if (!$user->userid && !$user->name)
		{
			$name = $visitorname;
		}
		else
		{
			$name = $config->username ? $user->username : $user->name;
		}

		if ($escape)
		{
			$name = htmlspecialchars($name, ENT_COMPAT, 'UTF-8');
		}

		return $name;
	}
}
