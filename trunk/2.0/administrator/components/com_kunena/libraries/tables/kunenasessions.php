<?php
/**
* @version $Id$
* Kunena Component - TableKunenaSessions class
* @package Kunena
*
* @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
**/
defined( '_JEXEC' ) or die();

require_once (dirname ( __FILE__ ) . DS . 'kunena.php');

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
}