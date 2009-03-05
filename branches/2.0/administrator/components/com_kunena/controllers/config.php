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
 * The Kunena Access Control Configuration Controller
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.0
 */
class KunenaControllerConfig extends JController
{
	function display()
	{
		$this->setRedirect('index.php?option=com_kunena&view=config');
	}

	/**
	 * Method to save the configuration changes.
	 *
	 * @access	public
	 * @return	void
	 * @since	2.0
	 */
	function apply()
	{
		// Get the model.
		$model = &$this->getModel('Config', 'KunenaModel');

		// Save the configuration.
		if ($model->save()) {
			$this->setRedirect('index.php?option=com_kunena&view=config', JText::_('Config Save Success'));
		} else {
			$this->setRedirect('index.php?option=com_kunena&view=config', JText::_('Config Save Fail'), 'notice');
		}
	}

	/**
	 * Method to cancel the configuration changes.
	 *
	 * @access	public
	 * @return	void
	 * @since	2.0
	 */
	function cancel()
	{
		$this->setRedirect('index.php?option=com_kunena');
	}

	function export()
	{
		$application = &JFactory::getApplication('administrator');

		$config = &JComponentHelper::getParams('com_kunena');
		$configString = $config->toString();

		header('Content-type: application/force-download');
	    header('Content-Transfer-Encoding: Binary');
	    header('Content-length: '.strlen($configString));
	    header('Content-disposition: attachment; filename="kunena.config.ini"');
		header('Pragma: no-cache');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Expires: 0');

	    echo $configString;

		$application->close();
	}

	function import()
	{
		$string = JRequest::getVar('configString', null, 'post', 'string', JREQUEST_ALLOWHTML);
		$file	= JRequest::getVar('configFile', array(), 'files', 'array');

		if (!empty($string)) {
			// import the configuration.
			$model = &$this->getModel('Config', 'KunenaModel');
			if (!$model->import($string)) {
				$message = JText::_('Config Import Fail');
				$this->setRedirect('index.php?option=com_kunena&view=config&layout=import', $message, 'notice');
			} else {
				$this->setRedirect('index.php?option=com_kunena&view=config&layout=success');
			}
		} elseif (!empty($file) and ($file['error'] == 0) and ($file['size'] > 0) and (is_readable($file['tmp_name']))) {
			// get the configuration string from the uploaded file
			$string = implode("\n", file($file['tmp_name']));

			// import the configuration.
			$model = &$this->getModel('Config', 'KunenaModel');
			if (!$model->import($string)) {
				$message = JText::_('Config Import Fail');
				$this->setRedirect('index.php?option=com_kunena&view=config&layout=import', $message, 'notice');
			} else {
				$this->setRedirect('index.php?option=com_kunena&view=config&layout=success');
			}
		} else {
			$this->setRedirect('index.php?option=com_kunena&view=config&layout=import');
		}
	}
}
