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

// TODO: Better Error detection
// TODO: User Mapping


// Import Joomla! libraries
jimport ( 'joomla.application.component.model' );

// Everything else than user import can be found from here:
require_once (JPATH_COMPONENT . DS . 'models' . DS . 'kunena.php');

class KunenaimporterModelImport extends JModel {
	function __construct() {
		parent::__construct ();
		$this->db = JFactory::getDBO ();
		$this->db->debug = 0;
	}

	function getImportOptions() {
		// version
		$options = array ('users','mapusers','config', 'userprofile', 'categories', 'messages', 'attachments', 'favorites', 'subscriptions', 'moderation', 'ranks', 'smilies', 'announcements', 'sessions', 'whoisonline' );
		return $options;
	}

	function commitStart() {
		$query = "SET autocommit=0;";
		$this->db->setQuery ( $query );
		$result = $this->db->query () or die ( "<br />Disabling autocommit failed:<br />$query<br />" . $this->db->errorMsg () );
	}

	function commitEnd() {
		$query = "COMMIT;";
		$this->db->setQuery ( $query );
		$result = $this->db->query () or die ( "<br />Commit failed:<br />$query<br />" . $this->db->errorMsg () );
		$query = "SET autocommit=1;";
		$this->db->setQuery ( $query );
		$result = $this->db->query () or die ( "<br />Enabling autocommit failed:<br />$query<br />" . $this->db->errorMsg () );
	}

	function disableKeys($table) {
		$query = "ALTER TABLE {$table} DISABLE KEYS";
		$this->db->setQuery ( $query );
		$result = $this->db->query () or die ( "<br />Disable keys failed:<br />$query<br />" . $this->db->errorMsg () );
	}

	function enableKeys($table) {
		$query = "ALTER TABLE {$table} ENABLE KEYS";
		$this->db->setQuery ( $query );
		$result = $this->db->query () or die ( "<br />Enable keys failed:<br />$query<br />" . $this->db->errorMsg () );
	}

	function setAuthMethod($auth_method) {
		$this->auth_method = $auth_method;
	}

	function getUsername($name) {
		//if ($this->auth_method == 'joomla') return $name;
		return strtr ( $name, "<>\"'%;()&", '_________' );
	}

	function findPotentialUsers($extuser, $all = false) {
		// Check if user exists in Joomla
		$query = "SELECT u.*
		FROM `#__users` AS u
		LEFT JOIN `#__kunenaimporter_users` AS e ON e.id=u.id
		WHERE (u.username LIKE {$this->db->quote($extuser->username)}
		OR u.email LIKE {$this->db->quote($extuser->email)})";
		if (!$all) {
			$query .= " AND e.id IS NULL";
		}
		else if ($extuser->id) {
			$query .= " OR u.id={$this->db->quote($extuser->id)}";
		}
		$this->db->setQuery ( $query );
		$userlist = $this->db->loadObjectList ( 'id' );

		$bestpoints = 0;
		$bestid = 0;
		$newlist = array ();
		foreach ( $userlist as $user ) {
			$points = 0;
			if (strtolower($extuser->username) == strtolower($user->username))
				$points += 2;
			if (strtolower($extuser->email) == strtolower($user->email))
				$points += 1;

			$user->points = $points;
			$newlist [$points] = $user;
		}
		krsort ( $newlist );
		return $newlist;
	}

	function mapUser($extuser) {
		if ($extuser->id !== null)
			return $extuser->id;

		$userlist = $this->findPotentialUsers ( $extuser );
		$best = array_shift ( $userlist );
		if (!$best)
			return 0;
		if (!empty($userlist))
			return -$best->id;
		return $best->id;
	}

	function truncateUsersMap() {
		$query = "TRUNCATE TABLE `#__kunenaimporter_users`";
		$this->db->setQuery ( $query );
		$result = $this->db->query () or die ( "<br />Invalid query:<br />$query<br />" . $this->db->errorMsg () );
	}

