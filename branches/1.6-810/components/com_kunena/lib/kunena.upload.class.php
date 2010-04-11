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

require_once(KUNENA_PATH_LIB .DS. 'kunena.file.class.php');
require_once (KUNENA_PATH_LIB .DS. 'kunena.image.class.php');

/**
 * Class to handle file uploads and process the uploaded files.
 *
 * @package		Kunena.lib
 * @since		1.6
 */

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
	protected $status = true;
	protected $error = false;

	function __construct() {
		$this->_db = &JFactory::getDBO ();
		$this->_my = &JFactory::getUser ();
		$this->_session = &KunenaFactory::getSession ();
		$this->_config = &CKunenaConfig::getInstance ();
		$this->_isimage = false;
		$this->_isfile = false;
	}

	function __destruct() {
		if (!$this->status) {
			if(JDEBUG == 1 && defined('JFIREPHP')){
				FB::log('Kunena upload failed: '.$this->error);
			}
		}
		// Delete any left over files in temp
		if (is_file($this->fileTemp)) unlink ( $this->fileTemp );
		if (is_file($this->fileTemp.'raw')) unlink ( $this->fileTemp.'raw' );
		if (is_file($this->fileTemp.'thumb')) unlink ( $this->fileTemp.'thumb' );
	}

	function fail($errormsg) {
		$this->error = $errormsg;
		$this->status = false;
	}

	function getStatus(){
		return $this->status;
	}

	function resetStatus(){
		$this->error = '';
		$this->status = true;
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
			 $this->fail(JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_SIZE_0' ));
		}

		if (!$this->_isfile && !$this->_isimage){
			$this->fail(JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_UNDEFINED' ));
		} else if (($this->_isfile && ($fileSize > $this->_config->filesize*1024))||
			($this->_isimage && ($fileSize > $this->_config->imagesize*1024))){
			$this->fail(JText::sprintf ( 'COM_KUNENA_UPLOAD_ERROR_SIZE_X', $fileSize ));
		}

		return $this->getStatus();
	}

	function resizeImage( $src, $target, $max_width, $max_height ){
		$source_pic = $src;
		$destination_pic = $target;

		$src = imagecreatefromjpeg($source_pic);
		if($src === false){
			$this->fail(JText::sprintf ( 'COM_KUNENA_UPLOAD_ERROR_RESIZE_1' ));
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
			$this->fail(JText::_( 'COM_KUNENA_UPLOAD_ERROR_RESIZE_SAVE'));
		}
		imagedestroy($src);
		imagedestroy($tmp);
	}

	function uploaded($input='kattachment') {
		$file = JRequest::getVar ( $input, NULL, 'FILES', 'array' );
		if (isset($file ['tmp_name']) && $file ['error'] == 0) return true;
	}

	function uploadFile($uploadPath, $input='kattachment', $ajax=true) {
		$result = array ();
		$this->resetStatus();

		// create thumb and upload directory if it does not exist
		if (!CKunenaFolder::exists($uploadPath.'/thumb')) {
			if (!CKunenaFolder::create($uploadPath.'/thumb')) {
				$this->fail(JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_CREATE_DIR' ));
				return false;
			}
		}
		// create originals/raw folder if it does not exist
		if (!CKunenaFolder::exists($uploadPath.'/raw')) {
			if (!CKunenaFolder::create($uploadPath.'/raw')) {
				$this->fail(JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_CREATE_DIR' ));
				return false;
			}
		}

		$this->fileName = CKunenaFile::makeSafe ( JRequest::getVar ( $input.'_name', '' ) );
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
						$this->fail(JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_SIZE' ) . "DEBUG: file[error]". $file ['error']);
						break;

					case 3 : // UPLOAD_ERR_PARTIAL :
						$this->fail(JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_PARTIAL' ));
						break;

					case 4 : // UPLOAD_ERR_NO_FILE :
						$this->fail(JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_NO_FILE' ));
						break;

					case 5 : // UPLOAD_ERR_NO_TMP_DIR :
						$this->fail(JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_NO_TMP_DIR' ));
						break;

					case 7 : // UPLOAD_ERR_CANT_WRITE, PHP 5.1.0
						$this->fail(JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_CANT_WRITE' ));
						break;

					case 8 : // UPLOAD_ERR_EXTENSION, PHP 5.2.0
						$this->fail(JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_PHP_EXTENSION' ));
						break;

					default :
						$this->fail(JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_UNKNOWN' ));
				}
			}
			else
			{
				$this->fail(JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_FORM_UNDEFINED' ));
			}
			if (!$this->error && !is_uploaded_file ( $file ['tmp_name'] )) {
				$this->fail(JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_NOT_UPLOADED' ));
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
					$this->fail(JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_NO_INPUT' ));
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
				$this->fail(JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_CANT_WRITE' ));
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
			$this->Fail(JText::sprintf ( 'COM_KUNENA_UPLOAD_ERROR_EXTENSION', $this->_config->imagetypes, $this->_config->filetypes ));
			return false;
		}

		$this->checkFileSize($this->fileSize);
		// Check again for error and terminate early if we already hit an error
		if ($this->error) {
			return false;
		}

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
					$this->fail(JText::sprintf ( 'COM_KUNENA_UPLOAD_ERROR_MIME', $this->imageInfo ['mime'], $this->_config->imagetypes) );
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

		// If this is a valid image we need to resize/resample it to the config settings
		if ($this->_isimage){
			// First rename the raw image file(original) with php function (FTP user cannot do this)
			if (!rename ( $this->fileTemp, $this->fileTemp.'.raw' )) {
				$this->fail(JText::sprintf('COM_KUNENA_UPLOAD_ERROR_NOT_MOVED',$this->fileName.'.raw'));
				return false;
			}

			// Quality for jpeg and png files
			$quality = intval($this->_config->imagequality);
			// If quality value provided is invalid, reset to default
			if ($quality < 1 || $quality > 100) $quality = 60;

			$options = array('quality' => $quality);

			$imageRaw = new CKunenaImage($this->fileTemp.'.raw');
			if ($imageRaw->getError()) {
				$this->fail(JText::_($imageRaw->getError()));
				return false;
			}
			$image = $imageRaw->resize($this->_config->imagewidth, $this->_config->imageheight);
			$type = $imageRaw->getType();
			unset($imageRaw);
			$imageThumb = $image->resize($this->_config->thumbwidth, $this->_config->thumbheight,true,CKunenaImage::SCALE_OUTSIDE);
			$imageThumb->crop($this->_config->thumbwidth, $this->_config->thumbheight,0,0,false,CKunenaImage::SCALE_INSIDE);

			$image->toFile($this->fileTemp,$type,$options);
			$imageThumb->toFile($this->fileTemp.'.thumb',$type,$options);
		}

		// Populate hash, file size and other info
		// Get a hash value from the file
		$this->fileHash = md5_file ( $this->fileTemp );

		// Also re-calculate physical file properties lize size as images might have been shrunk
		$stat = stat($this->fileTemp);
		if (! $stat) {
			$this->fail(JText::_('COM_KUNENA_UPLOAD_ERROR_STAT').' '.$this->fileTemp);
			return false;
		}

		$this->fileSize = $stat['size'];

		// All the processing is complete - now we need to move the file(s) into the final location
		if (! CKunenaFile::move ( $this->fileTemp, $uploadPath.'/'.$this->fileName )) {
			$this->fail(JText::sprintf('COM_KUNENA_UPLOAD_ERROR_NOT_MOVED', $uploadPath.'/'.$this->fileName));
			return false;
		}

		// For images we also have to move the raw (original) and thumbnails
		if ($this->_isimage){
			if (! CKunenaFile::move ( $this->fileTemp.'.raw', $uploadPath.'/raw/'.$this->fileName )) {
				$this->fail(JText::sprintf('COM_KUNENA_UPLOAD_ERROR_NOT_MOVED', $uploadPath.'/raw/'.$this->fileName));
				return false;
			}
			if (! CKunenaFile::move ( $this->fileTemp.'.thumb', $uploadPath.'/thumb/'.$this->fileName )) {
				$this->fail(JText::sprintf('COM_KUNENA_UPLOAD_ERROR_NOT_MOVED', $uploadPath.'/thumb/'.$this->fileName));
				return false;
			}
		}

		$this->ready = true;
		return $this->status = true;
	}
}