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
defined ( '_JEXEC' ) or die ();

// Initialize Kunena (if Kunena System Plugin isn't enabled)
require_once (JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_kunena' . DS . 'api.php');

// Make sure that language gets loaded also if we are using SVN
if (KunenaForum::isSVN()) {
	$lang = JFactory::getLanguage();
	$lang->load('com_kunena',KPATH_SITE);
}

$view = JRequest::getWord ( 'view' );

// Load view if it exists
if (is_file ( KPATH_SITE . "/controllers/{$view}.php" )) {

	// Initialize error handler
	kimport ( 'kunena.error' );
	$kconfig = KunenaFactory::getConfig ();
	if ($kconfig->debug) {
		KunenaError::initialize ();
	}

	// Get session and save it if user is not a guest
	$ksession = KunenaFactory::getSession ( true );
	if ($ksession->userid > 0) {
		$user = KunenaFactory::getUser ();
		if ($user->posts === null)
			$user->save ();
		if (! $ksession->save ())
			JFactory::getApplication ()->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_SESSION_SAVE_FAILED' ), 'error' );
	}

	// Update Who's Online
	require_once (KUNENA_PATH_LIB . DS . 'kunena.who.class.php');
	$who = CKunenaWhoIsOnline::getInstance ();
	$who->insertOnlineDatas ();

	// Load and execute controller
	kimport ( 'kunena.controller' );
	$controller = KunenaController::getInstance ();
	KunenaRoute::cacheLoad ();
	$controller->execute ( JRequest::getCmd ( 'task' ) );
	KunenaRoute::cacheStore ();
	$controller->redirect ();
	return;
}

// ***********************************************************************

require_once (JPATH_COMPONENT . DS . 'kunena.legacy.php');
