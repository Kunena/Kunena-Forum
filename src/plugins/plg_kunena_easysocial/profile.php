<?php
/**
 * @package        EasySocial
 * @copyright      Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
 * @license        GNU/GPL, see LICENSE.php
 * EasySocial is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

namespace Kunena\Forum\Plugin\Kunena\Easysocial;

defined('_JEXEC') or die('Unauthorized Access');

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Integration\Profile;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use function defined;

/**
 * @package     Kunena
 *
 * @since   Kunena 6.0
 */
class KunenaProfileEasySocial extends Profile
{
	/**
	 * @var     null
	 * @since   Kunena 6.0
	 */
	protected $params = null;

	/**
	 * KunenaProfileEasySocial constructor.
	 *
	 * @param   object  $params params
	 *
	 * @since   Kunena 6.0
	 */
	public function __construct($params)
	{
		$this->params = $params;
	}

	/**
	 * @param   string  $action  action
	 * @param   bool    $xhtml   xhtml
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function getUserListURL($action = '', $xhtml = true)
	{
		$config = KunenaFactory::getConfig();
		$my     = Factory::getApplication()->getIdentity();

		if ($config->userlist_allowed == 0 && $my->guest)
		{
			return false;
		}

		return FRoute::users([], $xhtml);
	}

	/**
	 * @param   int     $userid userid
	 * @param   string  $task   task
	 * @param   bool    $xhtml  xhtml
	 *
	 * @return  string
	 *
	 * @since   Kunena 6.0
	 */
	public function getProfileURL($userid, $task = '', $xhtml = true)
	{
		if ($userid)
		{
			$user = ES::user($userid);

			// When simple urls are enabled, we just hardcode the url
			$config  = ES::config();
			$jConfig = ES::jConfig();

			if (!ES::isSh404Installed() && $config->get('users.simpleUrl') && $jConfig->getValue('sef'))
			{
				$url = rtrim(Uri::root(), '/') . '/' . $user->getAlias(false);

				return $url;
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
	 * @return  array|void
	 *
	 * @since   Kunena 6.0
	 */
	public function _getTopHits($limit = 0)
	{
	}

	/**
	 * @param   object  $view   view
	 * @param   object  $params params
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	public function showProfile($view, &$params)
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
	 * @param   integer  $birthday birthday
	 *
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 */
	public function getLegacyDate($birthday)
	{
		$birthday = json_decode($birthday);
		$birthday = FD::date($birthday->day . '-' . $birthday->month . '-' . $birthday->year);

		return $birthday;
	}

	/**
	 * @param   int   $userid userid
	 * @param   bool  $xhtml  xhtml
	 *
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 */
	public function getEditProfileURL($userid, $xhtml = true)
	{
		$options = ['layout' => 'edit'];
		$url     = FRoute::profile($options, $xhtml);

		return $url;
	}
}
