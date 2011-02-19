<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 * Kunena 1.6.0: Migrate polls hack from K1.5
 * component: com_kunena
 **/

defined ( '_JEXEC' ) or die ();

function kunena_upgrade_160_polls($parent) {
	//Import filesystem libraries.
	jimport ( 'joomla.filesystem.folder' );

	$parent->db = JFactory::getDBO ();
	$upgraded = false;
	// Convert all old polls tables to new structure
	$tablelist = $parent->db->getTableList ();
	foreach ( $tablelist as $table ) {
		if ($table == $parent->db->getPrefix () . 'kunena_polls') {
			$fields = array_pop ( $parent->db->getTableFields ( $parent->db->getPrefix () . 'kunena_polls' ) );
			if (isset ( $fields ['catid'] ) && isset ( $fields ['polltimetolive'] )) {
				$query = "ALTER TABLE {$parent->db->nameQuote($parent->db->getPrefix().'kunena_polls')} DROP COLUMN catid, MODIFY title varchar(50)";
				$parent->db->setQuery ( $query );
				$parent->db->query ();
				if ($parent->db->getErrorNum ())
					throw new KunenaInstallerException ( $parent->db->getErrorMsg (), $parent->db->getErrorNum () );

				if (JDEBUG == 1 && defined ( 'JFIREPHP' )) {
					FB::log ( $query, 'kunena_polls Upgrade, removing catid field' );
				}
				$upgraded = true;
			}
			if (isset ( $fields ['catid'] ) && ! isset ( $fields ['polltimetolive'] )) {
				$query = "ALTER TABLE {$parent->db->nameQuote($parent->db->getPrefix().'kunena_polls')} DROP COLUMN catid, MODIFY title varchar(50),ADD `polltimetolive` timestamp";
				$parent->db->setQuery ( $query );
				$parent->db->query ();
				if ($parent->db->getErrorNum ())
					throw new KunenaInstallerException ( $parent->db->getErrorMsg (), $parent->db->getErrorNum () );
				$upgraded = true;
			}
			if (isset ( $fields ['topicid'] ) && isset ( $fields ['voters'] ) && isset ( $fields ['options'] )) {
				$query = "ALTER TABLE {$parent->db->nameQuote($parent->db->getPrefix().'kunena_polls')} DROP COLUMN voters, DROP COLUMN options,CHANGE topicid threadid int(11), ADD polltimetolive timestamp";
				$parent->db->setQuery ( $query );
				$parent->db->query ();
				if ($parent->db->getErrorNum ())
					throw new KunenaInstallerException ( $parent->db->getErrorMsg (), $parent->db->getErrorNum () );

				if (JDEBUG == 1 && defined ( 'JFIREPHP' )) {
					FB::log ( $query, 'kunena_polls Upgrade, renaming topicid field' );
				}
				$upgraded = true;
			}
		}

		if ($table == $parent->db->getPrefix () . 'kunena_polls_datas') {
			$query = "DROP TABLE {$parent->db->nameQuote($parent->db->getPrefix().'kunena_polls_options')}";
			$parent->db->setQuery ( $query );
			$parent->db->query ();
			if ($parent->db->getErrorNum ())
				throw new KunenaInstallerException ( $parent->db->getErrorMsg (), $parent->db->getErrorNum () );

			$query = "ALTER TABLE {$parent->db->nameQuote($parent->db->getPrefix().'kunena_polls_datas')} MODIFY `id` int(11) AUTO_INCREMENT, MODIFY `text` varchar(100), CHANGE `hits` `votes` int(11)";
			$parent->db->setQuery ( $query );
			$parent->db->query ();
			if ($parent->db->getErrorNum ())
				throw new KunenaInstallerException ( $parent->db->getErrorMsg (), $parent->db->getErrorNum () );

			$query = "ALTER TABLE {$parent->db->nameQuote($parent->db->getPrefix().'kunena_polls_users')} DROP COLUMN `id`, ADD `votes` int(11), ADD `lasttime` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP, ADD `lastvote` int(11), ADD UNIQUE KEY `pollid` (pollid,userid)";
			$parent->db->setQuery ( $query );
			$parent->db->query ();
			if ($parent->db->getErrorNum ())
				throw new KunenaInstallerException ( $parent->db->getErrorMsg (), $parent->db->getErrorNum () );

			$query = "ALTER TABLE {$parent->db->nameQuote($parent->db->getPrefix().'kunena_polls_datas')} RENAME TO {$parent->db->nameQuote($parent->db->getPrefix().'kunena_polls_options')}";
			$parent->db->setQuery ( $query );
			$parent->db->query ();
			if ($parent->db->getErrorNum ())
				throw new KunenaInstallerException ( $parent->db->getErrorMsg (), $parent->db->getErrorNum () );

			$upgraded = true;
		}

		if ($table == $parent->db->getPrefix () . 'kunena_polls_options') {
			$fields = array_pop ( $parent->db->getTableFields ( $parent->db->getPrefix () . 'kunena_polls_options' ) );
			$query = "ALTER TABLE {$parent->db->nameQuote($parent->db->getPrefix().'kunena_polls_options')} MODIFY text varchar(50)";
			$parent->db->setQuery ( $query );
			$parent->db->query ();
			if ($parent->db->getErrorNum ())
				throw new KunenaInstallerException ( $parent->db->getErrorMsg (), $parent->db->getErrorNum () );
		}

		if ($table == $parent->db->getPrefix () . 'kunena_polls_users') {
			$fields = array_pop ( $parent->db->getTableFields ( $parent->db->getPrefix () . 'kunena_polls_users' ) );
			if (! isset ( $fields ['id'] ) && ! isset ( $fields ['lastvote'] )) {
				$query = "ALTER TABLE {$parent->db->nameQuote($parent->db->getPrefix().'kunena_polls_users')} MODIFY votes int(11), ADD lastvote int(11)";
				$parent->db->setQuery ( $query );
				$parent->db->query ();
				if ($parent->db->getErrorNum ())
					throw new KunenaInstallerException ( $parent->db->getErrorMsg (), $parent->db->getErrorNum () );

				if (JDEBUG == 1 && defined ( 'JFIREPHP' )) {
					FB::log ( $query, 'kunena_polls_users Upgrade without field id' );
				}
				$upgraded = true;
			}
		}
	}
	if ($upgraded)
		return array ('action' => '', 'name' => JText::_ ( 'COM_KUNENA_INSTALL_160_POLLS' ), 'success' => true );
}