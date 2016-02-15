<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Controller
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
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
	 * Options object.
	 *
	 * @var    JRegistry
	 * @since  K4.0
	 */
	protected $options = null;

	/**
	 * Instantiate the controller.
	 *
	 * @param   JInput            $input    The input object.
	 * @param   JApplicationBase  $app      The application object.
	 * @param   JRegistry|array   $options  Array / JRegistry object with the options to load.
	 */
	public function __construct(JInput $input = null, $app = null, $options = null)
	{
		// Setup dependencies.
		$this->app = isset($app) ? $app : $this->loadApplication();
		$this->input = isset($input) ? $input : $this->loadInput();

		if ($options)
		{
			$this->setOptions($options);
		}
	}

	/**
	 * Set the options.
	 *
	 * @param   mixed  $options  Array / JRegistry object with the options to load.
	 *
	 * @return  KunenaControllerBase  Instance of $this to allow chaining.
	 *
	 * @since   K4.0
	 */
	public function setOptions($options = null)
	{
		// Received JRegistry
		if ($options instanceof JRegistry)
		{
			$this->options = $options;
		}
		// Received array
		elseif (is_array($options))
		{
			$this->options = new JRegistry($options);
		}
		else
		{
			$this->options = new JRegistry;
		}

		return $this;
	}

	/**
	 * Get the options.
	 *
	 * @return  JRegistry  Object with the options.
	 *
	 * @since   K4.0
	 */
	public function getOptions()
	{
		// Always return a JRegistry instance
		if (!($this->options instanceof JRegistry))
		{
			$this->resetOptions();
		}

		return $this->options;
	}

	/**
	 * Function to empty all the options.
	 *
	 * @return  KunenaControllerBase  Instance of $this to allow chaining.
	 *
	 * @since   K4.0
	 */
	public function resetOptions()
	{
		return $this->setOptions(null);
	}

	/**
	 * Execute the controller.
	 *
	 * @return  mixed
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
	public function getApplication()
	{
		return $this->app;
	}

	/**
	 * Get the input object.
	 *
	 * @return  JInput  The input object.
	 */
	public function getInput()
	{
		return $this->input;
	}

	/**
	 * Serialize the controller.
	 *
	 * @return  string  The serialized controller.
	 */
	public function serialize()
	{
		return serialize(array($this->input, $this->options));
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
	public function unserialize($input)
	{
		// Setup dependencies.
		$this->app = $this->loadApplication();

		// Unserialize the input and options.
		list ($this->input, $this->options) = unserialize($input);

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
	protected function loadApplication()
	{
		return JFactory::getApplication();
	}

	/**
	 * Load the input object.
	 *
	 * @return  JInput  The input object.
	 */
	protected function loadInput()
	{
		return $this->app->input;
	}
}
