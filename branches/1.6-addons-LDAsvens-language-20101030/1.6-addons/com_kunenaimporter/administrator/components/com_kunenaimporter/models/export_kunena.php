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

require_once (JPATH_COMPONENT . DS . 'models' . DS . 'export.php');

class KunenaimporterModelExport_Kunena extends KunenaimporterModelExport {
	var $version = null;

	function checkConfig() {
		parent::checkConfig ();
		if (JError::isError ( $this->ext_database ))
			return;

		$tables = $this->ext_database->getTableList ();
		if (in_array ( $this->ext_database->getPrefix () . 'fb_version', $tables )) {
			$query = "SELECT version FROM #__fb_version ORDER BY id DESC";
			$this->ext_database->setQuery ( $query );
			$this->version = $this->ext_database->loadResult ();
		}
		if (! $this->version) {
			$this->error = $this->ext_database->getErrorMsg ();
			if (! $this->error)
				$this->error = 'Configuration information missing: External Kunena version not found';
		}
		if ($this->error) {
			$this->addMessage ( '<div>External Kunena version: <b style="color:red">FAILED</b></div>' );
			$this->addMessage ( '<div><b>Error:</b> ' . $this->error . '</div>' );
			return false;
		}

		if ($this->ext_same) {
			$this->error = 'Cannot migrate Kunena into itself';
			$this->addMessage ( '<div><b style="color:red">' . $this->error . '</b></div>' );
			return false;
		}
		if (version_compare ( $this->version, '1.6.0', '>' ))
			$this->error = "Unsupported forum: Kunena $this->version";
		if ($this->error) {
			$this->addMessage ( '<div>External Kunena version: <b style="color:red">' . $this->version . '</b></div>' );
			$this->addMessage ( '<div><b>Error:</b> ' . $this->error . '</div>' );
			return false;
		}
		$this->addMessage ( '<div>External Kunena version: <b style="color:green">' . $this->version . '</b></div>' );

	}

	function buildImportOps() {
		// select, from, where, orderby
		$importOps = array ();
		$importOps ['announcements'] = array ('select' => '*', 'from' => '#__fb_announcement', 'orderby' => 'id' );
		$importOps ['attachments'] = array ('select' => '*', 'from' => '#__fb_attachments', 'orderby' => 'mesid' );
		$importOps ['categories'] = array ('select' => '*', 'from' => '#__fb_categories', 'orderby' => 'id' );
		$importOps ['config'] = array ('select' => '*', 'from' => '#__fb_config' );
		$importOps ['favorites'] = array ('select' => '*', 'from' => '#__fb_favorites', 'orderby' => 'thread' );
		$importOps ['messages'] = array ('select' => '*', 'from' => '#__fb_messages AS m LEFT JOIN #__fb_messages_text AS t ON m.id=t.mesid', 'orderby' => 'id' );
		$importOps ['moderation'] = array ('select' => '*', 'from' => '#__fb_moderation', 'orderby' => 'userid' );
		$importOps ['ranks'] = array ('select' => '*', 'from' => '#__fb_ranks', 'orderby' => 'rank_id' );
		$importOps ['sessions'] = array ('select' => '*', 'from' => '#__fb_sessions', 'orderby' => 'userid' );
		$importOps ['smilies'] = array ('select' => '*', 'from' => '#__fb_smileys', 'orderby' => 'id' );
		$importOps ['subscriptions'] = array ('select' => '*', 'from' => '#__fb_subscriptions', 'orderby' => 'thread' );
		$importOps ['userprofile'] = array ('select' => '*', 'from' => '#__fb_users', 'orderby' => 'userid' );
		$importOps ['version'] = array ('select' => '*', 'from' => '#__fb_version', 'orderby' => 'id' );
		$importOps ['whoisonline'] = array ('select' => '*', 'from' => '#__fb_whoisonline', 'orderby' => 'id' );
		$this->importOps = & $importOps;
	}
}