<?php
/**
 * Kunena Component
 * @package Kunena.Installer
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

// Kunena 2.0.0: Update some configuration settings
function kunena_upgrade_200_config($parent) {
	$config_old = KunenaFactory::getConfig ();

	if (isset($config->fbsessiontimeout)) {
		$config->set('sessiontimeout', $config->get('fbsessiontimeout', 1800));
		unset($config->fbsessiontimeout);
	}
	if (isset($config->fbdefaultpage)) {
		$config->set('defaultpage', $config->get('fbdefaultpage', 'recent'));
		unset($config->fbdefaultpage);
	}
	$config_old->save();

	return array ('action' => '', 'name' => JText::_ ( 'COM_KUNENA_INSTALL_200_CONFIGURATION_DEPRECATED_SETTINGS' ), 'success' => true );
}
