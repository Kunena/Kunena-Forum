<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class KunenaProfileCommunityBuilder extends KunenaProfile
{
	protected $integration = null;

	public function __construct() {
		$this->integration = KunenaIntegration::getInstance ('communitybuilder');
		if (! $this->integration || ! $this->integration->isLoaded())
			return;
		$this->priority = 50;
	}

	public function open()
	{
		$this->integration->open();
	}

	public function close()
	{
		$this->integration->close();
	}

/* TODO: do we need this anymore:
	public function getForumTabURL()
	{
		return cbSef( 'index.php?option=com_comprofiler&amp;tab=getForumTab' . getCBprofileItemid() );
	}
*/

	public function getUserListURL($action='', $xhtml = true)
	{
		$config = KunenaFactory::getConfig ();
		$my = JFactory::getUser();
		if ( $config->userlist_allowed == 1 && $my->id == 0  ) return false;
		return cbSef( 'index.php?option=com_comprofiler&amp;task=usersList', $xhtml );
	}

	public function getProfileURL($user, $task='', $xhtml = true)
	{
		$user = KunenaFactory::getUser($user);
		if ($user->userid == 0) return false;
		// Get CUser object
		$cbUser = CBuser::getInstance( $user->userid );
		if($cbUser === null) return false;
		return cbSef( 'index.php?option=com_comprofiler&task=userProfile&user=' . $user->userid. getCBprofileItemid(), $xhtml );
	}

	public function showProfile($user, &$msg_params)
	{
		global $_PLUGINS;

		$kunenaConfig = KunenaFactory::getConfig();
		$user = KunenaFactory::getUser($user);
		$_PLUGINS->loadPluginGroup('user');
		return implode( '', $_PLUGINS->trigger( 'forumSideProfile', array( 'kunena', null, $user->userid,
			array( 'config'=> &$kunenaConfig, 'userprofile'=> &$user, 'msg_params'=>&$msg_params) ) ) );
	}

	public function trigger($event, &$params)
	{
		return $this->integration->trigger($event, $params);
	}

	public function _getTopHits($limit=0) {
		$db = JFactory::getDBO ();
		$query = "SELECT user_id AS id, hits AS count FROM #__comprofiler WHERE hits>0 ORDER BY hits DESC";
		$db->setQuery ( $query, 0, $limit );
		$top = $db->loadObjectList ();
		KunenaError::checkDatabaseError();
		return $top;
	}
}
