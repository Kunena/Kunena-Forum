<?php
/**
 * @version $Id$
 * Kunena Component - CKunenaAjaxHelper class
 * @package Kunena
 *
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();

require_once (KUNENA_PATH_LIB . DS . 'kunena.session.class.php');

/**
 * @author fxstein
 *
 */
class CKunenaAjaxHelper {
	/**
	 * @var JDatabase
	 */
	protected $_db;
	protected $_my;
	protected $_session;

	function __construct() {
		$this->_db = &JFactory::getDBO ();
		$this->_my = &JFactory::getUser ();
		$this->_session = & CKunenaSession::getInstance ();
	}

	function &getInstance() {
		static $instance = NULL;

		if (! $instance) {
			$instance = new CKunenaAjaxHelper ( );
		}
		return $instance;
	}

	public function generateJsonResponse($action, $do, $data) {
		$response = '';

		if(JDEBUG == 1 && defined('JFIREPHP')){
			FB::log("Kunena JSON action: ".$action);
		}

		if ($this->_my->id) {
			// We only entertain json requests for registered and logged in users

			switch ($action) {
				case 'autocomplete' :
					$response = $this->_getAutoComplete ( $do, $data );

					break;
				case 'preview' :
					$body = JRequest::getVar ( 'body', '' );

					$response = $this->_getPreview ( $body );

					break;
				case 'pollcatsallowed' :

					$response = $this->_getPollsCatsAllowed ();

					break;
				case 'pollvote' :
					$vote	= JRequest::getInt('radio', '');
					$id = JRequest::getInt ( 'id', 0 );

					$response = $this->_addPollVote ($vote, $id, $this->_my->id);

					break;
				case 'pollchangevote' :
					$vote	= JRequest::getInt('radio', '');
					$id = JRequest::getInt ( 'id', 0 );

					$response = $this->_changePollVote ($vote, $id, $this->_my->id);

					break;
				case 'uploadfile' :

					$response = $this->_uploadFile ($do);

					break;
				default :

					break;
			}
		}
		// Output the JSON data.
		return json_encode ( $response );
	}

	// JSON helpers
	protected function _getAutoComplete($do, $data) {
		$result = array ();

		// only registered users when the board is online should endup here

		// Verify permissions
		if ($this->_session->allowed && $this->_session->allowed != 'na') {
			$allowed = "c.id IN ({$this->_session->allowed})";
		} else {
			$allowed = "c.published='1' AND c.pub_access='0'";
		}

		// When we query for topics or categories we have to check against permissions

		switch ($do) {
			case 'getcat' :
				$query = "SELECT c.name, c.id
							FROM #__fb_categories AS c
							WHERE $allowed AND name LIKE '" . $data . "%'
							ORDER BY 1 LIMIT 0, 10;";

				$this->_db->setQuery ( $query );
				$result = $this->_db->loadResultArray ();
				check_dberror ( "Unable to lookup categories by name." );

				break;
			case 'gettopic' :
				$query = "SELECT m.subject
							FROM #__fb_messages AS m
							JOIN #__fb_categories AS c ON m.catid = c.id
							WHERE m.hold=0 AND m.parent=0 AND $allowed
								AND m.subject LIKE '" . $data . "%'
							ORDER BY 1 LIMIT 0, 10;";

				$this->_db->setQuery ( $query );
				$result = $this->_db->loadResultArray ();
				check_dberror ( "Unable to lookup topics by subject." );

				break;
			case 'getuser' :
				$kunena_config = &CKunenaConfig::getInstance ();

				// User the configured display name
				$queryname = $kunena_config->username ? 'username' : 'name';
				// Exclude the main superadmin from the search for security purposes
				$query = "SELECT `$queryname` FROM #__users WHERE block=0 AND `id` != 62 AND `$queryname`
							LIKE '" . $data . "%' ORDER BY 1 LIMIT 0, 10;";

				$this->_db->setQuery ( $query );
				$result = $this->_db->loadResultArray ();
				check_dberror ( "Unable to lookup users by $queryname." );

				break;
			default :
			// Operation not supported


		}

		return $result;
	}

	protected function _getPreview($data) {
		$result = array ();

		$config = & CKunenaConfig::getInstance ();

		require_once(JPATH_ROOT  .DS . '/libraries/joomla/document/html/html.php');

		$message = utf8_urldecode ( utf8_decode ( stripslashes ( $data ) ) );

		$kunena_emoticons = smile::getEmoticons ( 0 );
		$msgbody = smile::smileReplace ( $message, 0, $config->disemoticons, $kunena_emoticons );
		$msgbody = nl2br ( $msgbody );
		$msgbody = str_replace ( "__FBTAB__", "\t", $msgbody );
		$msgbody = CKunenaTools::prepareContent ( $msgbody );

		$result ['preview'] = $msgbody;

		return $result;
	}

	protected function _getPollsCatsAllowed () {
		$result = array ();

		$query = "SELECT id
							FROM #__fb_categories
							WHERE allow_polls=1 AND parent=1;";
		$this->_db->setQuery ( $query );
		$allow_polls = $this->_db->loadResultArray ();
		check_dberror ( "Unable to lookup categories by name." );
		if(!empty($allow_polls)) {
			$result['allowed_polls'] = $allow_polls;
		}

		return $result;
	}

