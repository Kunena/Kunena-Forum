<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

require_once (dirname ( __FILE__ ) . '/kunena.php');
kimport('kunena.user.helper');

/**
 * Kunena Sessions
 * Provides access to the #__kunena_sessions table
 */
class TableKunenaSessions extends KunenaTable
{
	var $userid = 0;
	var $allowed = 'na';
	var $allowedcats = null;
	var $lasttime = 0;
	var $readtopics = 0;
	var $currvisit = 0;
	protected $_exists = false;

	function __construct($db) {
		parent::__construct('#__kunena_sessions', 'userid', $db);
	}

	function load($oid = null) {
		if (!$oid) return false;
		return parent::load($oid);
	}

	function check() {
		$user = KunenaUserHelper::get($this->userid);
		if (!$user->exists()) {
			$this->setError ( JText::sprintf ( 'COM_KUNENA_LIB_TABLE_SESSIONS_ERROR_USER_INVALID', (int) $user->userid ) );
		}
		return ($this->getError () == '');
	}
}