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

class KunenaMigratorFireboard {
	protected $versions = array (
		array ('component' => 'FireBoard', 'version' => '1.0.4', 'date' => '2007-12-23', 'table' => 'fb_sessions', 'column' => 'currvisit' ),
		array ('component' => 'FireBoard', 'version' => '1.0.3', 'date' => '2007-09-04', 'table' => 'fb_categories', 'column' => 'headerdesc' ),
		array ('component' => 'FireBoard', 'version' => '1.0.2', 'date' => '2007-08-03', 'table' => 'fb_users', 'column' => 'rank' ),
		array ('component' => 'FireBoard', 'version' => '1.0.1', 'date' => '2007-05-20', 'table' => 'fb_users', 'column' => 'uhits' ),
		array ('component' => 'FireBoard', 'version' => '1.0.0', 'date' => '2007-04-15', 'table' => 'fb_messages' )
	);

	function detect() {
	}
}