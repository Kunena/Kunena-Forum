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

class CKunenaAttachments {
	protected $_db;
	protected $_my;
	protected $_session;

	function __construct() {
		$this->_db = &JFactory::getDBO ();
		$this->_my = &JFactory::getUser ();
		$this->_config = KunenaFactory::getConfig ();
		$this->_session = KunenaFactory::getSession ();
	}

	function &getInstance() {
		static $instance = NULL;

		if (! $instance) {
			$instance = new CKunenaAttachments ( );
		}
		return $instance;
	}

	function upload($mesid=0, $key='kattachment', $ajax=true) {
		require_once (KUNENA_PATH_LIB .DS. 'kunena.upload.class.php');
		$upload = new CKunenaUpload();
		$upload->uploadFile(KUNENA_PATH_UPLOADED . DS . $this->_my->id, $key, $ajax);
		$fileinfo = $upload->getFileInfo();

		$folder = KUNENA_RELPATH_UPLOADED . '/' . $this->_my->id;
		if ($fileinfo['ready'] === true) {
			if(JDEBUG == 1 && defined('JFIREPHP')){
				FB::log('Kunena save attachment: ' . $fileinfo['name']);
			}
			$this->_db->setQuery ( "INSERT INTO #__kunena_attachments (mesid, userid, hash, size, folder, filetype, filename) values (" .
				(int)$mesid . "," . (int)$this->_my->id . "," . $this->_db->quote ( $fileinfo['hash'] ) . "," .
				$this->_db->quote ( $fileinfo['size'] ) . "," . $this->_db->quote ( $folder ) . "," . $this->_db->quote ( $fileinfo['mime'] ) . "," .
				$this->_db->quote ( $fileinfo['name'] ) . ")" );

				if (! $this->_db->query () || $this->_db->getErrorNum()) {
				$upload->fail(JText::_('COM_KUNENA_UPLOAD_ERROR_ATTACHMENT_DATABASE_STORE'));
				$fileinfo = $upload->fileInfo();
			}
		}
			if(JDEBUG == 1 && defined('JFIREPHP')){
				FB::log('Kunena save attachment ready');
			}
		return $fileinfo;
	}

	function multiupload($mesid=0) {
		$fileinfo = array();
		foreach ($_FILES as $key=>$file) {
			if ($file['error'] != UPLOAD_ERR_NO_FILE) $fileinfo[] = $this->upload($mesid, $key, false);
		}
		return $fileinfo;
	}

	function assign($mesid) {
		if (!$mesid) return;
		$this->_db->setQuery ( "UPDATE #__kunena_attachments SET mesid=".(int)$mesid." WHERE userid=".(int)$this->_my->id." AND mesid=0" );
		$this->_db->query ();
		check_dberror ( "Unable to assign attachments to message ".$mesid );
	}

	function get($mesids) {
		$ret = array();
		if (empty($mesids)) return $ret;
		$query = "SELECT * FROM #__kunena_attachments WHERE mesid IN ($mesids)";
		$this->_db->setQuery ( $query );
		$attachments = $this->_db->loadObjectList ();
		check_dberror ( 'Unable to load attachments' );
		foreach ($attachments as $attachment) {
			// Check if file has been pre-processed
			if (is_null ( $attachment->hash )) {
				// This attachment has not been processed.
				// It migth be a legacy file, or the settings might have been reset.
				// Force recalculation ...

				// TODO: Perform image re-prosessing
			}

			// combine all images into one type
			$attachment->shorttype = (stripos ( $attachment->filetype, 'image/' ) !== false) ? 'image' : $attachment->filetype;
			if ($attachment->shorttype == 'image' && !$this->_my->id && !$this->_config->showimgforguest) continue;
			if ($attachment->shorttype != 'image' && !$this->_my->id && !$this->_config->showfileforguest) continue;

			$ret[$attachment->mesid][] = $attachment;
		}
		return $ret;
	}
}