	protected function _addPollVote ($value_choosed, $id, $userid) {
		$result = array ();

		require_once (KUNENA_PATH_LIB .DS. 'kunena.poll.class.php');
		$poll = new CKunenaPolls();
		$result = $poll->save_results($id,$userid,$value_choosed);

		return $result;
	}

	protected function _changePollVote ($value_choosed, $id, $userid) {
		$result = array ();

		require_once (KUNENA_PATH_LIB .DS. 'kunena.poll.class.php');
		$poll = new CKunenaPolls();
		$result = $poll->save_changevote($id,$userid,$value_choosed);

		return $result;
	}

	protected function _uploadFile ($do) {

		$result = array();
		$error = false;

		//import joomlas filesystem functions, we will do all the filewriting with joomlas functions,
		//so if the ftp layer is on, joomla will write with that, not the apache user, which might
		//not have the correct permissions
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		if(JDEBUG == 1 && defined('JFIREPHP')){
			FB::log("Kunena JSON: _uploadFile");
		}

		$fileid = 'attachment';
		$file = $_FILES[$fileid];
		//any errors the server registered on uploading
		$fileError = $file['error'];
		if ($fileError > 0)
		{
	        switch ($fileError)
	        {
	        case 1:
		        $error = JText::_( 'FILE TO LARGE THAN PHP INI ALLOWS' );
		        break;

	        case 2:
		        $error = JText::_( 'FILE TO LARGE THAN HTML FORM ALLOWS' );
		        break;

	        case 3:
		        $error = JText::_( 'ERROR PARTIAL UPLOAD' );
		        break;

	        case 4:
		        $error = JText::_( 'ERROR NO FILE' );
		        break;

	        default:
		        $error = JText::_( 'UNKNOWN ERROR' );
	        }
		}

		// Terminate early if we already hit an error
		if ($error){
			return array(
				'id' => $fileid,
				'status' => '0',
				'name' => $file['name'],
				'error' => $error
			);
		}

		//check for filesize
		$fileSize = $file['size'];
		if($fileSize > 2 * 1024 * 1024)
		{
			return array(
				'id' => $fileid,
				'status' => '0',
				'name' => $file['name'],
				'error' => JText::_( 'FILE BIGGER THAN 2MB' )
			);
		}

		//check the file extension is ok
		$fileName = $file['name'];
		$uploadedFileNameParts = explode('.',$fileName);
		$uploadedFileExtension = array_pop($uploadedFileNameParts);

		$validFileExts = explode(',', 'jpeg,jpg,png,gif');

		//assume the extension is false until we know its ok
		$extOk = false;

		//go through every ok extension, if the ok extension matches the file extension (case insensitive)
		//then the file extension is ok
		foreach($validFileExts as $key => $value)
		{
		        if( preg_match("/$value/i", $uploadedFileExtension ) )
		        {
		                $extOk = true;
		        }
		}

		if ($extOk == false)
		{
			return array(
				'id' => $fileid,
				'status' => '0',
				'name' => $file['name'],
				'error' => JText::_( 'INVALID EXTENSION' )
			);
		}

		//the name of the file in PHP's temp directory that we are going to move to our folder
		$fileTemp = $file['tmp_name'];

		//for security purposes, we will also do a getimagesize on the temp file (before we have moved it
		//to the folder) to check the MIME type of the file, and whether it has a width and height
		$imageinfo = getimagesize($fileTemp);

		//we are going to define what file extensions/MIMEs are ok, and only let these ones in (whitelisting), rather than try to scan for bad
		//types, where we might miss one (whitelisting is always better than blacklisting)
		$okMIMETypes = 'image/jpeg,image/pjpeg,image/png,image/x-png,image/gif';
		$validFileTypes = explode(",", $okMIMETypes);

		//if the temp file does not have a width or a height, or it has a non ok MIME, return
		if( !is_int($imageinfo[0]) || !is_int($imageinfo[1]) ||  !in_array($imageinfo['mime'], $validFileTypes) )
		{
			return array(
				'id' => $fileid,
				'status' => '0',
				'name' => $file['name'],
				'error' => JText::_( 'INVALID FILETYPE' )
			);
		}

		//lose any special characters in the filename
		$fileName = preg_replace("[^A-Za-z0-9.]", "-", $fileName);

		//always use constants when making file paths, to avoid the possibilty of remote file inclusion
		$uploadPath = KUNENA_PATH_UPLOADED.DS.'images'.DS.$fileName;

		// Our processing, we get a hash value from the file
		$hash = md5_file($file['tmp_name']);

		// ... and if available, we get image data
		$info = @getimagesize($file['tmp_name']);

		if(!JFile::upload($fileTemp, $uploadPath))
		{
			return array(
				'id' => $fileid,
				'status' => '0',
				'name' => $file['name'],
				'error' => JText::_( 'ERROR MOVING FILE' )
			);
		}

		$result = array(
			'id' => $fileid,
			'status' => '1',
			'name' => $file['name']
		);

		$result['hash'] = $hash;
		if ($info) {
			$result['width'] = $info[0];
			$result['height'] = $info[1];
			$result['mime'] = $info['mime'];
			$result['size'] = $file['size'];
		}

		return $result;
	}

}