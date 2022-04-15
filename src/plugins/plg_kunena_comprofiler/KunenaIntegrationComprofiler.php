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

use Kunena\Forum\Libraries\Factory\KunenaFactory;

/**
 * Class KunenaIntegrationComprofiler
 *
 * @since   Kunena 6.0
 */
class KunenaIntegrationComprofiler
{
	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	protected static $open = false;

	/**
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public static function open()
	{
		if (self::$open)
		{
			return;
		}

		self::$open = true;
		$params     = [];
		self::trigger('onStart', $params);
	}

	/**
	 * Triggers CB events
	 *
	 * Current events: profileIntegration=0/1, avatarIntegration=0/1
	 *
	 * @param   string  $event   event
	 * @param   array   $params  params
	 *
	 * @return  void
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	public static function trigger(string $event, array $params): void
	{
		global $_PLUGINS;
		$config            = KunenaFactory::getConfig();
		$params ['config'] = $config;
		$_PLUGINS->loadPluginGroup('user');
		$_PLUGINS->trigger('kunenaIntegration', [$event, &$config, &$params]);
	}

	/**
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	public static function close(): void
	{
		if (!self::$open)
		{
			return;
		}

		self::$open = false;
		$params     = [];
		self::trigger('onEnd', $params);
	}
}
