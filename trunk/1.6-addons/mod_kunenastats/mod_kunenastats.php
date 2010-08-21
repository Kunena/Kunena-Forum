<?php
/**
 * @version $Id$
 * KunenaStats Module
 * @package Kunena Stats
 *
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */
defined ( '_JEXEC' ) or die ();

// Kunena detection and version check
$minKunenaVersion = '1.6.0-RC2';
if (! class_exists ( 'Kunena' ) || ! class_exists ( 'KunenaStatsAPI' ) || Kunena::versionBuild () < 3251) {
	echo JText::sprintf ( 'MOD_KUNENASTATS_KUNENA_NOT_INSTALLED', $minKunenaVersion );
	return;
}
// Kunena online check
if (! Kunena::enabled ()) {
	echo JText::_ ( 'MOD_KUNENASTATS_KUNENA_OFFLINE' );
	return;
}
require_once dirname ( __FILE__ ) . '/class.php';

$params = ( object ) $params;
$kstats = new ModuleKunenaStats ( $params );
$kstats->display ();