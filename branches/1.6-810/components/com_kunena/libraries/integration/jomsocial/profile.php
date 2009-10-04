<?php
/**
* @version $Id:  $
* Kunena Component - JomSocial Profile Integration
* @package Kunena
*
* @Copyright (C) 2009 www.kunena.com All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

kimport('integration.profile');

class KProfileJomSocial extends KProfile 
{
	protected function __construct() 
	{
		kimport('integration.jomsocial.integration');
		KIntegrationJomSocial::init(); 
		if (!KIntegrationJomSocial::useProfileIntegration() && !KIntegrationJomSocial::useAvatarIntegration())
		{ 
			return null;
		}
	}
	
	public function &getInstance() 
	{
		if (!self::$instance) {
			self::$instance = new KProfileJomsocial();
		}
		return self::$instance;
	}

	public function close() 
	{
		KIntegrationJomSocial::close();
	}

	public function enqueueErrors() 
	{
		return KIntegrationJomSocial::enqueueErrors();
	}

	public function useProfileIntegration() 
	{
		return KIntegrationJomSocial::useProfileIntegration();
	}

	/*
	public function getLoginURL() 
	{
		return parent::getLoginURL();
	}

	public function getLogoutURL() 
	{
		return parent::getLogoutURL();
	}

	public function getRegisterURL() 
	{
		return parent::getRegisterURL();
	}

	public function getLostPasswordURL() 
	{
		return parent::getLostPasswordURL();
	}
	*/

	public function getForumTabURL() 
	{
		if (!KIntegrationJomSocial::useProfileIntegration()) return parent::getForumTabURL();
		return parent::getForumTabURL(); /* TODO */
	}

	public function getUserListURL() 
	{
		if (!KIntegrationJomSocial::useProfileIntegration()) return parent::getUserListURL();
		return CRoute::_('index.php?option=com_community&view=friends');
	}

	public function getAvatarURL() 
	{
		if (!KIntegrationJomSocial::useProfileIntegration()) return parent::getAvatarURL();
		return CRoute::_('index.php?option=com_community&view=profile&task=uploadAvatar');
	}

	public function getProfileURL($userid, $thumb=true) 
	{
		if (!KIntegrationJomSocial::useProfileIntegration()) return parent::getProfileURL($userid);
		if ($userid == 0) return false;
		// Get CUser object
		$user =& CFactory::getUser($userid);
		if($user === null) return false;
		return CRoute::_('index.php?option=com_community&view=profile&userid='.$userid);
	}

	public function getAvatarImgURL($userid, $thumb=true) 
	{
		if (!KIntegrationJomSocial::useAvatarIntegration()) return parent::getAvatarImgURL($userid, $thumb);
		// Get CUser object
		$user =& CFactory::getUser($userid);
		if ($thumb)	$avatar = $user->getThumbAvatar();
		else $avatar = $user->getAvatar();
		return $avatar;
	}

	public function showProfile($userid, &$msg_params)
	{
		if (!KIntegrationJomSocial::useProfileIntegration()) return parent::showProfile($userid, $msg_params);
		/* TODO */
		return '';
	}

	public function trigger($event, &$params)
	{
		if (KIntegrationJomSocial::useProfileIntegration()) return parent::trigger($event, $params);
		return KIntegrationJomSocial::trigger($event, $params);
	}

}
