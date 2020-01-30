<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Controller
 *
 * @copyright     Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Controller;

defined('_JEXEC') or die();

use Exception;
use JController;
use Joomla\CMS\Application\BaseApplication;
use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\Input\Input;
use Joomla\Registry\Registry;
use LogicException;
use RuntimeException;
use Serializable;
use UnexpectedValueException;
use function defined;

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
	 * @param   Input            $input                           The input object.
	 * @param   BaseApplication  $app                             The application object.
	 * @param   Registry|array   $options                         Array Joomla\Registry\Registry object with the
	 *                                                            options to load.
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
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
	protected function loadApplication()
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
	protected function loadInput()
	{
		return $this->app->input;
	}

	/**
	 * Get the options.
	 *
	 * @return  Registry  Object with the options.
	 *
	 * @since   Kunena 4.0
	 */
	public function getOptions()
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
	public function setOptions($options = null)
	{
		// Received \Joomla\Registry\Registry
		if ($options instanceof Registry)
		{
			$this->options = $options;
		}
		// Received array
		elseif (is_array($options))
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
	public function resetOptions()
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
	public function getInput()
	{
		return $this->input;
	}

	/**
	 * Serialize the controller.
	 *
	 * @return  string  The serialized controller.
	 *
	 * @since   Kunena 6.0
	 */
	public function serialize()
	{
		return serialize([$this->input, $this->options]);
	}

	/**
	 * Unserialize the controller.
	 *
	 * @param   string  $input  The serialized controller.
	 *
	 * @return JController|KunenaControllerBase
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function unserialize($input)
	{
		// Setup dependencies.
		$this->app = $this->loadApplication();

		// Unserialize the input and options.
		list($this->input, $this->options) = unserialize($input);

		if (!($this->input instanceof Input))
		{
			throw new UnexpectedValueException(sprintf('%s::unserialize would not accept a `%s`.', get_class($this), gettype($this->input)));
		}

		return $this;
	}
}
