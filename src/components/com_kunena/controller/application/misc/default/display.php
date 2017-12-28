<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.Application
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerApplicationMiscDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerApplicationMiscDefaultDisplay extends KunenaControllerApplicationDisplay
{
	public $header;

	public $body;

	/**
	 * Return custom display layout.
	 *
	 * @return KunenaLayout
	 */
	protected function display()
	{
		$app       = JFactory::getApplication();
		$menu_item = $app->getMenu()->getActive();

		$doc = JFactory::getDocument();
		$config = JFactory::getApplication('site');
		$componentParams = $config->getParams('com_config');
		$robots = $componentParams->get('robots');

		if ($robots == '')
		{
			$doc->setMetaData('robots', 'index, follow');
		}
		elseif ($robots == 'noindex, follow')
		{
			$doc->setMetaData('robots', 'noindex, follow');
		}
		elseif ($robots == 'index, nofollow')
		{
			$doc->setMetaData('robots', 'index, nofollow');
		}
		else
		{
			$doc->setMetaData('robots', 'nofollow, noindex');
		}

		if ($menu_item)
		{
			$params             = $menu_item->params;
			$params_title       = $params->get('page_title');
			$params_keywords    = $params->get('menu-meta_keywords');
			$params_description = $params->get('menu-meta_description');
			$params_robots      = $params->get('robots');

			if (!empty($params_title))
			{
				$title = $params->get('page_title');
				$this->setTitle($title);
			}
			else
			{
				$title = $this->config->board_title;
				$this->setTitle($title);
			}

			if (!empty($params_keywords))
			{
				$keywords = $params->get('menu-meta_keywords');
				$this->setKeywords($keywords);
			}
			else
			{
				$keywords = $this->config->board_title;
				$this->setKeywords($keywords);
			}

			if (!empty($params_description))
			{
				$description = $params->get('menu-meta_description');
				$this->setDescription($description);
			}
			else
			{
				$description = $this->config->board_title;
				$this->setDescription($description);
			}

			if (!empty($params_robots))
			{
				$robots = $params->get('robots');
				$doc->setMetaData('robots', $robots);
			}
		}

		// Display layout with given parameters.
		$content = KunenaLayoutPage::factory('Misc/Default')
			->set('header', $this->header)
			->set('body', $this->body);

		return $content;
	}

	/**
	 * Prepare custom text output.
	 *
	 * @return void
	 */
	protected function before()
	{
		parent::before();

		$params = $this->app->getParams('com_kunena');
		$this->header = $params->get('page_title');

		$body = $params->get('body');
		$format = $params->get('body_format');

		$this->header = htmlspecialchars($this->header, ENT_COMPAT, 'UTF-8');

		if ($format == 'html')
		{
			$this->body = trim($body);
		}
		elseif ($format == 'text')
		{
			$this->body = function () use ($body) {

				return htmlspecialchars($body, ENT_COMPAT, 'UTF-8');
			};
		}
		else
		{
			$this->body = function () use ($body) {

				// @var JCache|JCacheControllerCallback $cache

				$cache = JFactory::getCache('com_kunena', 'callback');
				$cache->setLifeTime(180);

				return $cache->call(array('KunenaHtmlParser','parseBBCode'), $body);
			};
		}
	}
}
