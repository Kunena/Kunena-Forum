<?php
/**
* @version $Id:  $
* Kunena Component - Community Builder Profile Integration
* @package Kunena
*
* @Copyright (C) 2009 www.kunena.com All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

kimport('integration.profile');

/**
 * CB framework
 * @global CBframework $_CB_framework
 */
global $_CB_framework, $_CB_database, $ueConfig;

class KProfileCommunityBuilder extends KProfile 
{
	protected function __construct() 
	{
		kimport('integration.communitybuilder.integration');
		KIntegrationCommunityBuilder::init(); 
		if (!KIntegrationCommunityBuilder::useProfileIntegration() && !KIntegrationCommunityBuilder::useAvatarIntegration()) 
		{
			return null;
		}
	}
	
	public function &getInstance() 
	{
		if (!self::$instance) {
			self::$instance = new KProfileCommunityBuilder();
		}
		return self::$instance;
	}

	public function close() 
	{
		KIntegrationCommunityBuilder::close();
	}

	public function enqueueErrors() 
	{
		return KIntegrationCommunityBuilder::enqueueErrors();
	}
	
	public function useProfileIntegration() 
	{
		return KIntegrationCommunityBuilder::useProfileIntegration();
	}
	
	public function getLoginURL() 
	{
		if (!KIntegrationCommunityBuilder::useProfileIntegration()) return parent::getLoginURL();
		return cbSef( 'index.php?option=com_comprofiler&amp;task=login' );
	}

	public function getLogoutURL() 
	{
		if (!KIntegrationCommunityBuilder::useProfileIntegration()) return parent::getLogoutURL();
		return cbSef( 'index.php?option=com_comprofiler&amp;task=logout' );
	}

	public function getRegisterURL() 
	{
		if (!KIntegrationCommunityBuilder::useProfileIntegration()) return parent::getRegisterURL();
		return cbSef( 'index.php?option=com_comprofiler&amp;task=registers' );
	}

	public function getLostPasswordURL() 
	{
		if (!KIntegrationCommunityBuilder::useProfileIntegration()) return parent::getLostPasswordURL();
		return cbSef( 'index.php?option=com_comprofiler&amp;task=lostPassword' );
	}

	public function getForumTabURL() 
	{
		if (!KIntegrationCommunityBuilder::useProfileIntegration()) return parent::getForumTabURL();
		return cbSef( 'index.php?option=com_comprofiler&amp;tab=getForumTab' . getCBprofileItemid() );
	}

	public function getUserListURL() 
	{
		if (!KIntegrationCommunityBuilder::useProfileIntegration()) return parent::getUserListURL();
		return cbSef( 'index.php?option=com_comprofiler&amp;task=usersList' );
	}

	public function getAvatarURL() 
	{
		if (!KIntegrationCommunityBuilder::useProfileIntegration()) return parent::getAvatarURL();
		return cbSef( 'index.php?option=com_comprofiler&amp;task=userAvatar' . getCBprofileItemid() );
	}

	public function getProfileURL($userid) 
	{
		if (!KIntegrationCommunityBuilder::useProfileIntegration()) return parent::getProfileURL($userid);
		if ($userid == 0) return false;
		$cbUser =& CBuser::getInstance( (int) $userid );
		if($cbUser === null) return false;
		return cbSef( 'index.php?option=com_comprofiler&task=userProfile&user=' .$userid. getCBprofileItemid() );
	}

	public function getAvatarImgURL($userid, $thumb=true) 
	{
		if (!KIntegrationCommunityBuilder::useAvatarIntegration()) return parent::getAvatarImgURL($userid, $thumb);
		$cbUser =& CBuser::getInstance( (int) $userid );
		if ( $cbUser === null ) {
			$cbUser =& CBuser::getInstance( null );
		}
		if ($thumb==0) return $cbUser->getField( 'avatar' , null, 'csv' );
		return $cbUser->getField( 'avatar' , null, 'csv', 'none', 'list' );
	}
	
	public function showProfile($userid, &$msg_params)
	{
		if (!KIntegrationCommunityBuilder::useProfileIntegration()) return parent::showProfile($userid, $msg_params);
		
		global $_PLUGINS;

		$kunenaConfig =& KConfig::getInstance();
		$userprofile = new KUser($userid);
		$_PLUGINS->loadPluginGroup('user');
		return implode( '', $_PLUGINS->trigger( 'forumSideProfile', array( 'kunena', null, $userid,
			array( 'config'=> &$kunenaConfig, 'userprofile'=> &$userprofile, 'msg_params'=>&$msg_params) ) ) );
	}

	public function trigger($event, &$params)
	{
		if (!KIntegrationCommunityBuilder::useProfileIntegration()) return parent::trigger($event, $params);
		return KIntegrationCommunityBuilder::trigger($event, $params);
	}

}