	function mapUsers($result, $limit) {
		if (!$result['total']) {
			$query = "SELECT COUNT(*) FROM `#__kunenaimporter_users` WHERE id IS NULL";
			$this->db->setQuery ( $query );
			$result['total'] = $this->db->loadResult ();
		}
		$query = "SELECT * FROM `#__kunenaimporter_users` WHERE id IS NULL AND extid > ".intval($result['start']);
		$this->db->setQuery ( $query, 0, $limit );
		$users = $this->db->loadAssocList ();

		$result['now'] = 0;
		foreach ( $users as $userdata ) {
			$result['start'] = $userdata['extid'];
			$result['now']++;
			$extuser = JTable::getInstance ( 'ExtUser', 'CKunenaTable' );
			$extuser->bind ( $userdata );
			unset($userdata);
			$extuser->exists ( true );
			$uid = $this->mapUser ( $extuser );
			$result['all']++;
			if (!$uid) {
				continue;
			}
			$userdata = array();
			if ($uid > 0) {
				$userdata['id'] = abs ( $uid );
				$result['new']++;
			} else {
				$userdata['conflict'] = abs ( $uid );
				$result['conflict']++;
			}
			if ($extuser->save ( $userdata ) === false) {
				$result['failed']++;
				echo "ERROR: Saving external {$extuser->username} failed: " . $extuser->getError () . "<br />";
			} elseif ($uid > 0) {
				$this->updateUserData(-$extuser->extid, $uid);
			}
		}
		unset($users);
		return $result;
	}

	function createUsers(&$users) {
		foreach ( $users as $userdata ) {
		}
	}

	function UpdateCatStats() {
		// Update last message time from all categories.
		$query = "UPDATE `#__kunena_categories`, `#__kunena_messages` SET `#__kunena_categories`.time_last_msg=`#__kunena_messages`.time WHERE `#__kunena_categories`.id_last_msg=`#__kunena_messages`.id AND `#__kunena_categories`.id_last_msg>0";
		$this->db->setQuery ( $query );
		$result = $this->db->query () or die ( "<br />Invalid query:<br />$query<br />" . $this->db->errorMsg () );
		unset ( $query );
	}

	function truncateData($option) {
		if ($option == 'config')
			return;
		if ($option == 'mapusers')
			return;
		if ($option == 'users')
			$option = 'extuser';
		if ($option == 'messages')
			$this->truncateData ( $option . '_text' );
		$this->db = JFactory::getDBO ();
		$table = JTable::getInstance ( $option, 'CKunenaTable' );
		$query = "TRUNCATE TABLE " . $this->db->nameQuote ( $table->getTableName () );
		$this->db->setQuery ( $query );
		$result = $this->db->query () or die ( "<br />Invalid query:<br />$query<br />" . $this->db->errorMsg () );
	}

	/*
	function truncateJoomlaUsers() {
		// Leave only Super Administrators
		$this->db =& JFactory::getDBO();
		$query="DELETE FROM #__users WHERE gid != 25";
		$this->db->setQuery($query);
		$result = $this->db->query() or die("<br />Invalid query:<br />$query<br />" . $this->db->errorMsg());
		$query="ALTER TABLE `#__users` AUTO_INCREMENT = 0";
		$this->db->setQuery($query);
		$result = $this->db->query() or die("<br />Invalid query:<br />$query<br />" . $this->db->errorMsg());
		$query="DELETE #__core_acl_aro AS a FROM #__core_acl_aro AS a LEFT JOIN #__users AS u ON a.value=u.id WHERE u.id IS NULL";
		$this->db->setQuery($query);
		$result = $this->db->query() or die("<br />Invalid query:<br />$query<br />" . $this->db->errorMsg());
		$query="DELETE FROM #__core_acl_groups_aro_map WHERE group_id != 25";
		$this->db->setQuery($query);
		$result = $this->db->query() or die("<br />Invalid query:<br />$query<br />" . $this->db->errorMsg());
		$query="ALTER TABLE `#__core_acl_aro` AUTO_INCREMENT = 0";
		$this->db->setQuery($query);
		$result = $this->db->query() or die("<br />Invalid query:<br />$query<br />" . $this->db->errorMsg());
	}
	*/

