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
$task	= JRequest::getCmd( 'task' , 'display' );

// Import the Kunena loader and defines.
require_once (JPATH_COMPONENT_ADMINISTRATOR .DS. 'api.php');

$document =& JFactory::getDocument();
$document->addStyleSheet(KPATH_COMPONENT_RELATIVE .DS. 'media' .DS. 'css'. DS. 'administrator.css');

// Import the Kunena controller class.
require_once (JPATH_COMPONENT .DS. 'controllers' .DS. 'controller.php');

// We treat the view as the controller. Load other controller if there is any.
$controller	= JString::strtolower( JRequest::getWord( 'view' , 'kunena' ) );
$path		= JPATH_COMPONENT_ADMINISTRATOR . DS . 'controllers' . DS . $controller . '.php';

// Test if the controller really exists
if( file_exists( $path ) )
{
	require_once( $path );
}
else
{
	JError::raiseError( 500 , JText::_( 'Invalid Controller: <strong>'.$controller.'</strong> '.$path.' File does not exists.' ) );
}

$class	= 'KunenaController' . JString::ucfirst( $controller );

// Test if the object really exists in the current context
if( class_exists( $class ) )
{
	$controller	= new $class();
}
else
{
	// Throw some errors if the system is unable to locate the object's existance
	JError::raiseError( 500 , JText::_( 'Invalid Controller Object.' ) );
}

// Task's are methods of the controller. Perform the Request task
$controller->execute( $task );

// Redirect if set by the controller
$controller->redirect();



