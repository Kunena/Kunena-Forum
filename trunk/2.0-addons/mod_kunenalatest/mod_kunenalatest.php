<?php
/**
 * @version $Id: mod_kunenalatest.php 4209 2011-01-16 15:01:13Z xillibit $
 * KunenaLatest Module
 * @package Kunena Latest
 *
* @Copyright (C) 2010-2011 www.kunena.org. All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ( '' );

// Kunena detection and version check
$minKunenaVersion = '2.0.0-DEV';
$minKunenaBuild = 4682;
if (!class_exists('KunenaForum') || !KunenaForum::isCompatible($minKunenaVersion, $minKunenaBuild)) {
	echo JText::sprintf ( 'MOD_KUNENALATEST_KUNENA_NOT_INSTALLED', $minKunenaVersion );
	return;
}
// Kunena online check
if (!KunenaForum::enabled()) {
	echo JText::_ ( 'MOD_KUNENALATEST_KUNENA_OFFLINE' );
	return;
}

// Include the kunenalatest functions only once
require_once dirname ( __FILE__ ) . '/class.php';

$params = ( object ) $params;
$klatest = new modKunenaLatest ( $params );
$klatest->display();