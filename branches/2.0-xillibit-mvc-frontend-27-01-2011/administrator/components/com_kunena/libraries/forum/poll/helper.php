<?php
/**
 * @version $Id: helper.php 4299 2011-01-27 18:57:10Z mahagr $
 * Kunena Component - KunenaForumPollHelper Class
 * @package Kunena
 *
 * @Copyright (C) 2010 www.kunena.org All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

kimport ('kunena.error');
kimport ('kunena.user');
kimport ('kunena.forum.poll');

/**
 * Kunena Forum Poll Helper Class
 */
class KunenaForumPollHelper {
	protected static $_instances = array();

	private function __construct() {}

	/**
	 * Returns KunenaForumTopic object
	 *
	 * @access	public
	 * @param	identifier		The poll to load - Can be only an integer.
	 * @return	KunenaForumPoll		The poll object.
	 * @since	2.0
	 */
	static public function get($identifier = null, $reload = false) {
		if ($identifier instanceof KunenaForumPoll) {
			return $identifier;
		}
		$id = intval ( $identifier );
		if ($id < 1)
			return new KunenaForumPoll ();

		if ($reload || empty ( self::$_instances [$id] )) {
			self::$_instances [$id] = new KunenaForumPoll ( $id );
		}

		return self::$_instances [$id];
	}

	static public function getPollData($id) {
		$db = JFactory::getDBO ();
		$query = "SELECT a.*,b.*,b.id AS poll_option_id
					FROM #__kunena_polls AS a
					INNER JOIN #__kunena_polls_options AS b ON a.threadid=b.pollid
					WHERE a.threadid={$db->Quote($id)}";
		$db->setQuery($query);
		$polldata = $db->loadObjectList();
		KunenaError::checkDatabaseError();

		return $polldata;
	}

	static public function getUsersVotes($id) {
		$db = JFactory::getDBO ();
		$query = "SELECT pollid,userid,name,username
					FROM #__kunena_polls_users AS a
					INNER JOIN #__users AS b ON a.userid=b.id
					WHERE pollid={$db->Quote($id)}";
		$db->setQuery($query);
		$uservotedata = $db->loadObjectList();
		KunenaError::checkDatabaseError();

		return $uservotedata;
	}

	static public function getTotalVoters() {
		$db = JFactory::getDBO ();
		$query = "SELECT SUM(votes) FROM #__kunena_polls_users WHERE pollid={$db->Quote($pollid)}";
		$db->setQuery($query);
		$numvotes = $db->loadResult();
		KunenaError::checkDatabaseError();

		return $numvotes;
	}

	static public function userHasAlreadyVoted() {
		// return the vote number of the user specified
	}

	static public function canVote() {
		// check if it's not a guest

		// check if the votes number is below $config->pollnbvotesbyuser

		// check if the user can vote only one time or more with the config setting $config->pollnbvotesbyuser

		// check if the time between the previous vote and this one is ok, this is for prevent flood

		return true;
	}
}
