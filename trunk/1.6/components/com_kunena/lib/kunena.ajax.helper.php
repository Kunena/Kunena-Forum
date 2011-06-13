<?php
/**
 * @version $Id$
 * Kunena Component - CKunenaAjaxHelper class
 * @package Kunena
 *
 * @Copyright (C) 2008-2011 www.kunena.org All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();

/**
 * @author fxstein
 *
 */
class CKunenaAjaxHelper {
	/**
	 * @var JDatabase
	 */
	protected $_db;

	/**
	 * @var JUser
	 */
	protected $_my;

	/**
	 * @var KunenaSession
	 */
	protected $_session;

	function __construct() {
		$this->_db = &JFactory::getDBO ();
		$this->_my = &JFactory::getUser ();
		$this->_session = KunenaFactory::getSession ();
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

		// Sanitize $data variable
		$data = $this->_db->getEscaped($data);

		if ($this->_my->id) {
			// We only entertain json requests for registered and logged in users

			switch ($action) {
				case 'autocomplete' :
					$response = $this->_getAutoComplete ( $do, $data );

					break;
				case 'preview' :
					$body = JRequest::getVar('body', '', 'post', 'string', JREQUEST_ALLOWRAW);

					$response = $this->_getPreview ( $body );

					break;
				case 'pollcatsallowed' :
					// TODO: deprecated

					$response = $this->_getPollsCatsAllowed ();

					break;
				case 'pollvote' :
					$vote	= JRequest::getInt('kpollradio', '');
					$id = JRequest::getInt ( 'kpoll-id', 0 );

					if (!JRequest::checkToken()) {
						return false;
					}

					$response = $this->_addPollVote ($vote, $id, $this->_my->id);

					break;
				case 'pollchangevote' :
					$vote	= JRequest::getInt('kpollradio', '');
					$id = JRequest::getInt ( 'kpoll-id', 0 );

					if (!JRequest::checkToken()) {
						return false;
					}

					$response = $this->_changePollVote ($vote, $id, $this->_my->id);

					break;
				case 'anynomousallowed' :
					// TODO: deprecated

					$response = $this->_anynomousAllowed ();

					break;
				case 'uploadfile' :

					$response = $this->_uploadFile ($do);

					break;
				case 'modtopiclist' :

					$response = $this->_modTopicList ($data);

					break;
				case 'removeattachment' :

					$response = $this->_removeAttachment ($data);

					break;
					default :

					break;
			}
		}
		else {
			$response = array(
				'status' => '-1',
				'error' => JText::_('COM_KUNENA_AJAX_PERMISSION_DENIED')
			);
		}
		// Output the JSON data.
		return json_encode ( $response );
	}

	// JSON helpers
	protected function _getAutoComplete($do, $data) {
		$result = array ();

		// only registered users when the board is online will endup here

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
							FROM #__kunena_categories AS c
							WHERE $allowed AND name LIKE '" . $data . "%'
							ORDER BY 1 LIMIT 0, 10;";

				$this->_db->setQuery ( $query );
				$result = $this->_db->loadResultArray ();

				break;
			case 'gettopic' :
				$query = "SELECT m.subject
							FROM #__kunena_messages AS m
							JOIN #__kunena_categories AS c ON m.catid = c.id
							WHERE m.hold=0 AND m.parent=0 AND $allowed
								AND m.subject LIKE '" . $data . "%'
							ORDER BY 1 LIMIT 0, 10;";

				$this->_db->setQuery ( $query );
				$result = $this->_db->loadResultArray ();

				break;
			case 'getuser' :
				$kunena_config = KunenaFactory::getConfig ();

				// User the configured display name
				$queryname = $kunena_config->username ? 'username' : 'name';
				// Exclude the main superadmin from the search for security purposes
				$query = "SELECT {$this->_db->nameQuote($queryname)} FROM #__users WHERE block=0 AND `id` != 62 AND {$this->_db->nameQuote($queryname)}
							LIKE {$this->_db->Quote("{$data}%")} ORDER BY 1 LIMIT 0, 10;";

				$this->_db->setQuery ( $query );
				$result = $this->_db->loadResultArray ();

				break;
			default :
			// Operation not supported
				$result = array(
					'status' => '-1',
					'error' => JText::_('COM_KUNENA_AJAX_INVALID_OPERATION')
				);

		}

