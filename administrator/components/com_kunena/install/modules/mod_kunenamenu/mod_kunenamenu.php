<?php
/**
 * Kunena Menu Module
 * @package Kunena.Modules
 * @subpackage Menu
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined('_JEXEC') or die;

// Kunena detection and version check
$minKunenaVersion = '2.0';
if (!class_exists('KunenaForum') || !KunenaForum::installed() || !KunenaForum::isCompatible($minKunenaVersion)) {
	return;
}

// Include the class only once
require_once dirname ( __FILE__ ) . '/class.php';

$params = (object) $params;
$menu = new modKunenaMenu($params);
$menu->display();
