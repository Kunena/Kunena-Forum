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

function getKunenaItemid() {
	static $Itemid;

	$kunena_db = &JFactory::getDBO();
	if (!$Itemid)
	{
		$kunena_db->setQuery("SELECT id FROM #__menu WHERE link='index.php?option=com_kunena' AND published='1'");
		$Itemid = (int) $kunena_db->loadResult();

		if ($Itemid < 1) {
			$Itemid = 0;
		}
	}
	return $Itemid;
}

function getKunenaItemidSuffix() {
	return "&amp;Itemid=".getKunenaItemid();
}

class CKunenaPMS {
	function showPMIcon($userinfo) {
		global $fbIcons;
		
		$fbConfig =& CKunenaConfig::getInstance();
		$kunena_my = &JFactory::getUser();
		
		// Don't send messages from/to anonymous and to yourself
		if ($kunena_my->id == 0 || $userinfo->userid == 0 || $userinfo->userid == $kunena_my->id) return '';
		
		/*let's see if we should use Community Builder integration */
		if ($fbConfig->pm_component == "cb")
		{
			$msg_pms = CKunenaCBPrivateMessage::showPMIcon($userinfo);
		}
		
		/*let's see if we should use JomSocial integration */
		if ($fbConfig->pm_component == "jomsocial")
		{
			$msg_pms = CKunenaJomSocialPrivateMessage::showPMIcon($userinfo);
		}

		/*let's see if we should use Missus integration */
		if ($fbConfig->pm_component == "missus")
		{
			//we should offer the user a Missus link
			//first get the username of the user to contact
			$PMSName = $userinfo->username;
			$msg_pms
			= "<a href=\"" . JRoute::_('index.php?option=com_missus&amp;func=newmsg&amp;user=' . $userinfo->userid . '&amp;subject=' . _GEN_FORUM . ': ' . urlencode(utf8_encode($fmessage->subject))) . "\"><img src='";

			if ($fbIcons['pms']) {
				$msg_pms .= KUNENA_URLICONSPATH . $fbIcons['pms'];
			}
			else {
				$msg_pms .= KUNENA_URLICONSPATH  . $fbIcons['pms'];;
			}

			$msg_pms .= "' alt=\"" . _VIEW_PMS . "\" border=\"0\" title=\"" . _VIEW_PMS . "\" /></a>";
		}

		/*let's see if we should use JIM integration */
		if ($fbConfig->pm_component == "jim")
		{
			//we should offer the user a JIM link
			//first get the username of the user to contact
			$PMSName = $userinfo->username;
			$msg_pms = "<a href=\"" . JRoute::_('index.php?option=com_jim&amp;page=new&amp;id=' . $PMSName . '&title=' . $fmessage->subject) . "\"><img src='";

			if ($fbIcons['pms']) {
				$msg_pms .= KUNENA_URLICONSPATH . $fbIcons['pms'];
			}
			else {
				$msg_pms .= KUNENA_URLICONSPATH  .  $fbIcons['pms'];;
			}

			$msg_pms .= "' alt=\"" . _VIEW_PMS . "\" border=\"0\" title=\"" . _VIEW_PMS . "\" /></a>";
		}
		/*let's see if we should use uddeIM integration */
		if ($fbConfig->pm_component == "uddeim")
		{
			//we should offer the user a PMS link
			//first get the username of the user to contact
			$PMSName = $userinfo->username;
			$msg_pms = "<a href=\"" . JRoute::_('index.php?option=com_uddeim&amp;task=new&recip=' . $userinfo->userid) . "\"><img src=\"";

			if ($fbIcons['pms']) {
				$msg_pms .= KUNENA_URLICONSPATH . $fbIcons['pms'];
			}
			else {
				$msg_pms .= KUNENA_URLEMOTIONSPATH . "sendpm.gif";
			}

			$msg_pms .= "\" alt=\"" . _VIEW_PMS . "\" border=\"0\" title=\"" . _VIEW_PMS . "\" /></a>";
		}
		/*let's see if we should use myPMS2 integration */
		if ($fbConfig->pm_component == "pms")
		{
			//we should offer the user a PMS link
			//first get the username of the user to contact
			$PMSName = $userinfo->username;
			$msg_pms = "<a href=\"" . JRoute::_('index.php?option=com_pms&amp;page=new&amp;id=' . $PMSName . '&title=' . $fmessage->subject) . "\"><img src=\"";

			if ($fbIcons['pms']) {
				$msg_pms .= KUNENA_URLICONSPATH . $fbIcons['pms'];
			}
			else {
				$msg_pms .= KUNENA_URLEMOTIONSPATH . "sendpm.gif";
			}

			$msg_pms .= "\" alt=\"" . _VIEW_PMS . "\" border=\"0\" title=\"" . _VIEW_PMS . "\" /></a>";
		}

		return $msg_pms;
	}
}

class CKunenaProfile {

	var $error = 0;
	var $errormsg = '';
	protected static $instance;

