<?php
/**
 * @version $Id: kunena.session.class.php 2071 2010-03-17 11:27:58Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 **/
//
// Dont allow direct linking
defined( '_JEXEC' ) or die('');

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
	return "&Itemid=".getKunenaItemid();
}

class KunenaProfile
{
	protected static $instance = null;

	var $error = 0;
	var $errormsg = '';

	protected function __construct() {}

	public function &getInstance()
	{
		if (!self::$instance) {
			self::$instance = new KunenaProfile();
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
		return true;
	}

	public function getUserListURL()
	{
		return JRoute::_('index.php?option=com_kunena&func=userlist');
	}

	public function getMyAvatarURL()
	{
		return JRoute::_('index.php?option=com_kunena&func=profile&do=edit' . getKunenaItemidSuffix() );
	}

	public function getProfileURL($userid)
	{
		if ($userid == 0) return false;
		$user = KunenaFactory::getUser($userid);
		if ($user === false) return false;
		return JRoute::_("index.php?option=com_kunena&func=profile&userid={$userid}" . getKunenaItemidSuffix());
	}

	public function getAvatarImageURL($userid, $thumb=true)
	{
		$user = KunenaFactory::getUser($userid);
		$avatar = $user->avatar;
		if ($avatar == '') {
			$avatar = 'nophoto.jpg';
		}

		if ($thumb && file_exists( KPATH_COMPONENT_MEDIA .DS. 'images' .DS. 'avatars' .DS. 's_' . $avatar )) $avatar = 's_' . $avatar;
		else if (!file_exists( KPATH_COMPONENT_MEDIA .DS. 'images' .DS. 'avatars' .DS. $avatar )) {
			// If avatar does not exist use default image
			if ($thumb) $avatar = 's_nophoto.jpg';
			else $avatar = 'nophoto.jpg';
		}
		$avatar = KURL_COMPONENT_MEDIA . "images/avatars/{$avatar}";
		return $avatar;
	}

	public function showAvatar($userid, $class='', $thumb=true)
	{
		$avatar = $this->getAvatarImgURL($userid, $thumb);
		$kunenaConfig = CKunenaConfig::getInstance();
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
