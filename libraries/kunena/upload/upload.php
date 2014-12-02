<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

require_once(KPATH_SITE.'/lib/kunena.file.class.php');
require_once(KPATH_SITE.'/lib/kunena.image.class.php');

/**
 * Class to handle file uploads.
 */

class KunenaUpload extends JObject {
	protected $fileName = false;
	protected $fileTemp = false;
	protected $fileSize = false;
	protected $fileHash = false;

	protected $validExtensions = array();

	function __construct($extensions = null) {
		if ($extensions) $this->setValidExtensions($extensions);
	}

	function __destruct() {
		// Delete any left over files in temp
		if (is_file($this->fileTemp)) unlink ( $this->fileTemp );
	}

	public static function getInstance($extensions = null) {
		return new KunenaUpload($extensions);
	}
	function setValidExtensions($extensions=array()) {
		if (!is_array($extensions)) $extensions = explode ( ',', $extensions );
		foreach ($extensions as $ext) {
			$ext = trim((string)$ext);
			if (!$ext) continue;
			if ($ext[0] != '.') {
				$ext = '.'.$ext;
			}
			$this->validExtensions[$ext] = $ext;
		}
	}

	protected function splitFilename() {
		$ret = null;
		// Check if file extension matches any allowed extensions (case insensitive)
		foreach ( $this->validExtensions as $ext ) {
			$extension = substr($this->fileName, -strlen($ext));
			if (strtolower($extension) == strtolower($ext)) {
				// File must contain one letter before extension
				$ret[] = substr($this->fileName, 0, -strlen($ext));
				$ret[] = substr($extension, 1);
				break;
			}
		}
		return $ret;
	}

	protected function checkUpload($file) {
		// Check for upload errors
		switch ($file ['error']) {
			case UPLOAD_ERR_OK :
				break;

			case UPLOAD_ERR_INI_SIZE :
			case UPLOAD_ERR_FORM_SIZE :
				$this->setError(JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_SIZE' ));
				break;

			case UPLOAD_ERR_PARTIAL :
				$this->setError(JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_PARTIAL' ));
				break;

			case UPLOAD_ERR_NO_FILE :
				$this->setError(JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_NO_FILE' ));
				break;

			case UPLOAD_ERR_NO_TMP_DIR :
				$this->setError(JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_NO_TMP_DIR' ));
				break;

			case UPLOAD_ERR_CANT_WRITE :
				$this->setError(JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_CANT_WRITE' ));
				break;

			case UPLOAD_ERR_EXTENSION :
				$this->setError(JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_PHP_EXTENSION' ));
				break;

			default :
				$this->setError(JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_UNKNOWN' ));
		}
		if (!$this->getError() && (!isset($file['tmp_name']) || !is_uploaded_file ($file['tmp_name']))) {
			$this->setError(JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_NOT_UPLOADED' ));
		}
		return $this->getError();
	}

	function ajaxUpload($targetDir=null, $filename=null) {
		$chunk = JRequest::getInt ( 'chunk', 0 );
		$chunks = JRequest::getInt ( 'chunks', 0 );
		$this->fileName = JFile::makeSafe ( JRequest::getString ( 'name', null, 'POST' ) );

		if (!$this->fileName) {
			return JError::raiseError(404, JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_NO_FILE' ));
		}
		if (! JSession::checkToken ('get')) {
			$this->setError(JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_NOT_UPLOADED' ));
		}

		// Look for the content type header
		if (isset($_SERVER["HTTP_CONTENT_TYPE"])) {
			$contentType = $_SERVER["HTTP_CONTENT_TYPE"];
		} elseif (isset($_SERVER["CONTENT_TYPE"])) {
			$contentType = $_SERVER["CONTENT_TYPE"];
		} else {
			$contentType = '';
		}

		if ($chunks && $chunk >= $chunks) {
			$this->setError(JText::_ ( 'COM_KUNENA_UPLOAD_ERROR_EXTRA_CHUNK' ));
		}

		if (strpos($contentType, "multipart") !== false) {
			// Older WebKit browsers didn't support multipart in HTML5
			$error = $this->checkUpload($_FILES['file']);
			$in = !$error ? fopen($_FILES['file']['tmp_name'], "rb") : null;
		} else {
			// Multipart upload
			$error = null;
			$in = fopen("php://input", "rb");
		}
		if (!$in && !$this->getError()) {
			$this->setError(JText::_ ( 'Failed to open input stream.' ));
		}
		// Open temp file
		if (!$this->getError()) {
			//$out = fopen("{$targetDir}/{$filename}", $chunk == 0 ? "wb" : "ab");
			//if (!$out) $this->setError(JText::_ ( 'Failed to open output stream.' ));
		}
		if (!$this->getError()) {
			// 5 minutes execution time
			@set_time_limit(5 * 60);

			while (($buff = fread($in, 8192)) != '') {
				//fwrite($out, $buff);
			}
		}

		if ($in) fclose($in);
		//if ($out) fclose($out);
		if ($error === null) @unlink($_FILES['file']['tmp_name']);

		// Return JSON-RPC response
		$this->sendResponse();
	}

	protected function sendResponse() {
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		while(@ob_end_clean());
		ob_start();
		$error = $this->getError();
		if ($error) {
			jexit('{"error" : '.$error.'", "id" : "id"}');
		}
		jexit('{"success" : true, "id" : "id"}');
	}
}
