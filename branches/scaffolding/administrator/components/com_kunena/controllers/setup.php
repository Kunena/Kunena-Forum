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
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.0
 */
class KunenaControllerSetup extends JController
{
	/**
	 * Method to enable the JXtended Libraries plugin.
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function enablePlugin()
	{
		JRequest::checkToken('request') or jexit(JText::_('KUNENA_INVALID_TOKEN'));

		// Get the setup model.
		$model = &$this->getModel('Setup', 'KunenaModel');

		// Attempt to enable the JXtended Libraries plugin.
		$result	= $model->enableLibraries();

		// Check for installation routine errors.
		if (!$result) {
			$this->setMessage(JText::sprintf('KUNENA_ENABLE_PLUGIN_FAILED', $model->getError()), 'error');
		}

		// Set the redirect.
		$this->setRedirect('index.php?option=com_kunena');
	}

	/**
	 * Method to manually install Kunena.
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function install()
	{
		// Get the setup model.
		$model = &$this->getModel('Setup', 'KunenaModel');

		// Attempt to run the manual install routine.
		$result	= $model->install();

		// Check for installation routine errors.
		if (!$result) {
			$this->setMessage(JText::sprintf('KUNENA_MANUAL_INSTALL_FAILED', $model->getError()), 'notice');
		}
		else {
			$this->setMessage(JText::_('KUNENA_MANUAL_INSTALL_SUCCESS'));
		}

		// Set the redirect.
		$this->setRedirect('index.php?option=com_kunena');
	}

	/**
	 * Method to process any available database upgrades.
	 *
	 * @access	public
	 * @return	void
	 * @since	1.0
	 */
	function upgrade()
	{
		JRequest::checkToken('request') or jexit(JText::_('KUNENA_INVALID_TOKEN'));

		// Get the setup model.
		$model = &$this->getModel('Setup', 'KunenaModel');

		// Attempt to run the upgrade routine.
		$result	= $model->upgrade();

		// Check for upgrade routine errors.
		if (!$result) {
			$this->setMessage(JText::sprintf('KUNENA_DATABASE_UPGRADE_FAILED', $model->getError()), 'notice');
		}
		else {
			$this->setMessage(JText::_('KUNENA_DATABASE_UPGRADE_SUCCESS'));
		}

		// Set the redirect.
		$this->setRedirect('index.php?option=com_kunena');
	}
}