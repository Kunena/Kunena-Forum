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
	protected $_config;

	protected $_isimage;
	protected $_isfile;

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
		$this->_session = &CKunenaSession::getInstance ();
		$this->_config = &CKunenaConfig::getInstance ();
		$this->_isimage = false;
		$this->_isfile = false;
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

	function getFileInfo()
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

		if (!$this->_isfile && !$this->_isimage){
			$this->error = JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_UNDEFINED' );
		} else if (($this->_isfile && ($fileSize > $this->_config->filesize*1024))||
			($this->_isimage && ($fileSize > $this->_config->imagesize*1024))){
			$this->error = JText::sprintf ( 'COM_KUNENA_UPLOAD_ERROR_SIZE_X', $fileSize );
		}

		return ($this->error !== false);
	}

	function resizeImage( $file ){
		$source_pic = $file;
		$destination_pic = $file;
		$max_width = $this->_config->imagewidth;
		$max_height = $this->_config->imageheight;

		$src = imagecreatefromjpeg($source_pic);
		if($src === false){
			$this->error = JText::sprintf ( 'COM_KUNENA_UPLOAD_ERROR_RESIZE_1' );
			return;
		}
		list($width,$height)=getimagesize($source_pic);

		$x_ratio = $max_width / $width;
		$y_ratio = $max_height / $height;

		if( ($width <= $max_width) && ($height <= $max_height) ){
		    $tn_width = $width;
		    $tn_height = $height;
		    }elseif (($x_ratio * $height) < $max_height){
		        $tn_height = ceil($x_ratio * $height);
		        $tn_width = $max_width;
		    }else{
		        $tn_width = ceil($y_ratio * $width);
		        $tn_height = $max_height;
		}

		$tmp=imagecreatetruecolor($tn_width,$tn_height);
		imagecopyresampled($tmp,$src,0,0,0,0,$tn_width, $tn_height,$width,$height);

		$quality = intval($this->_config->imagequality);
		// If quality value provided is invalid, reset to default
		if ($quality < 1 || $quality > 100) $quality = 60;

		if (!imagejpeg($tmp,$destination_pic,$quality)){
			$this->error = JText::sprintf ( 'COM_KUNENA_UPLOAD_ERROR_RESIZE_SAVE');
		}
		imagedestroy($src);
		imagedestroy($tmp);
	}

	function createThumb( $file ){


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
						$this->error = JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_SIZE' ) . "DEBUG: file[error]". $file ['error'];
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

		$validFileExts = explode ( ',', $this->_config->filetypes );
		$validImageExts = explode ( ',', $this->_config->imagetypes );

		//assume the extension is false until we know its ok
		$extOk = false;

		//go through every ok extension, if the ok extension matches the file extension (case insensitive)
		//then the file extension is ok
		foreach ( $validFileExts as $key => $value ) {
			if (preg_match ( "/$value/i", $uploadedFileExtension )) {
				$this->_isfile = true;
				$extOk = true;
			}
		}
		foreach ( $validImageExts as $key => $value ) {
			if (preg_match ( "/$value/i", $uploadedFileExtension )) {
				$this->_isimage = true;
				$extOk = true;
			}
		}

		if ($extOk == false) {
			$this->error = JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_EXTENSION' );
			return false;
		}

		$this->checkFileSize($this->fileSize);
		// Check again for error and terminate early if we already hit an error
		if ($this->error) {
			return false;
		}

		// Populate hash, file size and other info
		// Get a hash value from the file
		$this->fileHash = md5_file ( $this->fileTemp );

		// Special processing for images
		if ($this->_isimage){
			$this->imageInfo = @getimagesize ( $this->fileTemp );
			// Let see if we need to check the MIME type
			if ($this->_config->checkmimetypes){
				// check against whitelist of MIME types
				$validFileTypes = explode ( ",", $this->_config->imagemimetypes );

				//if the temp file does not have a width or a height, or it has a non ok MIME, return
				if (!is_int ( $this->imageInfo [0] ) || !is_int ( $this->imageInfo [1] ) ||
					!in_array ( $this->imageInfo ['mime'], $validFileTypes )) {
					$this->error = JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_MIME' )." DEBUG Mimetype:". $this->imageInfo ['mime'] ;
					return false;
				}
			}
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

		// If this is a valid image we need to check dimensions and resize the image if it is too big
		if ($this->_isimage){
			$this->resizeImage($this->fileTemp);
			if ($this->error) {
				return false;
			}
		}

		if (! CKunenaFile::move ( $this->fileTemp, $uploadPath.'/'.$this->fileName )) {
			$this->error = JText::_('COM_KUNENA_UPLOAD_ERROR_NOT_MOVED').' '.$uploadPath.'/'.$this->fileName;
			return false;
		}

		// If this is a valid image we need to create a thumbnail for it
		if ($this->_isimage){
			$this->createThumb($this->fileName);
		}

		$this->ready = true;
		return $this->status = true;
	}

}