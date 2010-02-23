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

class CKunenaUpload {
	protected $_db;
	protected $_my;
	protected $_session;

	protected $fileName = false;
	protected $fileTemp = false;
	protected $fileSize = false;
	protected $fileHash = false;
	protected $imageInfo = false;

	protected $ready = false;
	protected $status = false;
	protected $error = false;

	function __construct() {
		$this->_db = &JFactory::getDBO ();
		$this->_my = &JFactory::getUser ();
		$this->_session = & CKunenaSession::getInstance ();
	}

	function __destruct() {
		if (!$this->status) {
			if(JDEBUG == 1 && defined('JFIREPHP')){
				FB::log('Kunena upload failed: '.$this->error);
			}
			if (is_file($this->fileTemp)) unlink ( $this->fileTemp );
		}
	}

	function fail($errormsg) {
		$this->error = $errormsg;
		$this->status = false;
	}

	function fileInfo()
	{
		$result = array(
			'status' => $this->status,
			'ready' => $this->ready,
			'name' => $this->fileName,
			'size' => $this->fileSize
		);

		if ($this->fileHash) {
			$result['hash'] = $this->fileHash;
		}
		if ($this->imageInfo) {
			$result['width'] = $this->imageInfo[0];
			$result['height'] = $this->imageInfo[1];
			$result['mime'] = $this->imageInfo['mime'];
		}
		if ($this->error) {
			$result['error'] = $this->error;
		}
		return $result;
	}

	function checkFileSize($fileSize)
	{
		//check for filesize
		if ( $fileSize <= 0 )
		{
			 $this->error = JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_EMPTY_FILE' );
		}
		if ( $fileSize > 2 * 1024 * 1024) // TODO: configuration option
		{
			$this->error = JText::sprintf ( 'COM_KUNENA_UPLOAD_ERROR_SIZE_X', $fileSize );
		}
		return ($this->error !== false);
	}

	function processFile( $file ){

		//imageInof gets us MIME tyep and size information if it is an image
		$this->imageInfo = @getimagesize ( $file );

		// Get a hash value from the file
		$this->fileHash = md5_file ( $this->fileTemp );



	}

