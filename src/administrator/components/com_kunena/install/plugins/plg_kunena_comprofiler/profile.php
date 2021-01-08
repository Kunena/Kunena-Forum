<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Comprofiler
 *
 * @copyright   (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;

require_once dirname(__FILE__) . '/integration.php';

/**
 * Class KunenaProfileComprofiler
 * @since Kunena
 */
class KunenaProfileComprofiler extends KunenaProfile
{
	/**
	 * @var null
	 * @since Kunena
	 */
	protected $params = null;

	/**
	 * KunenaProfileComprofiler constructor.
	 *
	 * @param $params
	 *
	 * @since Kunena
	 */
	public function __construct($params)
	{
		$this->params = $params;
	}

	/**
	 * @param $event
	 * @param $params
	 *
	 * @throws Exception
	 * @since Kunena
	 */
	public static function trigger($event, &$params)
	{
		KunenaIntegrationComprofiler::trigger($event, $params);
	}

	/**
	 * @since Kunena
	 */
	public function open()
	{
		KunenaIntegrationComprofiler::open();
	}

	/**
	 * @since Kunena
	 */
	public function close()
	{
		KunenaIntegrationComprofiler::close();
	}

	/**
	 * @param   string $action action
	 * @param   bool   $xhtml  xhtml
	 *
	 * @return boolean|string
	 * @throws Exception
	 * @since Kunena
	 */
	public function getUserListURL($action = '', $xhtml = true)
	{
		global $_CB_framework;

		$config = KunenaFactory::getConfig();
		$my     = Factory::getUser();

		if ($config->userlist_allowed == 0 && $my->id == 0)
		{
			return false;
		}

		return $_CB_framework->userProfilesListUrl(null, $xhtml);
	}

	/**
	 * @param          $user
	 * @param   string $task  task
	 * @param   bool   $xhtml xhtml
	 *
	 * @return boolean|string
	 * @throws Exception
	 * @since Kunena
	 */
	public function getProfileURL($user, $task = '', $xhtml = true)
	{
		global $_CB_framework;

		$user = KunenaFactory::getUser($user);

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
	 * @param $view
	 * @param $params
	 *
	 * @return string
	 * @since Kunena
	 */
	public function showProfile($view, &$params)
	{
		global $_PLUGINS;

		$_PLUGINS->loadPluginGroup('user');

		return implode(
			' ', $_PLUGINS->trigger(
				'forumSideProfile', array('kunena', $view, $view->profile->userid,
				array('config' => &$view->config, 'userprofile' => &$view->profile, 'params' => &$params), )
			)
		);
	}

	/**
	 * @param   int $limit limit
	 *
	 * @return array
	 * @throws Exception
	 * @since Kunena
	 */
	public function _getTopHits($limit = 0)
	{
		$db    = Factory::getDBO();
		$query = "SELECT cu.user_id AS id, cu.hits AS count
			FROM #__comprofiler AS cu
			INNER JOIN #__users AS u ON u.id=cu.user_id
			WHERE cu.hits>0
			ORDER BY cu.hits DESC";
		$db->setQuery($query, 0, $limit);

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
	 * @param        $userid
	 * @param   bool $xhtml xhtml
	 *
	 * @return string
	 * @since Kunena
	 */
	public function getEditProfileURL($userid, $xhtml = true)
	{
		global $_CB_framework;

		return $_CB_framework->userProfileEditUrl(null, $xhtml);
	}

	/**
	 * Return name or username of user with community builder settings
	 * 
	 * @param   integer $userid userid
	 * @param   bool    $xhtml  xhtml
	 *
	 * @since Kunena 5.2
	 * @return string
	 */
	public function getProfileName($user, $visitorname = '', $escape = true)
	{
		global $ueConfig;

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

		return false;
	}
}
