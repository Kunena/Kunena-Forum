<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	(C) 2008 Kunena. All rights reserved, see COPYRIGHT.php
 * @license		GNU General Public License, see LICENSE.php
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die('Invalid Request.');

require_once(JPATH_COMPONENT.DS.'version.php');
require_once(JPATH_COMPONENT.DS.'controller.php');

// Execute the task.
$controller	= &KunenaController::getInstance();
$controller->execute(JRequest::getVar('task'));
$controller->redirect();
