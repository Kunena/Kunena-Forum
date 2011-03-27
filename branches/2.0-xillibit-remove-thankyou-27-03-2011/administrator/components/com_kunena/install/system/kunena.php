<?php
/**
 * @version $Id$
 * Kunena System Plugin
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport('joomla.version');

class plgSystemKunena extends JPlugin {

	function __construct(& $subject, $config) {
		// Check if Kunena API exists
		$api = JPATH_ADMINISTRATOR . '/components/com_kunena/api.php';
		if (! is_file ( $api ))
			return false;

		jimport ( 'joomla.application.component.helper' );
		// Check if Kunena component is installed/enabled
		if (! JComponentHelper::isEnabled ( 'com_kunena', true )) {
			return false;
		}

		// Load Kunena API
		require_once $api;

		// Fix Joomla 1.5 bug
		$version = new JVersion();
		if (JFactory::getApplication()->isAdmin() && $version->RELEASE == '1.5') {
			JFactory::getLanguage()->load('com_kunena.menu', JPATH_ADMINISTRATOR);
		}

		parent::__construct ( $subject, $config );
	}

	// Joomla 1.5 support
	public function onAfterStoreUser($user, $isnew, $success, $msg) {
		$this->onUserAfterSave($user, $isnew, $success, $msg);
	}
	// Joomla 1.6 support
	public function onUserAfterSave($user, $isnew, $success, $msg) {
		//Don't continue if the user wasn't stored succesfully
		if (! $success) {
			return false;
		}
		if ($isnew) {
			$user = KunenaFactory::getUser(intval($user ['id']));
			$user->save();
		}

		/*
		// See: http://www.kunena.org/forum/159-k-16-common-questions/63438-category-subscriptions-default-subscribed#63554
		// TODO: Subscribe user to every category if he is new and Kunena is configured to do so
		if ($isnew) {
			$subscribedCategories = '1,2,3,4,5,6,7,8,9,10';
			$db = Jfactory::getDBO();
			$query = "INSERT INTO #__kunena_user_categories (user_id,category_id)
				SELECT {$user->userid} AS user_id, c.id as category_id
				FROM #__kunena_categories AS c
				LEFT JOIN #__kunena_user_categories AS s ON c.id=s.category_id AND s.user_id={$user->userid}
				WHERE c.parent>0 AND c.id IN ({$subscribedCategories}) AND s.user_id IS NULL";
			$db->setQuery ( $query );
			$db->query ();
			KunenaError::checkDatabaseError();

			// Here's also query to subscribe all users (including blocked) to all existing cats:
			$query = "INSERT INTO #__kunena_user_categories (user_id,category_id)
				SELECT u.id AS user_id, c.id AS category_id
				FROM #__users AS u
				JOIN #__kunena_categories AS c ON c.parent>0
				LEFT JOIN #__kunena_user_categories AS s ON u.id=s.user_id
				WHERE c.id IN ({$subscribedCategories}) AND s.user_id IS NULL";
		}
		*/
	}
}
