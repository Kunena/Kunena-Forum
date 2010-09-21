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
	}
}
