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
//
// Dont allow direct linking
defined( '_JEXEC' ) or die('');

class KunenaIntegrationUddeIM extends KunenaIntegration
{
	public function __construct() {
		$path = JPATH_SITE."/components/com_uddeim/uddeim.api.php";
		if (!is_file ( $path )) return;

		include_once ($path);
		$this->loaded = true;
	}

	public function trigger($event, &$params) {}
}
?>