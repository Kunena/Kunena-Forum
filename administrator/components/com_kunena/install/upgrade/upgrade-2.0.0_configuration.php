<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

kimport ('kunena.factory');

// Kunena 2.0.0: Convert deprecated configuration options
function kunena_upgrade_200_configuration($parent) {
	$config = KunenaFactory::getConfig ();

	if ($config->allowimageupload >= 0) {
		$config->image_upload = 'nobody';
		if ($config->allowimageregupload == 1) {
			$config->image_upload = 'registered';
		}
		if ($config->allowimageupload == 1) {
			$config->image_upload = 'everybody';
		}
		$config->allowimageupload = $config->allowimageregupload = -1;
	}

	if ($config->allowfileupload >= 0) {
		$config->file_upload = 'nobody';
		if ($config->allowfileregupload == 1) {
			$config->file_upload = 'registered';
		}
		if ($config->allowfileupload == 1) {
			$config->file_upload = 'everybody';
		}
		$config->allowfileupload = $config->allowfileregupload = -1;
	}

	// Save configuration
	$config->remove ();
	$config->create ();

	return array ('action' => '', 'name' => JText::_ ( 'COM_KUNENA_INSTALL_200_CONFIGURATION' ), 'success' => true );
}