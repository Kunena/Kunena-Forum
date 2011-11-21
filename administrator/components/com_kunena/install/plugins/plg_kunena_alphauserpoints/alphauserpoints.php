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

class plgKunenaAlphaUserPoints extends JPlugin {
	public function __construct(&$subject, $config) {
		// Do not load if Kunena version is not supported or Kunena is offline
		if (!(class_exists('KunenaForum') && KunenaForum::isCompatible('2.0') && KunenaForum::enabled())) return;

		$aup = JPATH_SITE . '/components/com_alphauserpoints/helper.php';
		if (! file_exists ( $aup ))
			return;
		require_once ($aup);

		parent::__construct ( $subject, $config );
		$this->loadLanguage ( 'plg_kunena_alphauserpoints.sys', JPATH_ADMINISTRATOR );

		$this->path = dirname ( __FILE__ ) . '/alphauserpoints';
	}

	/*
	 * Get Kunena avatar integration object.
	 *
	 * @return KunenaAvatar
	 */
	public function onKunenaGetAvatar() {
		require_once "{$this->path}/avatar.php";
		return new KunenaAvatarAlphaUserPoints();
	}

	/*
	 * Get Kunena profile integration object.
	 *
	 * @return KunenaProfile
	 */
	public function onKunenaGetProfile() {
		require_once "{$this->path}/profile.php";
		return new KunenaProfileAlphaUserPoints();
	}

	/*
	 * Get Kunena activity stream integration object.
	 *
	 * @return KunenaActivity
	 */
	public function onKunenaGetActivity() {
		require_once "{$this->path}/activity.php";
		return new KunenaActivityAlphaUserPoints();
	}}