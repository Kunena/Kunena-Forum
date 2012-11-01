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

// Kunena 1.0.8: Make favorites and subscriptions unique
function kunena_upgrade_108_favorites($parent) {
	$temporary = 1;

	$db = JFactory::getDbo();
	$db->setQuery ( "CREATE TABLE #__kunena_favorites_temp SELECT thread, userid FROM #__kunena_favorites WHERE userid>0 GROUP BY thread, userid" );
	if ($db->query () == FALSE) {
		$temporary = 0;
	}
	$db->setQuery ( "TRUNCATE #__kunena_favorites" );
	$db->query ();
	$db->setQuery ( "ALTER TABLE `#__kunena_favorites` DROP INDEX `thread`" );
	$db->query ();
	$db->setQuery ( "ALTER TABLE `#__kunena_favorites` ADD UNIQUE `thread`(`thread`,`userid`)" );
	$db->query ();
	if ($db->getErrorNum ())
		throw new KunenaInstallerException ( $db->getErrorMsg (), $db->getErrorNum () );
	if ($temporary) {
		$db->setQuery ( "INSERT INTO #__kunena_favorites (thread,userid) SELECT thread, userid FROM #__kunena_favorites_temp" );
		$db->query ();
		if ($db->getErrorNum ())
			throw new KunenaInstallerException ( $db->getErrorMsg (), $db->getErrorNum () );
		$db->setQuery ( "DROP TABLE #__kunena_favorites_temp" );
		$db->query ();
		if ($db->getErrorNum ())
			throw new KunenaInstallerException ( $db->getErrorMsg (), $db->getErrorNum () );
	}

	$temporary = 1;
	$db->setQuery ( "CREATE TABLE #__kunena_subscriptions_temp SELECT thread, userid, future1 FROM #__kunena_subscriptions WHERE userid>0 GROUP BY thread, userid" );
	if ($db->query () == FALSE) {
		$temporary = 0;
	}
	$db->setQuery ( "TRUNCATE #__kunena_subscriptions" );
	$db->query ();
	$db->setQuery ( "ALTER TABLE `#__kunena_subscriptions` DROP INDEX `thread`" );
	$db->query ();
	$db->setQuery ( "ALTER TABLE `#__kunena_subscriptions` ADD UNIQUE `thread`(`thread`,`userid`)" );
	$db->query ();
	if ($db->getErrorNum ())
		throw new KunenaInstallerException ( $db->getErrorMsg (), $db->getErrorNum () );
	if ($temporary) {
		$db->setQuery ( "INSERT INTO #__kunena_subscriptions (thread,userid,future1) SELECT thread, userid, future1 FROM #__kunena_subscriptions_temp" );
		$db->query ();
		if ($db->getErrorNum ())
			throw new KunenaInstallerException ( $db->getErrorMsg (), $db->getErrorNum () );
		$db->setQuery ( "DROP TABLE #__kunena_subscriptions_temp" );
		$db->query ();
		if ($db->getErrorNum ())
			throw new KunenaInstallerException ( $db->getErrorMsg (), $db->getErrorNum () );
	}
	return array('action'=>'', 'name'=>JText::_ ( 'COM_KUNENA_INSTALL_108_FAVORITES' ), 'success'=>true);
}
