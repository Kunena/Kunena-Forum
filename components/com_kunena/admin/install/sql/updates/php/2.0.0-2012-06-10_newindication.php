<?php
/**
 * Kunena Component
 * @package Kunena.Installer
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

// Kunena 2.0.0: Convert new indication into new format
function kunena_200_2012_06_10_newindication($parent) {
	$db = JFactory::getDbo();
	$now = JFactory::getDate()->toUnix();

	// First remove old session information (not used anyway, speeds up conversion)
	$lasttime = $now - max(intval(JFactory::getConfig()->get( 'config.lifetime' ))*60, intval(KunenaFactory::getConfig ()->sessiontimeout)) - 60;
	$query = "UPDATE #__kunena_sessions SET readtopics='0' WHERE currvisit<{$db->quote($lasttime)}";
	$db->setQuery ( $query );
	$db->query ();
	if ($db->getErrorNum ())
		throw new KunenaInstallerException ( $db->getErrorMsg (), $db->getErrorNum () );

	$limit = 100;
	do {
		unset($sessions);
		// Then look at users who have read the thread
		$query = "SELECT userid, readtopics FROM #__kunena_sessions WHERE readtopics != '0'";
		$db->setQuery ( $query, 0, $limit );
		$sessions = $db->loadObjectList ();
		if ($db->getErrorNum ())
			throw new KunenaInstallerException ( $db->getErrorMsg (), $db->getErrorNum () );

		// Create new data
		$users = $values = array();
		foreach ( $sessions as $session ) {
			$readtopics = explode ( ",", $session->readtopics );
			$userid = intval($session->userid);
			if (!$userid) continue;
			foreach ($readtopics as $id) {
				$id = intval($id);
				if (!$id) continue;
				$values["{$userid}.{$id}"] = "({$userid},{$id},0,0,{$now})";
			}
			$users[$userid] = $userid;
		}
		if ($values) {
			// Create smaller queries of 1000 elements (~45kb)
			$chunks = array_chunk($values, 1000);
			unset($values);
			foreach ($chunks as &$chunk) {
				$values = implode(',', $chunk);
				$query = "REPLACE INTO #__kunena_user_read (`user_id`, `topic_id`, `category_id`, `message_id`, `time`) VALUES {$values}";
				$db->setQuery ( $query );
				$db->query ();
				if ($db->getErrorNum ())
					throw new KunenaInstallerException ( $db->getErrorMsg (), $db->getErrorNum () );
			}
		}
		unset($chunks, $values);
		// Clean old session information for processed users
		if ($users) {
			$users = implode(',', $users);
			$query = "UPDATE #__kunena_sessions SET readtopics='0' WHERE userid IN ({$users})";
			$db->setQuery ( $query );
			$db->query ();
			if ($db->getErrorNum ())
				throw new KunenaInstallerException ( $db->getErrorMsg (), $db->getErrorNum () );
		}
		unset($users);
	} while ($sessions);

	// Update missing information
	$query = "UPDATE #__kunena_user_read AS ur
		INNER JOIN #__kunena_topics AS t ON t.id=ur.topic_id
		SET ur.category_id=t.category_id,
			ur.message_id=t.last_post_id";
	$db->setQuery($query);
	$db->query ();
	if ($db->getErrorNum ())
		throw new KunenaInstallerException ( $db->getErrorMsg (), $db->getErrorNum () );

	return array ('action' => '', 'name' => JText::_ ( 'COM_KUNENA_INSTALL_200_NEW_INDICATION' ), 'success' => true );
}
