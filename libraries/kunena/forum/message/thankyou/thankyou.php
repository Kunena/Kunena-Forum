<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Forum.Message.Thankyou
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class KunenaForumMessageThankyou
 *
 * @property int $postid
 * @property int $userid
 * @property int $targetuserid
 * @property string $time
 */
class KunenaForumMessageThankyou extends JObject {
	/**
	 * @var int
	 */
	protected $id = 0;
	protected $users = array();

	/**
	 * @param int $id
	 *
	 * @internal
	 */
	public function __construct($id) {
		$this->id = (int) $id;
	}

	/**
	 * @param null $identifier
	 * @param bool $reload
	 *
	 * @return KunenaForumMessageThankyou
	 */
	static public function getInstance($identifier = null, $reload = false) {
		return KunenaForumMessageThankyouHelper::get($identifier, $reload);
	}

	/**
	 * Check if the user has already said thank you.
	 *
	 * @param int $userid
	 *
	 * @return bool
	 */
	public function exists($userid) {
		return isset($this->users[(int) $userid]);
	}

	/**
	 * @param int $userid
	 * @param string $time
	 */
	public function _add($userid, $time) {
		$this->users[$userid] = $time;
	}

	/**
	 * Save thank you.
	 *
	 * @param mixed $user
	 *
	 * @return bool
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
			SET postid={$db->quote($this->id)} , userid={$db->quote($user->userid)} , targetuserid={$db->quote($message->userid)}, time={$db->quote($time->toSql())}";
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
	 * @param KunenaForumMessage $message
	 *
	 * @return bool
	 */
	protected function _savethankyou(KunenaForumMessage $message) {
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
	 * Get all users who have given thank you to this message.
	 * @return array List of userid=>time.
	 */
	public function getList() {
		return $this->users;
	}

	/**
	 * Detele thank you from the database.
	 *
	 * @param mixed $user
	 *
	 * @return bool
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
