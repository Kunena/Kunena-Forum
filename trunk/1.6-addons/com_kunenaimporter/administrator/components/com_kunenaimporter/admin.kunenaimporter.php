<?php
/**
 * Joomla! 1.5 component: Kunena Forum Importer
 *
 * @version $Id$
 * @author Kunena Team
 * @package Joomla
 * @subpackage Kunena Forum Importer
 * @license GNU/GPL
 *
 * Imports forum data into Kunena
 *
 * @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

set_time_limit(120);

/*
 * Define constants for all pages
 */
define( 'COM_KUNENAIMPORTER_BASEDIR', JPATH_COMPONENT_ADMINISTRATOR );
define( 'COM_KUNENAIMPORTER_BASEURL', JURI::root().'administrator/index.php?option=com_kunenaimporter');

$document = JFactory::getDocument();
$document->addStyleSheet('components/com_kunenaimporter/assets/importer.css');

// Require the base controller
require_once JPATH_COMPONENT.DS.'controller.php';

// Require the base controller
require_once JPATH_COMPONENT.DS.'helpers'.DS.'helper.php';

$lang = JFactory::getLanguage();
$lang->load('com_kunenaimporter', COM_KUNENAIMPORTER_BASEDIR);

$document->setTitle( JText::_('Kunena Forum Importer') );
JToolBarHelper::title(JText::_('Forum Importer'), 'kunenaimporter.png');

// Initialize the controller
$controller = new KunenaimporterController( );

// Perform the Request task
$controller->execute( JRequest::getCmd('task'));
$controller->redirect();

function getKunenaImporterParams($component="com_kunenaimporter")
{
	static $instance = null;
	if ($instance == null)
	{
		$table =& JTable::getInstance('component');
		$table->loadByOption( $component );

		// work out file path
		$option	= preg_replace( '#\W#', '', $table->option );
		$path	= JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'config.xml';
		if (file_exists( $path )) {
			$instance = new JParameter( $table->params, $path );
		} else {
			$instance = new JParameter( $table->params );
		}
	}
	return $instance;
}
?>
