<?php
/**
 * Joomla! 1.5 component: Kunena Forum Importer
 *
 * @version $Id$
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

// TODO: Better Error detection
// TODO: User Mapping

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');

// Everything else than user import can be found from here:
require_once( JPATH_COMPONENT.DS.'models'.DS.'kunena.php' );

class KunenaimporterModelImport extends JModel {
	function __construct() {
		parent::__construct();
		$this->db = JFactory::getDBO();
		$this->db->debug = 0;
	}

	function getImportOptions() {
		// version
		$options = array('config', 'userprofile', 'categories', 'messages', 'attachments', 'favorites', 'subscriptions', 'moderation', 'ranks', 'smilies', 'announcements', 'sessions', 'whoisonline');
		return $options;
	}

	function commitStart() {
		$query="SET autocommit=0;";
		$this->db->setQuery($query);
		$result = $this->db->query() or die("<br />Disabling autocommit failed:<br />$query<br />" . $this->db->errorMsg());
	}

	function commitEnd() {
		$query="COMMIT;";
		$this->db->setQuery($query);
		$result = $this->db->query() or die("<br />Commit failed:<br />$query<br />" . $this->db->errorMsg());
		$query="SET autocommit=1;";
		$this->db->setQuery($query);
		$result = $this->db->query() or die("<br />Enabling autocommit failed:<br />$query<br />" . $this->db->errorMsg());
	}

	function disableKeys($table) {
		$query="ALTER TABLE {$table} DISABLE KEYS";
		$this->db->setQuery($query);
		$result = $this->db->query() or die("<br />Disable keys failed:<br />$query<br />" . $this->db->errorMsg());
	}

	function enableKeys($table) {
		$query="ALTER TABLE {$table} ENABLE KEYS";
		$this->db->setQuery($query);
		$result = $this->db->query() or die("<br />Enable keys failed:<br />$query<br />" . $this->db->errorMsg());
	}

	function setAuthMethod($auth_method) {
		$this->auth_method = $auth_method;
	}

	function getUsername($name) {
		//if ($this->auth_method == 'joomla') return $name;
		return strtr($name, "<>\"'%;()&", '_________');
	}

	function findPotentialUsers($extuserid, $username=null, $email=null, $registerDate=null) {
				// Check if user exists in Joomla
		$query = "SELECT u.*, g.name AS groupname FROM `#__users` AS u INNER JOIN #__core_acl_aro_groups AS g ON g.id = u.gid WHERE u.username LIKE ".$this->db->quote($this->getUsername($username))." OR u.email LIKE ".$this->db->quote($email)." OR u.registerDate=".$this->db->quote($registerDate);
		$this->db->setQuery($query);
		$userlist = $this->db->loadObjectList('id');

		$bestpoints = 0;
		$bestid = 0;
		$newlist = array();
		foreach ($userlist as $user) {
			$points = 0;
			if ($username == $user->username) $points+=2;
			if ($this->getUsername($username) == $user->username) $points++;
			if ($registerDate == $user->registerDate) $points+=2;
			if ("0000-00-00 00:00:00" == $user->registerDate) $points+=1;
			if ($email == $user->email) $points+=2;

			$user->points = $points;
			$newlist[$points] = $user;
		}
		krsort($newlist);
		return $newlist;
	}

	function mapUser($extuserid, $username=null, $email=null, $registerDate=null) {
		//if ($this->auth_method == 'joomla') return $extuserid;

		// Check if we have already mapped our user
		$extuser = JTable::getInstance('ExtUser', 'CKunenaTable');
		$extuser->load($extuserid);
		if ($extuser->id > 0) return $extuser->id;
		if (empty($username)) return 0;

		$userlist = $this->findPotentialUsers($extuserid, $username, $email, $registerDate);
		$best = array_shift($userlist);
		if ($best->points >= 4) return $best->id;
		if ($best->points >= 3) return $best->id;
		return 0;
	}

	function truncateUsersMap() {
		$query="TRUNCATE TABLE `#__kunenaimporter_users`";
		$this->db->setQuery($query);
		$result = $this->db->query() or die("<br />Invalid query:<br />$query<br />" . $this->db->errorMsg());
	}

	function mapUsers(&$users) {
		$query = "SELECT id, name FROM `#__core_acl_aro_groups`";
		$this->db->setQuery($query);
		$groups = $this->db->loadObjectList('name');

		foreach ($users as $userdata)
		{
			$userdata->error = '';
			$userdata->extusername = $userdata->username;
			$userdata->username = $this->getUsername($userdata->username);
			$uid = $this->mapUser($userdata->extid, $userdata->extusername, $userdata->email, $userdata->registerDate);
			if ($uid > 0) $userdata->id = abs($uid);
			if ($uid < 0) $userdata->conflict = abs($uid);
			$userdata->gid = $groups[$userdata->usertype]->id;

			$extuser = JTable::getInstance('ExtUser', 'CKunenaTable');
			$extuser->load($userdata->extid);
			if ($extuser->username === NULL) {
				if ($extuser->save($userdata) === false) {
					echo "ERROR: Saving external data for $userdata->username failed: ". $extuser->getError() ."<br />";
				}
			}
		}
	}

	function UpdateCatStats() {
		// Update last message time from all categories.
		$query="UPDATE `#__fb_categories`, `#__fb_messages` SET `#__fb_categories`.time_last_msg=`#__fb_messages`.time WHERE `#__fb_categories`.id_last_msg=`#__fb_messages`.id AND `#__fb_categories`.id_last_msg>0";
		$this->db->setQuery($query);
		$result = $this->db->query() or die("<br />Invalid query:<br />$query<br />" . $this->db->errorMsg());
		unset($query);
	}

	function truncateData($option) {
		if ($option == 'config') return;
		if ($option == 'messages') $this->truncateData($option.'_text');
		$this->db =& JFactory::getDBO();
		$table =& JTable::getInstance($option, 'CKunenaTable');
		$query="TRUNCATE TABLE ".$this->db->nameQuote($table->getTableName());
		$this->db->setQuery($query);
		$result = $this->db->query() or die("<br />Invalid query:<br />$query<br />" . $this->db->errorMsg());
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
			case 'config':
				$newConfig = end($data);
				if (is_object($newConfig)) $newConfig = $newConfig->GetClassVars();
				$kunenaConfig = new CKunenaTableConfig();
				$kunenaConfig->save($newConfig);
				break;
			case 'messages':
				$this->importMessages($data);
				break;
			case 'subscriptions':
			case 'favorites':
				$this->importUnique($option, $data);
				break;
			default:
				$this->importDefault($option, $data);
		}
	}

	function importUnique($option, &$data) {
		$table =& JTable::getInstance($option, 'CKunenaTable');
		if (!$table) die($option);
		$this->commitStart();
		foreach ($data as $item)
		{
			if ($table->save($item) === false) {
				if (!strstr($table->getError(), 'Duplicate entry'))
					die("<br />ERROR: ". $table->getError());
			}
		}
		$this->commitEnd();
	}

	function importDefault($option, &$data) {
		$table =& JTable::getInstance($option, 'CKunenaTable');
		if (!$table) die($option);
		$this->commitStart();
		foreach ($data as $item)
		{
			if ($table->save($item) === false) die("ERROR: ". $table->getError());
		}
		$this->commitEnd();
	}

	function importMessages(&$messages) {
		$this->commitStart();
		$msgtable =& JTable::getInstance('messages', 'CKunenaTable');
		$txttable =& JTable::getInstance('messages_text', 'CKunenaTable');
		foreach ($messages as $message)
		{
			$message->mesid = $message->id;
			if (!isset($message->extuserid)) $message->extuserid = $message->userid;
			$message->userid = $this->mapUser($message->extuserid);
			if ($message->modified_by) $message->modified_by = $this->mapUser($message->modified_by);
			$user = JUser::getInstance($message->userid);
			if (empty($message->email)) $message->email = $user->email;
			if (empty($message->name)) $message->name = $user->username;

			if ($msgtable->save($message) === false) die("ERROR: ". $msgtable->getError());
			if ($txttable->save($message) === false) die("ERROR: ". $txttable->getError());
		}
		$this->commitEnd();

		$this->updateCatStats();
	}

	function importUsers(&$users) {
		foreach ($users as $userdata)
		{
			$conflict = 0;
			$error = '';
			$userdata->extname = $userdata->username;
			$userdata->username = $this->getUsername($userdata->username);
			$uid = $this->mapUser($userdata->extuserid, $userdata->extname, $userdata->email, $userdata->registerDate);

			if ($uid > 0) {
				$query = "INSERT INTO `#__fb_users` (userid, posts, signature) VALUES ('$uid', '$userdata->user_posts', '$userdata->user_sig')";
				$this->db->setQuery($query);
				$result = $this->db->query() or die("<br />Invalid query ($userdata->username):<br />$query<br />" . $this->db->errorMsg());
			}
		}
	}

}

?>
