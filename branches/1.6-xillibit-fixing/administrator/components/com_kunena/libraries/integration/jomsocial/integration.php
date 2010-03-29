<?php
/**
 * @version $Id$
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

class KunenaIntegrationJomSocial extends KunenaIntegration {
	public function __construct() {
		$jspath = JPATH_BASE . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php';
		if (!is_file ( $jspath )) return;

		include_once ($jspath);
		$this->loaded = true;
	}

	public function enqueueErrors() {
		if (self::GetError ()) {
			$app = JFactory::getApplication ();
			$app->enqueueMessage ( COM_KUNENA_INTEGRATION_JOMSOCIAL_WARN_GENERAL, 'notice' );
			$app->enqueueMessage ( self::$errormsg, 'notice' );
			$app->enqueueMessage ( COM__KUNENA_INTEGRATION_JOMSOCIAL_WARN_HIDE, 'notice' );
		}
	}

	/**
	 * Triggers Jomsocial events
	 *
	 * Current events: profileIntegration=0/1, avatarIntegration=0/1
	 **/
	public function trigger($event, &$params) {
		$kconfig = CKunenaConfig::getInstance ();
		$params ['config'] = & $kconfig;
		// TODO: jomsocial trigger
	}
}
