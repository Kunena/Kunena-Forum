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
defined ( '_JEXEC' ) or die ();

// Initialize Kunena (if Kunena System Plugin isn't enabled)
require_once JPATH_ADMINISTRATOR . '/components/com_kunena/api.php';

// Initialize error handlers
kimport ( 'kunena.error' );
KunenaError::initialize ();

// Initialize session
$ksession = KunenaFactory::getSession ( true );
if ($ksession->userid > 0) {
	// Create user if it does not exist
	$kuser = KunenaFactory::getUser ();
	if (! $kuser->exists ()) {
		$kuser->save ();
	}
	// Save session
	if (! $ksession->save ()) {
		JFactory::getApplication ()->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_SESSION_SAVE_FAILED' ), 'error' );
	}
}

// Support legacy urls (they need to be redirected)
$view = JRequest::getWord ( 'func', JRequest::getWord ( 'view' ) );
$task = JRequest::getCmd ( 'task' );

if (is_file ( KPATH_SITE . "/controllers/{$view}.php" )) {
	// Load and execute controller
	kimport ( 'kunena.controller' );
	$controller = KunenaController::getInstance ();
	KunenaRoute::cacheLoad ();
	$controller->execute ( $task );
	KunenaRoute::cacheStore ();
	$controller->redirect ();
	return;
} else {
	// Legacy support
	kimport ('kunena.route.legacy');
	$uri = KunenaRoute::current(true);
	if (KunenaRouteLegacy::convert($uri)) {
		// FIXME: using wrong Itemid
		JFactory::getApplication ()->redirect (KunenaRoute::_($uri, false));
	} else {
		return JError::raiseError( 404, "Kunena legacy function '{$view}' not found" );
	}
}