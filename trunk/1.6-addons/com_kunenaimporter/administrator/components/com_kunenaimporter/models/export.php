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

// Import Joomla! libraries
jimport ( 'joomla.application.component.model' );
jimport ( 'joomla.application.application' );

// Kunena wide defines
$kunena_defines = JPATH_ROOT . DS . 'components' . DS . 'com_kunena' . DS . 'lib' . DS . 'kunena.defines.php';
if (file_exists ( $kunena_defines ))
	require_once ($kunena_defines);

class KunenaimporterModelExport extends JModel {
	var $ext_database = null;
	var $ext_table_prefix;
	var $ext_same = false;
	var $messages = array ();
	var $error = '';
	var $importOps = array ();
	var $auth_method;

	function __construct() {
		$app = JFactory::getApplication ();
		parent::__construct ();

		$component = JComponentHelper::getComponent ( 'com_kunenaimporter' );
		$params = new JParameter ( $component->params );

		if (! $this->ext_database) {
			$db_name = $params->get ( 'db_name' );
			$db_tableprefix = $params->get ( 'db_tableprefix' );
			if (empty ( $db_name )) {
				$this->ext_database = JFactory::getDBO ();
				$this->ext_same = 1;
			} else {
				$option ['driver'] = $app->getCfg ( 'dbtype' );
				$option ['host'] = $params->get ( 'db_host' );
				$option ['user'] = $params->get ( 'db_user' );
				$option ['password'] = $params->get ( 'db_passwd' );
				$option ['database'] = $params->get ( 'db_name' );
				$option ['prefix'] = $params->get ( 'db_prefix' );
				$this->ext_database = & JDatabase::getInstance ( $option );
			}
		}
		// TODO: make this to work
		//jimport('joomla.error.exception');
		//$this->ext_database->debug(0);
		$this->buildImportOps ();
	}

	function getExportOptions($importer) {
		$app = JFactory::getApplication ();

		$options = $importer->getImportOptions ();
		$exportOpt = array ();
		foreach ( $options as $option ) {
			$count = $this->countData ( $option );
			if ($count !== false)
				$exportOpt [] = array (
				'name' => $option,
				'task' => 'KnImporter_Task_' . $option,
				'desc' => 'KnImporter_Description_' . $option,
				'status' => ( int ) $app->getUserState ( 'com_kunenaimporter.' . $option ),
				'total' => $count );
		}
		return $exportOpt;
	}

	function checkConfig() {
		$this->addMessage ( '<h2>Importer Status</h2>' );

		// Kunena detection and version check
		$minKunenaVersion = '1.6.0-RC2';
		if (! class_exists ( 'Kunena' ) || Kunena::versionBuild () < 3251) {
			$this->addMessage ( '<div>Kunena version: <b style="color:red">FAILED</b></div>' );
			$this->addMessage ( '<br /><div><b>You need to install Kunena 1.6!</b></div><div><b>Error:</b> Kunena 1.6 not detected</div>' );
			return false;
		}
		$this->addMessage ( '<div>Kunena version: <b style="color:green">' . KUNENA_VERSION . '</b></div>' );

		if (JError::isError ( $this->ext_database ))
			$this->error = $this->ext_database->toString ();
		if ($this->error) {
			$this->addMessage ( '<div>Database connection: <b style="color:red">FAILED</b></div>' );
			$this->addMessage ( '<br /><div><b>Please check that your external database settings are correct!</b></div><div><b>Error:</b> ' . $this->error . '</div>' );
			return false;
		}
		$this->addMessage ( '<div>Database connection: <b style="color:green">OK</b></div>' );
	}

	function getAuthMethod() {
		return $this->auth_method;
	}

	function addMessage($msg) {
		$this->messages [] = $msg;
	}

	function getMessages() {
		return implode ( '', $this->messages );
	}

	function getError() {
		return $this->error;
	}

	function getCount($query) {
		$this->ext_database->setQuery ( $query );
		$result = $this->ext_database->loadResult ();
		if ($this->ext_database->getErrorNum ()) {
			$this->error = $this->ext_database->getErrorMsg ();
			$this->addMessage ( '<div><b>Error:</b> ' . $this->error . '</div>' );
		}
		return $result;
	}

	function &getExportData($query, $start = 0, $limit = 0, $key = null) {
		$this->ext_database->setQuery ( $query, $start, $limit );
		$result = $this->ext_database->loadObjectList ( $key );
		if ($this->ext_database->getErrorNum ()) {
			$this->error = $this->ext_database->getErrorMsg ();
			$this->addMessage ( '<div><b>Error:</b> ' . $this->error . '</div>' );
		}
		return $result;
	}

	function countData($operation) {
		$result = 0;
		if (empty ( $this->importOps [$operation] ))
			return false;
		$info = $this->importOps [$operation];
		if (! empty ( $info ['from'] )) {
			$query = "SELECT COUNT(*) FROM " . $info ['from'];
			if (! empty ( $info ['where'] ))
				$query .= ' WHERE ' . $info ['where'];
			if (! empty ( $info ['orderby'] ))
				$query .= ' ORDER BY ' . $info ['orderby'];
			$result = $this->getCount ( $query );
		} else if (! empty ( $info ['count'] ))
			$result = $this->$info ['count'] ();
		return $result;
	}

	function &exportData($operation, $start = 0, $limit = 0) {
		$result = array ();
		if (empty ( $this->importOps [$operation] ))
			return $result;
		$info = $this->importOps [$operation];
		if (! empty ( $info ['select'] ) && ! empty ( $info ['from'] )) {
			$query = "SELECT " . $info ['select'] . " FROM " . $info ['from'];
			if (! empty ( $info ['where'] ))
				$query .= ' WHERE ' . $info ['where'];
			if (! empty ( $info ['orderby'] ))
				$query .= ' ORDER BY ' . $info ['orderby'];
			$result = $this->getExportData ( $query, $start, $limit );
		} else if (! empty ( $info ['export'] ))
			$result = $this->$info ['export'] ( $start, $limit );
		return $result;
	}

	function mapUsers($start = 0, $limit = 0) {
		$db = JFactory::getDBO();
		$query = "SELECT id, username FROM #__users";
		$db->setQuery ( $query, $start, $limit );
		$users = $db->loadObjectList ( 'id' );
		foreach ($users as $user) {
			$extid = $this->mapJoomlaUser($user);
			if ($extid) {
				$extuser = JTable::getInstance ( 'ExtUser', 'CKunenaTable' );
				$extuser->load ( $extid );
				if ($extuser->exists() && !$extuser->id) {
					$extuser->id = $user->id;
					if ($extuser->store () === false) {
						die("ERROR: Saving external data for $user->username failed: " . $extuser->getError () . "<br />");
					}
				}
			}
		}
	}

	function &exportJoomlaUsers($start = 0, $limit = 0) {

	}

}