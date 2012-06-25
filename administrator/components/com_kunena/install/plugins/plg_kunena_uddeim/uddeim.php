<?php
/**
 * Kunena Plugin
 * @package Kunena.Plugins
 * @subpackage UddeIM
 *
 * @Copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class plgKunenaUddeIM extends JPlugin {
	public function __construct(&$subject, $config) {
		// Do not load if Kunena version is not supported or Kunena is offline
		if (!(class_exists('KunenaForum') && KunenaForum::isCompatible('2.0') && KunenaForum::installed())) return;

		KunenaFactory::loadLanguage('plg_kunena_uddeim.sys', 'admin');
		$path = JPATH_SITE."/components/com_uddeim/uddeim.api.php";
		if (!is_file ( $path )) return;

		include_once ($path);

		$uddeim = new uddeIMAPI();
		if ($uddeim->version() < 1) return;

		parent::__construct ( $subject, $config );

		$this->loadLanguage ( 'plg_kunena_uddeim.sys', JPATH_ADMINISTRATOR ) || $this->loadLanguage ( 'plg_kunena_uddeim.sys', KPATH_ADMIN );

		$this->path = dirname ( __FILE__ ) . '/uddeim';
	}

	/*
	 * Get Kunena private message integration object.
	 *
	 * @return KunenaPrivate
	 */
	public function onKunenaGetPrivate() {
		if (!$this->params->get('private', 1)) return;

		require_once "{$this->path}/private.php";
		return new KunenaPrivateUddeIM($this->params);
	}
}
