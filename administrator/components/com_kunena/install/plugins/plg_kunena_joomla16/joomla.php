<?php
/**
 * Kunena System Plugin
 * @package Kunena.Integration
 * @subpackage Joomla16
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class plgKunenaJoomla extends JPlugin {
	public function __construct(&$subject, $config) {
		// Do not load if Kunena version is not supported or Kunena is offline
		if (!(class_exists('KunenaForum') && KunenaForum::isCompatible('2.0') && KunenaForum::enabled())) return;

		// Do not load in Joomla 1.5
		if (version_compare(JVERSION, '1.6','<')) {
			return;
		}

		parent::__construct ( $subject, $config );
		$this->loadLanguage ( 'plg_kunena_joomla.sys', JPATH_ADMINISTRATOR );

		$this->path = dirname ( __FILE__ );
	}

	/*
	 * Get Kunena access control object.
	 *
	 * @return KunenaAccess
	 */
	public function onKunenaGetAccessControl() {
		require_once "{$this->path}/access.php";
		return new KunenaAccessJoomla();
	}

	/*
	 * Get Kunena login integration object.
	 *
	 * @return KunenaLogin
	 */
	public function onKunenaGetLogin() {
		require_once "{$this->path}/login.php";
		return new KunenaLoginJoomla();
	}
}