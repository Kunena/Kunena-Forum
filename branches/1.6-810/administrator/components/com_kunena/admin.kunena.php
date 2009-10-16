<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined( '_JEXEC' ) or die('Restricted access');

// Allow for the new MVC subsystem where applicable.
$view = JRequest::getCmd('view', false);
$task = JRequest::getVar('task');

$legacy = JPATH_COMPONENT_ADMINISTRATOR .DS. 'legacy.admin.kunena.php';

if (!is_file($legacy) || $view || strpos($task, '.'))
{
	// Import the Kunena loader and defines.
	require_once (JPATH_COMPONENT_ADMINISTRATOR .DS. 'api.php');

	$document =& JFactory::getDocument();
	$document->addStyleSheet(KPATH_COMPONENT_RELATIVE .DS. 'media' .DS. 'css'. DS. 'administrator.css');

	// Import the Kunena controller class.
	require_once (JPATH_COMPONENT .DS. 'controller.php');

	// Execute the task.
	$controller	= KunenaController::getInstance();
	$controller->execute(JRequest::getVar('task'));
	$controller->redirect();
	return;
}

require_once($legacy);

?>
