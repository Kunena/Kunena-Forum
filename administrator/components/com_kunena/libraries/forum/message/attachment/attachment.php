<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Forum.Message.Attachment
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Kunena Forum Message Attachment Class
 */
class KunenaForumMessageAttachment extends JObject {
	protected $_exists = false;
	protected $_db = null;
	public $disabled = false;

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

	function isImage($mime) {
		return (stripos ( $mime, 'image/' ) !== false);
	}

	function getTextLink() {
		$this->generate();
		return $this->_textLink;
	}

	function getImageLink() {
		$this->generate();
		return $this->_imagelink;
	}

	function getThumbnailLink() {
		$this->generate();
		return $this->_thumblink;
	}

	protected function generate() {
		if (!isset($this->_shorttype)) {
			$this->_shorttype = $this->isImage($this->filetype) ? 'image' : $this->filetype;
			$this->_shortname = KunenaForumMessageAttachmentHelper::shortenFileName($this->filename);

			$config = KunenaFactory::getConfig();
			$this->_imagelink = null;
			$template = KunenaFactory::getTemplate();
			switch (strtolower ( $this->_shorttype )) {
				case 'image' :
					// Check for thumbnail and if available, use for display
					if (file_exists ( JPATH_ROOT . '/' . $this->folder . '/thumb/' . $this->filename )) {
						$thumb = $this->folder . '/thumb/' . $this->filename;
						$imgsize = '';
					} else {
						$thumb = $this->folder . '/' . $this->filename;
						$imgsize = 'width="' . $config->thumbwidth . 'px" height="' . $config->thumbheight . 'px"';
					}

					$img = '<img title="' . $this->escape ( $this->filename ) . '" ' . $imgsize . ' src="' . JURI::ROOT () . $thumb . '" alt="' . $this->escape ( $this->filename ) . '" />';
					$this->_thumblink = $this->_getAttachementLink ( $this->escape ( $this->folder ), $this->escape ( $this->filename ), $img, $this->escape ( $this->filename ), ($config->lightbox)? 'lightbox[thumb' . intval ( $this->mesid ). ']':'' );
					$img = '<img title="' . $this->escape ( $this->filename ) . '" src="' . JURI::ROOT () . $this->escape ( $this->folder ) . '/' . $this->escape ( $this->filename ) . '" alt="' . $this->escape ( $this->filename ) . '" />';
					$this->_imagelink = $this->_getAttachementLink ( $this->escape ( $this->folder ), $this->escape ( $this->filename ), $img, $this->escape ( $this->filename ), ($config->lightbox)?'lightbox[imagelink' . intval ( $this->mesid ) .']':'' );
					$this->_textLink = $this->_getAttachementLink ( $this->escape ( $this->folder ), $this->escape ( $this->filename ), $this->escape ( $this->_shortname ), $this->escape ( $this->filename ), ($config->lightbox)?'lightbox[simple' . $this->mesid . ']' . ' nofollow':' nofollow' ) . ' (' . number_format ( intval ( $this->size ) / 1024, 0, '', ',' ) . 'KB)';
					break;
				default :
					// Filetype without thumbnail or icon support - use default file icon
					$img = '<img src="' . JURI::root(). 'media/kunena/images/attach_generic.png" alt="' . JText::_ ( 'COM_KUNENA_ATTACH' ) . '" />';
					$this->_thumblink = $this->_getAttachementLink ( $this->escape ( $this->folder ), $this->escape ( $this->filename ), $img, $this->escape ( $this->filename ), 'nofollow' );
					$this->_textLink = $this->_getAttachementLink ( $this->escape ( $this->folder ), $this->escape ( $this->filename ), $this->escape ( $this->_shortname ), $this->escape ( $this->filename ), 'nofollow' ) . ' (' . number_format ( intval ( $this->size ) / 1024, 0, '', ',' ) . 'KB)';
			}
			$this->disabled = false;
			if (! KunenaUserHelper::getMyself()->exists()) {
				if ($this->_shorttype == 'image' && ! $config->showimgforguest) {
					$this->disabled = true;
					$this->_textLink = JText::_ ( 'COM_KUNENA_SHOWIMGFORGUEST_HIDEIMG' );
				}
				if ($this->_shorttype != 'image' && ! $config->showfileforguest) {
					$this->disabled = true;
					$this->_textLink = JText::_ ( 'COM_KUNENA_SHOWIMGFORGUEST_HIDEFILE' );
				}
				if ($this->disabled) {
					$this->_thumblink = '<img src="' . JURI::root() .'media/kunena/images/attach_generic.png" alt="' . JText::_ ( 'COM_KUNENA_ATTACH' ) . '" />';
					$this->_imagelink = null;
					$this->size = 0;
				}
			}
		}
	}

	public function getMessage() {
		return KunenaForumMessageHelper::get($this->mesid);
	}

	public function authorise($action='read', $user=null, $silent=false) {
		static $actions  = array(
			'read'=>array('Read'),
			'create'=>array(),
			'delete'=>array('Exists', 'Own'),
		);
		$user = KunenaUserHelper::get($user);
		if (!isset($actions[$action])) {
			if (!$silent) $this->setError ( __CLASS__.'::'.__FUNCTION__.'(): '.JText::sprintf ( 'COM_KUNENA_LIB_AUTHORISE_INVALID_ACTION', $action ) );
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

	function upload($key='kattachment', $catid=null) {
		require_once (KPATH_SITE . '/lib/kunena.upload.class.php');
		$path = JPATH_ROOT . '/media/kunena/attachments/' . $this->userid;
		$upload = new CKunenaUpload($catid);
		$upload->uploadFile($path, $key, '', false);
		$fileinfo = $upload->getFileInfo();

		if ($fileinfo ['status'] && $fileinfo['ready'] === true) {
			$this->hash =$fileinfo ['hash'];
			$this->size = $fileinfo ['size'];
			$this->folder = '/media/kunena/attachments/' . $this->userid;
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
		$table = $this->getTable ();

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
		// Do not save altered message
		if ($this->disabled) return;

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
		$table = $this->getTable ();

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
		$path = JPATH_ROOT."/{$this->folder}";
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

	protected function escape($var) {
		return htmlspecialchars($var, ENT_COMPAT, 'UTF-8');
	}

	protected function authoriseExists($user) {
		// Checks if attachment exists
		if (!$this->exists()) {
			$this->setError ( JText::_ ( 'COM_KUNENA_NO_ACCESS' ) );
			return false;
		}
		return true;
	}

	protected function authoriseRead($user) {
		// Checks if attachment exists
		if (!$this->exists()) {
			$this->setError ( JText::_ ( 'COM_KUNENA_NO_ACCESS' ) );
			return false;
		}
		$this->generate();
		// TODO: In the future we might want to separate read and peak
/*
		if ($this->disabled) {
			$this->setError ( JText::_ ( 'COM_KUNENA_NO_ACCESS' ) );
			return false;
		}
*/
		return true;
	}

	protected function authoriseOwn(KunenaUser $user) {
		// Checks if attachment is users own or user is moderator in the category (or global)
		if (($user->userid && $this->userid != $user->userid) && !$user->isModerator($this->getMessage()->getCategory())) {
			$this->setError ( JText::_ ( 'COM_KUNENA_NO_ACCESS' ) );
			return false;
		}
		return true;
	}

	protected function _getAttachementLink($folder,$filename,$name,$title = '', $rel = 'nofollow') {
		$link = JURI::ROOT()."{$folder}/{$filename}";
		return '<a href="'.$link.'" title="'.$title.'" rel="'.$rel.'">'.$name.'</a>';
	}
}