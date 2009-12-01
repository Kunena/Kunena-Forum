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

		// Check requirements
		$reqs = $model->getRequirements();
		if (!empty($reqs->fail))
		{
			// If requirements are not met, do nothing
			$this->setRedirect('index.php?option=com_kunena&view=install');
			return;
		}


		$schema = $model->getSchemaFromDatabase();
		//echo "<pre>",htmlentities($schema->saveXML()),"</pre>";
		$diff = $model->getSchemaDiff($schema, KUNENA_INSTALL_SCHEMA_FILE);
		echo "<pre>",htmlentities($diff->saveXML()),"</pre>";

		$sql = $model->getSchemaSQL($diff);
		echo "<pre>",print_r($sql),"</pre>";

		if (isset($sql['kunena_version'])) echo "UPDATE VERSION TABLE";
		return;


		$model->initialize();



		// Start installation
		$model->insertVersion();
	}
}
