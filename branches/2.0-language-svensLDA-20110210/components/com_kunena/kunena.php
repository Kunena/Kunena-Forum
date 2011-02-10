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

// Support legacy urls (they need to be redirected)
$view = JRequest::getWord ( 'func', JRequest::getWord ( 'view' ) );

// TODO: Move toggler support
$document = JFactory::getDocument();
$document->addScriptDeclaration('// <![CDATA[
var kunena_toggler_close = "'.JText::_('COM_KUNENA_TOGGLER_COLLAPSE').'";
var kunena_toggler_open = "'.JText::_('COM_KUNENA_TOGGLER_EXPAND').'";
// ]]>');

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
