<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 * @Copyright (C) 2011 Kunena All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/

defined( '_JEXEC' ) or die();

jimport ( 'joomla.version' );
$jversion = new JVersion ();
if ($jversion->RELEASE == 1.6) {
	require_once (KUNENA_PATH_LIB . '/kunena.file.class.1.6.php');
} else {
	require_once (KUNENA_PATH_LIB . '/kunena.file.class.1.5.php');
}
?>
