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

class KunenaActivityNone extends KunenaActivity {
	public function __construct() {
		$this->priority = 5;
	}

	public function onAfterPost($message) {}
	public function onAfterReply($message) {}
	public function onAfterEdit($message) {}
	public function onAfterDelete($message) {}
}
