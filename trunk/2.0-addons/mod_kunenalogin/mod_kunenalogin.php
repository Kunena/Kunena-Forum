<?php
/**
 * @version $Id: mod_kunenalogin.php 4088 2010-12-25 06:53:29Z fxstein $
 * Kunena Login Module
 * @package Kunena login
 *
 * @Copyright (C) 2010-2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 */
defined ( '_JEXEC' ) or die ();

// Kunena detection and version check
$minKunenaVersion = '2.0.0-DEV';
$minKunenaBuild = 2318;
if (!class_exists('KunenaForum') && !KunenaForum::isCompatible($minKunenaVersion, $minKunenaBuild)) {
	echo JText::sprintf ( 'MOD_KUNENALOGIN_KUNENA_NOT_INSTALLED', $minKunenaVersion );
	return;
}
// Kunena online check
if (!KunenaForum::enabled()) {
	echo JText::_ ( 'MOD_KUNENALOGIN_KUNENA_OFFLINE' );
	return;
}

require_once dirname ( __FILE__ ) . '/class.php';

$params = ( object ) $params;
$modKunenaLogin = new ModKunenaLogin ( $params );
$modKunenaLogin->display();