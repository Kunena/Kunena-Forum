<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Easysocial
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @copyright       Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
 * @license         GNU/GPL, see LICENSE.php
 * EasySocial is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Unauthorized Access');

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Integration\KunenaProfile;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\User\KunenaUser;

/**
 * @package     Kunena
 *
 * @since       Kunena 5.0
 */
class KunenaProfileEasySocial extends KunenaProfile
{
	/**
	 * @var     null
	 * @since   Kunena 5.0
	 */
	protected $params = null;

	/**
	 * KunenaProfileEasySocial constructor.
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
		$config = KunenaFactory::getConfig();
		$my     = Factory::getApplication()->getIdentity();

		if ($config->userlistAllowed == 0 && $my->guest)
		{
			return false;
		}

		return FRoute::users([], $xhtml);
	}

	/**
	 * @param   int     $userid     userid
	 * @param   string  $task       task
	 * @param   bool    $xhtml      xhtml
	 * @param   string  $avatarTab  avatartab
	 *
	 * @return boolean|void
	 *
	 * @since   Kunena 5.0
	 */
	public function getProfileURL(int $userid, string $task = '', bool $xhtml = true, string $avatarTab = '')
	{
		if ($userid)
		{
			$user = ES::user($userid);

			// When simple urls are enabled, we just hardcode the url
			$config  = ES::config();
			$jConfig = ES::jConfig();

			if (!ES::isSh404Installed() && $config->get('users.simpleUrl') && $jConfig->getValue('sef'))
			{
				return rtrim(Uri::root(), '/') . '/' . $user->getAlias(false);
			}

			// If it's not configured for simple urls, just set the alias
			$alias = $user->getAlias();
		}
		else
		{
			$alias = $userid;
		}

		$options = ['id' => $alias];

		if ($task)
		{
			$options['layout'] = $task;
		}

		$url = FRoute::profile($options, $xhtml);

		return $url;
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
		$userid = $view->profile->userid;

		$user = FD::user($userid);

		$gender = $user->getFieldData('GENDER');

		if (!empty($gender))
		{
			$view->profile->gender = $gender;
		}

		$data     = $user->getFieldData('BIRTHDAY');
		$json     = FD::json();
		$birthday = null;

		// Legacy
		if (isset($data['date']) && $json->isJsonString($data['date']) && !$birthday)
		{
			$birthday = $this->getLegacyDate($data['date']);
		}

		// Legacy
		if ($json->isJsonString($data) && !$birthday)
		{
			$birthday = $this->getLegacyDate($data);
		}

		// New format
		if (isset($data['date']) && !$birthday)
		{
			$birthday = FD::date($data['date']);
		}

		if (!is_null($birthday))
		{
			$view->profile->birthdate = $birthday->format('Y-m-d');
		}
	}

	/**
	 * @param   integer  $birthday  birthday
	 *
	 * @return  mixed
	 *
	 * @since   Kunena 5.0
	 */
	public function getLegacyDate(int $birthday)
	{
		$birthday = json_decode($birthday);
		$birthday = FD::date($birthday->day . '-' . $birthday->month . '-' . $birthday->year);

		return $birthday;
	}

	/**
	 * @param   int   $userid  userid
	 * @param   bool  $xhtml   xhtml
	 *
	 * @return  mixed
	 *
	 * @since   Kunena 5.0
	 */
	public function getEditProfileURL(int $userid, bool $xhtml = true): bool
	{
		$options = ['layout' => 'edit'];

		return FRoute::profile($options, $xhtml);
	}

	/**
	 * Return username of user
	 *
	 * @param   KunenaUser  $user         user
	 * @param   string      $visitorname  name
	 * @param   bool        $escape       escape
	 *
	 * @return string
	 * @since Kunena 5.2
	 */
	public function getProfileName(KunenaUser $user, string $visitorname = '', bool $escape = true)
	{
		$config          = ES::config();
		$displayusername = $config->get('users.displayName');

		if ($user->userid > 0)
		{
			if ($displayusername == 'username')
			{
				return FD::user($user->userid)->username;
			}
			else
			{
				return FD::user($user->userid)->name;
			}
		}
		else
		{
			return $visitorname;
		}
	}
}
