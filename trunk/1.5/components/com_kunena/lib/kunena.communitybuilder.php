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

require_once (KUNENA_PATH_LIB .DS. "kunena.profile.php");

class CKunenaCBIntegration {
	protected static $loaded = false;
	protected static $enabled = false;
	protected static $error = 0;
	protected static $errormsg = '';
	
	private function __construct() { }
	
	public function start() {
		if (self::$loaded === true) return self::$enabled;
		$fbConfig =& CKunenaConfig::getInstance();
		$cbpath = KUNENA_ROOT_PATH_ADMIN .DS. 'components' .DS. 'com_comprofiler' .DS. 'plugin.foundation.php';
		if (file_exists($cbpath)) { 
			include_once($cbpath);
			cbimport('cb.database');
			cbimport('cb.tables');
			cbimport('language.front');
			cbimport('cb.tabs');
			cbimport('cb.field');
			define("KUNENA_CB_ITEMID_SUFFIX", getCBprofileItemid());
			// Load CB forum plugin
			if ($fbConfig->fb_profile == 'cb') {
				$params = array();
				self::trigger('onStart', $params);
			}
			self::$loaded = true;
		}
		if (self::detectIntegration() === false) {
			$fbConfig->pm_component = $fbConfig->pm_component == 'cb' ? 'none' : $fbConfig->pm_component;
			$fbConfig->avatar_src = $fbConfig->avatar_src == 'cb' ? 'kunena' : $fbConfig->avatar_src;
			$fbConfig->fb_profile = $fbConfig->fb_profile == 'cb' ? 'kunena' : $fbConfig->fb_profile;
			return self::$enabled;
		}
		self::$enabled = true;
		if (self::useProfileIntegration() === false) {
			$fbConfig->fb_profile = $fbConfig->fb_profile == 'cb' ? 'kunena' : $fbConfig->fb_profile;
		}
		
		return self::$enabled;
	}
	
	public function status() {
		return self::$enabled;
	}
	
	public function close() {
		if (self::useProfileIntegration() === true) {
			$params = array();
			self::trigger('onEnd', $params);
		}
		self::$enabled = false;
	}
	
	public function enqueueErrors() {
		if (self::$error) {
			$app =& JFactory::getApplication();
			$app->enqueueMessage(_KUNENA_INTEGRATION_CB_WARN_GENERAL, 'notice');
			$app->enqueueMessage(self::$errormsg, 'notice');
			$app->enqueueMessage(_KUNENA_INTEGRATION_CB_WARN_HIDE, 'notice');
		}
	}
	
	protected function detectIntegration() {
		global $ueConfig;
		$fbConfig =& CKunenaConfig::getInstance();

		if (!isset($ueConfig['version'])) {
			self::$errormsg = sprintf(_KUNENA_INTEGRATION_CB_WARN_INSTALL, '1.2');
			self::$error = 1;
			return false;
		}
		if ($fbConfig->fb_profile != 'cb') return true;
		if (!getCBprofileItemid()) {
			self::$errormsg = _KUNENA_INTEGRATION_CB_WARN_PUBLISH;
			self::$error = 2;
		}
		if (!class_exists('getForumModel') && version_compare($ueConfig['version'], '1.2.1') < 0) {
			self::$errormsg = sprintf(_KUNENA_INTEGRATION_CB_WARN_UPDATE, '1.2.1');
			self::$error = 3;
		}
		else if (isset($ueConfig['xhtmlComply']) && $ueConfig['xhtmlComply'] == 0) {
			self::$errormsg = _KUNENA_INTEGRATION_CB_WARN_XHTML;
			self::$error = 4;
		}
		else if (!class_exists('getForumModel')) {
			self::$errormsg = _KUNENA_INTEGRATION_CB_WARN_INTEGRATION;
			self::$error = 5;
		}
		return true;
	}

	function useAvatarIntegration() {
		$fbConfig =& CKunenaConfig::getInstance();
		return ($fbConfig->avatar_src == 'cb' && self::$enabled);
	}

	function useProfileIntegration() {
		$fbConfig =& CKunenaConfig::getInstance();
		return ($fbConfig->fb_profile == 'cb' && self::$enabled && !self::$error);
	}

	function usePMSIntegration() {
		$fbConfig =& CKunenaConfig::getInstance();
		return ($fbConfig->pm_component == 'cb' && self::$enabled);
	}
	
	/**
	* Triggers CB events
	*
	* Current events: profileIntegration=0/1, avatarIntegration=0/1
	**/
	public function trigger($event, &$params)
	{
		global $_PLUGINS;
		$fbConfig =& CKunenaConfig::getInstance();
		$params['config'] =& $fbConfig;
		$_PLUGINS->loadPluginGroup('user');
		$_PLUGINS->trigger( 'kunenaIntegration', array( $event, &$fbConfig, &$params ));
	}
}

