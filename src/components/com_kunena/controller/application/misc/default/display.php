<?php
/**
 * Kunena Component
 * @package         Kunena.Site
 * @subpackage      Controller.Application
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

/**
 * Class ComponentKunenaControllerApplicationMiscDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerApplicationMiscDefaultDisplay extends KunenaControllerApplicationDisplay
{
	/**
	 * @var
	 * @since Kunena
	 */
	public $header;

	/**
	 * @var
	 * @since Kunena
	 */
	public $body;

	/**
	 * Return custom display layout.
	 *
	 * @return KunenaLayout
	 * @throws Exception
	 * @since Kunena
	 */
	protected function display()
	{
		$app       = Factory::getApplication();
		$menu_item = $app->getMenu()->getActive();

		$doc             = Factory::getDocument();
		$config          = Factory::getApplication('site');
		$componentParams = $config->getParams('com_config');
		$robots          = $componentParams->get('robots');

		if ($robots == 'noindex, follow')
		{
			$doc->setMetaData('robots', 'noindex, follow');
		}
		elseif ($robots == 'index, nofollow')
		{
			$doc->setMetaData('robots', 'index, nofollow');
		}
		elseif ($robots == 'noindex, nofollow')
		{
			$doc->setMetaData('robots', 'noindex, nofollow');
		}
		else
		{
			$doc->setMetaData('robots', 'index, follow');
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
	 * @since Kunena
	 * @throws Exception
	 * @throws null
	 */
	protected function before()
	{
		parent::before();

		$params       = $this->app->getParams('com_kunena');
		$this->header = $params->get('page_title');
		$Itemid       = $this->input->getInt('Itemid');

		if (!$Itemid)
		{
			if (KunenaConfig::getInstance()->custom_id)
			{
				$itemidfix = KunenaConfig::getInstance()->custom_id;
			}
			else
			{
				$menu      = $this->app->getMenu();
				$getid     = $menu->getItem(KunenaRoute::getItemID("index.php?option=com_kunena&view=misc"));
				$itemidfix = $getid->id;
			}

			if (!$itemidfix)
			{
				$itemidfix = KunenaRoute::fixMissingItemID();
			}

			$controller = BaseController::getInstance("kunena");
			$controller->setRedirect(KunenaRoute::_("index.php?option=com_kunena&view=misc&Itemid={$itemidfix}", false));
			$controller->redirect();
		}

		$body   = $params->get('body');
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

				$cache = Factory::getCache('com_kunena', 'callback');
				$cache->setLifeTime(180);

				return $cache->call(array('KunenaHtmlParser', 'parseBBCode'), $body);
			};
		}
	}
}
