<?php
/**
 * Kunena System Plugin
 * @package Kunena.Integration
 * @subpackage Joomla16
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class plgKunenaComprofiler extends JPlugin {
	public function __construct(&$subject, $config) {
		// Do not load if Kunena version is not supported or Kunena is offline
		if (!(class_exists('KunenaForum') && KunenaForum::isCompatible('2.0') && KunenaForum::enabled())) return;

		// Do not load if CommunityBuilder is not installed
		$path = JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php';
		if (!is_file ( $path )) return;

		require_once ($path);
		cbimport ( 'cb.database' );
		cbimport ( 'cb.tables' );
		cbimport ( 'language.front' );
		cbimport ( 'cb.tabs' );
		cbimport ( 'cb.field' );
		global $ueConfig;

		$app = JFactory::getApplication ();
		if (! isset ( $ueConfig ['version'] )) {
			$app->enqueueMessage ( COM_KUNENA_INTEGRATION_CB_WARN_GENERAL, 'notice' );
			$app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_INTEGRATION_CB_WARN_INSTALL', '1.7' ) );
			$app->enqueueMessage ( COM_KUNENA_INTEGRATION_CB_WARN_HIDE, 'notice' );
			return;
		} if (version_compare ( $ueConfig ['version'], '1.7' ) < 0) {
			$app->enqueueMessage ( COM_KUNENA_INTEGRATION_CB_WARN_GENERAL, 'notice' );
			$app->enqueueMessage ( JText::sprintf ( 'COM_KUNENA_INTEGRATION_CB_WARN_UPDATE', '1.7' ) );
			$app->enqueueMessage ( COM_KUNENA_INTEGRATION_CB_WARN_HIDE, 'notice' );
			return;
		}
		parent::__construct ( $subject, $config );
		$this->loadLanguage ( 'plg_kunena_comprofiler.sys', JPATH_ADMINISTRATOR );

		$this->path = dirname ( __FILE__ ) . '/comprofiler';
	}

	/*
	 * Get Kunena access control object.
	 *
	 * @return KunenaAccess
	 */
	public function onKunenaGetAccessControl() {
		require_once "{$this->path}/access.php";
		return new KunenaAccessComprofiler();
	}

	/*
	 * Get Kunena login integration object.
	 *
	 * @return KunenaLogin
	 */
	public function onKunenaGetLogin() {
		require_once "{$this->path}/login.php";
		return new KunenaLoginComprofiler();
	}
}