<?php
/**
 * @version $Id: kunena.session.class.php 2071 2010-03-17 11:27:58Z mahagr $
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
defined ( '_JEXEC' ) or die ( '' );

kimport ( 'integration.integration' );

abstract class KunenaLogin {
	public $priority = 0;

	protected static $instance = false;

	abstract public function __construct();

	static public function getInstance($integration = null) {
		if (self::$instance === false) {
			$config = KunenaFactory::getConfig ();
			if (! $integration)
				$integration = $config->integration_login;
			self::$instance = KunenaIntegration::initialize ( 'login', $integration );
		}
		return self::$instance;
	}

	abstract public function getLoginFormFields();
	abstract public function getLogoutFormFields();
	abstract public function getLoginURL();
	abstract public function getLogoutURL();
	abstract public function getRegistrationURL();
	abstract public function getResetURL();
	abstract public function getRemindURL();
}
