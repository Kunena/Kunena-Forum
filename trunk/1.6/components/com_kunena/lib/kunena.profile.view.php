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
defined ( '_JEXEC' ) or die ( 'Restricted access' );

class CKunenaProfile {
	public $user = null;
	public $profile = null;
	public $online = null;

	function __construct($userid) {
		require_once (KUNENA_PATH_LIB . DS . "kunena.user.class.php");

		$this->_db = JFactory::getDBO ();
		$this->_app = JFactory::getApplication ();
		$this->_config = CKunenaConfig::getInstance ();

		if (!$userid) {
			$this->user = JFactory::getUser();
		}
		else {
			$this->user = JUser::getInstance ( $userid );
		}
		if (!$this->user->id) return;
		$this->profile = CKunenaUserprofile::getInstance ( $this->user->id );
		if ($this->profile->posts === null) {
			$this->_db->setQuery ( "INSERT INTO #__fb_users (userid) VALUES ('{$this->user->id}')" );
			$this->_db->query ();
			check_dberror ( 'Unable to create user profile.' );
			$this->profile = CKunenaUserprofile::getInstance($this->user->id, true);
		}
		$this->profile->store();
		$this->avatarurl = KUNENA_LIVEUPLOADEDPATH . '/avatars/' . $this->profile->avatar;
		$this->personalText = CKunenaTools::parseText($this->profile->personalText);
		$this->signature = CKunenaTools::parseBBCode($this->profile->signature);
		$this->timezone = $this->user->getParam('timezone', 0);
		$this->moderator = CKunenaTools::isModerator($this->user->id);
		$this->admin = CKunenaTools::isAdmin($this->user->id);
		$this->rank = CKunenaTools::getRank($this->profile);

		$query = 'SELECT MAX(s.time) FROM #__session AS s WHERE s.userid = ' . $this->user->id . ' AND s.client_id = 0 GROUP BY s.userid';
		$this->_db->setQuery ( $query );
		$lastseen = $this->_db->loadResult ();
		check_dberror ( "Unable get user online information." );
		$timeout = $this->_app->getCfg ( 'lifetime', 15 ) * 60;
		$this->online = ($lastseen + $timeout) > time ();
	}

	function displaySummary() {
		$user = JFactory::getUser();
		if ($user->id != $this->user->id)
		{
			$this->profile->uhits++;
			$this->profile->store();
		}

		if (file_exists ( KUNENA_ABSTMPLTPATH . DS . 'profile' . DS . 'summary.php')) {
			include (KUNENA_ABSTMPLTPATH . DS . 'profile' . DS . 'summary.php');
		} else {
			include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'profile' . DS . 'summary.php');
		}
	}

	function display() {
		if (!$this->user->id) return;
		$this->displaySummary();
	}
}