<?php
/**
 * @version $Id$
 * KunenaLatest Module
 * @package Kunena Latest
 *
* @Copyright (C)2010-2011 www.kunena.org. All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
 **/

// no direct access
defined ( '_JEXEC' ) or die ( '' );

// Kunena detection and version check
$minKunenaVersion = '1.6.2';
if (!class_exists('Kunena') || Kunena::versionBuild() < 3892) {
	echo JText::sprintf ( 'MOD_KUNENALATEST_KUNENA_NOT_INSTALLED', $minKunenaVersion );
	return;
}
// Kunena online check
if (!Kunena::enabled()) {
	echo JText::_ ( 'MOD_KUNENALATEST_KUNENA_OFFLINE' );
	return;
}

// Include the kunenalatest functions only once
require_once (dirname ( __FILE__ ) . '/class.php');

$params = ( object ) $params;
$klatest = new modKunenaLatest ( $params );

