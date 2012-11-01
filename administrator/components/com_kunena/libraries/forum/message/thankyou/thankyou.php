<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Forum.Message.Thankyou
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Kunena Forum Message Thank You Class
 */
class KunenaForumMessageThankyou extends JObject {
	protected $id = 0;
	protected $users = array();
	/**
	 * Constructor -- please use getInstance()
	 *
	 * @access	protected
	 */
	public function __construct($id) {
		$this->id = (int) $id;
	}

	/**
	 * Returns KunenaForumMessage object
	 *
	 * @access	public
	 * @param int $identifier The message to load - Can be only an integer.
	 * @param bool $reload
	 * @return	KunenaForumMessage		The message object.
	 */
	static public function getInstance($identifier = null, $reload = false) {
		return KunenaForumMessageThankyouHelper::get($identifier, $reload);
	}

	/**
	 * Check if the user has already said thank you
	 * @param int $pid
	 * @param int $userid
	 * @return int userid if hes in table else empty
	 * @since 1.6
	 */
	public function exists($userid) {
		return isset($this->users[$userid]);
	}

	public function _add($userid, $time) {
		$this->users[$userid] = $time;
	}

	/**
	 * Perform insert the thank you into table
	 * @param int $userid
	 * @return bool true if succes
	 * @since 1.6
	 */
	public function save($user) {
		$user = KunenaFactory::getUser($user);
		$message = KunenaForumMessageHelper::get($this->id);

		if (!$user->exists()) {
			$this->setError( JText::_('COM_KUNENA_THANKYOU_LOGIN') );
			return false;
		}
		if ($user->userid == $message->userid) {
			$this->setError( JText::_ ( 'COM_KUNENA_THANKYOU_NOT_YOURSELF' ) );
			return false;
		}
		if ($this->exists ( $user->userid )) {
			$this->setError( JText::_ ( 'COM_KUNENA_THANKYOU_ALLREADY' ) );
			return false;
		}

		$db = JFactory::getDBO ();
		$time = JFactory::getDate();
		$query = "INSERT INTO #__kunena_thankyou
			SET postid={$db->quote($this->id)} , userid={$db->quote($user->userid)} , targetuserid={$db->quote($message->userid)}, time={$db->quote($time->toMySQL())}";
		$db->setQuery ( $query );
		$db->query ();

		// Check for an error message.
		if ($db->getErrorNum ()) {
			$this->setError ( $db->getErrorMsg () );
			return false;
		}

		$this->_savethankyou($message);

		return true;
	}

	/**
	* Perform insert the received thank you into user table
	* @param int $userid
	* @return bool true if succes
	* @since 2.0
	*/
	protected function _savethankyou($message) {
		$db = JFactory::getDBO ();
		$query = "UPDATE #__kunena_users
				SET thankyou=thankyou+1 WHERE userid={$db->quote($message->userid)}";
		$db->setQuery ( $query );
		$db->query ();

		// Check for an error message.
		if ($db->getErrorNum ()) {
			$this->setError ( $db->getErrorMsg () );
			return false;
		}

		return true;
	}

	/**
	 * Get the users who have given thank you to message
	 * @param int $pid
	 * @param string $named
	 * @param string number how much users you will show
	 * @return array List of userid=>time
	 * @since 1.6
	 */
	public function getList() {
		return $this->users;
	}

	/**
	 * Perform delete the thank you into table
	 * @param int $userid
	 * @return bool true if succes
	 * @since 1.6
	 */
	public function delete($user) {
		$user = KunenaFactory::getUser($user);
		$message = KunenaForumMessageHelper::get($this->id);

		if (!$user->exists()) {
			$this->setError( JText::_('COM_KUNENA_THANKYOU_LOGIN') );
			return false;
		}
		if (!$this->exists ( $user->userid )) {
			$this->setError( JText::_ ( 'COM_KUNENA_THANKYOU_NOT_PRESENT' ) );
			return false;
		}
		$db = JFactory::getDBO ();
		$time = JFactory::getDate();
		$query = "DELETE FROM #__kunena_thankyou WHERE postid={$db->quote($this->id)} AND userid={$db->quote($user->userid)}";
		$db->setQuery ( $query );
		$db->query ();

		$query = "UPDATE #__kunena_users SET thankyou=thankyou-1 WHERE userid={$db->quote($message->userid)}";
		$db->setQuery ( $query );
		$db->query ();

		// Check for an error message.
		if ($db->getErrorNum ()) {
			$this->setError ( $db->getErrorMsg () );
			return false;
		}

		return true;
	}
}
