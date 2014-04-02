<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Tables
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

require_once(__DIR__ . '/kunena.php');

/**
 * Kunena Sessions
 * Provides access to the #__kunena_sessions table
 */
class TableKunenaSessions extends KunenaTable {
	public $userid = 0;
	public $allowed = 'na';
	public $allowedcats = null;
	public $lasttime = 0;
	public $readtopics = 0;
	public $currvisit = 0;
	protected $_exists = false;

	public function __construct($db) {
		parent::__construct('#__kunena_sessions', 'userid', $db);
	}

	public function load($oid = null, $reset = true) {
		if (!$oid) return false;
		return parent::load($oid, $reset);
	}

	public function check() {
		$user = KunenaUserHelper::get($this->userid);
		if (!$user->exists()) {
			$this->setError ( JText::sprintf ( 'COM_KUNENA_LIB_TABLE_SESSIONS_ERROR_USER_INVALID', (int) $user->userid ) );
		}
		return ($this->getError () == '');
	}
}
