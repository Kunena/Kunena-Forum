<?php
/**
 * Kunena Component
 * @package       Kunena.Framework
 * @subpackage    Controller
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;

/**
 * @see   JController in Joomla! 3.0
 * @since Kunena
 */
abstract class KunenaControllerBase implements Serializable
{
	/**
	 * The application object.
	 *
	 * @var    \Joomla\CMS\Application\BaseApplication|JSite|JAdministrator
	 * @since Kunena
	 */
	protected $app;

	/**
	 * The input object.
	 *
	 * @var    \Joomla\Input\Input
	 * @since Kunena
	 */
	protected $input;

	/**
	 * Options object.
	 *
	 * @var    \Joomla\Registry\Registry
	 * @since  K4.0
	 */
	protected $options = null;

	/**
	 * Instantiate the controller.
	 *
	 * @param   \Joomla\Input\Input                     $input   The input object.
	 * @param   \Joomla\CMS\Application\BaseApplication $app     The application object.
	 * @param   \Joomla\Registry\Registry|array         $options Array \Joomla\Registry\Registry object with the
	 *                                                           options to load.
	 *
	 * @throws Exception
	 * @since Kunena
	 */
	public function __construct(\Joomla\Input\Input $input = null, $app = null, $options = null)
	{
		// Setup dependencies.
		$this->app   = isset($app) ? $app : $this->loadApplication();
		$this->input = isset($input) ? $input : $this->loadInput();

		if ($options)
		{
			$this->setOptions($options);
		}
	}

	/**
	 * Load the application object.
	 *
	 * @return  \Joomla\CMS\Application\BaseApplication  The application object.
	 * @throws Exception
	 * @since Kunena
	 */
	protected function loadApplication()
	{
		return Factory::getApplication();
	}

	/**
	 * Load the input object.
	 *
	 * @return  \Joomla\Input\Input  The input object.
	 * @since Kunena
	 */
	protected function loadInput()
	{
		return $this->app->input;
	}

	/**
	 * Get the options.
	 *
	 * @return  \Joomla\Registry\Registry  Object with the options.
	 *
	 * @since   K4.0
	 */
	public function getOptions()
	{
		// Always return a \Joomla\Registry\Registry instance
		if (!($this->options instanceof \Joomla\Registry\Registry))
		{
			$this->resetOptions();
		}

		return $this->options;
	}

	/**
	 * Set the options.
	 *
	 * @param   mixed $options Array / \Joomla\Registry\Registry object with the options to load.
	 *
	 * @return  KunenaControllerBase  Instance of $this to allow chaining.
	 *
	 * @since   K4.0
	 */
	public function setOptions($options = null)
	{
		// Received \Joomla\Registry\Registry
		if ($options instanceof \Joomla\Registry\Registry)
		{
			$this->options = $options;
		}
		// Received array
		elseif (is_array($options))
		{
			$this->options = new \Joomla\Registry\Registry($options);
		}
		else
		{
			$this->options = new \Joomla\Registry\Registry;
		}

		return $this;
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
	 * @since Kunena
	 */
	abstract public function execute();

	/**
	 * Get the application object.
	 *
	 * @return  \Joomla\CMS\Application\BaseApplication  The application object.
	 * @since Kunena
	 */
	public function getApplication()
	{
		return $this->app;
	}

	/**
	 * Get the input object.
	 *
	 * @return  \Joomla\Input\Input  The input object.
	 * @since Kunena
	 */
	public function getInput()
	{
		return $this->input;
	}

	/**
	 * Serialize the controller.
	 *
	 * @return  string  The serialized controller.
	 * @since Kunena
	 */
	public function serialize()
	{
		return serialize(array($this->input, $this->options));
	}

	/**
	 * Unserialize the controller.
	 *
	 * @param   string $input The serialized controller.
	 *
	 * @return JController|KunenaControllerBase
	 *
	 * @throws Exception
	 * @since Kunena
	 */
	public function unserialize($input)
	{
		// Setup dependencies.
		$this->app = $this->loadApplication();

		// Unserialize the input and options.
		list($this->input, $this->options) = unserialize($input);

		if (!($this->input instanceof \Joomla\Input\Input))
		{
			throw new UnexpectedValueException(sprintf('%s::unserialize would not accept a `%s`.', get_class($this), gettype($this->input)));
		}

		return $this;
	}
}
