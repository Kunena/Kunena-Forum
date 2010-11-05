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

jimport ( 'joomla.version' );
$jversion = new JVersion ();
if ($jversion->RELEASE != '1.5') return;

function com_install() {
	// Redirect to Kunena Installer
	ignore_user_abort ( true );
	header ( "HTTP/1.1 303 See Other" );
	header ( "Location: " . JURI::base () . "index.php?option=com_kunena&view=install" );
	require_once(basename(dirname(__FILE__)).'/install.script.php');
	Com_KunenaInstallerScript::install ( null );
}