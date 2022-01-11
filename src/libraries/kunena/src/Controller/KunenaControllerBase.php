<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Controller
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Controller;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Application\BaseApplication;
use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\Input\Input;
use Joomla\Registry\Registry;
use LogicException;
use RuntimeException;
use Serializable;
use UnexpectedValueException;

/**
 * @see     JController in Joomla! 3.0
 * @since   Kunena 6.0
 */
abstract class KunenaControllerBase implements Serializable
{
	/**
	 * The application object.
	 *
	 * @var     BaseApplication
	 * @since   Kunena 6.0
	 */
	protected $app;

	/**
	 * The input object.
	 *
	 * @var     Input
	 * @since   Kunena 6.0
	 */
	protected $input;

	/**
	 * Options object.
	 *
	 * @var     Registry
	 * @since   Kunena 4.0
	 */
	protected $options = null;

	/**
	 * Instantiate the controller.
	 *
	 * @param   Input|null  $input                                The input object.
	 * @param   null        $app                                  The application object.
	 * @param   null        $options                              Array Joomla\Registry\Registry object with the
	 *                                                            options to load.
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws Exception
	 */
	public function __construct(Input $input = null, $app = null, $options = null)
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
	 * @return  CMSApplicationInterface
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function loadApplication(): CMSApplicationInterface
	{
		return Factory::getApplication();
	}

	/**
	 * Load the input object.
	 *
	 * @return  Input  The input object.
	 *
	 * @since   Kunena 6.0
	 */
	protected function loadInput(): Input
	{
		return $this->app->input;
	}

	/**
	 * Get the options.
	 *
	 * @return \Joomla\Registry\Registry|null Object with the options.
	 *
	 * @since   Kunena 4.0
	 */
	public function getOptions(): ?Registry
	{
		// Always return a Joomla\Registry\Registry instance
		if (!($this->options instanceof Registry))
		{
			$this->resetOptions();
		}

		return $this->options;
	}

	/**
	 * Set the options.
	 *
	 * @param   mixed  $options  Array / Joomla\Registry\Registry object with the options to load.
	 *
	 * @return  KunenaControllerBase  Instance of $this to allow chaining.
	 *
	 * @since   Kunena 4.0
	 */
	public function setOptions($options = null): KunenaControllerBase
	{
		// Received \Joomla\Registry\Registry
		if ($options instanceof Registry)
		{
			$this->options = $options;
		}
		// Received array
		elseif (\is_array($options))
		{
			$this->options = new Registry($options);
		}
		else
		{
			$this->options = new Registry;
		}

		return $this;
	}

	/**
	 * Function to empty all the options.
	 *
	 * @return  KunenaControllerBase  Instance of $this to allow chaining.
	 *
	 * @since   Kunena 4.0
	 */
	public function resetOptions(): KunenaControllerBase
	{
		return $this->setOptions(null);
	}

	/**
	 * Execute the controller.
	 *
	 * @return  mixed
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  RuntimeException
	 * @throws  LogicException
	 */
	abstract public function execute();

	/**
	 * Get the application object.
	 *
	 * @return  BaseApplication  The application object.
	 *
	 * @since   Kunena 6.0
	 */
	public function getApplication()
	{
		return $this->app;
	}

	/**
	 * Get the input object.
	 *
	 * @return  Input  The input object.
	 *
	 * @since   Kunena 6.0
	 */
	public function getInput(): Input
	{
		return $this->input;
	}

	/**
	 * Needed since PHP 8.1.0 else give drepreciation warning
	 * 
	 * @since   Kunena 6.0
	 * 
	 * @return string
	 */
	public function __serialize()
	{
		return serialize([$this->input, $this->options]);
	}

	/**
	 * Serialize the controller.
	 *
	 * @return  string  The serialized controller.
	 *
	 * @since   Kunena 6.0
	 */
	public function serialize(): string
	{
		return $this->__serialize();
	}

	/**
	 * Needed since PHP 8.1.0 else give drepreciation warning
	 * 
	 * @since  Kunena 6.0
	 * @param  unknown $input
	 * @return \Joomla\Input\Input
	 */
	public function __unserialize($input)
	{
		list($this->input, $this->options) = unserialize($input);

		return $this->input;
	}

	/**
	 * Unserialize the controller.
	 *
	 * @param   string  $input  The serialized controller.
	 *
	 * @return  KunenaControllerBase
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws Exception
	 */
	public function unserialize($input): KunenaControllerBase
	{
		// Setup dependencies.
		$this->app = $this->loadApplication();

		// Unserialize the input and options.
		$this->input = $this->__unserialize($input);

		if (!($this->input instanceof Input))
		{
			throw new UnexpectedValueException(sprintf('%s::unserialize would not accept a `%s`.', \get_class($this), \gettype($this->input)));
		}

		return $this;
	}
}
