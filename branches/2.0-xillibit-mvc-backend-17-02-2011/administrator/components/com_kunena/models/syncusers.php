<?php
/**
 * @version $Id: report.php 4421 2011-02-16 19:51:30Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport ( 'joomla.application.component.model' );

/**
 * Syncusers Model for Kunena
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaAdminModelSyncusers extends JModel {
	function kUpdateNameInfo() {
		$db = JFactory::getDBO();
		$config = KunenaFactory::getConfig ();

		$queryName = $config->username ? "username" : "name";

		$query = "UPDATE #__kunena_messages AS m, #__users AS u
				SET m.name = u.$queryName
				WHERE m.userid = u.id";
		$db->setQuery($query);
		$db->query();
		KunenaError::checkDatabaseError();
		return $db->getAffectedRows();
	}

}
