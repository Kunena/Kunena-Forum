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

// Convert legacy urls into new ones
// TODO: move logic into kunena.legacy.php and turn into redirects when ready
$view = JRequest::getWord ( 'view', JRequest::getWord ( 'func') );
$layout = JRequest::getWord ( 'layout', 'default' );
$config = KunenaFactory::getConfig ();
switch ($view) {
	case 'listcat':
		$view = 'categories';
		break;
	case 'showcat':
		$view = 'category';
		break;
	case 'latest':
		$view = 'topics';
		$layout = JRequest::getInt ( 'do', 'latest' );
		// TODO: handle all layouts
		switch ($layout) {
			case 'latest':
				$layout = 'default';
				break;
		}
		$page = JRequest::getInt ( 'page', 0 );
		//if (!$page) break;
		$page = $page < 1 ? 1 : $page;
		$limit = $config->threads_per_page;
		$limitstart = ($page - 1) * $limit;
		JRequest::setVar ( 'limit', $limit );
		JRequest::setVar ( 'limitstart', $limitstart );
		break;
	case 'view':
		$view = 'topic';
		break;
}
JRequest::setVar ( 'view', $view );
JRequest::setVar ( 'layout', $layout );

// Load view
if (is_file(KPATH_SITE."/controllers/{$view}.php")) {

	// Initialize error handler
	kimport('kunena.error');
	$kconfig = KunenaFactory::getConfig ();
	if ($kconfig->debug) {
		KunenaError::initialize();
	}

	// Get and save session for registered users
	$ksession = KunenaFactory::getSession ( true );
	if ($ksession->userid > 0) {
		$user = KunenaFactory::getUser();
		if ($user->posts === null)
			$user->save();
		if (!$ksession->save ())
			JFactory::getApplication()->enqueueMessage ( JText::_('COM_KUNENA_ERROR_SESSION_SAVE_FAILED'), 'error' );
	}

	// Update Who's Online
	require_once (KUNENA_PATH_LIB .DS. 'kunena.who.class.php');
	$who = CKunenaWhoIsOnline::getInstance();
	$who->insertOnlineDatas ();

	// Load and execute controller
	kimport ('kunena.controller');
	$controller = KunenaController::getInstance();
	KunenaRoute::cacheLoad();
	$controller->execute( JRequest::getCmd ( 'task' ) );
	KunenaRoute::cacheStore();
	$controller->redirect();
	return;
}

// ***********************************************************************

require_once (JPATH_COMPONENT . DS . 'kunena.legacy.php');