class CKunenaCBPrivateMessage {
	function showPMIcon($userinfo) {
		global $fbIcons;
		global $_CB_framework, $_CB_PMS;
		
		$fbConfig =& CKunenaConfig::getInstance();
		$kunena_my = &JFactory::getUser();
		
		// Don't send messages from/to anonymous and to yourself
		if ($kunena_my->id == 0 || $userinfo->userid == 0 || $userinfo->userid == $kunena_my->id) return '';
		
		outputCbTemplate( $_CB_framework->getUi() );
		$resultArray = $_CB_PMS->getPMSlinks( $userinfo->userid, $_CB_framework->myId(), '', '', 1);
		$msg_pms = '';
		if ( count( $resultArray ) > 0) {
			if (isset($fbIcons['pms'])) {
				$linkItem = '<img src="'.KUNENA_URLICONSPATH.$fbIcons['pms'].'" alt="'._VIEW_PMS.'" border="0" title="'._VIEW_PMS.'" />';
			}
			else
			{
				$linkItem = _VIEW_PMS;
			}
			foreach ( $resultArray as $res ) {
				if ( is_array( $res ) ) {
					$msg_pms .= '<a href="' . cbSef( $res["url"] ) . '" title="' . getLangDefinition( $res["tooltip"] ) . '">' . $linkItem . '</a> ';
				}
			}
		}
		return $msg_pms;
	}
}

class CKunenaCBProfile extends CKunenaProfile {
	protected static $instance;
	
	protected function __construct() {
	}
	
	public function close() {
		CKunenaCBIntegration::close();
	}

	public function &getInstance() {
		if (!self::$instance) {
			self::$instance = new CKunenaCBProfile();
		}
		return self::$instance;
	}

	public function enqueueErrors() {
		return CKunenaCBIntegration::enqueueErrors();
	}
	
	public function useProfileIntegration() {
		return CKunenaCBIntegration::useProfileIntegration();
	}
	
	public function getLoginURL() {
		if (!CKunenaCBIntegration::useProfileIntegration()) return parent::getLoginURL();
		return cbSef( 'index.php?option=com_comprofiler&amp;task=login' );
	}

	public function getLogoutURL() {
		if (!CKunenaCBIntegration::useProfileIntegration()) return parent::getLogoutURL();
		return cbSef( 'index.php?option=com_comprofiler&amp;task=logout' );
	}

	public function getRegisterURL() {
		if (!CKunenaCBIntegration::useProfileIntegration()) return parent::getRegisterURL();
		return cbSef( 'index.php?option=com_comprofiler&amp;task=registers' );
	}

	public function getLostPasswordURL() {
		if (!CKunenaCBIntegration::useProfileIntegration()) return parent::getLostPasswordURL();
		return cbSef( 'index.php?option=com_comprofiler&amp;task=lostPassword' );
	}

	public function getForumTabURL() {
		if (!CKunenaCBIntegration::useProfileIntegration()) return parent::getForumTabURL();
		return cbSef( 'index.php?option=com_comprofiler&amp;tab=getForumTab' . getCBprofileItemid() );
	}

	public function getUserListURL() {
		if (!CKunenaCBIntegration::useProfileIntegration()) return parent::getUserListURL();
		return cbSef( 'index.php?option=com_comprofiler&amp;task=usersList' );
	}

	public function getAvatarURL() {
		if (!CKunenaCBIntegration::useProfileIntegration()) return parent::getAvatarURL();
		return cbSef( 'index.php?option=com_comprofiler&amp;task=userAvatar' . getCBprofileItemid() );
	}

	public function getProfileURL($userid) {
		if (!CKunenaCBIntegration::useProfileIntegration()) return parent::getProfileURL($userid);
		if ($userid == 0) return false;
		$cbUser =& CBuser::getInstance( (int) $userid );
		if($cbUser === null) return false;
		return cbSef( 'index.php?option=com_comprofiler&task=userProfile&user=' .$userid. getCBprofileItemid() );
	}

	public function getAvatarImgURL($userid, $thumb=true) {
		if (!CKunenaCBIntegration::useAvatarIntegration()) return parent::getAvatarImgURL($userid, $thumb);
		$cbUser =& CBuser::getInstance( (int) $userid );
		if ( $cbUser === null ) {
			$cbUser =& CBuser::getInstance( null );
		}
		if ($thumb==0) return $cbUser->getField( 'avatar' , null, 'csv' );
		return $cbUser->getField( 'avatar' , null, 'csv', 'none', 'list' );
	}
	
	public function showProfile($userid, &$msg_params)
	{
		if (!CKunenaCBIntegration::useProfileIntegration()) return parent::showProfile($userid, $msg_params);
		
		global $_PLUGINS;
		$fbConfig =& CKunenaConfig::getInstance();
		$userprofile = new CKunenaUserprofile($userid);
		$_PLUGINS->loadPluginGroup('user');
		return implode( '', $_PLUGINS->trigger( 'forumSideProfile', array( 'kunena', null, $userid,
			array( 'config'=> &$fbConfig, 'userprofile'=> &$userprofile, 'msg_params'=>&$msg_params) ) ) );
	}

	public function trigger($event, &$params)
	{
		if (CKunenaCBIntegration::useProfileIntegration()) CKunenaCBIntegration::trigger($event, $params);
		else parent::trigger($event, $params);
	}

}
?>
