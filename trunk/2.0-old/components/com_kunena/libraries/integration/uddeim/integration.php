<?php
/**
* @version $Id:  $
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/
//
// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

kimport('integration.integration');

class KIntegrationUddeIM extends KIntegration
{
	public function init() {
		if (self::$loaded === true) return self::$enabled;
		$kunenaConfig =& KConfig::getInstance();
		$path = JPATH_BASE .DS. 'components' .DS. 'com_uddeim' .DS. 'uddeim.php';
		if (file_exists($path)) { 
			self::$loaded = true;
		}
		if (self::detectIntegration() === false) {
			$kunenaConfig->pm_component = $kunenaConfig->pm_component == 'uddeim' ? 'none' : $kunenaConfig->pm_component;
			$kunenaConfig->avatar_src = $kunenaConfig->avatar_src == 'uddeim' ? 'kunena' : $kunenaConfig->avatar_src;
			$kunenaConfig->kunena_profile = $kunenaConfig->kunena_profile == 'uddeim' ? 'kunena' : $kunenaConfig->kunena_profile;
			return self::$enabled;
		}
		self::$enabled = true;
		if (self::useProfileIntegration() === false) {
			$kunenaConfig->kunena_profile = $kunenaConfig->kunena_profile == 'uddeim' ? 'kunena' : $kunenaConfig->kunena_profile;
		}
		
		return self::$enabled;
	}

	function useAvatarIntegration() 
	{
		return false;
	}

	function useProfileIntegration() 
	{
		return false;
	}

	function usePrivateMessagesIntegration() 
	{
		$kunenaConfig =& KConfig::getInstance();
		return ($kunenaConfig->pm_component == 'uddeim' && self::$enabled);
	}

}
?>