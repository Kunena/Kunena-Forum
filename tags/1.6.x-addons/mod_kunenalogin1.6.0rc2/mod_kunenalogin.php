<?php
/**
 * @version $Id$
 * Kunenalogin Module
 * @package Kunena login
 *
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */
defined ( '_JEXEC' ) or die ();

// Kunena detection and version check
$minKunenaVersion = '1.6.0-RC2';
if (! class_exists ( 'Kunena' ) || Kunena::versionBuild () < 3296) {
	echo JText::sprintf ( 'MOD_KUNENALOGIN_KUNENA_NOT_INSTALLED', $minKunenaVersion );
	return;
}
// Kunena online check
if (! Kunena::enabled ()) {
	echo JText::_ ( 'MOD_KUNENALOGIN_KUNENA_OFFLINE' );
	return;
}

require_once (dirname ( __FILE__ ) . DS . 'class.php');

$params = ( object ) $params;
$modKunenaLogin = new ModKunenaLogin ( $params );
$modKunenaLogin->display();
