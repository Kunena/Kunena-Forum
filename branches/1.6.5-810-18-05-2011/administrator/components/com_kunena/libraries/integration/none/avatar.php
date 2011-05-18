<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 **/
defined( '_JEXEC' ) or die('');

class KunenaAvatarNone extends KunenaAvatar
{
	public function __construct() {
		$this->priority = 5;
	}

	public function load($userlist) {}
	public function getEditURL() {}
	protected function _getURL($user, $sizex, $sizey) {}
}
