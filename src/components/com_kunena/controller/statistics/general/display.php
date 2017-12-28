<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.Statistics
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerStatisticsGeneralDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerStatisticsGeneralDisplay extends KunenaControllerDisplay
{
	protected $name = 'Statistics/General';

	/**
	 * Prepare general statistics display.
	 *
	 * @return void
	 *
	 * @throws KunenaExceptionAuthorise
	 */
	protected function before()
	{
		parent::before();

		$this->config = KunenaConfig::getInstance();

		if (!$this->config->get('showstats'))
		{
			throw new KunenaExceptionAuthorise(JText::_('COM_KUNENA_NO_ACCESS'), '404');
		}

		if (!$this->config->statslink_allowed && JFactory::getUser()->guest)
		{
			throw new KunenaExceptionAuthorise(JText::_('COM_KUNENA_NO_ACCESS'), '401');
		}

		$statistics = KunenaForumStatistics::getInstance();
		$statistics->loadAll();
		$this->setProperties($statistics);

		$this->latestMemberLink = KunenaFactory::getUser((int) $this->lastUserId)->getLink(null, null, '');
		$this->userlistUrl = KunenaFactory::getProfile()->getUserListUrl();
	}

	/**
	 * Prepare document.
	 *
	 * @return void
	 */
	protected function prepareDocument()
	{
		$app       = JFactory::getApplication();
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
				$this->setTitle(JText::_('COM_KUNENA_STAT_FORUMSTATS'));
			}

			if (!empty($params_keywords))
			{
				$keywords = $params->get('menu-meta_keywords');
				$this->setKeywords($keywords);
			}
			else
			{
				$keywords = $this->config->board_title . ', ' . JText::_('COM_KUNENA_STAT_FORUMSTATS');
				$this->setKeywords($keywords);
			}

			if (!empty($params_description))
			{
				$description = $params->get('menu-meta_description');
				$this->setDescription($description);
			}
			else
			{
				$description = JText::_('COM_KUNENA_STAT_FORUMSTATS') . ': ' . $this->config->board_title;
				$this->setDescription($description);
			}
		}
	}
}
