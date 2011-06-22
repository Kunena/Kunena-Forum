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
$minKunenaVersion = '1.6.3';
if (!class_exists('Kunena') || Kunena::versionBuild() < 4344) {
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

// Add basic caching for visitors (3 minutes)
$klatest = new modKunenaLatest ( $params );
$user = JFactory::getUser();
if (!$user->id) {
	$cache = JFactory::getCache('mod_kunenalatest', 'output');
	$cache->setLifeTime(180);
	if ($cache->start(md5(serialize($params)), 'mod_kunenalatest')) return;
}
$klatest->display();
if (!$user->id) {
	$cache->end();
}