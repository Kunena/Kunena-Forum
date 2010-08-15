<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 **/
//
// Dont allow direct linking
defined( '_JEXEC' ) or die('');

kimport ( 'integration.integration' );

abstract class KunenaActivity
{
	public $priority = 0;

	protected static $instance = false;

	abstract public function __construct();

	static public function getInstance($integration = null) {
		if (self::$instance === false) {
			$config = KunenaFactory::getConfig ();
			if (! $integration)
				$integration = $config->integration_activity;
			self::$instance = KunenaIntegration::initialize ( 'activity', $integration );
		}
		return self::$instance;
	}

	public function getUserMedals($userid) {}
	public function getUserPoints($userid) {}

	public function onAfterPost($message) {}
	public function onAfterReply($message) {}
	public function onAfterEdit($message) {}
	public function onAfterDelete($message) {}
	public function onAfterUndelete($message) {}
}
