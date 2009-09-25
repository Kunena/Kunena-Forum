<?php
/**
 * @version $Id: $
 * Kunena Component - Base classes for integration
 * @package Kunena
 *
 * @Copyright (C) 2009 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/

// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

function getKunenaItemid() {
	static $Itemid = 0;

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

class KProfile 
{
	protected static $instance = null;
	
	var $error = 0;
	var $errormsg = '';

	protected function __construct() {}

	public function &getInstance() 
	{
		if (!self::$instance) {
			self::$instance =& new KProfile();
		}
		return self::$instance;
	}

	public function close() {}

	public function enqueueErrors() 
	{
		if ($this->error) {
			$app =& JFactory::getApplication();
			$app->enqueueMessage(_KUNENA_INTEGRATION_CB_WARN_GENERAL, 'notice');
			$app->enqueueMessage($this->errormsg, 'notice');
			$app->enqueueMessage(_KUNENA_INTEGRATION_CB_WARN_HIDE, 'notice');
		}
	}

	public function _detectIntegration() 
	{
		return true;
	}

	public function useProfileIntegration() 
	{
		return false;
	}

	public function getForumTabURL() 
	{
		return JRoute::_(KUNENA_LIVEURLREL . '&amp;view=user');
	}

	public function getUserListURL() 
	{
		return JRoute::_(KUNENA_LIVEURLREL.'&amp;view=userlist');
	}

	public function getAvatarURL() 
	{
		return JRoute::_( 'index.php?option=com_comprofiler&amp;task=userAvatar' . getKunenaItemidSuffix() );
	}

	public function getProfileURL($userid) 
	{
		if ($userid == 0) return false;
		$user = KUser::getInstance(false);
		$user->load($userid);
		if ($user === false) return false;
		return JRoute::_("index.php?option=com_kunena&amp;func=kunenaprofile&amp;userid={$userid}" . getKunenaItemidSuffix());
	}

	public function getAvatarImgURL($userid, $thumb=true) 
	{
		$user = KUser::getInstance(false);
		$user->load($userid);
		$avatar = $user->avatar;
		if ($avatar == '') {
			$avatar = 'nophoto.jpg';
		}

		if ($thumb && file_exists( KUNENA_PATH_UPLOADED .DS. 'avatars' .DS. 's_' . $avatar )) $avatar = 's_' . $avatar;
		else if (!file_exists( KUNENA_PATH_UPLOADED .DS. 'avatars' .DS. $avatar )) {
			// If avatar does not exist use default image
			if ($thumb) $avatar = 's_nophoto.jpg';
			else $avatar = 'nophoto.jpg';
		}
		$avatar = JURI::root() . "images/kunenafiles/avatars/{$avatar}";
		return $avatar;		
	}

	public function showAvatar($userid, $class='', $thumb=true) 
	{
		$avatar = $this->getAvatarImgURL($userid, $thumb);
		$kunenaConfig =& KConfig::getInstance();
		if ($class) $class=' class="'.$class.'"';
		if ($thumb) {
			$width = $kunenaConfig->avatarsmallwidth.'px';
			$height = $kunenaConfig->avatarsmallheight.'px';
		}
		else {
			$width = $kunenaConfig->avatarwidth.'px';
			$height = $kunenaConfig->avatarheight.'px';
		}

		return '<img'.$class.' src="'.$avatar.'" alt="" style="max-width: '.$width.'; max-height: '.$height.';" />';
	}

	public function showProfile($userid, &$msg_params) {}

	public function trigger($event, &$params) {}

}
