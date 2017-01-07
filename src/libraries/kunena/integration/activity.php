<?php
/**
 * Kunena Component
 * @package       Kunena.Framework
 * @subpackage    Integration
 *
 * @copyright     Copyright (C) 2008 - 2017 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Class KunenaIntegrationActivity
 *
 * @since 3.0.4
 */
class KunenaIntegrationActivity
{
	/**
	 * @var array
	 * @since Kunena
	 */
	protected $instances = array();

	/**
	 * @var
	 * @since Kunena
	 */
	protected static $instance;

	/**
	 *
	 * @since Kunena
	 */
	public function __construct()
	{
		JPluginHelper::importPlugin('kunena');
		$dispatcher = JEventDispatcher::getInstance();
		$classes    = $dispatcher->trigger('onKunenaGetActivity');

		foreach ($classes as $class)
		{
			if (!is_object($class))
			{
				continue;
			}

			$this->instances[] = $class;
		}
	}

	/**
	 * @return static
	 * @since Kunena
	 */
	static public function getInstance()
	{
		if (!self::$instance)
		{
			self::$instance = new static;
		}

		return self::$instance;
	}

	/**
	 * Method magical to call the right method in plugin integration
	 *
	 * @param   string $method    Name of method to call
	 * @param   string $arguments Arguments need to be passed to the method
	 *
	 * @return mixed
	 * @since Kunena
	 */
	public function __call($method, $arguments)
	{
		$ret = null;

		foreach ($this->instances as $instance)
		{
			if (method_exists($instance, $method))
			{
				$r = call_user_func_array(array($instance, $method), $arguments);

				if ($r !== null & $ret === null)
				{
					$ret = $r;
				}
			}
		}

		return $ret;
	}
}
