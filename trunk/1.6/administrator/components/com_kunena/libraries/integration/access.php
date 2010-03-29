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
defined( '_JEXEC' ) or die('');

abstract class KunenaAccess {
	public $priority = 0;

	protected static $instance = false;

	abstract protected function __construct();

	static public function getInstance($integration = null)
	{
		if (self::$instance === false) {
			$config = KunenaFactory::getConfig();
			if (!$integration) $integration = $config->integration_access;

			// Result will be null if initialize fails (=no login)
			self::$instance = KunenaIntegration::initialize('access', $integration);
		}
		return self::$instance;
	}
}