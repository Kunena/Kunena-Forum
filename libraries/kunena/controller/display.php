<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Controller
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

abstract class KunenaControllerDisplay extends KunenaControllerBase
{
	public $output = null;

	/**
	 * @see KunenaControllerBase::execute()
	 */
	public function execute() {
		KUNENA_PROFILER ? KunenaProfiler::instance()->start('function '.get_class($this).'::'.__FUNCTION__.'()') : null;
		// Run before executing action.
		$result = $this->before();
		if ($result === false) {
			return KunenaLayout::factory('Empty');
		}

		// Display layout with given parameters.
		$this->output = $this->display();

		// Run after executing action.
		$this->after();

		KUNENA_PROFILER ? KunenaProfiler::instance()->stop('function '.get_class($this).'::'.__FUNCTION__.'()') : null;
		return $this->output;
	}

	/**
	 * Initialize and display the layout.
	 *
	 * @return KunenaLayout
	 */
	abstract protected function display();

	/**
	 * Executed before display.
	 */
	protected function before() {}

	/**
	 * Executed after display.
	 */
	protected function after() {}

	/**
	 * Return view as a string.
	 *
	 * @return string
	 */
	public function __toString() {
		$output = (string) $this->execute();

		return $output;
	}

	/**
	 * Returns an associative array of public object properties.
	 *
	 * @return  array
	 */
	public function getProperties()
	{
		$properties = (array) $this;
		$list = array();
		foreach ($properties as $property=>$value) {
			if ($property[0] != "\0") $list[$property] = $value;
		}
		return $list;
	}

	/**
	 * Shortcut for $this->input->set()
	 *
	 * @param $key
	 * @param $value
	 * @return $this
	 */
	public function set($key, $value)
	{
		$this->input->set($key, (string) $value);
		return $this;
	}
}
