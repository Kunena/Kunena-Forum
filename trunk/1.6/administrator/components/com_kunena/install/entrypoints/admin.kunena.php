<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/

defined( '_JEXEC' ) or die();

require_once JPATH_ADMINISTRATOR . '/components/com_kunena/api.php';

JToolBarHelper::title('&nbsp;', 'kunena.png');

$view = JRequest::getCmd ( 'view' );
$task = JRequest::getCmd ( 'task' );

require_once (KPATH_ADMIN.'/install/controller.php');
$controller = new KunenaControllerInstall();
$controller->execute( $task );
$controller->redirect();