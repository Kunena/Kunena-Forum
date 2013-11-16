<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Lib
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();

/**
 * Class to handle file uploads and process the uploaded files.
 *
 * @since		1.6
 * @deprecated 3.1
 */
class CKunenaUpload {
	protected $_db;
	protected $_my;
	protected $_session;
	protected $_config;

	protected $_isimage;
	protected $_isfile;

	protected $realName = false;
	protected $fileName = false;
	protected $fileTemp = false;
	protected $fileSize = false;
	protected $fileHash = false;
	protected $imageInfo = false;

	protected $ready = false;
	protected $status = true;
	protected $error = false;
	protected $not_valid_img_ext = true;

	protected $validImageExts = array();
	protected $validFileExts = array();

	function __construct($catid=null) {
		$this->_db = JFactory::getDBO ();
		$this->_my = JFactory::getUser ();
		$this->_session = KunenaFactory::getSession ();
		$this->_config = KunenaFactory::getConfig ();
		$this->_isimage = false;
		$this->_isfile = false;
		$me = KunenaUserHelper::getMyself();
		$this->validImageExts = (array) KunenaForumMessageAttachmentHelper::getImageExtensions($catid,$me->userid);
		$this->validFileExts = (array) KunenaForumMessageAttachmentHelper::getFileExtensions($catid,$me->userid);
		$this->setImageResize(intval($this->_config->imagesize)*1024, intval($this->_config->imagewidth), intval($this->_config->imageheight), intval($this->_config->imagequality));
	}

