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

// Everything else than user import can be found from here:
require_once (JPATH_COMPONENT . DS . 'models' . DS . 'export_phpbb2.php');

class KunenaimporterModelExport_PNphpBB2 extends KunenaimporterModelExport_phpBB2 {
	var $version;
	var $pnversion;

	function checkConfig() {
		$this->addMessage ( '<h2>Importer Status</h2>' );
		if (JError::isError ( $this->ext_database ))
			$this->error = $this->ext_database->toString ();
		if ($this->error) {
			$this->addMessage ( '<div>Database connection: <b style="color:red">FAILED</b></div>' );
			$this->addMessage ( '<br /><div><b>Please check that your external database settings are correct!</b></div><div><b>Error:</b> ' . $this->error . '</div>' );
			return false;
		}
		$this->addMessage ( '<div>Database connection: <b style="color:green">OK</b></div>' );

		$query = "SELECT config_value FROM #__config WHERE config_name='version'";
		$this->ext_database->setQuery ( $query );
		$this->version = $this->ext_database->loadResult ();
		if (! $this->version) {
			$this->error = $this->ext_database->getErrorMsg ();
			if (! $this->error)
				$this->error = 'Configuration information missing: phpBB version not found';
		}
		if ($this->error) {
			$this->addMessage ( '<div>phpBB version: <b style="color:red">FAILED</b></div>' );
			return false;
		}

		if ($this->version [0] == '.')
			$this->version = '2' . $this->version;
		$version = explode ( '.', $this->version, 3 );
		if ($version [0] != 2 || $version [1] != 0 || $version [2] < '15')
			$this->error = "Unsupported forum: phpBB $this->version";
		if ($this->error) {
			$this->addMessage ( '<div>phpBB version: <b style="color:red">' . $this->version . '</b></div>' );
			$this->addMessage ( '<div><b>Error:</b> ' . $this->error . '</div>' );
			return false;
		}
		$this->addMessage ( '<div>phpBB version: <b style="color:green">' . $this->version . '</b></div>' );

		$query = "SELECT config_value FROM #__config WHERE config_name='pnphpbb2_version'";
		$this->ext_database->setQuery ( $query );
		$this->pnversion = $this->ext_database->loadResult ();
		if (! $this->pnversion) {
			$this->error = $this->ext_database->getErrorMsg ();
			if (! $this->error)
				$this->error = 'Configuration information missing: PNphpBB2 version not found';
		}
		if ($this->error) {
			$this->addMessage ( '<div>PNphpBB2 version: <b style="color:red">FAILED</b></div>' );
			return false;
		}

		$version = explode ( '.', $this->pnversion, 2 );
		if ($version [0] != 1 || $version [1] != '2i-p3')
			$this->error = "Unsupported forum: PNphpBB2 $this->version";
		if ($this->error) {
			$this->addMessage ( '<div>PNphpBB2 version: <b style="color:red">' . $this->pnversion . '</b></div>' );
			$this->addMessage ( '<div><b>Error:</b> ' . $this->error . '</div>' );
			return false;
		}
		$this->addMessage ( '<div>PNphpBB2 version: <b style="color:green">' . $this->pnversion . '</b></div>' );
	}

	function countUsers() {
		$prefix = $this->ext_database->_table_prefix;
		$prefix = substr ( $prefix, 0, strpos ( $prefix, '_phpbb_' ) );

		$query = "SELECT count(*) FROM #__users AS f LEFT JOIN {$prefix}_users AS u ON u.pn_uid = user_id WHERE user_id > 0 && user_lastvisit>0 ";
		return $this->getCount ( $query );
	}

	function &exportUsers($start = 0, $limit = 0) {
		$prefix = $this->ext_database->_table_prefix;
		$prefix = substr ( $prefix, 0, strpos ( $prefix, '_phpbb_' ) );

		// PostNuke
		$query = "SELECT u.pn_uid AS extuserid, u.pn_uname AS username, pn_email AS email, pn_pass AS password, pn_user_regdate, f.*, (b.ban_userid>0) AS blocked FROM #__users AS f LEFT JOIN {$prefix}_users AS u ON u.pn_uid = user_id LEFT OUTER JOIN #__banlist AS b ON u.pn_uid = b.ban_userid WHERE user_id > 0 && user_lastvisit>0 ORDER BY u.pn_uid";

		$result = $this->getExportData ( $query, $start, $limit, 'extuserid' );

		foreach ( $result as $item ) {
			$row = & $result [$item->extuserid];
			$row->name = $row->username = $row->username;

			if ($row->user_regdate > $row->pn_user_regdate)
				$row->user_regdate = $row->pn_user_regdate;
				// Convert date for last visit and register date.
			$row->registerDate = date ( "Y-m-d H:i:s", $row->user_regdate );
			$row->lastvisitDate = date ( "Y-m-d H:i:s", $row->user_lastvisit );

			// Set user type and group id - 1=admin, 2=moderator
			if ($row->user_level == "1") {
				$row->usertype = "Administrator";
			} else {
				$row->usertype = "Registered";
			}

			// Convert bbcode in signature
			$row->user_sig = prep ( $row->user_sig );

			// No imported users will get mails from the admin
			$row->emailadmin = "0";

			unset ( $row->user_regdate, $row->user_lastvisit, $row->user_level );
		}
		return $result;
	}

}