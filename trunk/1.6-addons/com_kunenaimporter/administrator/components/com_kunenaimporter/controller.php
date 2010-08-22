<?php
/**
 * @version $Id$
 * Kunena Forum Importer Component
 * @package com_kunenaimporter
 *
 * Imports forum data into Kunena
 *
 * @Copyright (C) 2009 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 */
defined ( '_JEXEC' ) or die ();

jimport ( 'joomla.application.component.controller' );
jimport ( 'joomla.error.profiler' );

/**
 * Kunena importer Controller
 */
class KunenaImporterController extends JController {
	/**
	 * Constructor
	 * @access private
	 * @subpackage Kunena phpBB3 importer
	 */
	function __construct() {
		//Get View
		if (JRequest::getCmd ( 'view' ) == '') {
			JRequest::setVar ( 'view', 'default' );
		}
		$this->item_type = 'Default';
		$this->addModelPath ( JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_kunenaimporter' . DS . 'models' );
		parent::__construct ();
		$this->registerTask ( 'truncatemap', 'truncatemap' );
		$this->registerTask ( 'mapusers', 'mapusers' );
		$this->registerTask ( 'stopmapping', 'stopmapping' );
		$this->registerTask ( 'select', 'selectuser' );
		$this->registerTask ( 'import', 'importforum' );
		$this->registerTask ( 'stopimport', 'stopimport' );
		$this->registerTask ( 'truncate', 'truncatetable' );
	}

	function checkTimeout() {
		static $start = null;

		list ( $usec, $sec ) = explode ( ' ', microtime () );
		$time = (( float ) $usec + ( float ) $sec);

		if (empty ( $start ))
			$start = $time;

		if ($time - $start < 4)
			return false;
		return true;
	}

	function getParams() {
		$app = JFactory::getApplication ();
		$form = JRequest::getBool ( 'form' );

		if ($form) {
			$state = JRequest::getVar ( 'cid', array (), 'post', 'array' );
			$app->setUserState ( 'com_kunenaimporter.state', $state );
		} else {
			$state = $app->getUserState ( 'com_kunenaimporter.state' );
			if (! is_array ( $state ))
				$state = array ();
			JRequest::setVar ( 'cid', $state, 'post' );
		}
		return array_flip ( $state );
	}

	function stopmapping() {
		$app = JFactory::getApplication ();
		$this->setredirect ( 'index.php?option=com_kunenaimporter&view=users' );
	}

	function stopimport() {
		$app = JFactory::getApplication ();
		$this->setredirect ( 'index.php?option=com_kunenaimporter' );
	}

	function truncatetable() {
		$limit = 1000;
		$timeout = false;

		$db = JFactory::getDBO ();
		$app = JFactory::getApplication ();

		$importer = & $this->getModel ( 'import' );

		$options = $importer->getImportOptions ();
		$state = $this->getParams ();
		$optlist = array ();
		foreach ( $options as $option ) {
			if (isset ( $state [$option] )) {
				$app->setUserState ( 'com_kunenaimporter.' . $option, 0 );
				$importer->truncateData ( $option );
				$optlist [] = $option;
			}
		}
		// FIXME: !!!
		//$importer->truncateJoomlaUsers();

		$app->enqueueMessage ( 'Deleted ' . implode ( ', ', $optlist ) );
		$this->setredirect ( 'index.php?option=com_kunenaimporter' );
	}

	function truncatemap() {
		$importer = $this->getModel ( 'import' );
		$importer->truncateUsersMap ();
		$app = JFactory::getApplication ();
		$app->setUserState ( 'com_kunenaimporter.Users', 0 );
		$app->enqueueMessage ( 'Deleted user mapping' );
		$this->setredirect ( 'index.php?option=com_kunenaimporter&view=users' );
	}

	function mapusers() {
		$limit = 100;
		$timeout = false;

		$db = JFactory::getDBO ();
		$app = JFactory::getApplication ();

		$component = JComponentHelper::getComponent ( 'com_kunenaimporter' );
		$params = new JParameter ( $component->params );
		$exporter = $this->getModel ( 'export_' . $params->get ( 'extforum' ) );
		$exporter->checkConfig ();
		$errormsg = $exporter->getError ();
		$importer = $this->getModel ( 'import' );
		$importer->setAuthMethod ( $exporter->getAuthMethod () );

		if ($errormsg)
			return;

		$start = ( int ) $app->getUserState ( 'com_kunenaimporter.Users' );
		$count = 0;
		do {
			$data = $exporter->exportUsers ( $start, $limit );
			$importer->mapUsers ( $data );
			$count = count ( $data );
			$start += $count;
			$app->setUserState ( 'com_kunenaimporter.Users', $start );
			$timeout = $this->checkTimeout ();
			unset ( $data );
		} while ( $count && ! $timeout );

		//JToolBarHelper::back();
		if ($timeout)
			$view = '&view=mapusers';
		else {
			$view = '&view=users';
			$app->enqueueMessage ( "Mapped $start users" );
		}
		$this->setredirect ( 'index.php?option=com_kunenaimporter' . $view );
		/*
		// Check errors
		$query = "SELECT * FROM `#__kunenaimporter_users` WHERE id=0 OR conflict>0 OR error!=''";
		$db->setQuery($query);
		$userlist = $db->loadObjectList();
		if (count($userlist)) {
			echo "<ul>";
			foreach ($userlist as $user) {
				echo "<li>";
				if ($user->id == 0) {
					$error = JText::_($user->error);
					echo "<b>SAVING USER FAILED:</b> $user->extusername ($user->extid):  $error<br />";
				} else {
					echo "<b>USERNAME CONFLICT:</b> $user->extusername ($user->extid): $user->id == $user->conflict<br />";
				}
				echo "</li>";
			}
			echo "</ul>";
		}
		*/
	}

	function selectuser() {
		$extid = JRequest::getInt ( 'extid', 0 );
		$cid = JRequest::getVar ( 'cid', array (0 ), 'post', 'array' );
		$userdata ['id'] = array_shift ( $cid );
		require_once (JPATH_COMPONENT . DS . 'models' . DS . 'kunena.php');
		$extuser = JTable::getInstance ( 'ExtUser', 'CKunenaTable' );
		$extuser->load ( $extid );
		$extuser->_exists = true;
		if ($extuser->save ( $userdata ) === false) {
			echo "ERROR: Saving external data for $userdata->username failed: " . $extuser->getError () . "<br />";
		}

		$app = & JFactory::getApplication ();
		$this->setredirect ( 'index.php?option=com_kunenaimporter&view=users' );
	}

	function importforum() {
		$limit = 1000;
		$timeout = false;

		$db = JFactory::getDBO ();
		$app = JFactory::getApplication ();

		$component = JComponentHelper::getComponent ( 'com_kunenaimporter' );
		$params = new JParameter ( $component->params );
		$exporter = $this->getModel ( 'export_' . $params->get ( 'extforum' ) );
		$exporter->checkConfig ();
		$errormsg = $exporter->getError ();
		$importer = $this->getModel ( 'import' );
		$importer->setAuthMethod ( $exporter->getAuthMethod () );

		$options = $importer->getImportOptions ();
		$state = $this->getParams ();

		if ($errormsg)
			return;

		foreach ( $options as $option ) {
			$start = ( int ) $app->getUserState ( 'com_kunenaimporter.' . $option );
			if (isset ( $state [$option] )) {
				$count = 0;
				do {
					$data = & $exporter->exportData ( $option, $start, $limit );
					$importer->importData ( $option, $data );
					$count = count ( $data );
					$start += $count;
					$app->setUserState ( 'com_kunenaimporter.' . $option, $start );
					$timeout = $this->checkTimeout ();
					unset ( $data );
				} while ( $count && ! $timeout );
			}
			if ($timeout)
				break;
		}

		//JToolBarHelper::back();
		if ($timeout)
			$view = '&view=import';
		else {
			$app->enqueueMessage ( "Import done!" );
			$view = '';
		}
		$this->setredirect ( 'index.php?option=com_kunenaimporter' . $view );

	/*
		// Check errors
		$query = "SELECT * FROM `#__kunenaimporter_users` WHERE userid=0 OR conflict>0 OR error!=''";
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

	function save() {
		$component = 'com_kunenaimporter';

		$table = JTable::getInstance ( 'component' );
		if (! $table->loadByOption ( $component )) {
			JError::raiseWarning ( 500, 'Not a valid component' );
			return false;
		}

		$post = JRequest::get ( 'post' );
		$post ['option'] = $component;
		$table->bind ( $post );

		if ($table->save ( $post )) {
			$msg = JText::_ ( 'Configuration Saved' );
			$type = 'message';
		} else {
			$msg = JText::_ ( 'Error Saving Configuration' );
			$type = 'notice';
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$link = 'index.php?option=com_kunenaimporter';
		$this->setRedirect ( $link, $msg, $type );
	}

	function display() {
		$params = $this->getParams ();
		$cmd = JRequest::getCmd ( 'view', 'default' );
		$view = $this->getView ( $cmd, 'html' );
		$component = JComponentHelper::getComponent ( 'com_kunenaimporter' );
		$params = new JParameter ( $component->params );
		$view->setModel ( $this->getModel ( 'import' ), true );
		$view->setModel ( $this->getModel ( 'export_' . $params->get ( 'extforum', 'kunena' ) ), false );

		JSubMenuHelper::addEntry ( JText::_ ( 'Importer Configuration' ), 'index.php?option=com_kunenaimporter', $cmd == 'default' );
		JSubMenuHelper::addEntry ( JText::_ ( 'Migrate Users' ), 'index.php?option=com_kunenaimporter&view=users', $cmd == 'users' );

		$view->display ();
	}
}