	function importData($option, &$data) {
		switch ($option) {
			case 'config' :
				$newConfig = end ( $data );
				if (is_object ( $newConfig ))
					$newConfig = $newConfig->GetClassVars ();
				$kunenaConfig = new CKunenaTableConfig ();
				$kunenaConfig->save ( $newConfig );
				break;
			case 'messages' :
				$this->importMessages ( $data );
				break;
			case 'subscriptions' :
			case 'favorites' :
				$this->importUnique ( $option, $data );
				break;
			case 'mapusers':
				break;
			case 'users':
				$option = 'extuser';
			default :
				$this->importDefault ( $option, $data );
		}
	}

	function importUnique($option, &$data) {
		$table = JTable::getInstance ( $option, 'CKunenaTable' );
		if (! $table)
			die ( $option );

		$extids = array();
		foreach ( $data as $item ) {
			if (!empty($item->userid)) $extids[$item->userid] = $item->userid;
		}
		$extuser = JTable::getInstance ( 'ExtUser', 'CKunenaTable' );
		$idmap = $extuser->loadIdMap($extids);

		$this->commitStart ();
		foreach ( $data as $item ) {
			if (!empty($item->userid)) {
				$item->userid = $idmap[$item->userid]->id ? $idmap[$item->userid]->id : -$idmap[$item->userid]->extid;
			}
			if ($table->save ( $item ) === false) {
				if (! strstr ( $table->getError (), 'Duplicate entry' ))
					die ( "<br />ERROR: " . $table->getError () );
			}
		}
		$this->commitEnd ();
	}

	function importDefault($option, &$data) {
		$table = JTable::getInstance ( $option, 'CKunenaTable' );
		if (! $table)
			die ( $option );

		$extids = array();
		foreach ( $data as $item ) {
			if (!empty($item->userid)) $extids[$item->userid] = $item->userid;
		}
		$extuser = JTable::getInstance ( 'ExtUser', 'CKunenaTable' );
		$idmap = $extuser->loadIdMap($extids);

		$this->commitStart ();
		foreach ( $data as $item ) {
			if (isset($idmap[$item->userid])) {
				$item->userid = $idmap[$item->userid]->id ? $idmap[$item->userid]->id : -$idmap[$item->userid]->extid;
			}
			if ($table->save ( $item ) === false)
				die ( "ERROR: " . $table->getError () );
		}
		$this->commitEnd ();
	}

	function importMessages(&$messages) {
		$extids = array();
		foreach ( $messages as $message ) {
			if (!empty($message->userid)) $extids[$message->userid] = $message->userid;
			if (!empty($message->modified_by)) $extids[$message->modified_by] = $message->modified_by;
		}
		$extuser = JTable::getInstance ( 'ExtUser', 'CKunenaTable' );
		$idmap = $extuser->loadIdMap($extids);

		$this->commitStart ();
		foreach ( $messages as $message ) {
			$msgtable = JTable::getInstance ( 'messages', 'CKunenaTable' );
			$txttable = JTable::getInstance ( 'messages_text', 'CKunenaTable' );
			if ($message->userid) {
				if ($idmap[$message->userid]->extid && $idmap[$message->userid]->lastvisitDate < $message->time - 86400) {
					// user MUST have been in the forum in the past 24 hours, update last visit..
					$extuser = JTable::getInstance ( 'ExtUser', 'CKunenaTable' );
					$extuser->load($message->userid);
					$extuser->lastvisitDate = $idmap[$message->userid]->lastvisitDate =$message->time;
					$extuser->save();
				}
				$message->userid = $idmap[$message->userid]->id ? $idmap[$message->userid]->id : -$idmap[$message->userid]->extid;
			}
			if ($message->modified_by) {
				$message->modified_by = $idmap[$message->modified_by]->id ? $idmap[$message->modified_by]->id : -$idmap[$message->modified_by]->extid;
			}
			$message->mesid = $message->id;
			if ($message->userid > 0 && (empty ( $message->email ) || empty ( $message->name ))) {
				$user = JUser::getInstance ( $message->userid );
				if (empty ( $message->email ))
					$message->email = $user->email;
				if (empty ( $message->name ))
					$message->name = $user->username;
			}

			if ($msgtable->save ( $message ) === false)
				die ( "ERROR: " . $msgtable->getError () );
			if ($txttable->save ( $message ) === false)
				die ( "ERROR: " . $txttable->getError () );
		}
		$this->commitEnd ();

		$this->updateCatStats ();
	}

