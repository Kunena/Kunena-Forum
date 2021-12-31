<?php
/**
 * Kunena Component
 * @package       Kunena.Framework
 * @subpackage    Integration
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;

/**
 * Class KunenaIntegrationActivity
 *
 * @since 3.0.4
 */
class KunenaIntegrationActivity
{
	/**
	 * @var mixed
	 * @since Kunena
	 */
	protected static $instance;

	/**
	 * @var array
	 * @since Kunena
	 */
	protected $instances = array();

	/**
	 * @since Kunena
	 */
	public function __construct()
	{
		\Joomla\CMS\Plugin\PluginHelper::importPlugin('kunena');

		$classes = Factory::getApplication()->triggerEvent('onKunenaGetActivity');

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
	public static function getInstance()
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
	 * @param   array  $arguments Arguments need to be passed to the method
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

				if ($r !== null && $ret === null)
				{
					$ret = $r;
				}
			}
		}

		return $ret;
	}
}
