<?php
/**
 * Kunena Component
 * @package Kunena.Installer
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

// Kunena 2.0.0: Update menu items
function kunena_200_2012_06_10_pubwrite($parent) {
	$config = KunenaFactory::getConfig();
	if ($config->pubwrite) {
		$db = JFactory::getDbo();
		$params = '{"access_post":["1"],"access_reply":["1"]}';
		$query = "UPDATE #__kunena_categories SET params={$db->quote($params)} WHERE accesstype LIKE 'joomla.%' AND params=''";
		$db->setQuery ($query);
		$success = (bool) $db->query ();

		return array ('action' => '', 'name' => JText::sprintf ( 'COM_KUNENA_INSTALL_200_PUBWRITE' ), 'success' => $success);
	}
	return null;
}
