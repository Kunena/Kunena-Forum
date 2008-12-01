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

jimport('joomla.application.component.controller');

/**
 * The Kunena Setup Controller
 *  - JSON Protocol
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.0
 */
class KunenaControllerSetup extends JController
{
	/**
	 * Dummy method to disable the display method.
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function display()
	{
		return null;
	}

	/**
	 * Method to enable the JXtended Libraries plugin.
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function enableLibraries()
	{
		JRequest::checkToken('request') or jexit(JText::_('KUNENA_INVALID_TOKEN'));

		// Get the application object.
		$app = &JFactory::getApplication();

		// Get the setup model.
		$model = &$this->getModel('Setup', 'KunenaModel');

		// Attempt to enable the JXtended Libraries plugin.
		$result	= $model->enableLibraries();

		// Check for installation routine errors.
		if (!$result) {
			// An error was encountered while enabling the plugin.
			JError::raiseError(500, JText::sprintf('KUNENA_ENABLE_PLUGIN_FAILED', $model->getError()));
			return false;
		} else {
			// Plugin enabled successfully.
			echo '{"error":false}';
		}

		$app->close();
	}

	/**
	 * Method to display a JSON error object
	 *
	 * @access	private
	 * @param	object	$e	JException object
	 * @return	void
	 * @since	1.0
	 */
	function handleError($e)
	{
		// Send a 500 error code response.
		JResponse::setHeader('status', $e->getCode());
		JResponse::sendHeaders();

		// Convert the exception to JSON.
		echo '{"error":true,"message":"'.$e->getMessage().'"}';

		$app = &JFactory::getApplication();
		$app->close();
	}
}

// This needs to be AFTER the class declaration because of PHP 5.1.
JError::setErrorHandling(E_ALL, 'callback', array('KunenaControllerSetup', 'handleError'));