	function uploadFile($uploadPath, $input='kattachment', $ajax=true) {
		$result = array ();
		$this->error = false;

		require_once(KUNENA_PATH_LIB .DS. 'kunena.file.class.php');

		if (!CKunenaFolder::exists($uploadPath)) {
			if (!CKunenaFolder::create($uploadPath)) {
				$this->error = JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_CREATE_DIR' );
				return false;
			}
		}

		$this->fileName = CKunenaFile::makeSafe ( JRequest::getVar ( 'name', '' ) );
		$this->fileSize = 0;
		$chunk = JRequest::getInt ( 'chunk', 0 );
		$chunks = JRequest::getInt ( 'chunks', 0 );

		if ($chunks && $chunk >= $chunks)
			$this->error = JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_EXTRA_CHUNK' );
		//If uploaded by using normal form (no AJAX)
		if ($ajax == false || isset ( $_REQUEST ["multipart"])) {
			$file = JRequest::getVar ( $input, NULL, 'FILES', 'array' );
			if (isset($file ['tmp_name'])) {
				$this->fileTemp = $file ['tmp_name'];
				$this->fileSize = $file ['size'];
				if (! $this->fileName)
					$this->fileName = CKunenaFile::makeSafe ( $file ['name'] );
					//any errors the server registered on uploading
				switch ($file ['error']) {
					case 0 : // UPLOAD_ERR_OK :
						break;

					case 1 : // UPLOAD_ERR_INI_SIZE :
					case 2 : // UPLOAD_ERR_FORM_SIZE :
						$this->error = JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_SIZE' );
						break;

					case 3 : // UPLOAD_ERR_PARTIAL :
						$this->error = JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_PARTIAL' );
						break;

					case 4 : // UPLOAD_ERR_NO_FILE :
						$this->error = JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_NO_FILE' );
						break;

					case 5 : // UPLOAD_ERR_NO_TMP_DIR :
						$this->error = JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_NO_TMP_DIR' );
						break;

					case 7 : // UPLOAD_ERR_CANT_WRITE, PHP 5.1.0
						$this->error = JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_CANT_WRITE' );
						break;

					case 8 : // UPLOAD_ERR_EXTENSION, PHP 5.2.0
						$this->error = JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_EXTENSION' );
						break;

					default :
						$this->error = JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_UNKNOWN' );
				}
				if (!$this->error) $this->checkFileSize($this->fileSize);
			}
			else
			{
				$this->error = JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_UNDEFINED' );
			}
			if (!$this->error && !is_uploaded_file ( $file ['tmp_name'] )) {
				$this->error = JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_NOT_UPLOADED' );
			}
		} else {
			// Currently not in use: this is meant for experimental AJAX uploads
			// Open temp file
			$this->fileTemp = CKunenaPath::tmpdir() . DS . 'kunena_' . md5 ( $this->_my->id . '/' . $this->_my->username . '/' . $this->fileName );
			$out = fopen ($this->fileTemp, $chunk == 0 ? "wb" : "ab");
			if ($out) {
				// Read binary input stream and append it to temp file
				$in = fopen ( "php://input", "rb" );

				if ($in) {
					while ( ( $buff = fread ( $in, 8192 ) ) != false )
						fwrite ( $out, $buff );
				} else {
					$this->error = JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_NO_INPUT' );
				}

				$fileInfo = fstat($out);
				$this->fileSize = $fileInfo['size'];
				fclose ( $out );
				if (!$this->error) $this->checkFileSize($this->fileSize);
				if ($chunk+1 < $chunks) {
					$this->status = empty($this->error);
					return $this->status;
				}
			} else {
				$this->error = JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_NO_OUTPUT' );
			}
		}
		// Terminate early if we already hit an error
		if ($this->error) {
			return false;
		}

		//check the file extension is ok
		$uploadedFileNameParts = explode ( '.', $this->fileName );
		$uploadedFileExtension = array_pop ( $uploadedFileNameParts );

		$validFileExts = explode ( ',', 'jpeg,jpg,png,gif' );

		//assume the extension is false until we know its ok
		$extOk = false;

		//go through every ok extension, if the ok extension matches the file extension (case insensitive)
		//then the file extension is ok
		foreach ( $validFileExts as $key => $value ) {
			if (preg_match ( "/$value/i", $uploadedFileExtension )) {
				$extOk = true;
			}
		}

		if ($extOk == false) {
			$this->error = JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_EXTENSION' );
			return false;
		}

		//we are going to define what file extensions/MIMEs are ok, and only let these ones in (whitelisting), rather than try to scan for bad
		//types, where we might miss one (whitelisting is always better than blacklisting)
		$okMIMETypes = 'image/jpeg,image/pjpeg,image/png,image/x-png,image/gif';
		$validFileTypes = explode ( ",", $okMIMETypes );

		//for security purposes, we will also do a getimagesize on the temp file (before we have moved it
		//to the folder) to check the MIME type of the file, and whether it has a width and height
		$this->imageInfo = @getimagesize ( $this->fileTemp );


		$this->processFile($this->fileTemp);


		//if the temp file does not have a width or a height, or it has a non ok MIME, return
		if (! is_int ( $this->imageInfo [0] ) || ! is_int ( $this->imageInfo [1] ) || ! in_array ( $this->imageInfo ['mime'], $validFileTypes )) {
			$this->error = JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_MIME' );
			return false;
		}

		//TODO: Create a new version from the file (if hash is different)
		/*
		if (file_exists($uploadPath .DS. $newFileName)) {
			$newFileName = $imageName . '-' . date('Ymd') . "." . $imageExt;
			for ($i=2; file_exists($uploadPath .DS. $newFileName); $i++) {
				$newFileName = $imageName . '-' . date('Ymd') . "-$i." . $imageExt;
			}
		}
		*/

		if (! CKunenaFile::move ( $this->fileTemp, $uploadPath .DS.  $this->fileName )) {
			$this->error = JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_NOT_MOVED'.' '.$uploadPath .  $this->fileName );
			return false;
		}

		$this->ready = true;
		return $this->status = true;
	}

}