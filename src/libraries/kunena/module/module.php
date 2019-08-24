<?php
/**
 * Kunena Component
 * @package       Kunena.Framework
 * @subpackage    Module
 *
 * @copyright     Copyright (C) 2008 - 2019 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

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
	 * @var Joomla\Registry\Registry
	 * @since Kunena
	 */
	protected $params = null;

	/**
	 * @param   stdClass                  $module  module
	 * @param   Joomla\Registry\Registry  $params  params
	 *
	 * @since Kunena
	 */
	public function __construct($module, $params)
	{
		$this->module   = $module;
		$this->params   = $params;
		$this->document = Factory::getApplication()->getDocument();
	}

	/**
	 * Display module contents.
	 * @return void
	 * @since Kunena
	 * @throws Exception
	 */
	final public function display()
	{
		// Load CSS only once
		if (static::$css)
		{
			$this->document->addStyleSheet(Uri::root(true) . static::$css);
			static::$css = null;
		}

		// Use caching also for registered users if enabled.
		if ($this->params->get('owncache', 0))
		{
			// @var $cache Joomla\CMS\Cache\CacheControllerOutput

			$cache = Factory::getCache('com_kunena', 'output');

			$me = Factory::getApplication()->getIdentity();
			$cache->setLifeTime($this->params->get('cache_time', 180));
			$hash = md5(serialize($this->params));

			// Disable Cache for now: FIXME
			/*
			if ($cache->start("display.{$me->userid}.{$hash}", 'mod_kunenalatest'))
			{
				return;
			}*/
		}

		// Initialize Kunena.
		KunenaForum::setup();

		// Display module.
		$this->_display();

		// Disable Cache for now: FIXME
		/*
		// Store cached page.
		if (isset($cache))
		{
			$cache->end();
		}*/
	}

	/**
	 * Internal module function to display module contents.
	 * @return void
	 * @since Kunena
	 */
	abstract protected function _display();
}
