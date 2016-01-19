<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Site
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          http://www.kunena.org
 **/
defined('_JEXEC') or die ();

// Display offline message if Kunena hasn't been fully installed.
if (!class_exists('KunenaForum') || !KunenaForum::isCompatible('4.0') || !KunenaForum::installed())
{
	$lang = JFactory::getLanguage();
	$lang->load('com_kunena.install', JPATH_ADMINISTRATOR . '/components/com_kunena', 'en-GB');
	$lang->load('com_kunena.install', JPATH_ADMINISTRATOR . '/components/com_kunena');
	JResponse::setHeader('Status', '503 Service Temporarily Unavailable', true);
	?>
	<h2><?php echo JText::_('COM_KUNENA_INSTALL_OFFLINE_TOPIC') ?></h2>
	<div><?php echo JText::_('COM_KUNENA_INSTALL_OFFLINE_DESC') ?></div>
	<?php
	return;
}

// Display time it took to create the entire page in the footer.
$kunena_profiler = KunenaProfiler::instance('Kunena');
$kunena_profiler->start('Total Time');
KUNENA_PROFILER ? $kunena_profiler->mark('afterLoad') : null;

// Prevent direct access to the component if the option has been disabled.
if (!KunenaConfig::getInstance()->get('access_component', 1))
{
	$active = JFactory::getApplication()->getMenu()->getActive();

	if (!$active)
	{
		// Prevent access without using a menu item.
		JLog::add("Kunena: Direct access denied: " . JUri::getInstance()->toString(array('path', 'query')), JLog::WARNING, 'kunena');
		JError::raiseError(404, JText::_('JLIB_APPLICATION_ERROR_COMPONENT_NOT_FOUND'));
	}
	elseif ($active->type != 'component' || $active->component != 'com_kunena')
	{
		// Prevent spoofed access by using random menu item.
		JLog::add("Kunena: spoofed access denied: " . JUri::getInstance()->toString(array('path', 'query')), JLog::WARNING, 'kunena');
		JError::raiseError(404, JText::_('JLIB_APPLICATION_ERROR_COMPONENT_NOT_FOUND'));
	}
}

// Load router
require_once KPATH_SITE . '/router.php';

// Initialize Kunena Framework.
KunenaForum::setup();

// Initialize custom error handlers.
KunenaError::initialize();

// Initialize session.
$ksession = KunenaFactory::getSession(true);
if ($ksession->userid > 0)
{
	// Create user if it does not exist
	$kuser = KunenaUserHelper::getMyself();
	if (!$kuser->exists())
	{
		$kuser->save();
	}
	// Save session
	if (!$ksession->save())
	{
		JFactory::getApplication()->enqueueMessage(JText::_('COM_KUNENA_ERROR_SESSION_SAVE_FAILED'), 'error');
	}
}

// Support legacy urls (they need to be redirected).
$app   = JFactory::getApplication();
$input = $app->input;
$input->set('limitstart', $input->getInt('limitstart', $input->getInt('start')));
$view    = $input->getWord('func', $input->getWord('view', 'home'));
$subview = $input->getWord('layout', 'default');
$task    = $input->getCmd('task', 'display');

// Import plugins and event listeners.
JPluginHelper::importPlugin('kunena');

// Get HMVC controller and if exists, execute it.
$controller = KunenaControllerApplication::getInstance($view, $subview, $task, $input, $app);
if ($controller)
{
	KunenaRoute::cacheLoad();
	$contents = $controller->execute();
	KunenaRoute::cacheStore();

}
elseif (is_file(KPATH_SITE . "/controllers/{$view}.php"))
{
	// Execute old MVC.
	// Legacy support: If the content layout doesn't exist on HMVC, load and execute the old controller.
	$controller = KunenaController::getInstance();
	KunenaRoute::cacheLoad();
	ob_start();
	$controller->execute($task);
	$contents = ob_get_clean();
	KunenaRoute::cacheStore();
	$controller->redirect();
}
else
{
	// Legacy URL support.
	$uri = KunenaRoute::current(true);
	if ($uri)
	{
		// FIXME: using wrong Itemid
		JFactory::getApplication()->redirect(KunenaRoute::_($uri, false));
	}
	else
	{
		return JError::raiseError(404, "Kunena view '{$view}' not found");
	}
}

// Prepare and display the output.
$dispatcher = JDispatcher::getInstance();
$dispatcher->trigger('onKunenaBeforeRender', array("com_kunena.{$view}", &$contents));
$contents = (string) $contents;
$dispatcher->trigger('onKunenaAfterRender', array("com_kunena.{$view}", &$contents));
echo $contents;

// Remove custom error handlers.
KunenaError::cleanup();

// Display profiler information.
$kunena_time = $kunena_profiler->stop('Total Time');
if (KUNENA_PROFILER)
{
	echo '<div class="kprofiler">';
	echo "<h3>Kunena Profile Information</h3>";
	foreach ($kunena_profiler->getAll() as $item)
	{
		//if ($item->getTotalTime()<($kunena_time->getTotalTime()/20)) continue;
		//if ($item->getTotalTime()<0.002 && $item->calls < 20) continue;
		echo sprintf("Kunena %s: %0.3f / %0.3f seconds (%d calls)<br/>", $item->name, $item->getInternalTime(),
			$item->getTotalTime(), $item->calls);
	}
	echo '</div>';
}
