<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	(C) 2008 Kunena. All rights reserved, see COPYRIGHT.php
 * @license		GNU General Public License, see LICENSE.php
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die('Invalid Request.');

/**
 * Post Table for the Kunena Package
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.0
 */
class KunenaTablePost extends JTable
{
	/** @var int unsigned */
	var $id = null;
	/** @var int unsigned */
	var $parent_id = null;
	/** @var int unsigned */
	var $category_id = null;
	/** @var int unsigned */
	var $thread_id = null;
	/** @var int unsigned */
	var $user_id = null;
	/** @var int */
	var $icon = null;
	/** @var varchar */
	var $subject = null;
	/** @var text */
	var $message = null;
	/** @var datetime */
	var $created_time = null;
	/** @var int */
	var $user_ip = null;
	/** @var int */
	var $hits = null;
	/** @var varchar */
	var $name = null;
	/** @var varchar */
	var $email = null;
	/** @var int */
	var $published = null;
	/** @var int */
	var $moved = null;
	/** @var int */
	var $modified_user_id = null;
	/** @var datetime */
	var $modified_time = null;
	/** @var varchar */
	var $modified_reason = null;
	/** @var int */
	var $left_id = null;
	/** @var int */
	var $right_id = null;

	/**
	 * Constructor
	 *
	 * @access	protected
	 * @param	object	Database object
	 * @return	void
	 * @since	1.0
	 */
	function __construct(&$db)
	{
		parent::__construct('#__kunena_posts', 'id', $db);
	}

	/**
	 * Overloaded check function
	 *
	 * @return boolean
	 */
	function check()
	{
		// Check for a category id.
		if(empty($this->category_id)) {
			$this->setError(JText::_('KUNENA_CATEGORY_REQUIRED'));
			return false;
		}

		// Check for a valid subject.
		if((trim($this->subject)) == '') {
			$this->setError(JText::_('KUNENA_SUBJECT_REQUIRED'));
			return false;
		}

		// Check for a valid message.
		if((trim($this->message)) == '') {
			$this->setError(JText::_('KUNENA_MESSAGE_REQUIRED'));
			return false;
		}

		return true;
	}


	/**
	 * Loads a row from the database and binds the fields to the object properties
	 *
	 * @access	public
	 * @param	mixed	Optional primary key.  If not specifed, the value of current key is used
	 * @return	boolean	True if successful
	 */
	function load($postId = null)
	{
		// Initialize variables.
		$postId = is_null($postId) ? $this->id : $postId;

		// Get the database connection object.
		$db = & $this->_db;

		// If we have no load criteria, return false.
		if ($postId === null) {
			return false;
		}

		// Reset the object values.
		$this->reset();

		// Build and set the load query.
		$db->setQuery(
			'SELECT *' .
			' FROM '.$this->_tbl .
			' WHERE `id` = '.(int)$postId
		);

		// Get the result data.
		if ($result = $db->loadAssoc())
		{
			// Bind the result array to the object.
			foreach ($result as $k => $v)
			{
				$this->$k = $result[$k];
			}

			// Make sure the user IP is decoded.
			if (!empty($this->user_ip)) {
				$this->user_ip = long2ip($this->user_ip);
			}

			return true;
		}
		else {
			$this->setError($db->getErrorMsg());
			return false;
		}
	}

	/**
	 * Inserts a new row if id is zero or updates an existing row in the database table
	 *
	 * Can be overloaded/supplemented by the child class
	 *
	 * @access public
	 * @param boolean If false, null object variables are not updated
	 * @return null|string null if successful otherwise returns and error message
	 */
	function store($updateNulls = false)
	{
		// Ensure we have a client IP.
		if (empty($this->user_ip)) {
			preg_match('/-?[0-9\.]+/', (string) $_SERVER['REMOTE_ADDR'], $matches);
			$this->user_ip = @ ip2long($matches[0]);
		}

		// Ensure the client IP is encoded.
		$this->user_ip = (is_string($this->user_ip)) ? ip2long($this->user_ip) : $this->user_ip;

		// Set current user data.
		if (!empty($this->user_id)) {
			$user = &JFactory::getUser($this->user_id);
			$this->name		= $user->get('username');
			$this->email	= $user->get('email');
		}

		// Get a current JDate object.
		$now = & JFactory::getDate();

		// Ensure a modified date exists and is current.
		$this->modified_time = $now->toMySQL();

		// Ensure a created time exists if necessary.
		if (empty($this->id)) {
			$this->created_time = (empty($this->created_time)) ? $now->toMySQL() : $this->created_time;
		}

		// Set the published state.
		$this->published = (is_null($this->published) ? 1 : $this->published);

		// If there is no thread set, create a new one.
		if (empty($this->thread_id))
		{
			$thread = JTable::getInstance('Thread', 'KunenaTable');

			// Bind the post data.
			$thread->category_id	= $this->category_id;
			$thread->user_id		= $this->user_id;
			$thread->user_ip		= $this->user_ip;
			$thread->created_time	= $this->created_time;
			$thread->published		= $this->published;
			$thread->icon			= $this->icon;

			// Check the post data.
			if (!$thread->check()) {
				$this->setError($thread->getError());
				return false;
			}

			// Store the post data.
			if (!$thread->store()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			// Set the thread id of the new post.
			$this->thread_id = $thread->id;
		}

		// Execute the parent store method.
		return parent::store($updateNulls);
	}

	/**
	 * Binds a named array/hash to this object
	 *
	 * Can be overloaded/supplemented by the child class
	 *
	 * @access	public
	 * @param	$from	mixed	An associative array or object
	 * @param	$ignore	mixed	An array or space separated list of fields not to bind
	 * @return	boolean
	 */
	function bind($from, $ignore=array())
	{
		/*
		 * CUSTOM BIND CODE
		 */

		// Execute the parent bind method.
		return parent::bind($from, $ignore);
	}
}