	protected function __construct() {
	}

	public function close() {
	}

	public function &getInstance() {
		$fbConfig =& CKunenaConfig::getInstance();
		if (!self::$instance) {
			if ($fbConfig->pm_component == 'cb' || $fbConfig->fb_profile == 'cb' || $fbConfig->avatar_src == 'cb')
			{
				// Get Community Builder compability
				require_once (KUNENA_PATH_LIB .DS. 'kunena.communitybuilder.php');
				CkunenaCBIntegration::start(); 
				if (CkunenaCBIntegration::useProfileIntegration() || CkunenaCBIntegration::useAvatarIntegration()) self::$instance =& CkunenaCBProfile::getInstance();
			}
			else if ($fbConfig->pm_component == 'jomsocial' || $fbConfig->fb_profile == 'jomsocial' || $fbConfig->avatar_src == 'jomsocial')
			{
				// Get JomSocial compability
				require_once (KUNENA_PATH_LIB .DS. 'kunena.jomsocial.php');
				CkunenaJomSocialIntegration::start(); 
				if (CkunenaJomSocialIntegration::useProfileIntegration() || CkunenaJomSocialIntegration::useAvatarIntegration()) self::$instance =& CkunenaJomSocialProfile::getInstance();
			}
			if (!self::$instance)
			{
				self::$instance =& new CKunenaProfile();
			}
		}
		return self::$instance;
	}

	public function enqueueErrors() {
		if ($this->error) {
			$app =& JFactory::getApplication();
			$app->enqueueMessage(_KUNENA_INTEGRATION_CB_WARN_GENERAL, 'notice');
			$app->enqueueMessage($this->errormsg, 'notice');
			$app->enqueueMessage(_KUNENA_INTEGRATION_CB_WARN_HIDE, 'notice');
		}
	}

	public function _detectIntegration() {
		return true;
	}

	public function useProfileIntegration() {
		return false;
	}

	public function getLoginURL() {
		return JRoute::_('index.php?option=com_user&amp;view=login');
	}

	public function getLogoutURL() {
		return JRoute::_('index.php?option=com_user&amp;view=login');
	}

	public function getRegisterURL() {
		return JRoute::_('index.php?option=com_user&amp;view=register&amp;Itemid=' . getKunenaItemidSuffix());
	}

	public function getLostPasswordURL() {
		return JRoute::_('index.php?option=com_user&amp;view=reset&amp;Itemid=' . getKunenaItemidSuffix());
	}

	public function getForumTabURL() {
		return JRoute::_(KUNENA_LIVEURLREL . '&amp;func=userprofile&amp;do=show');
	}

	public function getUserListURL() {
		return JRoute::_(KUNENA_LIVEURLREL.'&amp;func=userlist');
	}

	public function getAvatarURL() {
		return cbSef( 'index.php?option=com_comprofiler&amp;task=userAvatar' . getKunenaItemidSuffix() );
	}

	public function getProfileURL($userid) {
		if ($userid == 0) return false;
		$users = CKunenaUsers::getInstance();
		$user = $users->get($userid);
		if ($user === false) return false;
		return JRoute::_("index.php?option=com_kunena&amp;func=fbprofile&amp;userid={$userid}" . getKunenaItemidSuffix());
	}

	public function getAvatarImgURL($userid, $thumb=true) {
		$users = CKunenaUsers::getInstance();
		$user = $users->get($userid);
		$avatar = $user->get('avatar');
		if ($avatar == '') {
			$avatar = 'nophoto.jpg';
		}

		if ($thumb && file_exists( KUNENA_PATH_UPLOADED .DS. 'avatars' .DS. 's_' . $avatar )) $avatar = 's_' . $avatar;
		else if (!file_exists( KUNENA_PATH_UPLOADED .DS. 'avatars' .DS. $avatar )) {
			// If avatar does not exist use default image
			if ($thumb) $avatar = 's_nophoto.jpg';
			else $avatar = 'nophoto.jpg';
		}
		$avatar = KUNENA_LIVEUPLOADEDPATH . "/avatars/{$avatar}";
		return $avatar;		
	}

	public function showAvatar($userid, $class='', $thumb=true) {
		$avatar = $this->getAvatarImgURL($userid, $thumb);
		$fbConfig =& CKunenaConfig::getInstance();
		if ($class) $class=' class="'.$class.'"';
		if ($thumb) {
			$width = $fbConfig->avatarsmallwidth.'px';
			$height = $fbConfig->avatarsmallheight.'px';
		}
		else {
			$width = $fbConfig->avatarwidth.'px';
			$height = $fbConfig->avatarheight.'px';
		}

		return '<img'.$class.' src="'.$avatar.'" alt="" style="max-width: '.$width.'; max-height: '.$height.';" />';
	}

	public function showProfile($userid, &$msg_params)
	{
	}

	public function trigger($event, &$params)
	{
		// Nothing to do
	}

}
?>