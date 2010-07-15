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
		if (!file_exists ( $aup )) return;
		require_once ($aup);
		$this->priority = 50;
		$this->_config = KunenaFactory::getConfig();
	}

	public function onAfterPost($message) {
		if ($this->_config->alphauserpointsrules) {
			$datareference = '<a href="' . CKunenaLink::GetMessageURL($message->get('id'), $message->get('catid')) . '">' . $message->get('subject') . '</a>';
			AlphaUserPointsHelper::newpoints ( 'plgaup_newtopic_kunena', '', $message->get('id'), $datareference );
		}
	}

	public function onAfterReply($message) {
		if ($this->_config->alphauserpointsrules) {
			$datareference = '<a href="' . CKunenaLink::GetMessageURL($message->get('id'), $message->get('catid')) . '">' . $message->get('subject') . '</a>';
			if ($this->_config->alphauserpointsnumchars > 0) {
				// use if limit chars for a response
				if (JString::strlen ( $message->get('message') ) > $this->_config->alphauserpointsnumchars) {
					AlphaUserPointsHelper::newpoints ( 'plgaup_reply_kunena', '', $message->get('id'), $datareference );
				}
			} else {
				AlphaUserPointsHelper::newpoints ( 'plgaup_reply_kunena', '', $message->get('id'), $datareference );
			}
		}
	}
}
