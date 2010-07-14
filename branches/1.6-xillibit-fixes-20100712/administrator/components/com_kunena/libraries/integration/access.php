<?php
/**
 * @version $Id: kunena.session.class.php 2071 2010-03-17 11:27:58Z mahagr $
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

kimport ( 'integration.integration' );

abstract class KunenaAccess {
	public $priority = 0;

	protected static $instance = false;

	abstract public function __construct();

	static public function getInstance($integration = null) {
		if (self::$instance === false) {
			$config = KunenaFactory::getConfig ();
			if (! $integration)
				$integration = $config->integration_access;
			self::$instance = KunenaIntegration::initialize ( 'access', $integration );
		}
		return self::$instance;
	}

	function getAllowedHold($user, $catid, $string=true) {
		// hold = 0: normal
		// hold = 1: unapproved
		// hold = 2: deleted
		$user = KunenaFactory::getUser($user);
		$config = KunenaFactory::getConfig ();

		$hold [] = 0;
		if ($this->isModerator ( $user->userid, $catid )) {
			$hold [] = 1;
			if ( $config->seen_deleted_messages && !$this->isAdmin ( $user->userid, $catid ) ) $hold [] = 2;
		}
		if ($this->isAdmin ( $user->userid, $catid ) )
			$hold [] = 2;
		if ($string) $hold = implode ( ',', $hold );
		print_r($hold);
		return $hold;
	}

	abstract function isAdmin($uid = null, $catid = 0);
	abstract function isModerator($uid = null, $catid = 0);
	abstract function getAllowedCategories($userid);
	abstract function getSubscribers($catid, $thread, $subscriptions = false, $moderators = false, $admins = false, $excludeList = '0');
}