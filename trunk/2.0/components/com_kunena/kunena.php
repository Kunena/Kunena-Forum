<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined( '_JEXEC' ) or die();

require_once (JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_kunena' . DS . 'api.php');

$view = JRequest::getWord ( 'view' );
if (is_file(KPATH_SITE."/controllers/{$view}.php")) {
	require_once (KPATH_ADMIN . '/controllers/controller.php');
	$controller = KunenaController::getInstance();
	KunenaRoute::cacheLoad();
	$controller->execute( JRequest::getCmd ( 'task' ) );
	KunenaRoute::cacheStore();
	$controller->redirect();
	return;
}

// ***********************************************************************

require_once (JPATH_COMPONENT . DS . 'kunena.legacy.php');
