<?php
/**
* @version $Id:  $
* Kunena Component - JomSocial Integration
* @package Kunena
*
* @Copyright (C) 2009 www.kunena.com All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

kimport('integration.integration');

class KIntegrationJomSocial extends KIntegration
{
	public function init()
	{
		if (self::isLoaded() === true) return self::IsEnabled();

		$kconfig =& KConfig::getInstance();

		$jspath = JPATH_BASE.DS.'components'.DS.'com_community'.DS.'libraries'.DS.'core.php';
		if (file_exists($jspath))
		{
			// Prevent JomSocial from loading their jquery library - we got one loaded already
			// define( 'C_ASSET_JQUERY', 1 ); --> moved to kunena.php as part of the jquery load

			include_once($jspath);
			if ($kconfig->pm_component == 'jomsocial')
			{
				include_once(JPATH_BASE.DS. 'components'.DS.'com_community'.DS.'libraries'.DS.'messaging.php');
				//PM popup requires JomSocial css to be loaded from selected template
				$cconfig =& CFactory::getConfig();
				$document =& JFactory::getDocument();
				$document->addStyleSheet('components/com_community/assets/window.css');
				$document->addStyleSheet('components/com_community/templates/'.$cconfig->get('template').'/css/style.css');
			}
			if ($kconfig->kunena_profile == 'jomsocial') {
				$params = array();
				self::trigger('onStart', $params);
			}
			self::loaded();
		}
		if (self::detectIntegration() === false) {
			$kconfig->pm_component = $kconfig->pm_component == 'jomsocial' ? 'none' : $kconfig->pm_component;
			$kconfig->kunena_profile = $kconfig->kunena_profile == 'jomsocial' ? 'kunena' : $kconfig->kunena_profile;
			$kconfig->avatar_src = $kconfig->avatar_src == 'jomsocial' ? 'kunena' : $kconfig->avatar_src;
			return self::IsEnabled();
		}
		self::enable();

		if (self::useProfileIntegration() === false) {
			$kconfig->kunena_profile = $kconfig->kunena_profile == 'jomsocial' ? 'kunena' : $kconfig->kunena_profile;
		}

		return self::IsEnabled();
	}

	public function close() 
	{
		if (self::useProfileIntegration() === true) {
			$params = array();
			self::trigger('onEnd', $params);
		}
		self::enable(false);
	}

	public function enqueueErrors() 
	{
		if (self::GetError()) {
			$app =& JFactory::getApplication();
			$app->enqueueMessage(_KUNENA_INTEGRATION_JOMSOCIAL_WARN_GENERAL, 'notice');
			$app->enqueueMessage(self::$errormsg, 'notice');
			$app->enqueueMessage(_KUNENA_INTEGRATION_JOMSOCIAL_WARN_HIDE, 'notice');
		}
	}

	protected function detectIntegration() 
	{
		$kconfig =& KConfig::getInstance();
/* TODO:
		if (!isset($ueConfig['version'])) {
			self::$errormsg = sprintf(_KUNENA_INTEGRATION_CB_WARN_INSTALL, '1.2');
			self::$error = 1;
			return false;
		}
		if ($kconfig->kunena_profile != 'cb') return true;
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

	function useAvatarIntegration() 
	{
		$kconfig =& KConfig::getInstance();
		return ($kconfig->avatar_src == 'jomsocial' && self::IsEnabled());
	}

	function useProfileIntegration() 
	{
		$kconfig =& KConfig::getInstance();
		return ($kconfig->kunena_profile == 'jomsocial' && self::IsEnabled() && !self::GetError());
	}

	function useLoginIntegration() 
	{
		$kconfig =& KConfig::getInstance();
		return ($kconfig->pm_component == 'jomsocial' && self::$enabled);
	}

	function usePrivateMessagesIntegration() 
	{
		$kconfig =& KConfig::getInstance();
		return ($kconfig->pm_component == 'jomsocial' && self::IsEnabled());
	}

	/**
	* Triggers Jomsocial events
	*
	* Current events: profileIntegration=0/1, avatarIntegration=0/1
	**/
	public function trigger($event, &$params)
	{
		$kconfig =& KConfig::getInstance();
		$params['config'] =& $kconfig;
		// TODO: jomsocial trigger( 'kunenaIntegration', array( $event, &$kconfig, &$params ));
	}
}
