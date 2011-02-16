<?php
/**
 * @version $Id$
 * Kunena System Plugin
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 www.kunena.org All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class plgSystemKunena extends JPlugin {

	function __construct(& $subject, $config) {
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

		// Fix Joomla 1.5 bug
		$version = new JVersion();
		if (JFactory::getApplication()->isAdmin() && $version->RELEASE == '1.5') {
			JFactory::getLanguage()->load('com_kunena.menu', JPATH_ADMINISTRATOR);
		}

		parent::__construct ( $subject, $config );
	}

	/*
	function onAfterStoreUser($user, $isnew, $succes, $msg) {
		//Don't continue if the user wasn't stored succesfully
		if (! $succes) {
			return false;
		}
		if (! $isnew) {
			return true;
		}
		// Set the db function
		$db = JFactory::getDBO ();
		$db->setQuery ( "INSERT INTO #__kunena_users (userid) VALUES ('" . intval($user ['id']) . "')" );
		$db->query ();
	}
	*/
}
