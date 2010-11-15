<?php
/**
 * @version $Id$
 * Kunena Component - KunenaForumMessageAttachmentAttachment Class
 * @package Kunena
 *
 * @Copyright (C) 2010 www.kunena.org All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

kimport ('kunena.error');
kimport ('kunena.user.helper');
kimport ('kunena.forum.message.helper');

/**
 * Kunena Forum Message Attachment Class
 */
class KunenaForumMessageAttachment extends JObject {
	protected $_exists = false;
	protected $_db = null;

	protected static $_directory = 'media/kunena/attachments';

	/**
	 * Constructor
	 *
	 * @access	protected
	 */
	public function __construct($identifier = 0) {
		// Always load the attachment -- if attachment does not exist: fill empty data
		$this->_db = JFactory::getDBO ();
		$this->load ( $identifier );
	}

	public function __destruct() {
		if (!$this->exists()) {
			$this->deleteFile();
		}
	}

	/**
	 * Returns KunenaForumMessageAttachment object
	 *
	 * @access	public
	 * @param	identifier		The message to load - Can be only an integer.
	 * @return	KunenaForumMessageAttachment		The message object.
	 * @since	1.7
	 */
	static public function getInstance($identifier = null, $reload = false) {
		return KunenaForumMessageAttachmentHelper::get($identifier, $reload);
	}

	function exists($exists = null) {
		$return = $this->_exists;
		if ($exists !== null) $this->_exists = $exists;
		return $return;
	}

	public function getMessage() {
		return KunenaForumMessageHelper::get($this->mesid);
	}

	public function authorise($action='read', $user=null, $silent=false) {
		static $actions  = array(
			'read'=>array(),
			'create'=>array(),
			'delete'=>array(),
		);
		$user = KunenaUser::getInstance($user);
		if (!isset($actions[$action])) {
			if (!$silent) $this->setError ( JText::_ ( 'COM_KUNENA_LIB_ATTACHMENT_NO_ACTION' ) );
			return false;
		}
		$message = $this->getMessage();
		$auth = $message->authorise('attachment.'.$action, $user, $silent);
		if (!$auth) {
			if (!$silent) $this->setError ( $message->getError() );
			return false;
		}
		foreach ($actions[$action] as $function) {
			$authFunction = 'authorise'.$function;
			if (! method_exists($this, $authFunction) || ! $this->$authFunction($user)) {
				if (!$silent) $this->setError ( JText::_ ( 'COM_KUNENA_NO_ACCESS' ) );
				return false;
			}
		}
		return true;
	}

	function upload($key='kattachment') {
		require_once (KUNENA_PATH_LIB .DS. 'kunena.upload.class.php');
		$path = KUNENA_PATH_UPLOADED . DS . $this->userid;
		$upload = new CKunenaUpload();
		$upload->uploadFile($path, $key, '', false);
		$fileinfo = $upload->getFileInfo();

		if ($fileinfo ['status'] && $fileinfo['ready'] === true) {
			$this->hash =$fileinfo ['hash'];
			$this->size = $fileinfo ['size'];
			$this->folder = KUNENA_RELPATH_UPLOADED . '/' . $this->userid;
			$this->filetype = $fileinfo ['mime'];
			$this->filename = $fileinfo ['name'];
			return true;
		}
		$this->setError( JText::sprintf ( 'COM_KUNENA_UPLOAD_FAILED', $fileinfo ['name'] ) . ': ' . $fileinfo ['error'] );
		return false;
	}
	/**
	 * Method to get the messages table object
	 *
	 * This function uses a static variable to store the table name of the user table to
	 * it instantiates. You can call this function statically to set the table name if
	 * needed.
	 *
	 * @access	public
	 * @param	string	The messages table name to be used
	 * @param	string	The messages table prefix to be used
	 * @return	object	The messages table object
	 * @since	1.6
	 */
	public function getTable($type = 'KunenaAttachments', $prefix = 'Table') {
		static $tabletype = null;

		//Set a custom table type is defined
		if ($tabletype === null || $type != $tabletype ['name'] || $prefix != $tabletype ['prefix']) {
			$tabletype ['name'] = $type;
			$tabletype ['prefix'] = $prefix;
		}

		// Create the user table object
		return JTable::getInstance ( $tabletype ['name'], $tabletype ['prefix'] );
	}

	public function bind($data, $ignore = array()) {
		$data = array_diff_key($data, array_flip($ignore));
		$this->setProperties ( $data );
	}

	/**
	 * Method to load a KunenaForumMessageAttachment object by id
	 *
	 * @access	public
	 * @param	mixed	$id The message id to be loaded
	 * @return	boolean			True on success
	 * @since 1.6
	 */
	public function load($id) {
		// Create the table object
		$table = &$this->getTable ();

		// Load the KunenaTable object based on id
		$this->_exists = $table->load ( $id );

		// Assuming all is well at this point lets bind the data
		$this->setProperties ( $table->getProperties () );
		return $this->_exists;
	}

	/**
	 * Method to save the KunenaForumMessageAttachment object to the database
	 *
	 * @access	public
	 * @param	boolean $updateOnly Save the object only if not a new message
	 * @return	boolean True on success
	 * @since 1.6
	 */
	public function save($updateOnly = false) {
		// Create the messages table object
		$table = $this->getTable ();
		$table->bind ( $this->getProperties () );
		$table->exists ( $this->_exists );

		if ($this->getError()) {
			return false;
		}
		// Check and store the object.
		if (! $table->check ()) {
			$this->setError ( $table->getError () );
			return false;
		}

		//are we creating a new message
		$isnew = ! $this->_exists;

		// If we aren't allowed to create new message return
		if ($isnew && $updateOnly) {
			return true;
		}

		//Store the message data in the database
		if (! $result = $table->store ()) {
			$this->setError ( $table->getError () );
		}

		// Set the id for the KunenaForumMessageAttachment object in case we created a new message.
		if ($result && $isnew) {
			$this->load ( $table->get ( 'id' ) );
			$this->_exists = true;
		}

		return $result;
	}

	/**
	 * Method to delete the KunenaForumMessageAttachment object from the database
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since 1.6
	 */
	public function delete() {
		if (!$this->exists()) {
			return true;
		}

		// Create the table object
		$table = &$this->getTable ();

		$result = $table->delete ( $this->id );
		if (! $result) {
			$this->setError ( $table->getError () );
			return false;
		}
		$this->_exists = false;

		return $result;
	}

	// Internal functions

	protected function check() {
		$author = KunenaUserHelper::get($this->userid);
	}

	protected function deleteFile() {
		if (self::$_directory != substr($this->folder, 0, strlen(self::$_directory)))
			return;
		jimport('joomla.filesystem.file');
		$path = JPATH_ROOT.DS.$this->folder;
		$filetoDelete = $path.'/'.$this->filename;
		if (JFile::exists($filetoDelete)) {
			JFile::delete($filetoDelete);
		}
		$filetoDelete = $path.'/raw/'.$this->filename;
		if (JFile::exists($filetoDelete)) {
			JFile::delete($filetoDelete);
		}
		$filetoDelete = $path.'/thumb/'.$this->filename;
		if (JFile::exists($filetoDelete)) {
			JFile::delete($filetoDelete);
		}
	}
}