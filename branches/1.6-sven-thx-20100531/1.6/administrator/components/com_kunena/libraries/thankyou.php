<?php
/**
* @version $Id$
* Kunena Component - KunenaUserBan class
* @package Kunena
*
* @Copyright (C) 2010 www.kunena.com All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die();

/**
* Kunena Thank You
*
*/
class KunenaThankYou extends JObject
{
	/**
	* Constructor
	*
	* @access	protected
	*/
	public function __construct()
	{
		$this->_db = JFactory::getDBO ();
	}
	/**
	 * Returns a reference to the kunena thankyou table object
	 * @return A database object
	 * @since 1.6
	 */
	public function getTable()
	{
		// Create the user table object
		return JTable::getInstance('KunenaThankYou', 'Table');
	}
	 /**
	  * Total number of Thank you
	  * @param start unix time
	  * @param end unix time
	  * @return int
	  * @since 1.6
	  */
	public function get_total_thankyou($start='',$end=''){
	  	// Create the user table object
		$table = self::getTable();
		return $table->loadtotalthankyou($start,$end);
	  }

	/**
	* most thank you
	* @param string $saidgot
	* @return array
	* @since 1.6
	*/
	public function get_most_thankyou($saidgot=''){
	   	$table = self::getTable();
	   	return $table->getmostthankyou($saidgot);
	}

	/**
	* topics with most thank you
	* @return array
	* @since 1.6
	*/
	function get_top_thankyou_topics(){
		$table = self::getTable();
		return $table->gettopthankyoutopics();
	}
	/**
	 * Check if thx already
	 * @param int $pid
	 * @param int $userid
	 * @return int userid if its in table else empty
	 * @since 1.6
	 */
	function checkifthx($pid,$userid){
		$table = self::getTable();
		return $table->checkifthx($pid,$userid);
	}
	/**
	 * Write thx
	 * @param int $pid
	 * @param int $userid
	 * @param int $targetid
	 * @return bool true if success
	 * @since 1.6
	 */
	function insertthankyou($pid, $userid, $targetid){
		$table = self::getTable();
		return $table->insertthankyou($pid, $userid, $targetid);
	}

	/**
	 * Get the users who thank youd to that message
	 * @param string $named
	 * @return Objectlist List of users
	 * @since 1.6
	 */
	 function getthxusers($pid , $named ){
	 	$table = self::getTable();
	 	return $table->getthxusers($pid,$named);
	 }

	 function getthankyouposts($userid,$saidgot){
	 	$table= self::getTable();
	 	return $table->getThankyouPosts($userid, $saidgot);
	 }

}
?>
