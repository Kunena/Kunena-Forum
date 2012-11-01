<?php
/**
 * Kunena Plugin
 * @package Kunena.Plugins
 * @subpackage Kunena
 *
 * @Copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class plgKunenaKunena extends JPlugin {
	public function __construct(&$subject, $config) {
		// Do not load if Kunena version is not supported or Kunena is offline
		if (!(class_exists('KunenaForum') && KunenaForum::isCompatible('2.0') && KunenaForum::installed())) return;

		parent::__construct ( $subject, $config );

		$this->loadLanguage ( 'plg_kunena_kunena.sys', JPATH_ADMINISTRATOR ) || $this->loadLanguage ( 'plg_kunena_kunena.sys', KPATH_ADMIN );

		$this->path = dirname ( __FILE__ ) . '/kunena';
	}

	/*
	 * Get Kunena avatar integration object.
	 *
	 * @return KunenaAvatar
	 */
	public function onKunenaGetAvatar() {
		if (!$this->params->get('avatar', 1)) return;

		require_once "{$this->path}/avatar.php";
		return new KunenaAvatarKunena($this->params);
	}

	/*
	 * Get Kunena profile integration object.
	 *
	 * @return KunenaProfile
	 */
	public function onKunenaGetProfile() {
		if (!$this->params->get('profile', 1)) return;

		require_once "{$this->path}/profile.php";
		return new KunenaProfileKunena($this->params);
	}
}
