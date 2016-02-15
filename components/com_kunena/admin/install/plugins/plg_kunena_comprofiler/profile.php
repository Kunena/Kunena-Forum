<?php
/**
 * Kunena Plugin
 *
 * @package       Kunena.Plugins
 * @subpackage    Comprofiler
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die ();

require_once dirname(__FILE__) . '/integration.php';

class KunenaProfileComprofiler extends KunenaProfile
{
	protected $params = null;

	public function __construct($params)
	{
		$this->params = $params;
	}

	public function open()
	{
		KunenaIntegrationComprofiler::open();
	}

	public function close()
	{
		KunenaIntegrationComprofiler::close();
	}

	public function getUserListURL($action = '', $xhtml = true)
	{
		$config = KunenaFactory::getConfig();
		$my     = JFactory::getUser();
		if ($config->userlist_allowed == 1 && $my->id == 0)
		{
			return false;
		}

		return cbSef('index.php?option=com_comprofiler&amp;task=usersList', $xhtml);
	}

	public function getProfileURL($user, $task = '', $xhtml = true)
	{
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

		return cbSef('index.php?option=com_comprofiler&task=userProfile&user=' . $user->userid . getCBprofileItemid(), $xhtml);
	}

	public function showProfile($view, &$params)
	{
		global $_PLUGINS;

		$_PLUGINS->loadPluginGroup('user');

		return implode(' ', $_PLUGINS->trigger('forumSideProfile', array('kunena', $view, $view->profile->userid,
			array('config' => &$view->config, 'userprofile' => &$view->profile, 'params' => &$params))));
	}

	public static function trigger($event, &$params)
	{
		KunenaIntegrationComprofiler::trigger($event, $params);
	}

	public function _getTopHits($limit = 0)
	{
		$db    = JFactory::getDBO();
		$query = "SELECT cu.user_id AS id, cu.hits AS count
			FROM #__comprofiler AS cu
			INNER JOIN #__users AS u ON u.id=cu.user_id
			WHERE cu.hits>0
			ORDER BY cu.hits DESC";
		$db->setQuery($query, 0, $limit);
		$top = (array) $db->loadObjectList();
		KunenaError::checkDatabaseError();

		return $top;
	}

	public function getEditProfileURL($userid, $xhtml = true)
	{
		return cbSef('index.php?option=com_comprofiler&task=userDetails' . getCBprofileItemid(), $xhtml);
	}
}
