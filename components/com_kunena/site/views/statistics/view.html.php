<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Site
 * @subpackage    Views
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          http://www.kunena.org
 **/
defined('_JEXEC') or die ();

/**
 * Statistics View
 */
class KunenaViewStatistics extends KunenaView
{
	function displayDefault($tpl = null)
	{
		$kunena_stats = KunenaForumStatistics::getInstance();
		$kunena_stats->loadAll();

		$this->assign($kunena_stats);
		$this->latestMemberLink = KunenaFactory::getUser(intval($this->lastUserId))->getLink();
		$this->userlist         = $this->_getUserListLink('', intval($this->get('memberCount')));

		if (!KunenaFactory::getConfig()->statslink_allowed && JFactory::getUser()->guest)
		{
			return $this->app->enqueueMessage(JText::_('COM_KUNENA_STATS_NOT_ALLOWED_TOSEE_STATS'));
		}

		$this->_prepareDocument();

		$this->render('Statistics/General', $tpl);
	}

	protected function _getUserListLink($action, $name, $title = null, $rel = 'nofollow')
	{
		$profile = KunenaFactory::getProfile();
		$link    = $profile->getUserListURL($action, true);

		return "<a href=\"{$link}\" title=\"{$title}\" rel=\"{$rel}\">{$name}</a>";
	}

	protected function _prepareDocument()
	{
		$app = JFactory::getApplication();
		$menu_item   = $app->getMenu()->getActive(); // get the active item
		$params = $menu_item->params; // get the params
		$params_title = $params->get('page_title');
		$params_keywords = $params->get('menu-meta_keywords');
		$params_description = $params->get('menu-description');

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
			$this->setKeywords(JText::_('COM_KUNENA_STAT_FORUMSTATS'));
		}

		if (!empty($params_description))
		{
			$description = $params->get('menu-meta_description');
			$this->setDescription($description);
		}
		else
		{
			$this->setDescription(JText::_('COM_KUNENA_STAT_FORUMSTATS'));
		}
	}
}
