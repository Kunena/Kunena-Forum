<?php
/**
* @version $Id: kunena.communitybuilder.php 570 2009-03-31 10:04:30Z mahagr $
* Kunena Component - Community Builder compability
* @package Kunena
*
* @Copyright (C) 2009 www.kunena.com All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

/**
 * CB framework
 * @global CBframework $_CB_framework
 */
global $_CB_framework, $_CB_database, $ueConfig;

$cbpath = KUNENA_ROOT_PATH_ADMIN .DS. 'components/com_comprofiler/plugin.foundation.php';
if ( ! file_exists( $cbpath ) )
{
	$fbConfig->pm_component = $fbConfig->pm_component == 'cb' ? 'none' : $fbConfig->pm_component;
	$fbConfig->fb_profile = $fbConfig->fb_profile == 'cb' ? 'kunena' : $fbConfig->fb_profile;
	$fbConfig->avatar_src = $fbConfig->avatar_src == 'cb' ? 'kunena' : $fbConfig->avatar_src;
	
	return;
}
include_once( $cbpath );
cbimport( 'cb.database' );
cbimport( 'cb.tables' );
cbimport( 'language.front' );
cbimport( 'cb.tabs' );

unset ($tmp_db);

define("KUNENA_CB_ITEMID_SUFFIX", getCBprofileItemid());

class CKunenaCBProfile {
	var $sidebarText;

	function CKunenaCBProfile() {
	}

	function &getInstance() {
		static $instance;
		if (!$instance) $instance = new CKunenaCBProfile();
		return $instance;
	}

	function getLoginURL() {
		return cbSef( 'index.php?option=com_comprofiler&amp;task=login' );
	}

	function getLogoutURL() {
		return cbSef( 'index.php?option=com_comprofiler&amp;task=logout' );
	}

	function getRegisterURL() {
		return cbSef( 'index.php?option=com_comprofiler&amp;task=registers' );
	}

	function getLostPasswordURL() {
		return cbSef( 'index.php?option=com_comprofiler&amp;task=lostPassword' );
	}

	function getForumTabURL() {
		return cbSef( 'index.php?option=com_comprofiler&amp;tab=getForumTab' . getCBprofileItemid() );
	}

	function getUserListURL() {
		return cbSef( 'index.php?option=com_comprofiler&amp;task=usersList' );
	}

	function getAvatarURL() {
		return cbSef( 'index.php?option=com_comprofiler&amp;task=userAvatar' . getCBprofileItemid() );
	}

	function getProfileURL($userid) {
		$cbUser =& CBuser::getInstance( (int) $userid );
		if($cbUser === null) return;
		return cbSef( 'index.php?option=com_comprofiler&task=userProfile&user=' .$userid. getCBprofileItemid() );
	}

	function showAvatar($userid, $class='', $thumb=true) {
		$cbUser =& CBuser::getInstance( (int) $userid );
		if ( $cbUser === null ) {
			$cbUser =& CBuser::getInstance( null );
		}
		if ($class) $class=' class="'.$class.'"';
		if ($thumb==0) return $cbUser->getField( 'avatar' );
		else return '<img'.$class.' src="'.$cbUser->avatarFilePath( 2 ).'" alt="" />';
	}

	function showProfile($userid, &$msg_params)
	{
		global $_PLUGINS;

		$fbConfig =& CKunenaConfig::getInstance();
		$userprofile = new CKunenaUserprofile($userid);
		$_PLUGINS->loadPluginGroup('user');
		return implode( '', $_PLUGINS->trigger( 'forumSideProfile', array( 'kunena', null, $userid,
			array( 'config'=> &$fbConfig, 'userprofile'=> &$userprofile, 'msg_params'=>&$msg_params) ) ) );
	}

	/**
	* Triggers CB events
	*
	* Current events: profileIntegration=0/1, avatarIntegration=0/1
	**/
	function trigger($event, &$params)
	{
		global $_PLUGINS;

		$fbConfig =& CKunenaConfig::getInstance();
		$params['config'] =& $fbConfig;
		$_PLUGINS->loadPluginGroup('user');
		$_PLUGINS->trigger( 'kunenaIntegration', array( $event, &$fbConfig, &$params ));
	}

}
?>
