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
defined('_JEXEC') or die();

class plgSystemKunena extends JPlugin {

	function __construct(& $subject, $config) {
		parent::__construct($subject, $config);

		jimport('joomla.application.component.helper');
		// Check if Kunena component is installed/enabled
		if (!JComponentHelper::isEnabled ( 'com_kunena', true )) {
			return;
		}

		// Check if Kunena API exists
		$kunena_api = JPATH_ADMINISTRATOR . '/components/com_kunena/api.php';
		if (! is_file ( $kunena_api ))
			return;

		// Load Kunena API
		require_once ($kunena_api);
	}

}
