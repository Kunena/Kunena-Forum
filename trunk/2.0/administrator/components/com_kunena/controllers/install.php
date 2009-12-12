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
class KunenaControllerInstall extends KunenaController
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Method to install Kunena
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function install()
	{
		JToolBarHelper::title('<span>KUNENA '.KUNENA_VERSION.'</span> '. JText::_( 'Installer' ), 'about' );

		$model	= $this->getModel('Install');

		// Check requirements
		$reqs = $model->getRequirements();
		if (!empty($reqs->fail))
		{
			// If requirements are not met, do not install
			$this->setRedirect('index.php?option=com_kunena&view=install');
			return;
		}

		$version = $model->getLastVersion();

		switch ($version->state)
		{
			case '':
				$results = $model->beginInstall();
				if (count($results)) break;
			case 'migrateDatabase':
				$results = $model->migrateDatabase();
				if (count($results)) break;
			case 'upgradeDatabase':
				$results = $model->upgradeDatabase();
				//if (count($results)) break;
			case 'installSampleData':
				$results = $model->installSampleData();
				$stop = true;
				break;
			default:
				$results = $model->beginInstall();
		}

		foreach ($results as $result)
		{
			if (empty($result['action'])) continue;
			echo '<div>', $result['action'], ': ', $result['name'], '(', $result['sql'],')</div>';
		}

		if (!isset($stop)) {
			$document =& JFactory::getDocument();
			$document->addScriptDeclaration("setTimeout(\"location='".JRoute::_('index.php?option=com_kunena&view=install&task=install', false)."'\", 500);");
			JRequest::setVar('hidemainmenu', 1);
		} else {
			echo "Done!";
		}
	}
}