	function __destruct() {
		// Delete any left over files in temp
		if (is_file($this->fileTemp)) unlink ( $this->fileTemp );
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

	function setAllowedExtensions($imageExts=array(), $fileExts=array()) {
		if (!is_array($imageExts)) $imageExts = explode ( ',', $imageExts );
		if (!is_array($fileExts)) $fileExts = explode ( ',', $fileExts );
		$this->validImageExts = $imageExts;
		$this->validFileExts = $fileExts;
	}

	function setImageResize($size, $maxwidth, $maxheight, $quality) {
		$this->imagesize =  intval($size);
		$this->imagewidth = intval($maxwidth);
		$this->imageheight = intval($maxheight);
		$this->imagequality = intval($quality);
		if ($this->imagequality < 1 || $this->imagequality > 100) $this->imagequality = 70;
	}

	function getFileInfo()
	{
		$result = array(
			'status' => $this->status,
			'ready' => $this->ready,
			'name' => $this->fileName,
			'real' => $this->realName,
			'size' => $this->fileSize
		);

		if ($this->fileHash) {
			$result['hash'] = $this->fileHash;
		}
		if ($this->imageInfo) {
			$result['width'] = $this->imageInfo->width;
			$result['height'] = $this->imageInfo->height;
			$result['mime'] = $this->imageInfo->mime;
		} else {
			$result['mime'] = '';
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

	function uploaded($input='kattachment') {
		$file = JRequest::getVar ( $input, NULL, 'FILES', 'array' );
		if (is_uploaded_file ( $file ['tmp_name'] ) && $file ['error'] == 0) return true;
		return false;
	}

	function getValidExtension($validExts) {
		$ret = null;
		// Go through every allowed extension, if the extension matches the file extension (case insensitive)
		//then the file extension is ok
		foreach ( $validExts as $ext ) {
			$ext = trim ( $ext );
			if (!$ext) {
				// Do not allow empty extensions
				continue;
			}
			if ($ext[0] != '.') {
				// Add first dot if it is missing in extension list
				$ext = '.'.$ext;
			}
			$extension = substr($this->realName, -strlen($ext));
			if (strtolower($extension) == strtolower($ext)) {
				// File must contain one letter before extension
				$ret[] = substr($this->realName, 0, -strlen($ext));
				$ret[] = substr($extension, 1);
				break;
			}
		}
		return $ret;
	}

	function uploadFile($uploadPath, $input='kattachment', $filename='', $ajax=true) {
		$this->resetStatus();

		// create upload directory if it does not exist
		if (!JFolder::exists($uploadPath)) {
			if (!JFolder::create($uploadPath)) {
				$this->fail(JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_CREATE_DIR' ));
				return false;
			}
		}
		KunenaFolder::createIndex($uploadPath);

		// Get file name and validate with path type
		$this->realName = JFile::makeSafe(JRequest::getString ( $input.'_name', '', 'post' ));
		$this->fileSize = 0;
		$chunk = JRequest::getInt ( 'chunk', 0 );
		$chunks = JRequest::getInt ( 'chunks', 0 );

		if ($chunks && $chunk >= $chunks)
			$this->error = JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_EXTRA_CHUNK' );

		//If uploaded by using normal form (no AJAX)
		if ($ajax == false || isset ( $_REQUEST ["multipart"])) {
			$file = JRequest::getVar ( $input, NULL, 'FILES', 'array' );
			if (!is_uploaded_file ( $file ['tmp_name'] )) {
				$this->fail(JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_NOT_UPLOADED' ));
				return false;
			}
			$this->fileTemp = $file ['tmp_name'];
			$this->fileSize = $file ['size'];
			if (!$this->realName) {
				$this->realName = $file['name'];
			}
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
		} else {
			// Currently not in use: this is meant for experimental AJAX uploads
			// Open temp file
			$this->fileTemp = KunenaPath::tmpdir() . '/kunena_' . md5 ( $this->_my->id . '/' . $this->_my->username . '/' . $this->realName );
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

				clearstatcache();
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

		// assume the extension is false until we know its ok
		$extOk = false;
		$fileparts = $this->getValidExtension($this->validFileExts);
		$uploadedFileExtension = '';
		if ($fileparts) {
			$this->_isfile = true;
			$extOk = true;
			$uploadedFileBasename = $fileparts[0];
			$uploadedFileExtension = $fileparts[1];
		}
		$fileparts = $this->getValidExtension($this->validImageExts);
		if ($fileparts) {
			$this->_isimage = true;
			$extOk = true;
			$uploadedFileBasename = $fileparts[0];
			$uploadedFileExtension = $fileparts[1];
		}

		if ($extOk == false) {
			$imglist = implode(', ',$this->validImageExts);
			$filelist = implode(', ',$this->validFileExts);
			if ($imglist && $filelist) $this->Fail(JText::sprintf ( 'COM_KUNENA_UPLOAD_ERROR_EXTENSION', $imglist, $filelist ));
			else if ($imglist && !$filelist) $this->Fail(JText::sprintf ( 'COM_KUNENA_UPLOAD_ERROR_EXTENSION_FILE', $this->_config->filetypes ));
			else if (!$imglist && $filelist) $this->Fail(JText::sprintf ( 'COM_KUNENA_UPLOAD_ERROR_EXTENSION_IMAGE', $this->_config->imagetypes ));
			else $this->Fail(JText::sprintf ( 'COM_KUNENA_UPLOAD_ERROR_NOT_ALLOWED', $filelist ));
			$this->not_valid_img_ext = false;
			return false;
		}

		// Special processing for images
		if ($this->_isimage){
			try {
				$this->imageInfo = JImage::getImageFileProperties($this->fileTemp);
			} catch (Exception $e) {
				// TODO: better error message.
				$this->fail(JText::_($e->getMessage()));
				return false;
			}

			// Let see if we need to check the MIME type
			if ($this->_config->checkmimetypes){
				// check against whitelist of MIME types
				$validFileTypes = explode ( ",", $this->_config->imagemimetypes );

				//if the temp file does not have a width or a height, or it has a non ok MIME, return
				if (!is_int ( $this->imageInfo->width ) || !is_int ( $this->imageInfo->height ) ||
					!in_array ( $this->imageInfo->mime, $validFileTypes )) {
					$this->fail(JText::sprintf ( 'COM_KUNENA_UPLOAD_ERROR_MIME', $this->imageInfo->mime, $this->_config->imagetypes) );
					return false;
				}
			}

			// If image is not inside allowed size limits, resize it
			if ($this->fileSize > $this->imagesize || $this->imageInfo->width > $this->imagewidth || $this->imageInfo->height > $this->imageheight) {
				// Calculate quality for both JPG and PNG.
				$quality = $this->imagequality;
				if ($quality < 1 || $quality > 100) $quality = 70;
				if ($this->imageInfo->type == IMAGETYPE_PNG) $quality = intval(($quality-1)/10);
				$options = array('quality' => $quality);

				try {
					$image = new JImage($this->fileTemp);
					$image = $image->resize($this->imagewidth, $this->imageheight, false);
					$image->toFile($this->fileTemp, $this->imageInfo->type, $options);
					unset($image);
				} catch (Exception $e) {
					// TODO: better error message.
					$this->fail(JText::_($e->getMessage()));
					return false;
				}

				// Re-calculate physical file size: image has been shrunk
				clearstatcache();
				$stat = stat($this->fileTemp);
				if (! $stat) {
					$this->fail(JText::_('COM_KUNENA_UPLOAD_ERROR_STAT', $this->fileTemp));
					return false;
				}
				$this->fileSize = $stat['size'];
			}
		}

		$this->checkFileSize($this->fileSize);
		// Check again for error and terminate early if we already hit an error
		if ($this->error) {
			return false;
		}

		// Populate hash, file size and other info
		// Get a hash value from the file
		$this->fileHash = md5_file ( $this->fileTemp );

		if ($filename === null) {
			// Use random non-existing filename.
			do {
				$this->fileName = md5(rand());
			} while (file_exists("{$uploadPath}/{$this->fileName}"));
		} else {
			// Override filename if given in the parameter
			if($filename) $uploadedFileBasename = $filename;
			$uploadedFileBasename = KunenaFile::makeSafe($uploadedFileBasename);
			if (empty($uploadedFileBasename)) $uploadedFileBasename = 'h'.substr($this->fileHash, 2, 7);

			// Rename file if there is already one with the same name
			$newFileName = $uploadedFileBasename . "." . $uploadedFileExtension;
			if (file_exists($uploadPath .'/'. $newFileName)) {
				$newFileName = $uploadedFileBasename . date('_Y-m-d') . "." . $uploadedFileExtension;
				for ($i=2; file_exists("{$uploadPath}/{$newFileName}"); $i++) {
					$newFileName = $uploadedFileBasename . date('_Y-m-d') . "-$i." . $uploadedFileExtension;
				}
			}
			$this->fileName = $newFileName;
			$this->fileName = preg_replace('/[[:space:]]/', '',$this->fileName);
		}

		// All the processing is complete - now we need to move the file(s) into the final location
		@chmod($this->fileTemp, 0644);
		if (! JFile::copy ( $this->fileTemp, $uploadPath.'/'.$this->fileName )) {
			$this->fail(JText::sprintf('COM_KUNENA_UPLOAD_ERROR_NOT_MOVED', $uploadPath.'/'.$this->fileName));
			unlink($this->fileTemp);
			return false;
		}
		unlink($this->fileTemp);
		JPath::setPermissions($uploadPath.'/'.$this->fileName);

		$this->ready = true;
		return $this->status = true;
	}
}
