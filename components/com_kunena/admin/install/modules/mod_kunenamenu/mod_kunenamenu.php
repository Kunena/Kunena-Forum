<?php
/**
 * Kunena Menu Module
 * @package Kunena.Modules
 * @subpackage Menu
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined('_JEXEC') or die;

// Kunena detection and version check
if (!class_exists('KunenaForum') || !KunenaForum::isCompatible('3.0') || !KunenaForum::installed()) {
	return;
}

// Include the class only once
require_once __DIR__ . '/class.php';

/** @var object $params */
$menu = new modKunenaMenu($params);
$menu->display();
