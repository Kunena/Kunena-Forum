<?php
/**
 * Kunena Component
 * @package Kunena.Framework
 * @subpackage Module
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class KunenaModule
 */
abstract class KunenaModule {
	/**
	 * CSS file to be loaded.
	 * @var string
	 */
	static protected $css = null;

	/**
	 * @var stdClass
	 */
	protected $module = null;
	/**
	 * @var JRegistry
	 */
	protected $params = null;

	/**
	 * @param stdClass $module
	 * @param JRegistry $params
	 */
	public function __construct($module, $params) {
		$this->module = $module;
		$this->params = $params;
		$this->document = JFactory::getDocument();
	}

	/**
	 * Internal module function to display module contents.
	 */
	abstract protected function _display();

	/**
	 * Display module contents.
	 */
	final public function display() {
		// Load CSS only once
		if (static::$css) {
			$this->document->addStyleSheet(JURI::root(true) . static::$css);
			static::$css = null;
		}

		// Use caching also for registered users if enabled.
		if ($this->params->get('owncache', 0)) {
			/** @var $cache JCacheControllerOutput */
			$cache = JFactory::getCache('com_kunena', 'output');

			$me = KunenaFactory::getUser();
			$cache->setLifeTime($this->params->get('cache_time', 180));
			$hash = md5(serialize($this->params));
			if ($cache->start("display.{$me->userid}.{$hash}", 'mod_kunenalatest')) {
				return;
			}
		}

		// Initialize Kunena.
		KunenaForum::setup();

		// Display module.
		$this->_display();

		// Store cached page.
		if (isset($cache)) {
			$cache->end();
		}
	}
}
