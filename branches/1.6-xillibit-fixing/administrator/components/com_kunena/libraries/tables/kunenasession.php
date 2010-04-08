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
// Dont allow direct linking
defined( '_JEXEC' ) or die();

require_once(dirname(__FILE__).DS.'kunena.php');

class TableKunenaSession extends TableKunena
{
	var $userid = 0;
	var $allowed = 'na';
	var $allowedcats = null;
	var $lasttime = 0;
	var $readtopics = '';
	var $currvisit = 0;
	protected $_exists = false;

	function __construct($db) {
		parent::__construct('#__fb_sessions', 'userid', $db);
	}
}

?>
