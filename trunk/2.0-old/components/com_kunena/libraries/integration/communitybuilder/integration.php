<?php
/**
* @version $Id:  $
* Kunena Component - Community Builder Integration
* @package Kunena
*
* @Copyright (C) 2009 www.kunena.com All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

kimport('integration.integration');

/**
 * CB framework
 * @global CBframework $_CB_framework
 */
global $_CB_framework, $_CB_database, $ueConfig;

class KIntegrationCommunityBuilder  extends KIntegration 
{
	public function init() 
	{
		if (self::$loaded === true) return self::$enabled;
		$kunenaConfig =& KConfig::getInstance();
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
			if ($kunenaConfig->kunena_profile == 'cb') {
				$params = array();
				self::trigger('onStart', $params);
			}
			self::$loaded = true;
		}
		if (self::detectIntegration() === false) {
			$kunenaConfig->pm_component = $kunenaConfig->pm_component == 'cb' ? 'none' : $kunenaConfig->pm_component;
			$kunenaConfig->avatar_src = $kunenaConfig->avatar_src == 'cb' ? 'kunena' : $kunenaConfig->avatar_src;
			$kunenaConfig->kunena_profile = $kunenaConfig->kunena_profile == 'cb' ? 'kunena' : $kunenaConfig->kunena_profile;
			return self::$enabled;
		}
		self::$enabled = true;
		if (self::useProfileIntegration() === false) {
			$kunenaConfig->kunena_profile = $kunenaConfig->kunena_profile == 'cb' ? 'kunena' : $kunenaConfig->kunena_profile;
		}
		
		return self::$enabled;
	}
	
	public function status() 
	{
		return self::$enabled;
	}
	
	public function close() 
	{
		if (self::useProfileIntegration() === true) {
			$params = array();
			self::trigger('onEnd', $params);
		}
		self::$enabled = false;
	}
	
	public function enqueueErrors() 
	{
		if (self::$error) {
			$app =& JFactory::getApplication();
			$app->enqueueMessage(_KUNENA_INTEGRATION_CB_WARN_GENERAL, 'notice');
			$app->enqueueMessage(self::$errormsg, 'notice');
			$app->enqueueMessage(_KUNENA_INTEGRATION_CB_WARN_HIDE, 'notice');
		}
	}
	
	protected function detectIntegration() 
	{
		global $ueConfig;
		$kunenaConfig =& KConfig::getInstance();

		if (!isset($ueConfig['version'])) {
			self::$errormsg = sprintf(_KUNENA_INTEGRATION_CB_WARN_INSTALL, '1.2');
			self::$error = 1;
			return false;
		}
		if ($kunenaConfig->kunena_profile != 'cb') return true;
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

	function useAvatarIntegration() 
	{
		$kunenaConfig =& KConfig::getInstance();
		return ($kunenaConfig->avatar_src == 'cb' && self::$enabled);
	}

	function useProfileIntegration() 
	{
		$kunenaConfig =& KConfig::getInstance();
		return ($kunenaConfig->kunena_profile == 'cb' && self::$enabled && !self::$error);
	}

	function usePrivateMessagesIntegration() 
	{
		$kunenaConfig =& KConfig::getInstance();
		return ($kunenaConfig->pm_component == 'cb' && self::$enabled);
	}
	
	function useLoginIntegration() 
	{
		$kunenaConfig =& KConfig::getInstance();
		return ($kunenaConfig->pm_component == 'cb' && self::$enabled);
	}
	
	/**
	* Triggers CB events
	*
	* Current events: profileIntegration=0/1, avatarIntegration=0/1
	**/
	public function trigger($event, &$params)
	{
		global $_PLUGINS;
		$kunenaConfig =& KConfig::getInstance();
		$params['config'] =& $kunenaConfig;
		$_PLUGINS->loadPluginGroup('user');
		$_PLUGINS->trigger( 'kunenaIntegration', array( $event, &$kunenaConfig, &$params ));
	}
}
