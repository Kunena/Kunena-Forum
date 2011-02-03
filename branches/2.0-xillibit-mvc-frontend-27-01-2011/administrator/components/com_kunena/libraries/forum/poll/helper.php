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

	static public function getTotalVoters($id) {
		$db = JFactory::getDBO ();
		$query = "SELECT SUM(votes) FROM #__kunena_polls_users WHERE pollid={$db->Quote($id)}";
		$db->setQuery($query);
		$numvotes = $db->loadResult();
		KunenaError::checkDatabaseError();

		return $numvotes;
	}

	static public function getTimediffLastVote($id) {
		$db = JFactory::getDBO ();
		$user = KunenaUser::getInstance($user);
		$query = "SELECT pollid,userid,lasttime,votes,
						TIMEDIFF(CURTIME(),DATE_FORMAT(lasttime, '%H:%i:%s')) AS timediff
					FROM #__kunena_polls_users
					WHERE pollid={$db->Quote($id)} AND userid={$db->Quote($user->userid)}";
		$db->setQuery($query);
		$timediff = $db->loadObject();
		KunenaError::checkDatabaseError();

		return $timediff;
	}

	static public function userHasAlreadyVoted($id) {
		$user = KunenaUser::getInstance($user);
		$db = JFactory::getDBO ();

		$query = "SELECT votes FROM #__kunena_polls_users WHERE pollid={$db->Quote($id)} AND userid={$db->Quote($user->userid)};";
		$db->setQuery($query);
		$votes = $db->loadResult();
		if (KunenaError::checkDatabaseError()) return;

		return $votes;
	}

	static public function canVote($id) {
		$config = KunenaFactory::getConfig();
		$timevotedatas = self::getTimediffLastVote($id);
		$poll = self::get($id);
		$user = KunenaUser::getInstance($user);

		if ( !$user->userid ) return false;

		if ( $timevotedatas->votes > $config->pollnbvotesbyuser || $timevotedatas->votes > 1 && $config->pollallowvoteone ) return false;

		if ( $timevotedatas->lasttime != '0000-00-00 00:00:00' ) {
			if ( $timevotedatas->timediff > $config->polltimebtvotes ) return false;
		}

		return true;
	}

	static public function saveVote($id, $vote) {
		$db = JFactory::getDBO ();
		$now	= JFactory::getDate();
		$user = KunenaUser::getInstance($user);
		if ( self::canVote($id) ) {
			if ( self::userHasAlreadyVoted($id) ) {
				$query = "UPDATE #__kunena_polls_users SET votes=votes+1,lastvote={$db->Quote($vote)},lasttime={$db->Quote($now->toMySQL())} WHERE pollid={$db->Quote($id)} AND userid={$db->Quote($user->userid)};";
				$db->setQuery($query);
				$db->query();
				if (KunenaError::checkDatabaseError()) return;
			} else {
				$query = "INSERT INTO #__kunena_polls_users (pollid,userid,votes,lasttime,lastvote) VALUES({$db->Quote($id)},{$db->Quote($user->userid)},'1',{$db->Quote($now->toMySQL())},{$db->Quote($vote)});";
				$db->setQuery($query);
				$db->query();
				if (KunenaError::checkDatabaseError()) return;
			}
			$query = "UPDATE #__kunena_polls_options SET votes=votes+1 WHERE id={$db->Quote($vote)};";
			$db->setQuery($query);
			$db->query();
			if (KunenaError::checkDatabaseError()) return;

			return true;
		} else {
			return false;
		}
	}

	static protected function reset_vote($userid,$id) {
		$db 	= JFactory::getDBO ();
		$query = "SELECT a.id, a.pollid,a.votes AS option_votes, b.votes AS user_votes, b.lastvote, b.userid FROM #__kunena_polls_options AS a
				INNER JOIN #__kunena_polls_users AS b ON a.id=b.lastvote
				WHERE a.pollid={$this->_db->Quote($id)} AND b.userid={$db->Quote($userid)}";
		$db->setQuery($query);
		$poll_options_user = $db->loadObject();
		if (KunenaError::checkDatabaseError()) return;


		if($poll_options_user->option_votes > '0' && $poll_options_user->user_votes > '0') {
			$query = "UPDATE #__kunena_polls_options SET votes=votes-1 WHERE id={$db->Quote($poll_options_user->lastvote)} AND pollid={$db->Quote($id)};";
			$db->setQuery($query);
			$db->query();
			if (KunenaError::checkDatabaseError()) return;

			$query = "UPDATE #__kunena_polls_users SET votes=votes-1 WHERE userid={$db->Quote($userid)} AND pollid={$db->Quote($id)};";
			$db->setQuery($query);
			$db->query();
			if (KunenaError::checkDatabaseError()) return;

			return true;
		}
		return false;
	}

	static public function saveChangedVote($id, $vote) {
		$db 	= JFactory::getDBO ();
		$now	= JFactory::getDate();
		$user 	= KunenaUser::getInstance($user);

		if ( !$user->userid ) return false;

		if ( self::getTimediffLastVote($id) > $config->polltimebtvotes ) return false;

		if ( self::reset_vote($user->userid,$id) ) {
			$query = "UPDATE #__kunena_polls_options SET votes=votes+1 WHERE id={$db->Quote($vote)};";
			$db->setQuery($query);
			$db->query();
			if (KunenaError::checkDatabaseError()) return;

			$query = "UPDATE #__kunena_polls_users SET votes=votes+1, lastvote={$db->Quote($vote)}, lasttime={$db->Quote($now->toMySQL())} WHERE pollid={$db->Quote($id)} AND userid={$db->Quote($user->userid)};";
			$db->setQuery($query);
			$db->query();
			if (KunenaError::checkDatabaseError()) return;

			return true;
		}
		return false;
	}

	static public function saveUpdatedVote($id, $fields=array()) {
		if ( !$id) {
		// save a new poll

		} else {
		// delete the poll if the user has let blank all fields

		// Update poll
		}
	}
}
