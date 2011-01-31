<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 * @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.bestofjoomla.com
 *
 * Kunena 1.0.8: Make favorites and subscriptions unique
 * component: com_kunena
 **/

defined ( '_JEXEC' ) or die ();

function kunena_upgrade_108_favorites($parent) {
	$temporary = 1;

	$parent->db->setQuery ( "CREATE TABLE #__kunena_favorites_temp SELECT thread, userid FROM #__kunena_favorites WHERE userid>0 GROUP BY thread, userid" );
	if ($parent->db->query () == FALSE) {
		$temporary = 0;
	}
	$parent->db->setQuery ( "TRUNCATE #__kunena_favorites" );
	$parent->db->query ();
	$parent->db->setQuery ( "ALTER TABLE `#__kunena_favorites` DROP INDEX `thread`" );
	$parent->db->query ();
	$parent->db->setQuery ( "ALTER TABLE `#__kunena_favorites` ADD UNIQUE `thread`(`thread`,`userid`)" );
	$parent->db->query ();
	if ($parent->db->getErrorNum ())
		throw new KunenaInstallerException ( $parent->db->getErrorMsg (), $parent->db->getErrorNum () );
	if ($temporary) {
		$parent->db->setQuery ( "INSERT INTO #__kunena_favorites (thread,userid) SELECT thread, userid FROM #__kunena_favorites_temp" );
		$parent->db->query ();
		if ($parent->db->getErrorNum ())
			throw new KunenaInstallerException ( $parent->db->getErrorMsg (), $parent->db->getErrorNum () );
		$parent->db->setQuery ( "DROP TABLE #__kunena_favorites_temp" );
		$parent->db->query ();
		if ($parent->db->getErrorNum ())
			throw new KunenaInstallerException ( $parent->db->getErrorMsg (), $parent->db->getErrorNum () );
	}

	$temporary = 1;
	$parent->db->setQuery ( "CREATE TABLE #__kunena_subscriptions_temp SELECT thread, userid, future1 FROM #__kunena_subscriptions WHERE userid>0 GROUP BY thread, userid" );
	if ($parent->db->query () == FALSE) {
		$temporary = 0;
	}
	$parent->db->setQuery ( "TRUNCATE #__kunena_subscriptions" );
	$parent->db->query ();
	$parent->db->setQuery ( "ALTER TABLE `#__kunena_subscriptions` DROP INDEX `thread`" );
	$parent->db->query ();
	$parent->db->setQuery ( "ALTER TABLE `#__kunena_subscriptions` ADD UNIQUE `thread`(`thread`,`userid`)" );
	$parent->db->query ();
	if ($parent->db->getErrorNum ())
		throw new KunenaInstallerException ( $parent->db->getErrorMsg (), $parent->db->getErrorNum () );
	if ($temporary) {
		$parent->db->setQuery ( "INSERT INTO #__kunena_subscriptions (thread,userid,future1) SELECT thread, userid, future1 FROM #__kunena_subscriptions_temp" );
		$parent->db->query ();
		if ($parent->db->getErrorNum ())
			throw new KunenaInstallerException ( $parent->db->getErrorMsg (), $parent->db->getErrorNum () );
		$parent->db->setQuery ( "DROP TABLE #__kunena_subscriptions_temp" );
		$parent->db->query ();
		if ($parent->db->getErrorNum ())
			throw new KunenaInstallerException ( $parent->db->getErrorMsg (), $parent->db->getErrorNum () );
	}
	return array('action'=>'', 'name'=>JText::_ ( 'COM_KUNENA_INSTALL_108_FAVORITES' ), 'success'=>true);
}