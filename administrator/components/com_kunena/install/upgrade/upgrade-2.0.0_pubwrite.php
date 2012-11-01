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

jimport('joomla.filter.output');

// Kunena 2.0.0: Update menu items
function kunena_upgrade_200_pubwrite($parent) {
	$config = KunenaFactory::getConfig();
	if ($config->pubwrite) {
		$db = JFactory::getDbo();
		if (version_compare(JVERSION, '1.6', '>')) {
			$params = '{"access_post":["1"],"access_reply":["1"]}';
		} else {
			$params = "access_post=29|30\naccess_reply=29|30";
		}
		$query = "UPDATE #__kunena_categories SET params={$db->quote($params)} WHERE accesstype LIKE 'joomla.%' AND params=''";
		$db->setQuery ($query);
		$success = (bool) $db->query ();

		return array ('action' => '', 'name' => JText::sprintf ( 'COM_KUNENA_INSTALL_200_PUBWRITE' ), 'success' => $success);
	}
}
