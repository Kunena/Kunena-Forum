<?php
/**
 * Kunena Component
 * @package         Kunena.Site
 * @subpackage      Controller.Statistics
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;

/**
 * Class ComponentKunenaControllerStatisticsGeneralDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerStatisticsGeneralDisplay extends KunenaControllerDisplay
{
	/**
	 * @var string
	 * @since Kunena
	 */
	protected $name = 'Statistics/General';

	/**
	 * Prepare general statistics display.
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @since Kunena
	 * @throws null
	 */
	protected function before()
	{
		parent::before();

		$this->config = KunenaConfig::getInstance();

		$Itemid = $this->input->getInt('Itemid');

		if (!$Itemid && KunenaConfig::getInstance()->sef_redirect)
		{
			$itemid     = KunenaRoute::fixMissingItemID();
			$controller = BaseController::getInstance("kunena");
			$controller->setRedirect(KunenaRoute::_("index.php?option=com_kunena&view=statistics&Itemid={$itemid}", false));
			$controller->redirect();
		}

		if (!$this->config->get('showstats'))
		{
			throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), '404');
		}

		if (!$this->config->statslink_allowed && Factory::getUser()->guest)
		{
			throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), '401');
		}

		$statistics = KunenaForumStatistics::getInstance();
		$statistics->loadAll();
		$this->setProperties($statistics);

		$this->latestMemberLink = KunenaFactory::getUser((int) $this->lastUserId)->getLink(null, null, '');
		$this->userlistUrl      = KunenaFactory::getProfile()->getUserListUrl();
	}

	/**
	 * Prepare document.
	 *
	 * @return void
	 * @throws Exception
	 * @since Kunena
	 */
	protected function prepareDocument()
	{
		$app       = Factory::getApplication();
		$menu_item = $app->getMenu()->getActive();

		if ($menu_item)
		{
			$params             = $menu_item->params;
			$params_title       = $params->get('page_title');
			$params_keywords    = $params->get('menu-meta_keywords');
			$params_description = $params->get('menu-meta_description');

			if (!empty($params_title))
			{
				$title = $params->get('page_title');
				$this->setTitle($title);
			}
			else
			{
				$this->setTitle(Text::_('COM_KUNENA_STAT_FORUMSTATS'));
			}

			if (!empty($params_keywords))
			{
				$keywords = $params->get('menu-meta_keywords');
				$this->setKeywords($keywords);
			}
			else
			{
				$keywords = $this->config->board_title . ', ' . Text::_('COM_KUNENA_STAT_FORUMSTATS');
				$this->setKeywords($keywords);
			}

			if (!empty($params_description))
			{
				$description = $params->get('menu-meta_description');
				$this->setDescription($description);
			}
			else
			{
				$description = Text::_('COM_KUNENA_STAT_FORUMSTATS') . ': ' . $this->config->board_title;
				$this->setDescription($description);
			}
		}
	}
}
