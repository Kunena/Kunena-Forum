<?php
/**
* @version $Id: kunena.php 938 2009-07-23 23:39:48Z mahagr $
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

$view = JRequest::getCmd('view', false);

// Import the languages.
$language =& JFactory::getLanguage();
$language->load('com_kunena');

// Import the Kunena library loader.
require_once (JPATH_COMPONENT.'/libraries/import.php');

// Import the Kunena controller class.
require_once (JPATH_COMPONENT.'/controller.php');

kimport('factory');

// Execute the task.
$controller	= KunenaController::getInstance();
$controller->execute(JRequest::getVar('task'));
$controller->redirect();
