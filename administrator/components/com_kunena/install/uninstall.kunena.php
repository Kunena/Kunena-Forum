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

function com_uninstall() {
	if (version_compare(JVERSION, '1.6','>')) return;

	require_once(JPATH_ADMINISTRATOR . '/components/com_kunena/install/model.php');
	$installer = new KunenaModelInstall();
	$installer->uninstall();
}
