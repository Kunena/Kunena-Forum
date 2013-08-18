<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Controllers.Misc
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class ComponentKunenaControllerApplicationMiscDisplay
 */
class ComponentKunenaControllerApplicationMiscDisplay extends KunenaControllerApplicationDisplay
{
	protected $header;
	protected $body;

	protected function display() {
		// Display layout with given parameters.
		$content = KunenaLayout::factory('Page/Custom')
			->set('header', $this->header)
			->set('body', $this->body);
		return $content;
	}

	protected function before() {
		parent::before();

		$params = $this->app->getParams('com_kunena');
		$this->header = $params->get('page_title');

		$body = $params->get('body');
		$format = $params->get('body_format');

		$this->header = htmlspecialchars($this->header, ENT_COMPAT, 'UTF-8');
		if ($format == 'html') {
			$this->body = trim($body);
		} elseif ($format == 'text') {
			$this->body = function () use ($body) { return htmlspecialchars($body, ENT_COMPAT, 'UTF-8'); };
		} else {
			$this->body = function () use ($body) {
				/** @var JCache|JCacheControllerCallback $cache */
				$cache = JFactory::getCache('com_kunena', 'callback');
				$cache->setLifeTime(180);
				return $cache->call(array('KunenaHtmlParser','parseBBCode'), $body);
			};
		}
	}
}
