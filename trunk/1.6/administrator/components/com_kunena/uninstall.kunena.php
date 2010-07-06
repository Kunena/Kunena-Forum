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

function com_uninstall() {
	$jversion = new JVersion ();
	if ($jversion->RELEASE != '1.5') return;
	include_once(dirname(__FILE__).'/install.script.php');
	Com_KunenaInstallerScript::uninstall ( null );
}