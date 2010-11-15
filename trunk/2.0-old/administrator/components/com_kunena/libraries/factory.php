<?php
/**
* @version $Id$
* Kunena Component - Kunena Factory
* @package Kunena
*
* @Copyright (C) 2009 www.kunena.com All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

class KFactory
{
	protected static $_login = null;
	protected static $_profile = null;
	protected static $_pms = null;
	
	public static function getLogin()
	{
		if (!is_object(self::$_login)) 
			self::$_login = KFactory::_createLogin();
		return self::$_login;
	}
	
	public static function getProfile()
	{
		if (!is_object(self::$_profile)) 
			self::$_profile = KFactory::_createProfile();
		return self::$_profile;
	}
	
	public static function getPMS()
	{
		if (!is_object(self::$_pms)) 
			self::$_pms = KFactory::_createPMS();
		return self::$_pms;
	}
	
	protected static function _createLogin()
	{
		$config =& KConfig::getInstance();
		
		$login = null;
		switch ($config->kunena_profile) {
			case 'cb':
				kimport('integration.communitybuilder.login');
				$login = KLoginCommunityBuilder::getInstance();
				break;
			case 'jomsocial':
				kimport('integration.jomsocial.login');
				$login = KLoginJomSocial::getInstance();
				break;
		}
		if (!is_object($login)) {
			kimport('integration.login');
			$login = Klogin::getInstance();
		}
		return $login;		
	}
	
	protected static function _createProfile()
	{
		$config =& KConfig::getInstance();
		
		$profile = null;
		switch ($config->kunena_profile) {
			case 'cb':
				kimport('integration.communitybuilder.profile');
				$profile = KProfileCommunityBuilder::getInstance();
				break;
			case 'jomsocial':
				kimport('integration.jomsocial.profile');
				$profile = KProfileJomSocial::getInstance();
				break;
		}
		if (!is_object($profile)) {
			kimport('integration.profile');
			$profile = KProfile::getInstance();
		}
		return $profile;		
	}

	protected static function _createPMS()
	{
		$config =& KConfig::getInstance();
		
		$pms = null;
		switch ($config->pm_component) {
			case 'cb':
				kimport('integration.communitybuilder.private');
				$pms = KPrivateMessagesCommunityBuilder::getInstance();
				break;
			case 'jomsocial':
				kimport('integration.jomsocial.private');
				$pms = KPrivateMessagesJomSocial::getInstance();
				break;
			case 'uddeim':
				kimport('integration.uddeim.private');
				$pms = KPrivateMessagesUddeIM::getInstance();
				break;
		}
		if (!is_object($pms)) {
			kimport('integration.private');
			$pms = KPrivateMessages::getInstance();
		}
		return $pms;
	}
}