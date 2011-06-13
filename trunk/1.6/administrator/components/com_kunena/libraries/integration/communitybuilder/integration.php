<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 **/
//
// Dont allow direct linking
defined( '_JEXEC' ) or die('');

class KunenaIntegrationCommunityBuilder extends KunenaIntegration {
	protected static $error = 0;
	protected static $errormsg = null;
	protected $open = false;

	public function __construct() {
		$path = KUNENA_ROOT_PATH_ADMIN . '/components/com_comprofiler/plugin.foundation.php';
		if (!is_file ( $path )) return;

		require_once ($path);
		cbimport ( 'cb.database' );
		cbimport ( 'cb.tables' );
		cbimport ( 'language.front' );
		cbimport ( 'cb.tabs' );
		cbimport ( 'cb.field' );
		$this->loaded = self::detectErrors();
	}

	public function open() {
		if ($this->open) return;
		$this->open = true;
		$params = array ();
		self::trigger ( 'onStart', $params );
	}

	public function close() {
		if (!$this->open) return;
		$this->open = false;
		$params = array ();
		self::trigger ( 'onEnd', $params );
	}

	public function enqueueErrors() {
		if (self::$error) {
			$app = JFactory::getApplication ();
			$app->enqueueMessage ( COM_KUNENA_INTEGRATION_CB_WARN_GENERAL, 'notice' );
			$app->enqueueMessage ( self::$errormsg, 'notice' );
			$app->enqueueMessage ( COM_KUNENA_INTEGRATION_CB_WARN_HIDE, 'notice' );
		}
	}

	protected function detectErrors() {
		global $ueConfig;
		$kunenaConfig = KunenaFactory::getConfig ();

		if (! isset ( $ueConfig ['version'] )) {
			self::$errormsg = JText::sprintf ( 'COM_KUNENA_INTEGRATION_CB_WARN_INSTALL', '1.2' );
			self::$error = 1;
			return false;
		} if (version_compare ( $ueConfig ['version'], '1.2.3' ) < 0) {
			self::$errormsg = JText::sprintf ( 'COM_KUNENA_INTEGRATION_CB_WARN_UPDATE', '1.2.3' );
			self::$error = 3;
			return false;
		}
/*		if (! getCBprofileItemid ()) {
			self::$errormsg = JText::_('COM_KUNENA_INTEGRATION_CB_WARN_PUBLISH');
			self::$error = 2;
		} else if (isset ( $ueConfig ['xhtmlComply'] ) && $ueConfig ['xhtmlComply'] == 0) {
			self::$errormsg = JText::_('COM_KUNENA_INTEGRATION_CB_WARN_XHTML');
			self::$error = 4;
		} else if (! class_exists ( 'getForumModel' )) {
			self::$errormsg = JText::_('COM_KUNENA_INTEGRATION_CB_WARN_INTEGRATION');
			self::$error = 5;
		}*/
		return true;
	}

	/**
	 * Triggers CB events
	 *
	 * Current events: profileIntegration=0/1, avatarIntegration=0/1
	 **/
	public function trigger($event, &$params) {
		global $_PLUGINS;
		$kunenaConfig = KunenaFactory::getConfig ();
		$params ['config'] = & $kunenaConfig;
		$_PLUGINS->loadPluginGroup ( 'user' );
		$_PLUGINS->trigger ( 'kunenaIntegration', array ($event, &$kunenaConfig, &$params ) );
	}
}
