<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport ( 'joomla.application.component.model' );

/**
 * Model for Kunena
 *
 * @since		2.0
 */
class KunenaModel extends JModel {
	public $app = null;
	public $me = null;
	public $config = null;

	protected $__state_set = null;
	protected $state = null;
	protected $embedded = false;

	public function __construct($config = array()) {
		parent::__construct($config);
		if (isset($this->_state)) {
			$this->state = $this->_state;
		}
		$this->app = JFactory::getApplication();
		$this->me = KunenaUserHelper::getMyself();
		$this->config = KunenaFactory::getConfig();
	}

	/**
	 * Method to get model state variables (from Joomla 1.6)
	 *
	 * @param	string	Optional parameter name
	 * @param	mixed	Optional default value
	 * @return	object	The property where specified, the state object where omitted
	 */
	public function getState($property = null, $default = null) {
		if (!$this->__state_set) {
			// Private method to auto-populate the model state.
			$this->populateState();

			// Set the model state set flat to true.
			$this->__state_set = true;
		}

		$value = $property === null ? $this->state : $this->state->get($property, $default);
		return $value;
	}

	/**
	 * Method to set model state variables (from Joomla 1.6)
	 *
	 * @param	string	The name of the property
	 * @param	mixed	The value of the property to set
	 * @return	mixed	The previous value of the property
	 */
	public function setState($property, $value=null) {
		return $this->state->set($property, $value);
	}

	public function initialize($params = array()) {
		$this->embedded = true;
		$this->setState('embedded', true);

		if ($params instanceof JRegistry) {
			$this->params = $params;
		} else {
			if (version_compare(JVERSION, '1.6', '>')) {
				// Joomla 1.6+
				$this->params = new JRegistry($params);
			} else {
				// Joomla 1.5
				$this->params = new JParameter('');
				$this->params->bind($params);
			}
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
	 * Method to auto-populate the model state (from Joomla 1.6)
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 */
	protected function populateState() {
	}

	/**
	 * Escapes a value for output in a view script.
	 *
	 * If escaping mechanism is one of htmlspecialchars or htmlentities.
	 *
	 * @param  mixed $var The output to escape.
	 * @return mixed The escaped value.
	 */
	public function escape($var) {
		if (in_array ( $this->_escape, array ('htmlspecialchars', 'htmlentities' ) )) {
			return call_user_func ( $this->_escape, $var, ENT_COMPAT, 'UTF-8' );
		}
		return call_user_func ( $this->_escape, $var );
	}

	/**
	 * Sets the _escape() callback.
	 *
	 * @param mixed $spec The callback for _escape() to use.
	 */
	public function setEscape($spec) {
		$this->_escape = $spec;
	}

	protected function getParameters() {
		// If we are not in embedded mode, get variable from application
		if (!$this->embedded) {
			return JFactory::getApplication()->getPageParameters('com_kunena');
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