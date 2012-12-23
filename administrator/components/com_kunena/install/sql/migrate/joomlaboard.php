<?php
/**
 * Kunena Component
* @package Kunena.Installer
*
* @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
**/
defined ( '_JEXEC' ) or die ();

class KunenaMigratorJoomlaboard {
	protected $versions = array (
		array('component'=>'JoomlaBoard', 'version' =>'1.0', 'date' => '0000-00-00', 'table' => 'sb_messages')
	);

	function detect() {
	}
}