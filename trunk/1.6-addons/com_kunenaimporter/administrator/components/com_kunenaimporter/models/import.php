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
	}

	function getImportOptions() {
		// version
		$options = array('config', 'userprofile', 'categories', 'messages', 'attachments', 'favorites', 'subscriptions', 'moderation', 'ranks', 'smilies', 'announcements', 'sessions', 'whoisonline');
		return $options;
	}

	function commitStart() {
		$db =& JFactory::getDBO();
		$query="SET autocommit=0;";
		$db->setQuery($query);
		$result = $db->query() or die("<br />Disabling autocommit failed:<br />$query<br />" . $db->errorMsg());
	}

	function commitEnd() {
		$db =& JFactory::getDBO();
		$query="COMMIT;";
		$db->setQuery($query);
		$result = $db->query() or die("<br />Commit failed:<br />$query<br />" . $db->errorMsg());
		$query="SET autocommit=1;";
		$db->setQuery($query);
		$result = $db->query() or die("<br />Enabling autocommit failed:<br />$query<br />" . $db->errorMsg());
	}

	function disableKeys($table) {
		$db =& JFactory::getDBO();
		$query="ALTER TABLE {$table} DISABLE KEYS";
		$db->setQuery($query);
		$result = $db->query() or die("<br />Disable keys failed:<br />$query<br />" . $db->errorMsg());
	}

	function enableKeys($table) {
		$db =& JFactory::getDBO();
		$query="ALTER TABLE {$table} ENABLE KEYS";
		$db->setQuery($query);
		$result = $db->query() or die("<br />Enable keys failed:<br />$query<br />" . $db->errorMsg());
	}

	function setAuthMethod($auth_method) {
		$this->auth_method = $auth_method;
	}

	function getUsername($name) {
		//if ($this->auth_method == 'joomla') return $name;
		return strtr($name, "<>\"'%;()&", '_________');
	}

	function mapUser($extuserid, $username=null, $email=null, $registerDate=null) {
		//if ($this->auth_method == 'joomla') return $extuserid;

		$db =& JFactory::getDBO();
		// Check if we have already mapped our user
		$query = "SELECT * FROM `#__knimport_extuser` WHERE extuserid=".$db->quote($extuserid);
		$db->setQuery($query, 0, 1);
		$user = $db->loadObject();
		if (is_object($user) && $user->userid > 0) return $user->userid;
		if (empty($username)) return 0;

		// Check if user exists in Joomla
		$query = "SELECT id, username, email, registerDate FROM `#__users` WHERE username=".$db->quote($this->getUsername($username))." OR email=".$db->quote($email)." OR registerDate=".$db->quote($registerDate);
		$db->setQuery($query);
		$userlist = $db->loadObjectList('id');

		$bestpoints = 0;
		$bestid = 0;
		foreach ($userlist as $user) {
			$points = 0;
			if ($username == $user->username) $points++;
			if ($this->getUsername($username) == $user->username) $points++;
			if ($registerDate == $user->registerDate) $points+=2;
			if ("1970-01-01 02:00:00" == $user->registerDate) $points+=1;
			if ($email == $user->email) $points+=2;
			if ($points == 6) return $user->id;
			echo "User: $username ($registerDate) vs $user->username ($user->registerDate): ";
			if ($username == $user->username) { echo "u"; }
			if ($this->getUsername($username) == $user->username) { echo "u"; }
			if ($registerDate == $user->registerDate) { echo "rr"; }
			if ("1970-01-01 02:00:00" == $user->registerDate) { echo "r"; }
			if ($email == $user->email) { echo "ee"; }
			echo "<br />";
			if ($points > $bestpoints) {
				$bestpoints = $points;
				$bestid = $user->id;
			}
		}
		unset ($userlist);
		if ($bestpoints >= 3) return -$bestid;
		return 0;
	}

	function truncateUsersMap() {
		$db =& JFactory::getDBO();
		$query="TRUNCATE TABLE `#__knimport_extuser`";
		$db->setQuery($query);
		$result = $db->query() or die("<br />Invalid query:<br />$query<br />" . $db->errorMsg());
	}

	function mapUsers(&$users) {
		$db =& JFactory::getDBO();

		$query = "SELECT id, name FROM `#__core_acl_aro_groups`";
		$db->setQuery($query);
		$groups = $db->loadObjectList('name');

		foreach ($users as $userdata)
		{
			$conflict = 0;
			$error = '';
			$userdata->extname = $userdata->username;
			$userdata->username = $this->getUsername($userdata->username);
			$uid = $this->mapUser($userdata->extuserid, $userdata->extname, $userdata->email, $userdata->registerDate);

			$userdata->gid = $groups[$userdata->usertype]->id;

			$extuser = JTable::getInstance('ExtUser', 'CKunenaTable');
			$extuser->load($userdata->extuserid);
			if ($extuser->userid === NULL) {
				$extuserdata = array ('userid'=>abs($uid), 'extuserid'=>$userdata->extuserid, 'extname'=>$userdata->extname, 'conflict'=>$conflict, 'error'=>$error);
				if ($extuser->save($extuserdata) === false) {
					echo "ERROR: Saving external data for $userdata->username failed: ". $extuser->getError() ."<br />";
				}
			}
		}
	}

	function UpdateCatStats() {
		$db =& JFactory::getDBO();
		// Update last message time from all categories.
		$query="UPDATE `#__fb_categories`, `#__fb_messages` SET `#__fb_categories`.time_last_msg=`#__fb_messages`.time WHERE `#__fb_categories`.id_last_msg=`#__fb_messages`.id AND `#__fb_categories`.id_last_msg>0";
		$db->setQuery($query); 
		$result = $db->query() or die("<br />Invalid query:<br />$query<br />" . $db->errorMsg());
		unset($query);
	}

	function truncateData($option) {
		if ($option == 'config') return;
		if ($option == 'messages') $this->truncateData($option.'_text');
		$db =& JFactory::getDBO();
		$table =& JTable::getInstance($option, 'CKunenaTable');
		$query="TRUNCATE TABLE ".$db->nameQuote($table->getTableName());
		$db->setQuery($query);
		$result = $db->query() or die("<br />Invalid query:<br />$query<br />" . $db->errorMsg());
	}

	function truncateJoomlaUsers() {
		// Leave only Super Administrators
		$db =& JFactory::getDBO();
		$query="DELETE FROM #__users WHERE gid != 25";
		$db->setQuery($query);
		$result = $db->query() or die("<br />Invalid query:<br />$query<br />" . $db->errorMsg());
		$query="ALTER TABLE `#__users` AUTO_INCREMENT = 0";
		$db->setQuery($query);
		$result = $db->query() or die("<br />Invalid query:<br />$query<br />" . $db->errorMsg());
		$query="DELETE #__core_acl_aro AS a FROM #__core_acl_aro AS a LEFT JOIN #__users AS u ON a.value=u.id WHERE u.id IS NULL";
		$db->setQuery($query);
		$result = $db->query() or die("<br />Invalid query:<br />$query<br />" . $db->errorMsg());
		$query="DELETE FROM #__core_acl_groups_aro_map WHERE group_id != 25";
		$db->setQuery($query);
		$result = $db->query() or die("<br />Invalid query:<br />$query<br />" . $db->errorMsg());
		$query="ALTER TABLE `#__core_acl_aro` AUTO_INCREMENT = 0";
		$db->setQuery($query);
		$result = $db->query() or die("<br />Invalid query:<br />$query<br />" . $db->errorMsg());
	}

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
		$db =& JFactory::getDBO();

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
		$db =& JFactory::getDBO();

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
		$db =& JFactory::getDBO();
		$this->commitStart();
		$msgtable =& JTable::getInstance('messages', 'CKunenaTable');
		$txttable =& JTable::getInstance('messages_text', 'CKunenaTable');
		foreach ($messages as $message)
		{
			$message->mesid = $message->id;
			if (!isset($message->extuserid)) $message->extuserid = $message->userid;
			$message->userid = $this->mapUser($message->extuserid);
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
		$db =& JFactory::getDBO();

		foreach ($users as $userdata)
		{
			$conflict = 0;
			$error = '';
			$userdata->extname = $userdata->username;
			$userdata->username = $this->getUsername($userdata->username);
			$uid = $this->mapUser($userdata->extuserid, $userdata->extname, $userdata->email, $userdata->registerDate);

			if ($uid > 0) {
				$query = "INSERT INTO `#__fb_users` (userid, posts, signature) VALUES ('$uid', '$userdata->user_posts', '$userdata->user_sig')";
				$db->setQuery($query);
				$result = $db->query() or die("<br />Invalid query ($userdata->username):<br />$query<br />" . $db->errorMsg());
			}
		}
	}

}

?>
