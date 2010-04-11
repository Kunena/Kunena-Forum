<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
**/
// Dont allow direct linking
defined( '_JEXEC' ) or die();

require_once (KUNENA_PATH .DS. "class.kunena.php");
require_once (KUNENA_PATH_LIB .DS. "kunena.config.class.php");
require_once (KUNENA_PATH_LIB .DS. "kunena.timeformat.class.php");

class KunenaSession extends JObject
{
	protected $_exists = false;
	protected $_sessiontimeout = false;
	private static $_instance;

	function __construct($identifier)
	{
		// New user gets a month of unread messages
		$this->lasttime = CKunenaTimeformat::internalTime() - 3600*24*30;
		$this->currvisit = CKunenaTimeformat::internalTime();
		$this->load($identifier);
	}

	static public function getInstance( $update=false )
	{
		if (!self::$_instance) {
			$my = JFactory::getUser();
			$db = JFactory::getDBO();
			self::$_instance = new KunenaSession($my->id);
			if ($update) self::$_instance->updateSessionInfo();
		}
		return self::$_instance;
	}

	/**
	 * Method to get the session table object
	 *
	 * This function uses a static variable to store the table name of the session table to
	 * it instantiates. You can call this function statically to set the table name if
	 * needed.
	 *
	 * @access	public
	 * @param	string	The session table name to be used
	 * @param	string	The session table prefix to be used
	 * @return	object	The session table object
	 * @since	1.5
	 */
	function getTable($type = 'KunenaSession', $prefix = 'Table')
	{
		static $tabletype = null;

		//Set a custom table type is defined
		if ($tabletype === null || $type != $tabletype['name'] || $prefix != $tabletype['prefix']) {
			$tabletype['name']		= $type;
			$tabletype['prefix']	= $prefix;
		}

		// Create the user table object
		return JTable::getInstance($tabletype['name'], $tabletype['prefix']);
	}

	/**
	 * Method to load a KunenaSession object by userid
	 *
	 * @access	public
	 * @param	mixed	$identifier The user id of the user to load
	 * @param	string	$path		Path to a parameters xml file
	 * @return	boolean			True on success
	 * @since 1.5
	 */
	public function load($userid)
	{
		// Create the user table object
		$table	= &$this->getTable();

		// Load the KunenaTableUser object based on the user id
		if ($table->load($userid)) {
			$this->_exists = true;
		}

		// Assuming all is well at this point lets bind the data
		$this->setProperties($table->getProperties());
		$this->userid = $userid;

		jimport('joomla.utilities.arrayhelper');
		$readtopics = JArrayHelper::toInteger(explode(',', $this->readtopics));
		if (empty($readtopics)) $readtopics = array(0);
		$this->readtopics = implode(',', $readtopics);

		return true;
	}

	/**
	 * Method to save the KunenaSession object to the database
	 *
	 * @access	public
	 * @param	boolean $updateOnly Save the object only if not a new session
	 * @return	boolean True on success
	 * @since 1.5
	 */
	function save($updateOnly = false)
	{
		// Do not save session for anonymous users
		if (!$this->userid) {
			return false;
		}
		// Create the user table object
		$table	= &$this->getTable();
		$table->bind($this->getProperties());
		$table->exists($this->_exists);

		// Check and store the object.
		if (!$table->check()) {
			$this->setError($table->getError());
			return false;
		}

		//are we creating a new user
		$isnew = !$this->_exists;

		// If we aren't allowed to create new users return
		if ($isnew && $updateOnly) {
			return true;
		}

		//Store the user data in the database
		if (!$result = $table->store()) {
			$this->setError($table->getError());
		}

		// Set the id for the JUser object in case we created a new user.
		if (empty($this->userid)) {
			$this->userid = $table->get('userid');
		}

		return $result;
	}

	/**
	 * Method to delete the KunenaSession object from the database
	 *
	 * @access	public
	 * @return	boolean			True on success
	 * @since 1.5
	 */
	function delete()
	{
		// Create the user table object
		$table	= &$this->getTable();

		$result = $table->delete($this->userid);
		if (!$result) {
			$this->setError($table->getError());
		}
		return $result;

	}

	function isNewUser()
	{
		return !$this->_exists;
	}

	function isNewSession()
	{
		$kunena_config =& CKunenaConfig::getInstance();

		// perform session timeout check
		$this->_sessiontimeout = ($this->currvisit + $kunena_config->fbsessiontimeout < CKunenaTimeformat::internalTime());
		return $this->_sessiontimeout;
	}

	function markAllCategoriesRead()
	{
		$this->lasttime = CKunenaTimeformat::internalTime();
		$this->readtopics = 0;
	}

	function updateSessionInfo()
	{
		// If this is a new session, reset the lasttime colum with the timestamp
		// of the last saved currvisit - only after that can we reset currvisit to now before the store
		if ($this->isNewSession())
		{
			$this->lasttime = $this->currvisit;
			$this->readtopics = 0;
		}
		$this->updateAllowedForums();
		$this->currvisit = CKunenaTimeformat::internalTime();
	}

	function updateAllowedForums()
	{
		// check to see if we need to refresh the allowed forums cache
		// get all accessaible forums if needed (eg on forum modification, new session)
		if (!$this->allowed or $this->allowed == 'na' or $this->isNewSession()) {
			$allow_forums = CKunenaTools::getAllowedForums($this->userid);

			if (!$allow_forums)
			{
				$allow_forums = '0';
			}

			$this->allowed = $allow_forums;
		}
	}

	function canRead($catid) {
		if ($this->allowedcats === null) {
			$this->updateAllowedForums();
			$this->allowedcats = ($this->allowed) ? explode ( ',', $this->allowed ) : array ();
		}
		return in_array ( $catid, $this->allowedcats );
	}
}

?>
