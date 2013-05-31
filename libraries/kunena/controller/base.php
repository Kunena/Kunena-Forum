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

/**
 * @see JController in Joomla! 3.0
 */
abstract class KunenaControllerBase implements Serializable
{
	/**
	 * The application object.
	 *
	 * @var    JApplicationBase|JSite|JAdministrator
	 */
	protected $app;

	/**
	 * The input object.
	 *
	 * @var    JInput
	 */
	protected $input;

	/**
	 * Instantiate the controller.
	 *
	 * @param   JInput            $input  The input object.
	 * @param   JApplicationBase  $app    The application object.
	 */
	public function __construct(JInput $input = null, $app = null) {
		// Setup dependencies.
		$this->app = isset($app) ? $app : $this->loadApplication();
		$this->input = isset($input) ? $input : $this->loadInput();
	}

	/**
	 * Execute the controller.
	 *
	 * @return  boolean  True if controller finished execution, false if the controller did not
	 *                   finish execution. A controller might return false if some precondition for
	 *                   the controller to run has not been satisfied.
	 *
	 * @throws  LogicException
	 * @throws  RuntimeException
	 */
	abstract public function execute();

	/**
	 * Get the application object.
	 *
	 * @return  JApplicationBase  The application object.
	 */
	public function getApplication() {
		return $this->app;
	}

	/**
	 * Get the input object.
	 *
	 * @return  JInput  The input object.
	 */
	public function getInput() {
		return $this->input;
	}

	/**
	 * Serialize the controller.
	 *
	 * @return  string  The serialized controller.
	 */
	public function serialize() {
		return serialize($this->input);
	}

	/**
	 * Unserialize the controller.
	 *
	 * @param   string  $input  The serialized controller.
	 *
	 * @return  JController  Supports chaining.
	 *
	 * @throws  UnexpectedValueException if input is not the right class.
	 */
	public function unserialize($input) {
		// Setup dependencies.
		$this->app = $this->loadApplication();

		// Unserialize the input.
		$this->input = unserialize($input);

		if (!($this->input instanceof JInput))
		{
			throw new UnexpectedValueException(sprintf('%s::unserialize would not accept a `%s`.', get_class($this), gettype($this->input)));
		}

		return $this;
	}

	/**
	 * Load the application object.
	 *
	 * @return  JApplicationBase  The application object.
	 */
	protected function loadApplication() {
		return JFactory::getApplication();
	}

	/**
	 * Load the input object.
	 *
	 * @return  JInput  The input object.
	 */
	protected function loadInput() {
		return $this->app->input;
	}
}
