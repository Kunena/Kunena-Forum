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

	function upload($mesid=0, $key='kattachment', $ajax=true, &$message=null) {
		require_once (KUNENA_PATH_LIB .DS. 'kunena.upload.class.php');
		$path = KUNENA_PATH_UPLOADED . DS . $this->_my->id;
		$upload = new CKunenaUpload();
		$upload->uploadFile($path, $key, '', $ajax);
		$fileinfo = $upload->getFileInfo();

		$folder = KUNENA_RELPATH_UPLOADED . '/' . $this->_my->id;
		if ($fileinfo['ready'] === true) {
			if(JDEBUG == 1 && defined('JFIREPHP')){
				FB::log('Kunena save attachment: ' . $fileinfo['name']);
			}
			// FIXME: Warning raised, no MIME in other than images!!!
			$this->_db->setQuery ( "INSERT INTO {$this->_db->nameQuote('#__kunena_attachments')} (mesid, userid, hash, size, folder, filetype, filename) values (" .
				(int)$mesid . "," . (int)$this->_my->id . "," . $this->_db->quote ( $fileinfo['hash'] ) . "," .
				$this->_db->quote ( $fileinfo['size'] ) . "," . $this->_db->quote ( $folder ) . "," . $this->_db->quote ( $fileinfo['mime'] ) . "," .
				$this->_db->quote ( $fileinfo['name'] ) . ")" );

				if (! $this->_db->query () || $this->_db->getErrorNum()) {
				$upload->fail(JText::_('COM_KUNENA_UPLOAD_ERROR_ATTACHMENT_DATABASE_STORE'));
				$fileinfo = $upload->fileInfo();
			}
		}

		if ($this->isImage($fileinfo['mime']))
			CKunenaImageHelper::version($path . DS . $fileinfo['name'], $path .DS. 'thumb', $fileinfo['name'], $this->_config->thumbwidth, $this->_config->thumbheight, intval($this->_config->imagequality));

			// Fix attachments names inside message
		$found = preg_match('/\D*(\d)+/', $key, $matches);
		if (!empty($message) && $found) {
			$intkey = $matches[1];
			if (!$fileinfo['error']) {
				$message = preg_replace('/\[attachment\:'.$intkey.'\].*?\[\/attachment\]/u', '[attachment]'.$fileinfo['name'].'[/attachment]', $message);
			} else {
				$message = preg_replace('/\[attachment\:'.$intkey.'\](.*?)\[\/attachment\]/u', '[attachment]\\1[/attachment]', $message);
			}
		}
				if(JDEBUG == 1 && defined('JFIREPHP')){
			FB::log('Kunena save attachment ready');
		}

		return $fileinfo;
	}

	function multiupload($mesid=0, &$message=null) {
		$fileinfo = array();
		foreach ($_FILES as $key=>$file) {
			if ($file['error'] != UPLOAD_ERR_NO_FILE) $fileinfo[] = $this->upload($mesid, $key, false, $message);
		}
		return $fileinfo;
	}

	function assign($mesid) {
		// FIXME: this code seems to cause some weird bugs (and its not used) :)
		/*
		if (!$mesid) return;
		$this->_db->setQuery ( "UPDATE #__kunena_attachments SET mesid=".(int)$mesid." WHERE userid=".(int)$this->_my->id." AND mesid=0" );
		$this->_db->query ();
		if (KunenaError::checkDatabaseError()) return;
		*/
	}

	function isImage($mime) {
		return (stripos ( $mime, 'image/' ) !== false);
	}

	function get($mesids) {
		$ret = array();
		if (empty($mesids)) return $ret;
		$query = "SELECT * FROM {$this->_db->nameQuote('#__kunena_attachments')} WHERE mesid IN ($mesids) ORDER BY id";
		$this->_db->setQuery ( $query );
		$attachments = $this->_db->loadObjectList ('id');
		if (KunenaError::checkDatabaseError()) return $ret;
		foreach ($attachments as $attachment) {
			// Check if file has been pre-processed
			if (is_null ( $attachment->hash )) {
				// This attachment has not been processed.
				// It migth be a legacy file, or the settings might have been reset.
				// Force recalculation ...

				// TODO: Perform image re-prosessing
			}

			// combine all images into one type
			$attachment->shorttype = $this->isImage($attachment->filetype) ? 'image' : $attachment->filetype;
			if ($attachment->shorttype == 'image' && !$this->_my->id && !$this->_config->showimgforguest) continue;
			if ($attachment->shorttype != 'image' && !$this->_my->id && !$this->_config->showfileforguest) continue;
			$attachment->shortname = CKunenaTools::shortenFileName($attachment->filename);

			switch (strtolower($attachment->shorttype)){
				case 'image' :
					// Check for thumbnail and if available, use for display
					if (file_exists(JPATH_ROOT.'/'.$attachment->folder.'/thumb/'.$attachment->filename)){
						$thumb = $attachment->folder.'/thumb/'.$attachment->filename;
						$imgsize = '';
					} else {
						$thumb = $attachment->folder.'/'.$attachment->filename;
						$imgsize = 'width="'.$this->_config->thumbwidth.'px" height="'.$this->_config->thumbheight.'px"';
					}

					$img = '<img title="'.$attachment->filename.'" '.$imgsize.' src="'.JURI::ROOT().$thumb.'" alt="'.$attachment->filename.'" />';
					$attachment->thumblink = CKunenaLink::GetAttachmentLink($attachment->folder,$attachment->filename,$img,$attachment->filename, 'lightbox-attachments'.$attachment->id);
					$img = '<img title="'.$attachment->filename.'" src="'.JURI::ROOT().$attachment->folder.'/'.$attachment->filename.'" alt="'.$attachment->filename.'" />';
					$attachment->imagelink = CKunenaLink::GetAttachmentLink($attachment->folder,$attachment->filename,$img,$attachment->filename, 'lightbox-attachments'.$attachment->id);
					break;
				default :
					// Filetype without thumbnail or icon support - use default file icon
					$img = '<img src="'.KUNENA_URLICONSPATH.'attach_generic.png" alt="'.JText::_('COM_KUNENA_ATTACH').'" />';
					$attachment->thumblink = CKunenaLink::GetAttachmentLink($attachment->folder,$attachment->filename,$img,$attachment->filename, 'nofollow');
			}
			$ret[$attachment->mesid][$attachment->id] = $attachment;
		}
		return $ret;
	}

	function deleteMessage($mesids) {
		if (is_array($mesids) && !empty($mesids)) {
			$mesids = implode(',', $mesids);
		} else {
			$mesids = intval($mesids);
		}
		if ($mesids == 0) return;

		// Do not delete files which are used in other attachments or are not really Kunena attachments
		$this->_db->setQuery ( "SELECT a.* FROM {$this->_db->nameQuote('#__kunena_attachments')} AS a LEFT JOIN {$this->db->nameQuote('#__kunena_attachments')} AS b ON a.folder=b.folder AND a.filename=b.filename
			WHERE a.mesid IN ({$mesids}) AND (a.folder LIKE '%media/kunena/attachments%' OR a.folder LIKE '%images/fbfiles%') AND b.filename IS NULL" );
		$fileList = $this->_db->loadObjectlist ();
		if (KunenaError::checkDatabaseError()) return;
		foreach ( $fileList as $file ) {
			$this->deleteFile($file);
		}
		// Delete attachments in the messages
		$sql = "DELETE FROM {$this->_db->nameQuote('#__kunena_attachments')} WHERE mesid IN ({$mesids})";
		$this->_db->setQuery ( $sql );
		$this->_db->query ();
		KunenaError::checkDatabaseError();
	}

	function deleteAttachment($attachids) {
		if (is_array($attachids) && !empty($attachids)) {
			$attachids = implode(',', $attachids);
		} else {
			$attachids = intval($attachids);
		}
		if ($attachids == 0) return;

		// Do not delete files which are used in other attachments or are not really Kunena attachments
		$this->_db->setQuery ( "SELECT a.* FROM {$this->_db->nameQuote('#__kunena_attachments')} AS a LEFT JOIN {$this->db->nameQuote('#__kunena_attachments')} AS b ON a.folder=b.folder AND a.filename=b.filename
			WHERE a.id IN ({$attachids}) AND (a.folder LIKE '%media/kunena/attachments%' OR a.folder LIKE '%images/fbfiles%') AND b.filename IS NULL" );
		$fileList = $this->_db->loadObjectlist ();
		if (KunenaError::checkDatabaseError()) return;
		if ( is_array($fileList) ) {
			foreach ( $fileList as $file ) {
				$this->deleteFile($file);
			}
		}
		// Delete selected attachments
		$sql = "DELETE FROM {$this->_db->nameQuote('#__kunena_attachments')} WHERE id IN ({$attachids})";
		$this->_db->setQuery ( $sql );
		$this->_db->query ();
		KunenaError::checkDatabaseError();
	}

	protected function deleteFile($file) {
		jimport('joomla.filesystem.file');
		$path = JPATH_ROOT.DS.$file->folder;
		$filetoDelete = $path.'/'.$file->filename;
		if (JFile::exists($filetoDelete)) {
			JFile::delete($filetoDelete);
		}
		$filetoDelete = $path.'/raw/'.$file->filename;
		if (JFile::exists($filetoDelete)) {
			JFile::delete($filetoDelete);
		}
		$filetoDelete = $path.'/thumb/'.$file->filename;
		if (JFile::exists($filetoDelete)) {
			JFile::delete($filetoDelete);
		}
	}
}