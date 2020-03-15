<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Application
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Application\Misc;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Layout\Layout;
use Kunena\Forum\Libraries\Layout\Page;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use function defined;

/**
 * Class ComponentKunenaControllerApplicationMiscDisplay
 *
 * @since   Kunena 4.0
 */
class ComponentKunenaControllerApplicationMiscDefaultDisplay extends KunenaControllerDisplay
{
	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $header;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $body;

	/**
	 * Return custom display layout.
	 *
	 * @return  Layout
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function display()
	{
		$menu_item = $this->app->getMenu()->getActive();

		$componentParams = ComponentHelper::getParams('com_config');
		$robots          = $componentParams->get('robots');

		if ($robots == 'noindex, follow')
		{
			$this->setMetaData('robots', 'noindex, follow');
		}
		elseif ($robots == 'index, nofollow')
		{
			$this->setMetaData('robots', 'index, nofollow');
		}
		elseif ($robots == 'noindex, nofollow')
		{
			$this->setMetaData('robots', 'noindex, nofollow');
		}
		else
		{
			$this->setMetaData('robots', 'index, follow');
		}

		if ($menu_item)
		{
			$params             = $menu_item->getParams();
			$params_title       = $params->get('page_title');
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
				$this->setMetaData('robots', $robots);
			}
		}

		// Display layout with given parameters.
		$content = Page::factory('Misc/Default')
			->set('header', $this->header)
			->set('body', $this->body);

		return $content;
	}

	/**
	 * Prepare custom text output.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	protected function before()
	{
		parent::before();

		$params       = ComponentHelper::getParams('com_kunena');
		$this->header = $params->get('page_title');
		$Itemid       = $this->input->getInt('Itemid');

		if (!$Itemid)
		{
			if ($this->config->custom_id)
			{
				$itemidfix = $this->config->custom_id;
			}
			else
			{
				$menu      = $this->app->getMenu();
				$getid     = $menu->getItem(\Kunena\Forum\Libraries\Route\KunenaRoute::getItemID("index.php?option=com_kunena&view=misc"));
				$itemidfix = $getid->id;
			}

			if (!$itemidfix)
			{
				$itemidfix = KunenaRoute::fixMissingItemID();
			}

			$controller = BaseController::getInstance("kunena");
			$controller->setRedirect(\Kunena\Forum\Libraries\Route\KunenaRoute::_("index.php?option=com_kunena&view=misc&Itemid={$itemidfix}", false));
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

				return $cache->get(['KunenaHtmlParser', 'parseBBCode'], [$body]);
			};
		}
	}
}
