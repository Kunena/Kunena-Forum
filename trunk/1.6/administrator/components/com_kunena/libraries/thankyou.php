<?php
/**
 * @version $Id$
 * Kunena Component - Thank You class
 * @package Kunena
 *
 * @Copyright (C) 2008-2011 www.kunena.org All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();

/**
 * Kunena Thank You
 *
 */
class KunenaThankYou extends JObject {
	/**
	 * Constructor
	 *
	 * @access	protected
	 */
	public function __construct() {
		$this->_db = JFactory::getDBO ();
	}
	/**
	 * Returns a reference to the kunena thankyou table object
	 * @return A database object
	 * @since 1.6
	 */
	public function getTable() {
		// Create the user table object
		return JTable::getInstance ( 'KunenaThankYou', 'Table' );
	}
	/**
	 * Total number of Thank you
	 * @param start unix time
	 * @param end unix time
	 * @return int
	 * @since 1.6
	 */
	public function getTotalThankYou($start = null, $end = null) {
		// Create the user table object
		$table = self::getTable ();
		return $table->getTotal ( $start, $end );
	}

	/**
	 * most thank you
	 * @param string $saidgot
	 * @return array
	 * @since 1.6
	 */
	public function getMostThankYou($saidgot = '') {
		$table = self::getTable ();
		return $table->getTopUsers ( $saidgot );
	}

	/**
	 * topics with most thank you
	 * @return array
	 * @since 1.6
	 */
	public function getTopThankYouTopics() {
		$table = self::getTable ();
		return $table->getTopTopics ();
	}
	/**
	 * Check if thx already
	 * @param int $pid
	 * @param int $userid
	 * @return int userid if its in table else empty
	 * @since 1.6
	 */
	public function checkIfThankYouAllready($pid, $userid) {
		$table = self::getTable ();
		return $table->checkIfExists ( $pid, $userid );
	}
	/**
	 * Write thx
	 * @param int $pid
	 * @param int $userid
	 * @param int $targetid
	 * @return bool true if success
	 * @since 1.6
	 */
	public function storeThankYou($pid, $userid, $targetid) {
		$table = self::getTable ();
		return $table->storeThankYou ( $pid, $userid, $targetid );
	}

	/**
	 * Get the users who thank youd to that message
	 * @param string $named
	 * @return Objectlist List of users
	 * @since 1.6
	 */
	public function getThankYouUsers($pid, $named = '') {
		$table = self::getTable ();
		return $table->getUsers ( $pid, $named );
	}

	public function getThankYouPosts($userid, $saidgot, $limit = 10) {
		$table = self::getTable ();
		return $table->getPosts ( $userid, $saidgot, $limit );
	}
}