	function updateUserData($oldid, $newid, $replace = false) {
		if ($replace) {
			$this->db->setQuery ( "DELETE FROM `#__kunena_users` WHERE `userid` = {$this->db->quote($oldid)}" );
			$this->db->query ();
		}
		$queries[] = "UPDATE `#__kunena_users` SET `userid` = {$this->db->quote($newid)} WHERE `userid` = {$this->db->quote($oldid)}";
		$queries[] = "UPDATE `#__kunena_attachments` SET `userid` = {$this->db->quote($newid)} WHERE `userid` = {$this->db->quote($oldid)}";
		$queries[] = "UPDATE `#__kunena_favorites` SET `userid` = {$this->db->quote($newid)} WHERE `userid` = {$this->db->quote($oldid)}";
		$queries[] = "UPDATE `#__kunena_messages` SET `userid` = {$this->db->quote($newid)} WHERE `userid` = {$this->db->quote($oldid)}";
		$queries[] = "UPDATE `#__kunena_messages` SET `modified_by` = {$this->db->quote($newid)} WHERE `modified_by` = {$this->db->quote($oldid)}";
		$queries[] = "UPDATE `#__kunena_moderation` SET `userid` = {$this->db->quote($newid)} WHERE `userid` = {$this->db->quote($oldid)}";
		$queries[] = "UPDATE `#__kunena_polls_users` SET `userid` = {$this->db->quote($newid)} WHERE `userid` = {$this->db->quote($oldid)}";
		//$queries[] = "UPDATE `#__kunena_sessions` SET `userid` = {$this->db->quote($newid)} WHERE `userid` = {$this->db->quote($oldid)}";
		$queries[] = "UPDATE `#__kunena_subscriptions` SET `userid` = {$this->db->quote($newid)} WHERE `userid` = {$this->db->quote($oldid)}";
		$queries[] = "UPDATE `#__kunena_subscriptions_categories` SET `userid` = {$this->db->quote($newid)} WHERE `userid` = {$this->db->quote($oldid)}";
		$queries[] = "UPDATE `#__kunena_thankyou` SET `userid` = {$this->db->quote($newid)} WHERE `userid` = {$this->db->quote($oldid)}";
		$queries[] = "UPDATE `#__kunena_thankyou` SET `targetuserid` = {$this->db->quote($newid)} WHERE `targetuserid` = {$this->db->quote($oldid)}";
		$queries[] = "UPDATE `#__kunena_users_banned` SET `userid` = {$this->db->quote($newid)} WHERE `userid` = {$this->db->quote($oldid)}";
		$queries[] = "UPDATE `#__kunena_users_banned` SET `created_by` = {$this->db->quote($newid)} WHERE `created_by` = {$this->db->quote($oldid)}";
		$queries[] = "UPDATE `#__kunena_users_banned` SET `modified_by` = {$this->db->quote($newid)} WHERE `modified_by` = {$this->db->quote($oldid)}";
		$queries[] = "UPDATE `#__kunena_whoisonline` SET `userid` = {$this->db->quote($newid)} WHERE `userid` = {$this->db->quote($oldid)}";

		foreach ($queries as $query) {
			$this->db->setQuery ( $query );
			$this->db->query ();
			if ($this->db->getErrorNum()) {
				$app = JFactory::getApplication ();
				$app->enqueueMessage ( "WARNING: Userid {$newid} is already in use! Cannot map user to it." );
				return false;
			}
		}
		return true;
	}
}