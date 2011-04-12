<?php
/**
 * @version $Id$
 * Kunena Translate Component
 * 
 * @package	Kunena Translate
 * @Copyright (C) 2010-2011 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */


// no direct access
defined('_JEXEC') or die('Restricted access');

JHTML::stylesheet('kunenatranslate.css', 'administrator'.DS.'components'.DS.'com_kunenatranslate'.DS.'assets'.DS.'css'.DS);

require_once (dirname(__FILE__).DS.'controller.php');

//Submenu
$view = JRequest::getVar('view');
switch ($view){
	case 'extension':
		JSubMenuHelper::addEntry(JText::_('Kunena Translate'), 'index.php?option=com_kunenatranslate');
		JSubMenuHelper::addEntry(JText::_('Extension Manager'), 'index.php?option=com_kunenatranslate&view=extension', true);
		JSubMenuHelper::addEntry(JText::_('COM_KUNENATRANSLATE_IMPORT'), 'index.php?option=com_kunenatranslate&controller=import&task=display&view=import&show=import');
		JSubMenuHelper::addEntry(JText::_('COM_KUNENATRANSLATE_EXPORT'), 'index.php?option=com_kunenatranslate&controller=import&task=display&view=import&show=export');
		break;
	case 'import':
		break;
	default:
		JSubMenuHelper::addEntry(JText::_('Kunena Translate'), 'index.php?option=com_kunenatranslate', true);
		JSubMenuHelper::addEntry(JText::_('Extension Manager'), 'index.php?option=com_kunenatranslate&view=extension');
		JSubMenuHelper::addEntry(JText::_('COM_KUNENATRANSLATE_IMPORT'), 'index.php?option=com_kunenatranslate&controller=import&task=display&view=import&show=import');
		JSubMenuHelper::addEntry(JText::_('COM_KUNENATRANSLATE_EXPORT'), 'index.php?option=com_kunenatranslate&controller=import&task=display&view=import&show=export');
}
// Require specific controller if requested
$controller = JRequest::getWord('controller');

if($controller) {
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if (file_exists($path)) {
		require_once $path;
	} else {
		$controller = '';
	}
}

// Create the controller
$classname	= 'KunenaTranslateController'.$controller;
$controller	= new $classname( );

$task = JRequest::getCmd('task', null);
// Perform the Request task
$controller->execute($task);
// Redirect if set by the controller
$controller->redirect();
