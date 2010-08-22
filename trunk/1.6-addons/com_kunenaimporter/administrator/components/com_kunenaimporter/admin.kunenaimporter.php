<?php
/**
 * @version $Id$
 * Kunena Forum Importer Component
 * @package com_kunenaimporter
 *
 * Imports forum data into Kunena
 *
 * @Copyright (C) 2009 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 */
defined ( '_JEXEC' ) or die ();

set_time_limit ( 120 );

/*
 * Define constants for all pages
 */
define ( 'COM_KUNENAIMPORTER_BASEDIR', JPATH_COMPONENT_ADMINISTRATOR );
define ( 'COM_KUNENAIMPORTER_BASEURL', JURI::root () . 'administrator/index.php?option=com_kunenaimporter' );

$document = JFactory::getDocument ();
$document->addStyleSheet ( 'components/com_kunenaimporter/assets/importer.css' );

// Require the base controller
require_once JPATH_COMPONENT . DS . 'controller.php';

$lang = JFactory::getLanguage ();
$lang->load ( 'com_kunenaimporter', COM_KUNENAIMPORTER_BASEDIR );

$document->setTitle ( JText::_ ( 'Kunena Forum Importer' ) );
JToolBarHelper::title ( JText::_ ( 'Forum Importer' ), 'kunenaimporter.png' );

// Initialize the controller
$controller = new KunenaImporterController ();

// Perform the Request task
$controller->execute ( JRequest::getCmd ( 'task' ) );
$controller->redirect ();

function getKunenaImporterParams($component = "com_kunenaimporter") {
	static $instance = null;
	if ($instance == null) {
		$table = JTable::getInstance ( 'component' );
		$table->loadByOption ( $component );

		// work out file path
		$option = preg_replace ( '#\W#', '', $table->option );
		$path = JPATH_ADMINISTRATOR . DS . 'components' . DS . $option . DS . 'config.xml';
		if (file_exists ( $path )) {
			$instance = new JParameter ( $table->params, $path );
		} else {
			$instance = new JParameter ( $table->params );
		}
	}
	return $instance;
}
?>
