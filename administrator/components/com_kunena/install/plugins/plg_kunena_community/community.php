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

class plgKunenaCommunity extends JPlugin {
	public function __construct(&$subject, $config) {
		// Do not load if Kunena version is not supported or Kunena is offline
		if (!(class_exists('KunenaForum') && KunenaForum::isCompatible('2.0') && KunenaForum::enabled())) return;

		// Do not load if JomSocial is not installed
		$path = JPATH_ROOT . '/components/com_community/libraries/core.php';
		if (!is_file ( $path )) return;
		include_once ($path);

		parent::__construct ( $subject, $config );
		$this->loadLanguage ( 'plg_kunena_community.sys', JPATH_ADMINISTRATOR );

		$this->path = dirname ( __FILE__ ) . '/community';
	}

	/*
	 * Get Kunena access control object.
	 *
	 * @return KunenaAccess
	 */
	public function onKunenaGetAccessControl() {
		require_once "{$this->path}/access.php";
		return new KunenaAccessCommunity();
	}

	/*
	 * Get Kunena login integration object.
	 *
	 * @return KunenaLogin
	 */
	public function onKunenaGetLogin() {
		require_once "{$this->path}/login.php";
		return new KunenaLoginCommunity();
	}

	/*
	 * Get Kunena avatar integration object.
	 *
	 * @return KunenaAvatar
	 */
	public function onKunenaGetAvatar() {
		require_once "{$this->path}/avatar.php";
		return new KunenaAvatarCommunity();
	}

	/*
	 * Get Kunena profile integration object.
	 *
	 * @return KunenaProfile
	 */
	public function onKunenaGetProfile() {
		require_once "{$this->path}/profile.php";
		return new KunenaProfileCommunity();
	}

	/*
	 * Get Kunena private message integration object.
	 *
	 * @return KunenaPrivate
	 */
	public function onKunenaGetPrivate() {
		require_once "{$this->path}/private.php";
		return new KunenaPrivateCommunity();
	}

	/*
	 * Get Kunena activity stream integration object.
	 *
	 * @return KunenaActivity
	 */
	public function onKunenaGetActivity() {
		require_once "{$this->path}/activity.php";
		return new KunenaActivityCommunity();
	}
}