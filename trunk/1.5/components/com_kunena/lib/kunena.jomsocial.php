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

include_once(KUNENA_PATH_LIB.DS.'kunena.profile.php');

class CKunenaJomSocialIntegration {
	protected static $loaded = false;
	protected static $enabled = false;
	protected static $error = 0;
	protected static $errormsg = '';

	private function __construct() { }

	public function start() {
		if (self::$loaded === true) return self::$enabled;
		$fbConfig =& CKunenaConfig::getInstance();

		$jspath = JPATH_BASE.DS.'components'.DS.'com_community'.DS.'libraries'.DS.'core.php';
		if (file_exists($jspath))
		{
			// Prevent JomSocial from loading their jquery library - we got one loaded already
			// define( 'C_ASSET_JQUERY', 1 ); --> moved to kunena.php as part of the jquery load

			include_once($jspath);
			if ($fbConfig->pm_component == 'jomsocial')
			{
				include_once(KUNENA_ROOT_PATH .DS. 'components'.DS.'com_community'.DS.'libraries'.DS.'messaging.php');
				//PM popup requires JomSocial css to be loaded from selected template
				$config =& CFactory::getConfig();
				$document =& JFactory::getDocument();
				$document->addStyleSheet(KUNENA_JLIVEURL.'/components/com_community/assets/window.css');
				$document->addStyleSheet(KUNENA_JLIVEURL.'/components/com_community/templates/'.$config->get('template').'/css/style.css');
			}
			if ($fbConfig->fb_profile == 'jomsocial') {
				$params = array();
				self::trigger('onStart', $params);
			}
			self::$loaded = true;
		}
		if (self::detectIntegration() === false) {
			$fbConfig->pm_component = $fbConfig->pm_component == 'jomsocial' ? 'none' : $fbConfig->pm_component;
			$fbConfig->fb_profile = $fbConfig->fb_profile == 'jomsocial' ? 'kunena' : $fbConfig->fb_profile;
			$fbConfig->avatar_src = $fbConfig->avatar_src == 'jomsocial' ? 'kunena' : $fbConfig->avatar_src;
			return self::$enabled;
		}
		self::$enabled = true;
		if (self::useProfileIntegration() === false) {
			$fbConfig->fb_profile = $fbConfig->fb_profile == 'jomsocial' ? 'kunena' : $fbConfig->fb_profile;
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
			$app->enqueueMessage(_KUNENA_INTEGRATION_JOMSOCIAL_WARN_GENERAL, 'notice');
			$app->enqueueMessage(self::$errormsg, 'notice');
			$app->enqueueMessage(_KUNENA_INTEGRATION_JOMSOCIAL_WARN_HIDE, 'notice');
		}
	}

	protected function detectIntegration() {
		$fbConfig =& CKunenaConfig::getInstance();
/* TODO:
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
*/
		return true;
	}

	function useAvatarIntegration() {
		$fbConfig =& CKunenaConfig::getInstance();
		return ($fbConfig->avatar_src == 'jomsocial' && self::$enabled);
	}

	function useProfileIntegration() {
		$fbConfig =& CKunenaConfig::getInstance();
		return ($fbConfig->fb_profile == 'jomsocial' && self::$enabled && !self::$error);
	}

	function usePMSIntegration() {
		$fbConfig =& CKunenaConfig::getInstance();
		return ($fbConfig->pm_component == 'jomsocial' && self::$enabled);
	}

	/**
	* Triggers Jomsocial events
	*
	* Current events: profileIntegration=0/1, avatarIntegration=0/1
	**/
	public function trigger($event, &$params)
	{
		$fbConfig =& CKunenaConfig::getInstance();
		$params['config'] =& $fbConfig;
		// TODO: jomsocial trigger( 'kunenaIntegration', array( $event, &$fbConfig, &$params ));
	}
}

class CKunenaJomSocialPrivateMessage {
	function showPMIcon($userinfo) {
		global $fbIcons;

		$onclick = CMessaging::getPopup($userinfo->userid);
		$msg_pms = '';
		$msg_pms = '<a href="javascript:void(0)" onclick="'.$onclick.'">';

		if (isset($fbIcons['pms'])) {
			$msg_pms .= '<img src="'.KUNENA_URLICONSPATH.$fbIcons['pms'].'" alt="'._VIEW_PMS.'" border="0" title="'._VIEW_PMS.'" />';
		}
		else
		{
			$msg_pms .= _VIEW_PMS;
		}

		$msg_pms .= '</a>';

		return $msg_pms;
	}
}

class CKunenaJomSocialProfile extends CKunenaProfile {
	protected static $instance;

	protected function __construct() {
	}

	public function close() {
		CKunenaJomsocialIntegration::close();
	}

	public function &getInstance() {
		if (!self::$instance) {
			self::$instance = new CKunenaJomsocialProfile();
		}
		return self::$instance;
	}

	public function enqueueErrors() {
		return CKunenaJomsocialIntegration::enqueueErrors();
	}

	public function useProfileIntegration() {
		return CKunenaJomsocialIntegration::useProfileIntegration();
	}

	/*
	public function getLoginURL() {
		return parent::getLoginURL();
	}

	public function getLogoutURL() {
		return parent::getLogoutURL();
	}

	public function getRegisterURL() {
		return parent::getRegisterURL();
	}

	public function getLostPasswordURL() {
		return parent::getLostPasswordURL();
	}
	*/

	public function getForumTabURL() {
		if (!CKunenaJomsocialIntegration::useProfileIntegration()) return parent::getForumTabURL();
		return parent::getForumTabURL(); /* TODO */
	}

	public function getUserListURL() {
		if (!CKunenaJomsocialIntegration::useProfileIntegration()) return parent::getUserListURL();
		return CRoute::_('index.php?option=com_community&view=friends');
	}

	public function getAvatarURL() {
		if (!CKunenaJomsocialIntegration::useProfileIntegration()) return parent::getAvatarURL();
		return CRoute::_('index.php?option=com_community&view=profile&task=uploadAvatar');
	}

	public function getProfileURL($userid, $thumb=true) {
		if (!CKunenaJomsocialIntegration::useProfileIntegration()) return parent::getProfileURL($userid);
		if ($userid == 0) return false;
		// Get CUser object
		$user =& CFactory::getUser($userid);
		if($user === null) return false;
		return CRoute::_('index.php?option=com_community&view=profile&userid='.$userid);
	}

	public function getAvatarImgURL($userid, $thumb=true) {
		if (!CKunenaJomsocialIntegration::useAvatarIntegration()) return parent::getAvatarImgURL($userid, $thumb);
		// Get CUser object
		$user =& CFactory::getUser($userid);
		if ($thumb)	$avatar = $user->getThumbAvatar();
		else $avatar = $user->getAvatar();
		return $avatar;
	}

	public function showProfile($userid, &$msg_params)
	{
		if (!CKunenaJomsocialIntegration::useProfileIntegration()) return parent::showProfile($userid, $msg_params);
		/* TODO */
		return '';
	}

	public function trigger($event, &$params)
	{
		if (CKunenaJomsocialIntegration::useProfileIntegration()) CKunenaJomsocialIntegration::trigger($event, $params);
		else parent::trigger($event, $params);
	}

}
?>