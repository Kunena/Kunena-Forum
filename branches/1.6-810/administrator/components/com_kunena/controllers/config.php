<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * The Kunena Configuration Controller
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaControllerConfig extends JController
{
	/**
	 * Method to import the configuration via string or upload.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function import()
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('K_Invalid_Token'));

		// Get the configuration values from the Request.
		$string = JRequest::getVar('configString', '', 'post', 'string', JREQUEST_ALLOWHTML);
		$file	= JRequest::getVar('configFile', array(), 'files', 'array');
		$return	= null;

		// Handle the possible import methods.
		if (!empty($file) && ($file['error'] == 0) && ($file['size'] > 0) && (is_readable($file['tmp_name'])))
		{
			// Handle import via uploaded file.
			$string = implode("\n", file($file['tmp_name']));
			$model	= $this->getModel('Config');
			$return	= $model->import($string);
		}
		elseif (strlen($string) > 1)
		{
			// Handle import via pasted string.
			$model	= $this->getModel('Config');
			$return	= $model->import($string);
		}

		// Handle the response.
		if ($return === false)
		{
			$message = JText::sprintf('K_Config_Import_Failed', $model->getError());
			$this->setRedirect('index.php?option=com_kunena&view=config&layout=import', $message, 'notice');
		}
		else {
			$this->setRedirect('index.php?option=com_kunena');
		}
	}

	/**
	 * Method to export the configuration via download.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function export()
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('K_Invalid_Token'));

		// Get the component configuration values.
		$config = JComponentHelper::getParams('com_kunena');
		$string	= $config->toString();

		// Send file headers.
		header('Content-type: application/force-download');
	    header('Content-Transfer-Encoding: Binary');
	    header('Content-length: '.strlen($string));
	    header('Content-disposition: attachment; filename="kunena.config.ini"');
		header('Pragma: no-cache');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Expires: 0');

		// Print the configuration values.
	    echo $string;

		JFactory::getApplication()->close();
	}

	/**
	 * Method to save the configuration.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function save()
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('K_Invalid_Token'));

		// Save the configuration.
		$model	= $this->getModel('Config');
		$return	= $model->save();

		if ($return === false)
		{
			$message = JText::sprintf('K_Config_Save_Failed', $model->getError());
			$this->setRedirect('index.php?option=com_kunena&view=config', $message, 'notice');
		}
		else {
			$this->setRedirect('index.php?option=com_kunena', JText::_('Saved'));
		}
	}
	
	public function apply()
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('K_Invalid_Token'));

		// Save the configuration.
		$model	= $this->getModel('Config');
		$return	= $model->save();

		if ($return === false)
		{
			$message = JText::sprintf('K_Config_Save_Failed', $model->getError());
			$this->setRedirect('index.php?option=com_kunena&view=config', $message, 'notice');
		}
		else {
			$this->setRedirect('index.php?option=com_kunena&view=config', JText::_('Saved'));
		}
	}
	
}