		if ($this->_db->getErrorNum ()) {
			$result = array( 'status' => '-1', 'error' => KunenaError::getDatabaseError() );
		}
		return $result;
	}

	protected function _getPreview($data) {
		$result = array ();

		$config = KunenaFactory::getConfig ();

		$this->msg->userid = JFactory::getUser()->id;
		$msgbody = KunenaParser::parseBBCode( $data, $this );
		$result ['preview'] = $msgbody;

		return $result;
	}

	// TODO: deprecated
	protected function _getPollsCatsAllowed () {
		$result = array ();

		$query = "SELECT id
							FROM #__kunena_categories
							WHERE allow_polls=1;";
		$this->_db->setQuery ( $query );
		$allow_polls = $this->_db->loadResultArray ();
		if ($this->_db->getErrorNum ()) {
			$result = array( 'status' => '-1', 'error' => KunenaError::getDatabaseError() );
		} else {
			$result['status'] = '1';
			$result['allowed_polls'] = $allow_polls;
		}

		return $result;
	}

	protected function _addPollVote ($value_choosed, $id, $userid) {
		$result = array ();

		require_once (KUNENA_PATH_LIB . '/kunena.poll.class.php');
		$kunena_polls =& CKunenaPolls::getInstance();
		$result = $kunena_polls->save_results($id,$userid,$value_choosed);

		return $result;
	}

	protected function _changePollVote ($value_choosed, $id, $userid) {
		$result = array ();

		require_once (KUNENA_PATH_LIB . '/kunena.poll.class.php');
		$kunena_polls =& CKunenaPolls::getInstance();
		$result = $kunena_polls->save_changevote($id,$userid,$value_choosed);

		return $result;
	}

	// TODO: deprecated
	protected function _anynomousAllowed () {
		$result = array ();

		$query = "SELECT id
							FROM #__kunena_categories
							WHERE allow_anonymous=1;";
		$this->_db->setQuery ( $query );
		$allow_anonymous = $this->_db->loadResultArray ();
		if ($this->_db->getErrorNum ()) {
			$result = array( 'status' => '-1', 'error' => KunenaError::getDatabaseError() );
		} else {
			$result['status'] = '1';
			$result['allowed_anonymous'] = $allow_anonymous;
		}

		return $result;
	}

	protected function _uploadFile ($do) {
		require_once (KUNENA_PATH_LIB . '/kunena.attachments.class.php');
		$attachments = CKunenaAttachments::getInstance();
		return $attachments->upload();
	}

	protected function _removeAttachment($data) {
		$result = array ();

		// only registered users when the board is online will endup here
		// $data has already been escaped as part of this class

		// TODO: Get attachment details

		$query = "SELECT a.*, m.*
			FROM #__kunena_attachments AS a
			JOIN #__kunena_messages AS m ON a.mesid = m.id
			WHERE a.id = '".$data."'";

		$this->_db->setQuery ( $query );
		$attachment = $this->_db->loadObject ();
		if ($this->_db->getErrorNum ()) {
			$result = array( 'status' => '-1', 'error' => KunenaError::getDatabaseError() );
			return $result;
		}

		// Verify permissions, user must be author of the message this
		// attachment is attached to or be a moderator or admin of the site

		if ($attachment->userid != $this->_my->id &&
			!CKunenaTools::isModerator($this->_my->id, $attachment->catid) &&
			!CKunenaTools::isAdmin()){
			// not the author, not a moderator, not an admin
			// nothing todo here - end with permission error
			$result = array(
				'status' => '-1',
				'error' => JText::_('COM_KUNENA_AJAX_PERMISSION_DENIED')
			);
			return $result;
		}

		// Request coming form valid user, moderator or admin...

		// First remove files from filsystem - check for thumbs and raw in case this is an image
		if (file_exists(JPATH_ROOT.$attachment->folder.$attachment->filename))
			JFile::delete (JPATH_ROOT.$attachment->folder.$attachment->filename);
		if (file_exists(JPATH_ROOT.$attachment->folder.'/raw/'.$attachment->filename))
			JFile::delete (JPATH_ROOT.$attachment->folder.'/raw/'.$attachment->filename);
		if (file_exists(JPATH_ROOT.$attachment->folder.'/thumb/'.$attachment->filename))
			JFile::delete (JPATH_ROOT.$attachment->folder.'/thumb/'.$attachment->filename);

		// Finally delete attachment record from db
		$query = "DELETE FROM #__kunena_attachments AS a
					WHERE a.id = {$this->_db->Quote($data)}";

		$this->_db->setQuery ( $query );
		$this->_db->query ();
		if ($this->_db->getErrorNum ()) {
			$result = array( 'status' => '-1', 'error' => KunenaError::getDatabaseError() );
		} else {
			$result = array(
				'status' => '1',
				'error' => JText::_('COM_KUNENA_AJAX_ATTACHMENT_DELETED')
			);
		}

		return $result;
	}

	protected function _modTopicList ($data) {
		$result = array ();

		$catid = intval($data);
		$user = KunenaFactory::getuser();
		if ( $catid && $user->isModerator($catid) ) {
			$query = "SELECT id, subject
							FROM #__kunena_messages
							WHERE catid={$this->_db->Quote($catid)} AND parent=0 AND moved=0
							ORDER BY id DESC";
			$this->_db->setQuery ( $query, 0, 15 );
			$topics_list = $this->_db->loadObjectlist ();
			if ($this->_db->getErrorNum ()) {
				$result = array( 'status' => '-1', 'error' => KunenaError::getDatabaseError() );
			} else {
				$result['status'] = '1';
				$result['topiclist'] = $topics_list;
			}

		} else {
			$result['status'] = '0';
			$result['error'] = 'Error';
		}

		return $result;
	}

}