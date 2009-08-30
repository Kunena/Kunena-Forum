<?php
/**
 * Joomla! 1.5 component: Kunena Forum Importer
 *
 * @version $Id: $
 * @author Kunena Team
 * @package Joomla
 * @subpackage Kunena Forum Importer
 * @license GNU/GPL
 *
 * Imports forum data into Kunena
 *
 * @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );
jimport( 'joomla.error.profiler' );

require_once( JPATH_COMPONENT.DS.'helpers'.DS.'helper.php' );

/**
 * Kunena phpBB3 importer Controller
 *
 * @package Joomla
 * @subpackage Kunena phpBB3 importer
 */
class KunenaimporterController extends JController {
	/**
	* Constructor
	* @access private
	* @subpackage Kunena phpBB3 importer
	*/
	function __construct() {
		//Get View
		if(JRequest::getCmd('view') == '') {
			JRequest::setVar('view', 'default');
		}
		$this->item_type = 'Default';
		$this->addModelPath( JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_kunenaimporter' . DS . 'models' );
		parent::__construct();
		$this->registerTask( 'import', 'importforum' );
		$this->registerTask( 'stopimport', 'stopimport' );
		$this->registerTask( 'truncate', 'truncatetable' );
	}

	function checkTimeout() {
		static $start = null;

        list( $usec, $sec ) = explode( ' ', microtime() );
        $time = ((float)$usec + (float)$sec);

		if (empty($start)) $start = $time;

		if ($time-$start < 4) return false;
		return true;
	}

	function getParams()
	{
		$app =& JFactory::getApplication();
		$form = JRequest::getBool('form');

		if ($form) 
		{
			$state = JRequest::getVar('cid', array(), 'post', 'array');
			$app->setUserState('com_knimporter.state', $state);
		} else {
			$state = $app->getUserState('com_knimporter.state');
			if (!is_array($state)) $state = array();
			JRequest::setVar('cid', $state, 'post');
		}
		return array_flip($state);
	}

	function stopimport()
	{
		$app =& JFactory::getApplication();
		$this->setredirect('index.php?option=com_kunenaimporter');
	}

	function truncatetable()
	{
		$limit = 1000;
		$timeout = false;

		$db =& JFactory::getDBO();
		$app =& JFactory::getApplication();

		$importer =& $this->getModel('import');

		$options = $importer->getImportOptions();
		$state = $this->getParams();
		foreach ($options as $option) 
		{
			if (isset($state[$option])) {
				$app->setUserState('com_knimporter.'.$option, 0); 				$importer->truncateData($option);
			}
		}
		// FIXME: !!!
		$importer->truncateJoomlaUsers();

		$this->setredirect('index.php?option=com_kunenaimporter');
	}

	function importforum()
	{
		$limit = 1000;
		$timeout = false;

		$db =& JFactory::getDBO();
		$app =& JFactory::getApplication();

		$component = JComponentHelper::getComponent( 'com_kunenaimporter' );
		$params = new JParameter( $component->params );
		$exporter =& $this->getModel('export_'.$params->get('extforum'));
		$exporter->checkConfig();
		$errormsg = $exporter->getError();
		$importer =& $this->getModel('import');
		$importer->setAuthMethod($exporter->getAuthMethod());

		$options = $importer->getImportOptions();
		$state = $this->getParams();

		if ($errormsg) return;

		foreach ($options as $option) {
			$start = (int)$app->getUserState('com_knimporter.'.$option);
			if (isset($state[$option])) {
				$count = 0;
				do {
					$data =& $exporter->exportData($option, $start, $limit);					$importer->importData($option, $data);
					$count = count($data);
					$start += $count;
					$app->setUserState('com_knimporter.'.$option, $start);
					$timeout = $this->checkTimeout();
					unset($data);
				} while ($count && !$timeout);			}
			if ($timeout) break;
		}

		//JToolBarHelper::back();
		if ($timeout) $view = '&view=import';
		else $view = '';
		$this->setredirect('index.php?option=com_kunenaimporter'.$view);

/*
		// Check errors
		$query = "SELECT * FROM `#__knimport_extuser` WHERE userid=0 OR conflict>0 OR error!=''";
		$db->setQuery($query);
		$userlist = $db->loadObjectList();
		if (count($userlist)) {
			echo "<ul>";
			foreach ($userlist as $user) {
				echo "<li>";
				if ($user->userid == 0) {
					$error = JText::_($user->error);
					echo "<b>SAVING USER FAILED:</b> $user->extname ($user->extuserid):  $error<br />";
				} else {
					echo "<b>USERNAME CONFLICT:</b> $user->extname ($user->extuserid): $user->userid == $user->conflict<br />";
				}
				echo "</li>";
			}
			echo "</ul>";
		}
*/
	}

	function save()
	{
		$component = 'com_kunenaimporter';

		$table =& JTable::getInstance('component');
		if (!$table->loadByOption( $component ))
		{
			JError::raiseWarning( 500, 'Not a valid component' );
			return false;
		}

		$post = JRequest::get( 'post' );
		$post['option'] = $component;
		$table->bind( $post );

		if ($table->save($post)) {
			$msg = JText::_( 'Configuration Saved' );
			$type = 'message';
		} else {
			$msg = JText::_( 'Error Saving Configuration');
			$type = 'notice';
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$link = 'index.php?option=com_kunenaimporter';
		$this->setRedirect($link, $msg, $type);
	}

	function display() {
		$params = $this->getParams();
		$view =& $this->getView( JRequest::getCmd('view'), 'html' );
		$component = JComponentHelper::getComponent( 'com_kunenaimporter' );
		$params = new JParameter( $component->params );
		$view->setModel( $this->getModel( 'import' ), true );
		$view->setModel( $this->getModel( 'export_'.$params->get('extforum') ), false );
		$view->display();
	}
}
?>
