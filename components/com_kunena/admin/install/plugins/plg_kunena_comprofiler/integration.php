<?php
/**
 * Kunena Plugin
 *
 * @package       Kunena.Plugins
 * @subpackage    Comprofiler
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die ();

class KunenaIntegrationComprofiler
{
	protected static $open = false;

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

	/**
	 * Triggers CB events
	 *
	 * Current events: profileIntegration=0/1, avatarIntegration=0/1
	 **/
	public static function trigger($event, &$params)
	{
		global $_PLUGINS;
		$config            = KunenaFactory::getConfig();
		$params ['config'] = $config;
		$_PLUGINS->loadPluginGroup('user');
		$_PLUGINS->trigger('kunenaIntegration', array($event, &$config, &$params));
	}
}
