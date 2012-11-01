<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Lib
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/

defined( '_JEXEC' ) or die();

if (version_compare(JVERSION, '1.6','>')) {
	// Joomla 1.6+
	require_once (KPATH_SITE.'/lib/kunena.file.class.1.6.php');
} else {
	// Joomla 1.5
	require_once (KPATH_SITE.'/lib/kunena.file.class.1.5.php');
}
?>
