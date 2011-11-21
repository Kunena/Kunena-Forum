<?php
/**
 * Kunena System Plugin
 * @package Kunena.Plugins
 * @subpackage System
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

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

		if (version_compare(JVERSION, '1.6','<')) {
			// Joomla 1.5: Fix bugs and bad performance
			$lang = JFactory::getLanguage();
			if (JFactory::getApplication()->isAdmin()) {
				// Load the missing language files in administration
				$lang->load('com_kunena.menu', JPATH_ADMINISTRATOR);
				if (JRequest::getCmd('option')=='com_plugins' && JRequest::getCmd('view')=='plugin' && JRequest::getCmd('task')=='edit') {
					// Support for J!1.7 .sys language files
					$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
					$row = JTable::getInstance('plugin');
					$row->load( (int) $cid[0] );
					$lang->load( 'plg_' . trim( $row->folder ) . '_' . trim( $row->element ) . '.sys', JPATH_ADMINISTRATOR );
				}
			} else {
				// Never load language file
				$filename = JLanguage::getLanguagePath( JPATH_BASE, $lang->_lang)."/{$lang->_lang}.com_kunena.ini";
				$lang->_paths['com_kunena'][$filename] = 1;
			}
		}

		parent::__construct ( $subject, $config );
	}

	// Joomla 1.5+ support for native onContentPrepare plugins
	public function onKunenaContentPrepare($context, &$row, &$params, $page = 0) {
		$jcontentplugins = $this->params->get('jcontentplugins', false);
		if ( $jcontentplugins ) {

			$dispatcher = JDispatcher::getInstance();
			JPluginHelper::importPlugin('content');

			if (version_compare(JVERSION, '1.6','>')) {
				// Joomla 1.6+
				$results = $dispatcher->trigger('onContentPrepare', array ('text', &$row, &$params, 0));
			} else {
				// Joomla 1.5
				$results = $dispatcher->trigger('onPrepareContent', array (&$row, &$params, 0));
			}
		}

		return $row->text;
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
			$query = "INSERT INTO #__kunena_user_categories (user_id,category_id,subscribed)
				SELECT {$user->userid} AS user_id, c.id as category_id, 1
				FROM #__kunena_categories AS c
				LEFT JOIN #__kunena_user_categories AS s ON c.id=s.category_id AND s.user_id={$user->userid}
				WHERE c.parent>0 AND c.id IN ({$subscribedCategories}) AND s.user_id IS NULL";
			$db->setQuery ( $query );
			$db->query ();
			KunenaError::checkDatabaseError();

			// Here's also query to subscribe all users (including blocked) to all existing cats:
			$query = "INSERT INTO #__kunena_user_categories (user_id,category_id,subscribed)
				SELECT u.id AS user_id, c.id AS category_id, 1
				FROM #__users AS u
				JOIN #__kunena_categories AS c ON c.parent>0
				LEFT JOIN #__kunena_user_categories AS s ON u.id=s.user_id
				WHERE c.id IN ({$subscribedCategories}) AND s.user_id IS NULL";
		}
		*/
	}
}