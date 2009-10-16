<?php
/**
 * @version		$Id: install.php 1014 2009-08-17 07:18:07Z louis $
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * The Kunena Installer Controller
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaControllerInstall extends JController
{
	public function __construct()
	{
		parent::__construct();
		JRequest::setVar('hidemainmenu', 1);
		JToolBarHelper::title('<span>'.KUNENA_VERSION.'</span> '.JText::_('Installer'));
	}

	/**
	 * Method to install Kunena
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function install()
	{
		$model	= $this->getModel('Install');
		$schema = $model->getSchemaFromDatabase();
		//echo "<pre>",htmlentities($schema->saveXML()),"</pre>";
		$diff = $model->getSchemaDiff($schema, KUNENA_INSTALL_SCHEMA_FILE);
		echo "<pre>",htmlentities($diff->saveXML()),"</pre>";

		return;
		$model->initialize();

		// Check requirements
		$reqs	= $model->getRequirements();
		if (!empty($reqs->fail))
		{
			$this->setRedirect('index.php?option=com_kunena&view=install');
			return;
		}
		// Start installation
		$model->insertVersion();
	}
}
