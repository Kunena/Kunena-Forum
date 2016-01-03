<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Integration
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class KunenaIntegrationActivity
 *
 * @since 3.0.4
 */
class KunenaIntegrationActivity
{
	protected $instances = array();

	protected static $instance;

	public function __construct()
	{
		JPluginHelper::importPlugin('kunena');
		$dispatcher = JDispatcher::getInstance();
		$classes = $dispatcher->trigger('onKunenaGetActivity');

		foreach ($classes as $class)
		{
			if (!is_object($class))
			{
				continue;
			}

			$this->instances[] = $class;
		}
	}

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
	 * @param   string  $method     Name of method to call
	 * @param   string  $arguments  Arguments need to be passed to the method
	 *
	 * @return mixed
	 */
	public function __call($method, $arguments)
	{
		$ret = null;
		foreach ($this->instances as $instance)
		{
			if (method_exists($instance, $method))
			{
				$r = call_user_func_array(array($instance, $method), $arguments);

				if($r !== null & $ret === null)
				{
					$ret = $r;
				}
			}
		}

		return $ret;
	}
}
