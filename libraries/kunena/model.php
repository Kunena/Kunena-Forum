<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Model for Kunena
 *
 * @since		2.0
 */
class KunenaModel extends JModelLegacy {
	/**
	 * @var JSite|JAdministrator
	 */
	public $app = null;

	/**
	 * @var KunenaUser
	 */
	public $me = null;

	/**
	 * @var KunenaConfig
	 */
	public $config = null;

	/**
	 * @var JRegistry
	 */
	public $params = null;

	/**
	 * @var JObject
	 */
	protected $state = null;

	/**
	 * @var bool
	 */
	protected $embedded = false;

	public function __construct($config = array()) {
		$this->option = 'com_kunena';
		parent::__construct($config);

		$this->app = JFactory::getApplication();
		$this->me = KunenaUserHelper::getMyself();
		$this->config = KunenaFactory::getConfig();
	}

	public function initialize($params = array()) {
		$this->embedded = true;
		$this->setState('embedded', true);

		if ($params instanceof JRegistry) {
			$this->params = $params;
		} else {
			$this->params = new JRegistry($params);
		}
	}

	public function getItemid() {
		$Itemid = 0;
		if (!$this->embedded) {
			$active = JFactory::getApplication()->getMenu ()->getActive ();
			$Itemid = $active ? (int) $active->id : 0;
		}
		return $Itemid;
	}

	/**
	 * Escapes a value for output in a view script.
	 *
	 * @param  mixed $var The output to escape.
	 * @return mixed The escaped value.
	 */
	public function escape($var) {
		return htmlspecialchars($var, ENT_COMPAT, 'UTF-8');
	}

	protected function getParameters() {
		// If we are not in embedded mode, get variable from application
		if (!$this->embedded) {
			return $this->app->getParams('com_kunena');
		}
		return $this->params;
	}

	protected function getUserStateFromRequest($key, $request, $default = null, $type = 'none') {
		// If we are not in embedded mode, get variable from application
		if (!$this->embedded) {
			return JFactory::getApplication()->getUserStateFromRequest($key, $request, $default, $type);
		}

		// Embedded models/views do not have user state -- all variables come from parameters
		return $this->getVar($request, $default, 'default', $type);
	}

	protected function getVar($name, $default = null, $hash = 'default', $type = 'none', $mask = 0) {
		// If we are not in embedded mode, get variable from request
		if (!$this->embedded) {
			return JRequest::getVar($name, $default, $hash, $type, $mask);
		}

		return self::_cleanVar($this->params->get($name, $default), $mask, strtoupper($type));
	}

	protected function getBool($name, $default = false, $hash = 'default') {
		return $this->getVar($name, $default, $hash, 'bool');
	}
	protected function getCmd($name, $default = '', $hash = 'default') {
		return $this->getVar($name, $default, $hash, 'cmd');
	}
	protected function getFloat($name, $default = 0.0, $hash = 'default') {
		return $this->getVar($name, $default, $hash, 'float');
	}
	protected function getInt($name, $default = 0, $hash = 'default') {
		return $this->getVar($name, $default, $hash, 'int');
	}
	protected function getString($name, $default = '', $hash = 'default', $mask = 0) {
		return $this->getVar($name, $default, $hash, 'string', $mask);
	}
	protected function getWord($name, $default = '', $hash = 'default') {
		return $this->getVar($name, $default, $hash, 'word');
	}

	protected function _cleanVar($var, $mask = 0, $type=null) {
		// Static input filters for specific settings
		static $noHtmlFilter	= null;
		static $safeHtmlFilter	= null;

		// If the no trim flag is not set, trim the variable
		if (!($mask & 1) && is_string($var)) {
			$var = trim($var);
		}

		// Now we handle input filtering
		if ($mask & 2) {
			// If the allow raw flag is set, do not modify the variable
		}
		elseif ($mask & 4) {
			// If the allow html flag is set, apply a safe html filter to the variable
			if (is_null($safeHtmlFilter)) {
				$safeHtmlFilter = JFilterInput::getInstance(null, null, 1, 1);
			}
			$var = $safeHtmlFilter->clean($var, $type);
		} else {
			// Since no allow flags were set, we will apply the most strict filter to the variable
			if (is_null($noHtmlFilter)) {
				$noHtmlFilter = JFilterInput::getInstance();
			}
			$var = $noHtmlFilter->clean($var, $type);
		}
		return $var;
	}
}
