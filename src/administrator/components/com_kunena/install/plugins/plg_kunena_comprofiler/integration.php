<?php
/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Comprofiler
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Class KunenaIntegrationComprofiler
 * @since Kunena
 */
class KunenaIntegrationComprofiler
{
	/**
	 * @var boolean
	 * @since Kunena
	 */
	protected static $open = false;

	/**
	 * @since Kunena
	 * @throws Exception
	 */
	public static function open()
	{
		if (self::$open)
		{
			return;
		}

		self::$open = true;
		$params     = array();
		self::trigger('onStart', $params);
	}

	/**
	 * Triggers CB events
	 *
	 * Current events: profileIntegration=0/1, avatarIntegration=0/1
	 *
	 * @param $event
	 * @param $params
	 *
	 * @throws Exception
	 * @since Kunena
	 */
	public static function trigger($event, &$params)
	{
		global $_PLUGINS;
		$config            = KunenaFactory::getConfig();
		$params ['config'] = $config;
		$_PLUGINS->loadPluginGroup('user');
		$_PLUGINS->trigger('kunenaIntegration', array($event, &$config, &$params));
	}

	/**
	 * @since Kunena
	 * @throws Exception
	 */
	public static function close()
	{
		if (!self::$open)
		{
			return;
		}

		self::$open = false;
		$params     = array();
		self::trigger('onEnd', $params);
	}
}
