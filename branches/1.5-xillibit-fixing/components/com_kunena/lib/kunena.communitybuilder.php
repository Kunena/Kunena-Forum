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

class CKunenaCBProfile {
	var $error = 0;
	var $errormsg = '';

	function __construct() {
		$fbConfig =& CKunenaConfig::getInstance();
		$cbpath = KUNENA_ROOT_PATH_ADMIN .DS. 'components' .DS. 'com_comprofiler' .DS. 'plugin.foundation.php';
		if (file_exists($cbpath)) {
			include_once($cbpath);
			cbimport('cb.database');
			cbimport('cb.tables');
			cbimport('language.front');
			cbimport('cb.tabs');
			define("KUNENA_CB_ITEMID_SUFFIX", getCBprofileItemid());
			if ($fbConfig->fb_profile == 'cb') {
				$params = array();
				$this->trigger('onStart', $params);
			}
		}
		if ($this->_detectIntegration() === false) {
			$fbConfig->pm_component = $fbConfig->pm_component == 'cb' ? 'none' : $fbConfig->pm_component;
			$fbConfig->avatar_src = $fbConfig->avatar_src == 'cb' ? 'kunena' : $fbConfig->avatar_src;
			$fbConfig->fb_profile = $fbConfig->fb_profile == 'cb' ? 'kunena' : $fbConfig->fb_profile;
		}
		else if ($this->useProfileIntegration() === false) {
			$fbConfig->fb_profile = $fbConfig->fb_profile == 'cb' ? 'kunena' : $fbConfig->fb_profile;
		}
	}

	function close() {
		$fbConfig =& CKunenaConfig::getInstance();
		if ($fbConfig->fb_profile == 'cb') {
			$params = array();
			$this->trigger('onEnd', $params);
		}
	}

	function &getInstance() {
		static $instance;
		if (!$instance) {
			$instance = new CKunenaCBProfile();
		}
		return $instance;
	}

function enqueueErrors() {
		if ($this->error) {
			$app =& JFactory::getApplication();
			$app->enqueueMessage(_KUNENA_INTEGRATION_CB_WARN_GENERAL, 'notice');
			$app->enqueueMessage($this->errormsg, 'notice');
			$app->enqueueMessage(_KUNENA_INTEGRATION_CB_WARN_HIDE, 'notice');
		}
	}

	function _detectIntegration() {
		global $ueConfig;
		$fbConfig =& CKunenaConfig::getInstance();

		// Detect
		if (!isset($ueConfig['version'])) {
			$this->errormsg = sprintf(_KUNENA_INTEGRATION_CB_WARN_INSTALL, '1.2');
			$this->error = 1;
			return false;
		}
		if ($fbConfig->fb_profile != 'cb') return true;
		if (!class_exists('getForumModel') && version_compare($ueConfig['version'], '1.2.1') < 0) {
			$this->errormsg = sprintf(_KUNENA_INTEGRATION_CB_WARN_UPDATE, '1.2.1');
			$this->error = 3;
		}
		else if (isset($ueConfig['xhtmlComply']) && $ueConfig['xhtmlComply'] == 0) {
			$this->errormsg = _KUNENA_INTEGRATION_CB_WARN_XHTML;
			$this->error = 4;
		}
		else if (!class_exists('getForumModel')) {
			$this->errormsg = _KUNENA_INTEGRATION_CB_WARN_INTEGRATION;
			$this->error = 5;
		}
		return true;
	}

	function useProfileIntegration() {
		$fbConfig =& CKunenaConfig::getInstance();
		return ($fbConfig->fb_profile == 'cb' && !$this->error);
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

	function showAvatar($userid, $class='', $thumb=true)
	{
		static $instances = array();

		if (!isset($instances[$userid]))
		{
			$cbUser = CBuser::getInstance( (int) $userid );
			if ( $cbUser === null ) {
				$cbUser = CBuser::getInstance( null );
			}
			$instances[$userid]['large'] = $cbUser->getField( 'avatar' );
			$instances[$userid]['thumb'] = $cbUser->avatarFilePath( 2 );
		}
		if ($class) $class=' class="'.$class.'"';
		if (!$thumb) return $instances[$userid]['large'];
		else return '<img'.$class.' src="'.$instances[$userid]['thumb'].'" alt="" />';
	}

	function showProfile($userid, &$msg_params)
	{
		static $instances = array();

		if (!isset($instances[$userid]))
		{
			global $_PLUGINS;
			$fbConfig = CKunenaConfig::getInstance();
			$userprofile = new CKunenaUserprofile($userid);
			$_PLUGINS->loadPluginGroup('user');
			$instances[$userid] = implode( '', $_PLUGINS->trigger( 'forumSideProfile', array( 'kunena', null, $userid,
				array( 'config'=> &$fbConfig, 'userprofile'=> &$userprofile, 'msg_params'=>&$msg_params) ) ) );
		}
		return $instances[$userid];
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
