<?php
/**
* @version $Id:  $
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

class KDatabaseMaintenance
{
	var $tables = array();
	var $_tables = array ( '#__kunena_announcement', '#__kunena_attachments', '#__kunena_categories', '#__kunena_favorites', '#__kunena_groups', '#__kunena_messages', '#__kunena_moderation', '#__kunena_ranks', '#__kunena_sessions', '#__kunena_smileys', '#__kunena_subscriptions', '#__kunena_users', '#__kunena_version', '#__kunena_whoisonline');

	function __construct()
	{
       	$db = &JFactory::getDBO();
		$db->setQuery( "SHOW TABLES LIKE '" .$db->getPrefix(). "kunena_%'");
		$tables = $db->loadResultArray();
		$prelen = strlen($db->getPrefix());
		foreach	($tables as $table) $this->tables['#__'.substr($table,$prelen)] = 1;
	}

	function &getInstance()
	{
		static $instance = null;
		if (!$instance) {
			$instance = new KDatabaseMaintenance();
		}
		return $instance;
	}

	function check($table)
	{
		return isset($this->tables[$table]);
	}

	function installed()
	{
		foreach ($this->_tables as $table) {
			if (!isset($this->tables[$table])) return false;
		}
		return true;
	}
}
