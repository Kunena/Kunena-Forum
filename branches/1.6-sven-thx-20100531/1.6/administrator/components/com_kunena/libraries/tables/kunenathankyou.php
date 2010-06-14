<?php
/**
* @version $Id$
* Kunena Component - TableKunenaThankYou class
* @package Kunena
*
* @Copyright (C) 2010 www.kunena.com All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die();

require_once(dirname(__FILE__).DS.'kunena.php');

/**
* Kunena User Table
* Provides access to the #__kunena_users_banlist table
*/
class TableKunenaThankYou extends JTable{

	var $start	= null;
	var $end	= null;

	/**
	* Constructor
	*
	* @access	protected
	*/
	function __construct($db) {
		parent::__construct('#__kunena_thankyou', 'postid', $db);
	}

	/**
	  * # of Thank you
	  * @param start unix time
	  * @param end unix time
	  * @return int
	  * @since 1.6
	  */
	function loadTotalThankYou($start,$end){
		$where=array();
		if (!empty($start))
			$where[]="time > UNIX_TIMESTAMP('{$start}')";
		if (!empty($end))
			$where[]="time < UNIX_TIMESTAMP('{$end}')";
		$query="SELECT count(*) FROM {$this->_tbl}";
		if (count($where)>0)
			$query.=" WHERE ".implode(" AND ",$where);
		$this->_db->setQuery($query);
		$res = $this->_db->loadResult();

		// Check for an error message.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return $res;
	}

	/**
	 * Get user with most thankyou or said thankyou
	 * @param string $saidgot empty or 'said'
	 * @param string $limit how much ThankYou Users you want to view - default 10
	 * @return ObjectList returns a list of user
	 * @since 1.6
	 */
	function getMostThankYou($saidgot, $limit="10"){
		$uid = 'targetuserid';
		if($saidgot === 'said') $uid = 'userid';

		$query = "SELECT count(s.{$uid}) AS countid, u.username " .
				"FROM {$this->_tbl} AS s " .
				"INNER JOIN #__users AS u " .
				"WHERE s.{$uid}=u.id " .
				"GROUP BY s.{$uid} " .
				"ORDER BY countid DESC";
		$this->_db->setQuery($query, "0", $limit);
		$res = $this->_db->loadObjectList();

		// Check for an error message.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return $res;
	}

	/**
	 * Get the Messages with the most thankyous
	 * @param string number of messages you want to show
	 * @return Objectlist List of messages
	 * @since 1.6
	 */
	function getTopThankYouTopics($limit="10"){
		$query = "SELECT s.postid, count(s.postid) AS countid, u.id, u.catid,u.subject " .
				"FROM {$this->_tbl} AS s " .
				"INNER JOIN #__kunena_messages AS u " .
				"WHERE s.postid=u.id " .
				"GROUP BY s.postid " .
				"ORDER BY countid DESC";

		$this->_db->setQuery($query, "0" , $limit);
		$res = $this->_db->loadObjectList();

		// Check for an error message.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return $res;
	}

	/**
	 * Check if the user allready said thankyou
	 * @param int $pid
	 * @param int $userid
	 * @return int userid if hes in table else empty
	 * @since 1.6
	 */
	function checkIfThankYouAllready($pid,$userid){
		$query = "SELECT userid " .
				"FROM {$this->_tbl} " .
				"WHERE postid={$pid} " .
				"AND userid={$userid}";
		$this->_db->setQuery ( $query , '0' , '1');

		$res = $this->_db->loadResult();

		// Check for an error message.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return $res;
	}

	/**
	 * Perform insert the thank into table
	 * @param int $pid
	 * @param int $userid
	 * @param int $targetid
	 * @return bool true if succes
	 * @since 1.6
	 */
	function storeThankYou($pid, $userid, $targetid){
		$query = "INSERT INTO {$this->_tbl} SET postid={$pid} , userid={$userid} , targetuserid={$targetid}";
		$this->_db->setQuery( $query );
		$this->_db->query();

		// Check for an error message.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	/**
	 * Get the users who thank youd to that message
	 * @param int $pid
	 * @param string $named
	 * @param string number how much users you will show
	 * @return Objectlist List of users
	 * @since 1.6
	 */
	 function getThankYouUsers($pid, $named='', $limit='10'){
	 	$name = 'username';
	 	if($named === 'name') $name = $named.' AS username';
	 	$query	= "SELECT u.{$name}, u.id " .
	 			"FROM #__users AS u " .
	 			"LEFT JOIN {$this->_tbl} AS s " .
	 			"ON u.id = s.userid " .
	 			"WHERE s.postid={$pid}";
	 	$this->_db->setQuery($query, '0', $limit);
	 	$res = $this->_db->loadObjectList();

	 	// Check for an error message.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return $res;
	 }

	/**
	 * Get the Messages where a user said Thank You or got Thank You
	 * @param int $userid
	 * @param string $saidgot have value 'said' or empty
	 * @param string $limit number of messages you want default 10
	 * @return Objectlist List of the wanted messages
	 */
	 function getThankYouPosts($userid,$saidgot,$limit="10"){
	 	$sg = 'targetuserid';
	 	if($saidgot === 'said') $sg= 'userid';
	 	$query = "SELECT m.thread, m.id " .
	 			"FROM #__kunena_messages AS m " .
	 			"INNER JOIN {$this->_tbl} AS t " .
	 			"ON m.id=t.postid " .
	 			"WHERE t.{$sg}={$userid}";
	 	$this->_db->setQuery($query, '0' , $limit);

	 	$res = $this->_db->loadObjectList();

	 	// Check for an error message.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return $res;

	 }

}
?>
