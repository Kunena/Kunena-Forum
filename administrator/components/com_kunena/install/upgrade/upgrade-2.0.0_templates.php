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

// Kunena 2.0.0: Convert deprecated configuration templates options
function kunena_upgrade_200_templates($parent) {
	$config = KunenaFactory::getConfig ();
	jimport( 'joomla.filesystem.file' );
	jimport( 'joomla.filesystem.folder' );

	if ($config->template == 'default' ) {
		$config->template = 'blue_eagle';

		// Save configuration
		$config->save ();

		if ( JFile::exists(JPATH_ROOT.'/components/com_kunena/template/default/params.ini') ) {
			JFile::copy(JPATH_ROOT.'/components/com_kunena/template/default/params.ini', JPATH_ROOT.'/components/com_kunena/template/blue_eagle/params.ini');
		}

		if ( JFolder::exists(JPATH_ROOT.'/components/com_kunena/template/default') ) {
			JFolder::delete(JPATH_ROOT.'/components/com_kunena/template/default');
		}
	}

	return array ('action' => '', 'name' => JText::_ ( 'COM_KUNENA_INSTALL_200_TEMPLATES' ), 'success' => true );
}