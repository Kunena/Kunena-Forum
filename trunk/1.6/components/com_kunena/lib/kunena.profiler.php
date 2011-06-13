<?php
/**
 * @version $Id$
 * Kunena Component - CKunenaAjaxHelper class
 * @package Kunena
 *
 * @Copyright (C) 2008-2011 www.kunena.org All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();

jimport ( 'joomla.version' );
$jversion = new JVersion ();
if ($jversion->RELEASE == 1.6) {
	require_once (KUNENA_PATH_LIB . '/kunena.profiler.1.6.php');
} else {
	require_once (KUNENA_PATH_LIB . '/kunena.profiler.1.5.php');
}

?>
