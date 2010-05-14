<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die();

function com_install() {
	$app = JFactory::getApplication();
	$app->setUserState('com_kunena.install.step', 0);

	// Redirect to Kunena Installer
	header ( "HTTP/1.1 303 See Other" );
	header ( "Location: ".JURI::base () . "index.php?option=com_kunena&view=install" );
}