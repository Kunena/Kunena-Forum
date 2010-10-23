<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 **/
//
// Dont allow direct linking
defined ( '_JEXEC' ) or die ( '' );

class KunenaActivityAlphaUserPoints extends KunenaActivity {
	protected $integration = null;

	public function __construct() {
		$aup = JPATH_SITE . DS . 'components' . DS . 'com_alphauserpoints' . DS . 'helper.php';
		if (! file_exists ( $aup ))
			return;
		require_once ($aup);
		$this->priority = 50;
		$this->_config = KunenaFactory::getConfig ();
	}
	
	private function _getAUPversion(){
		return AlphaUserPointsHelper::getAupVersion();
	}

	public function onAfterPost($message) {
		require_once KPATH_SITE.'/lib/kunena.link.class.php';
		$datareference = '<a href="' . CKunenaLink::GetMessageURL ( $message->get ( 'id' ), $message->get ( 'catid' ) ) . '">' . $message->get ( 'subject' ) . '</a>';
		if ( $this->_getAUPversion() < '1.5.12' ) AlphaUserPointsHelper::newpoints ( 'plgaup_newtopic_kunena', '', $message->get ( 'id' ), $datareference );
		elseif ( $this->_getAUPversion() >= '1.5.12' ) AlphaUserPointsHelper::newpoints ( 'plgaup_kunena_topic_create', '', $message->get ( 'id' ), $datareference );
	}

	public function onAfterReply($message) {
		require_once KPATH_SITE.'/lib/kunena.link.class.php';
		$datareference = '<a href="' . CKunenaLink::GetMessageURL ( $message->get ( 'id' ), $message->get ( 'catid' ) ) . '">' . $message->get ( 'subject' ) . '</a>';
		if ($this->_config->alphauserpointsnumchars > 0) {
			// use if limit chars for a response
			if (JString::strlen ( $message->get ( 'message' ) ) > $this->_config->alphauserpointsnumchars) {
				if ( $this->_getAUPversion() < '1.5.12' ) AlphaUserPointsHelper::newpoints ( 'plgaup_reply_kunena', '', $message->get ( 'id' ), $datareference );
				elseif ( $this->_getAUPversion() >= '1.5.12' ) AlphaUserPointsHelper::newpoints ( 'plgaup_kunena_topic_reply', '', $message->get ( 'id' ), $datareference );
			}
		} else {
			if ( $this->_getAUPversion() < '1.5.12' ) AlphaUserPointsHelper::newpoints ( 'plgaup_reply_kunena', '', $message->get ( 'id' ), $datareference );
			elseif ( $this->_getAUPversion() >= '1.5.12' ) AlphaUserPointsHelper::newpoints ( 'plgaup_kunena_topic_reply', '', $message->get ( 'id' ), $datareference );
		}
	}
	
	public function onAfterDelete($message, $userid) {
		$aupid = AlphaUserPointsHelper::getAnyUserReferreID( $userid );
		if ( $aupid ) {
			if ( $this->_getAUPversion() < '1.5.12' ) AlphaUserPointsHelper::newpoints( 'plgaup_delete_post_kunena', $aupid);
			elseif ( $this->_getAUPversion() >= '1.5.12' ) AlphaUserPointsHelper::newpoints( 'plgaup_kunena_message_delete', $aupid);		
		}		
	}

	public function onAfterThankyou($thankyoutargetid, $username) {
		$info = (JText::_ ( 'COM_KUNENA_THANKYOU_SAID' ).': ' . $username);
		$aupid = AlphaUserPointsHelper::getAnyUserReferreID( $thankyoutargetid );
		if ( $aupid ) {
			if ( $this->_getAUPversion() < '1.5.12' ) AlphaUserPointsHelper::newpoints('plgaup_thankyou_kunena', $aupid, '', $info);
			elseif ( $this->_getAUPversion() >= '1.5.12' ) AlphaUserPointsHelper::newpoints('plgaup_kunena_message_thankyou', $aupid, '', $info);
		}  
	}

	function escape($var) {
		return htmlspecialchars ( $var, ENT_COMPAT, 'UTF-8' );
	}

	public function getUserMedals($userid) {
		if ($userid == 0)
			return false;

		if (! defined ( "_AUP_MEDALS_LIVE_PATH" )) {
			define ( '_AUP_MEDALS_LIVE_PATH', JURI::base ( true ) . '/components/com_alphauserpoints/assets/images/awards/icons/' );
		}

		$aupmedals = AlphaUserPointsHelper::getUserMedals ( '', $userid );
		$medals = array ();
		foreach ( $aupmedals as $medal ) {
			$medals [] = '<img src="' . _AUP_MEDALS_LIVE_PATH . $this->escape ( $medal->icon ) . '" alt="' . $this->escape ( $medal->rank ) . '" title="' . $this->escape ( $medal->rank ) . '" />';
		}

		return $medals;
	}

	public function getUserPoints($userid) {
		if ($userid == 0)
			return false;
		$_db = JFactory::getDBO ();

		$_db->setQuery ( "SELECT points FROM #__alpha_userpoints WHERE `userid`='" . ( int ) $userid . "'" );
		$userpoints = $_db->loadResult ();
		KunenaError::checkDatabaseError ();
		return $userpoints;
	}
}
