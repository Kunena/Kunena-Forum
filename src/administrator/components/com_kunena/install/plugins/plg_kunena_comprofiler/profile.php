<?php
/**
 * Kunena Plugin
 *
 * @package     Kunena.Plugins
 * @subpackage  Comprofiler
 *
 * @copyright   (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

require_once dirname(__FILE__) . '/integration.php';

class KunenaProfileComprofiler extends KunenaProfile
{
	protected $params = null;

	/**
	 * KunenaProfileComprofiler constructor.
	 *
	 * @param $params
	 */
	public function __construct($params)
	{
		$this->params = $params;
	}

	/**
	 *
	 */
	public function open()
	{
		KunenaIntegrationComprofiler::open();
	}

	/**
	 *
	 */
	public function close()
	{
		KunenaIntegrationComprofiler::close();
	}

	/**
	 * @param string $action
	 * @param bool   $xhtml
	 *
	 * @return bool|string
	 */
	public function getUserListURL($action = '', $xhtml = true)
	{
		global $_CB_framework;

		$config = KunenaFactory::getConfig();
		$my     = JFactory::getUser();

		if ($config->userlist_allowed == 0 && $my->id == 0)
		{
			return false;
		}

		return $_CB_framework->userProfilesListUrl(null, $xhtml);
	}

	/**
	 * @param        $user
	 * @param string $task
	 * @param bool   $xhtml
	 *
	 * @return bool|string
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
	 */
	public function showProfile($view, &$params)
	{
		global $_PLUGINS;

		$_PLUGINS->loadPluginGroup('user');

		return implode(
			' ', $_PLUGINS->trigger(
			'forumSideProfile', array('kunena', $view, $view->profile->userid,
			array('config' => &$view->config, 'userprofile' => &$view->profile, 'params' => &$params))));
	}

	/**
	 * @param $event
	 * @param $params
	 */
	public static function trigger($event, &$params)
	{
		KunenaIntegrationComprofiler::trigger($event, $params);
	}

	/**
	 * @param int $limit
	 *
	 * @return array
	 */
	public function _getTopHits($limit = 0)
	{
		$db    = JFactory::getDBO();
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
	 * @param      $userid
	 * @param bool $xhtml
	 *
	 * @return string
	 */
	public function getEditProfileURL($userid, $xhtml = true)
	{
		global $_CB_framework;

		return $_CB_framework->userProfileEditUrl(null, $xhtml);
	}
}
