<?php
/**
 * Kunena Component
 * @package       Kunena.Framework
 * @subpackage    Module
 *
 * @copyright     Copyright (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Class KunenaModule
 * @since Kunena
 */
abstract class KunenaModule
{
	/**
	 * CSS file to be loaded.
	 * @var string
	 * @since Kunena
	 */
	protected static $css = null;

	/**
	 * @var stdClass
	 * @since Kunena
	 */
	protected $module = null;

	/**
	 * @var \Joomla\Registry\Registry
	 * @since Kunena
	 */
	protected $params = null;

	/**
	 * @param   stdClass                  $module
	 * @param   \Joomla\Registry\Registry $params
	 *
	 * @since Kunena
	 */
	public function __construct($module, $params)
	{
		$this->module   = $module;
		$this->params   = $params;
		$this->document = \Joomla\CMS\Factory::getDocument();
	}

	/**
	 * Display module contents.
	 * @since Kunena
	 * @throws Exception
	 */
	final public function display()
	{
		// Load CSS only once
		if (static::$css)
		{
			/** @noinspection PhpDeprecationInspection */
			$this->document->addStyleSheet(\Joomla\CMS\Uri\Uri::root(true) . static::$css);
			static::$css = null;
		}

		// Use caching also for registered users if enabled.
		if ($this->params->get('owncache', 0))
		{
			// @var $cache \Joomla\CMS\Cache\CacheControllerOutput

			$cache = \Joomla\CMS\Factory::getCache('com_kunena', 'output');

			$me = KunenaFactory::getUser();
			$cache->setLifeTime($this->params->get('cache_time', 180));
			$hash = md5(serialize($this->params));

			if ($cache->start("display.{$me->userid}.{$hash}", 'mod_kunenalatest'))
			{
				return;
			}
		}

		// Initialize Kunena.
		KunenaForum::setup();

		// Display module.
		$this->_display();

		// Store cached page.
		if (isset($cache))
		{
			$cache->end();
		}
	}

	/**
	 * Internal module function to display module contents.
	 * @since Kunena
	 */
	abstract protected function _display();
}
