<?php
/**
 * @version $Id$
 * Kunena System Plugin
 * @package Kunena
 *
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/
defined ( '_JEXEC' ) or die ();

class plgSystemKunena extends JPlugin {

	function __construct(& $subject, $config) {
		// Do not load plugin in administration
		if (JFactory::getApplication()->isAdmin()) {
			return false;
		}

		jimport ( 'joomla.application.component.helper' );
		// Check if Kunena component is installed/enabled
		if (! JComponentHelper::isEnabled ( 'com_kunena', true )) {
			return false;
		}

		// Check if Kunena API exists
		$kunena_api = JPATH_ADMINISTRATOR . '/components/com_kunena/api.php';
		if (! is_file ( $kunena_api ))
			return false;

		// Load Kunena API
		require_once ($kunena_api);

		parent::__construct ( $subject, $config );
	}

	function onAfterStoreUser($user, $isnew, $succes, $msg) {
		//Don't continue if the user wasn't stored succesfully
		if (! $succes) {
			return false;
		}
		$user = KunenaFactory::getUser(intval($user ['id']));
		$user->save();

		/*
		// See: http://www.kunena.com/forum/159-k-16-common-questions/63438-category-subscriptions-default-subscribed#63554
		// TODO: Subscribe user to every category if he is new and Kunena is configured to do so
		if ($isnew) {
			$subscribedCategories = '1,2,3,4,5,6,7,8,9,10';
			$db = Jfactory::getDBO();
			$query = "INSERT INTO #__kunena_subscriptions_categories (catid, userid)
				SELECT c.id, {$user->id} AS userid
				FROM #__kunena_categories AS c
				LEFT JOIN #__kunena_subscriptions_categories AS s ON c.id=s.catid AND s.userid={$user->id}
				WHERE c.parent>0 AND c.id IN ({$subscribedCategories}) AND s.userid IS NULL";
			$db->setQuery ( $query );
			$db->query ();
			KunenaError::checkDatabaseError();

			// Here's also query to subscribe all users (including blocked) to all existing cats:
			$query = "INSERT INTO #__kunena_subscriptions_categories (catid, userid)
				SELECT c.id, u.id AS userid
				FROM #__users AS u
				JOIN #__kunena_categories AS c ON c.parent>0
				LEFT JOIN #__kunena_subscriptions_categories AS s ON u.id=s.userid
				WHERE c.id IN ({$subscribedCategories}) AND s.userid IS NULL";
		}
		*/
	}
}
