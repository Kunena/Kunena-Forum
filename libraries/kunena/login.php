<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Integration
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class KunenaLogin
 */
class KunenaLogin {
	protected static $instance = false;
	/**
	 * @var array|KunenaLogin[]
	 */
	protected $instances = array();

	public function __construct() {
		JPluginHelper::importPlugin('kunena');
		$dispatcher = JDispatcher::getInstance();
		$classes = $dispatcher->trigger('onKunenaGetLogin');
		foreach ($classes as $class) {
			if (!is_object($class)) continue;
			$this->instances[] = $class;
		}
	}

	public function enabled() {
		// TODO: do better
		return !empty($this->instances);
	}

	public static function getInstance($integration = null) {
		if (self::$instance === false) {
			self::$instance = new KunenaLogin();
		}
		return self::$instance;
	}

	public function loginUser($username, $password, $rememberme=0, $return=null) {
		foreach ($this->instances as $login) {
			if (method_exists($login, 'loginUser')) {
				return $login->loginUser($username, $password, $rememberme, $return);
			}
		}
		return false;
	}

	public function logoutUser($return=null) {
		foreach ($this->instances as $login) {
			if (method_exists($login, 'logoutUser')) {
				return $login->logoutUser($return);
			}
		}
		return false;
	}

	public function getRememberMe() {
		foreach ($this->instances as $login) {
			if (method_exists($login, 'getRememberMe')) {
				return $login->getRememberMe();
			}
		}
		return false;
	}

	public function getLoginURL() {
		foreach ($this->instances as $login) {
			if (method_exists($login, 'getLoginURL')) {
				return $login->getLoginURL();
			}
		}
		return null;
	}

	public function getLogoutURL() {
		foreach ($this->instances as $login) {
			if (method_exists($login, 'getLogoutURL')) {
				return $login->getLogoutURL();
			}
		}
		return null;
	}

	public function getRegistrationURL() {
		foreach ($this->instances as $login) {
			if (method_exists($login, 'getRegistrationURL')) {
				return $login->getRegistrationURL();
			}
		}
		return null;
	}

	public function getResetURL() {
		foreach ($this->instances as $login) {
			if (method_exists($login, 'getResetURL')) {
				return $login->getResetURL();
			}
		}
		return null;
	}

	public function getRemindURL() {
		foreach ($this->instances as $login) {
			if (method_exists($login, 'getRemindURL')) {
				return $login->getRemindURL();
			}
		}
		return null;
	}
}
