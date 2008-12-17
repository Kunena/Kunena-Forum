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
 * Thread Table for the Kunena Package
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @version		1.0
 */
class KunenaTableThread extends JTable
{
	/** @var int unsigned */
	var $id = null;
	/** @var int unsigned */
	var $category_id = null;
	/** @var int unsigned */
	var $user_id = null;
	/** @var int */
	var $user_ip = null;
	/** @var datetime */
	var $created_time = null;
	/** @var int */
	var $icon = null;
	/** @var varchar */
	var $subject = null;
	/** @var varchar */
	var $name = null;
	/** @var varchar */
	var $email = null;
	/** @var int */
	var $published = null;
	/** @var int */
	var $locked = null;
	/** @var int */
	var $ordering = null;
	/** @var int */
	var $moved = null;
	/** @var int */
	var $hits = null;
	/** @var int */
	var $total_posts = null;
	/** @var int */
	var $last_post_id = null;
	/** @var datetime */
	var $last_post_time = null;
	/** @var int */
	var $last_post_icon = null;
	/** @var varchar */
	var $last_post_subject = null;
	/** @var varchar */
	var $last_post_name = null;
	/** @var varchar */
	var $last_post_email = null;
	/** @var int */
	var $last_post_user_id = null;

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
		parent::__construct('#__kunena_threads', 'id', $db);
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

		return true;
	}

	/**
	 * Loads a row from the database and binds the fields to the object properties
	 *
	 * @access	public
	 * @param	mixed	Optional primary key.  If not specifed, the value of current key is used
	 * @return	boolean	True if successful
	 */
	function load($threadId = null)
	{
		// Initialize variables.
		$threadId = is_null($threadId) ? $this->id : $threadId;

		// Get the database connection object.
		$db = & $this->_db;

		// If we have no load criteria, return false.
		if ($threadId === null) {
			return false;
		}

		// Reset the object values.
		$this->reset();

		// Build and set the load query.
		$db->setQuery(
			'SELECT *' .
			' FROM '.$this->_tbl .
			' WHERE `id` = '.(int)$threadId
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

		// Get a current JDate object.
		$now = & JFactory::getDate();

		// Ensure a created time exists if necessary.
		if (empty($this->id)) {
			$this->created_time = (empty($this->created_time)) ? $now->toMySQL() : $this->created_time;
		}

		// Set the published state.
		$this->published = (is_null($this->published) ? 1 : $this->published);

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

	function updateAggregates($threadId = null)
	{
		// Initialize variables.
		$threadId = is_null($threadId) ? $this->id : $threadId;

		// Get the database connection object.
		$db = & $this->_db;

		// If we have no load criteria, return false.
		if ($threadId === null) {
			return false;
		}

		// Attempt to load the thread.
		if (!$this->load($threadId)) {
			$this->setError(JText::_('KUNENA_INVALID_THREAD'));
			return false;
		}

		// Build and set the load query.
		$db->setQuery(
			'SELECT a.id, a.created_time, a.icon, a.subject, a.name, a.email, a.user_id, COUNT(b.id) as total_posts' .
			' FROM `#__kunena_posts` AS a' .
			' LEFT JOIN `#__kunena_posts` AS b ON a.thread_id = b.thread_id' .
			' WHERE a.thread_id = '.(int)$threadId .
			' AND a.published = 1' .
			' GROUP BY a.id' .
			' ORDER BY a.created_time DESC'
		);
		$aggregate = $db->loadObject();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Set the new aggregate data to the table object.
		$this->last_post_id			= $aggregate->id;
		$this->last_post_time		= $aggregate->created_time;
		$this->last_post_icon		= $aggregate->icon;
		$this->last_post_subject	= $aggregate->subject;
		$this->last_post_name		= $aggregate->name;
		$this->last_post_email		= $aggregate->email;
		$this->last_post_user_id	= $aggregate->user_id;
		$this->total_posts			= $aggregate->total_posts;

		// Check the thread data.
		if (!$this->check()) {
			return false;
		}

		// Attempt to store the thread.
		if (!$this->store()) {
			return false;
		}

		return true;
	}
}
