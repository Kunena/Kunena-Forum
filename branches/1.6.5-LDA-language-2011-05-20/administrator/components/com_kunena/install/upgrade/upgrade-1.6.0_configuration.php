<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 * Kunena 1.6.0: Convert deprecated configuration options
 * component: com_kunena
 **/

defined ( '_JEXEC' ) or die ();

kimport ('factory');

function kunena_upgrade_160_configuration($parent) {
	$config = KunenaFactory::getConfig ();

	// Switch to default template
	$config->template = 'default';

	// Keep integration settings
	$integration = array ('jomsocial' => 'jomsocial', 'cb' => 'communitybuilder', 'uddeim' => 'uddeim', 'aup' => 'alphauserpoints', 'none' => 'none' );
	if (! $config->allowavatar) {
		$config->allowavatar = 1;
		$config->integration_avatar = 'none';
	} else if ($config->avatar_src) {
		if (isset ( $integration [$config->avatar_src] )) {
			$config->integration_avatar = $integration [$config->avatar_src];
		} else {
			$config->integration_avatar = 'kunena';
		}
		$config->avatar_src = '';
	}
	if ($config->fb_profile) {
		if (isset ( $integration [$config->fb_profile] )) {
			$config->integration_access = $integration [$config->fb_profile];
			$config->integration_login = $integration [$config->fb_profile];
			$config->integration_profile = $integration [$config->fb_profile];
			$config->integration_activity = $integration [$config->fb_profile];
		} else {
			$config->integration_access = 'joomla';
			$config->integration_login = 'joomla';
			$config->integration_profile = 'kunena';
			$config->integration_activity = 'none';
		}
		$config->fb_profile = '';
	}
	if ($config->js_actstr_integration) {
		$config->integration_activity = 'jomsocial';
		$config->js_actstr_integration = 0;
	} else if ($config->integration_activity == 'jomsocial') {
		$config->integration_activity = 'none';
	}
	if ($config->pm_component) {
		if (isset ( $integration [$config->pm_component] )) {
			$config->integration_private = $integration [$config->pm_component];
		} else {
			$config->integration_private = 'none';
		}
		$config->pm_component = '';
	}

	// Save configuration
	$config->remove ();
	$config->create ();

	return array ('action' => '', 'name' => JText::_ ( 'COM_KUNENA_INSTALL_160_CONFIGURATION' ), 'success' => true );
}