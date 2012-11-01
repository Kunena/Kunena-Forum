<?php
/**
 * Kunena Component
 * @package Kunena.Site
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

// Initialize Kunena (if Kunena System Plugin isn't enabled)
$api = JPATH_ADMINISTRATOR . '/components/com_kunena/api.php';
if (file_exists($api)) require_once $api;

// Display offline message if Kunena hasn't been fully installed
if (!class_exists('KunenaForum') || !KunenaForum::isCompatible('2.0') || !KunenaForum::installed()) {
	$lang = JFactory::getLanguage();
	$lang->load('com_kunena.install', JPATH_ADMINISTRATOR . '/components/com_kunena', 'en-GB');
	$lang->load('com_kunena.install', JPATH_ADMINISTRATOR . '/components/com_kunena');
	?>
	<h2><?php echo JText::_('COM_KUNENA_INSTALL_OFFLINE_TOPIC')?></h2>
	<div><?php echo JText::_('COM_KUNENA_INSTALL_OFFLINE_DESC')?></div>
<?php
	return;
}

// Load router
require_once KPATH_SITE . '/router.php';

// Display time it took to create the entire page in the footer
$kunena_profiler = KunenaProfiler::instance('Kunena');
$kunena_profiler->start('Total Time');
KUNENA_PROFILER ? $kunena_profiler->mark('afterLoad') : null;

KunenaFactory::loadLanguage('com_kunena.controllers');
KunenaFactory::loadLanguage('com_kunena.models');
KunenaFactory::loadLanguage('com_kunena.views');
KunenaFactory::loadLanguage('com_kunena.templates');
KunenaFactory::loadLanguage('com_kunena.sys', 'admin');
// Load last to get deprecated language files to work
KunenaFactory::loadLanguage('com_kunena');
KunenaForum::setup();

// Initialize error handlers
KunenaError::initialize ();

// Initialize session
$ksession = KunenaFactory::getSession ( true );
if ($ksession->userid > 0) {
	// Create user if it does not exist
	$kuser = KunenaUserHelper::getMyself ();
	if (! $kuser->exists ()) {
		$kuser->save ();
	}
	// Save session
	if (! $ksession->save ()) {
		JFactory::getApplication ()->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_SESSION_SAVE_FAILED' ), 'error' );
	}
}

// Support legacy urls (they need to be redirected)
$view = JRequest::getWord ( 'func', JRequest::getWord ( 'view', 'home' ) );
$task = JRequest::getCmd ( 'task' );

if (is_file ( KPATH_SITE . "/controllers/{$view}.php" )) {
	// Load and execute controller
	$controller = KunenaController::getInstance ();
	KunenaRoute::cacheLoad ();
	$controller->execute ( $task );
	KunenaRoute::cacheStore ();
	$controller->redirect ();
} else {
	// Legacy support
	$uri = KunenaRoute::current(true);
	if ($uri) {
		// FIXME: using wrong Itemid
		JFactory::getApplication ()->redirect (KunenaRoute::_($uri, false));
	} else {
		return JError::raiseError( 404, "Kunena view '{$view}' not found" );
	}
}

KunenaError::cleanup ();

// Display profiler information
$kunena_time = $kunena_profiler->stop('Total Time');
if (KUNENA_PROFILER) {
	echo '<div class="kprofiler">';
	echo "<h3>Kunena Profile Information</h3>";
	foreach($kunena_profiler->getAll() as $item) {
		//if ($item->getTotalTime()<($kunena_time->getTotalTime()/20)) continue;
		if ($item->getTotalTime()<0.002 && $item->calls < 20) continue;
		echo sprintf ("Kunena %s: %0.3f / %0.3f seconds (%d calls)<br/>", $item->name, $item->getInternalTime(), $item->getTotalTime(), $item->calls);
	}
	echo '</div>';
}