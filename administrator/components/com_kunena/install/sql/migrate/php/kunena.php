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

class KunenaMigratorKunena {
	public function __construct($installer) {
		$this->installer = $installer;
	}

	function detect() {
		$kunena = $this->installer->getInstalledVersion('kunena_', $this->_kVersions);
	}
}