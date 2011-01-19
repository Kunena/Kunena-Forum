<?php
/**
 * @version		$Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 */
defined ( '_JEXEC' ) or die ();

jimport ( 'joomla.application.component.model' );
kimport('kunena.forum.category.helper');

/**
 * Model for Kunena
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		2.0
 */
class KunenaModel extends JModel {
	protected $__state_set = null;
	protected $state = null;

	public function __construct($config = array()) {
		parent::__construct($config);
		if (isset($this->_state)) {
			$this->state = $this->_state;
		}
	}

	/**
	 * Method to get model state variables (from Joomla 1.6)
	 *
	 * @param	string	Optional parameter name
	 * @param	mixed	Optional default value
	 * @return	object	The property where specified, the state object where omitted
	 */
	public function getState($property = null, $default = null)
	{
		if (!$this->__state_set) {
			// Private method to auto-populate the model state.
			$this->populateState();

			// Set the model state set flat to true.
			$this->__state_set = true;
		}

		return $property === null ? $this->state : $this->state->get($property, $default);
	}

	/**
	 * Method to set model state variables (from Joomla 1.6)
	 *
	 * @param	string	The name of the property
	 * @param	mixed	The value of the property to set
	 * @return	mixed	The previous value of the property
	 */
	public function setState($property, $value=null)
	{
		return $this->state->set($property, $value);
	}

	public function initialize($params)
	{
		$this->embedded = true;
		$this->params = $params;
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
	protected function populateState()
	{
	}

}