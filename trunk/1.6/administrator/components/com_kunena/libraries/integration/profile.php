<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 **/
//
// Dont allow direct linking
defined( '_JEXEC' ) or die('');

kimport('integration.integration');

abstract class KunenaProfile
{
	public $priority = 0;

	protected static $instance = false;

	abstract public function __construct();

	static public function getInstance($integration = null) {
		if (self::$instance === false) {
			$config = KunenaFactory::getConfig ();
			if (! $integration)
				$integration = $config->integration_profile;
			self::$instance = KunenaIntegration::initialize ( 'profile', $integration );
		}
		return self::$instance;
	}

	public function open() {}
	public function close() {}
	public function trigger($event, &$params) {}

	abstract public function getUserListURL($action='', $xhtml = true);
	abstract public function getProfileURL($user, $task='', $xhtml = true);
	abstract public function showProfile($userid, &$msg_params);
	public function getProfileView($PopUserCount=0) {}
}
