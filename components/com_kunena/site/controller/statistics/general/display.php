<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Controllers.Statistics
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class ComponentKunenaControllerStatisticsGeneralDisplay
 */
class ComponentKunenaControllerStatisticsGeneralDisplay extends KunenaControllerDisplay
{
	protected $name = 'Statistics/General';

	protected function before()
	{
		parent::before();

		$this->config = KunenaConfig::getInstance();
		if (!$this->config->get('showstats')) throw new KunenaExceptionAuthorise(JText::_('COM_KUNENA_NO_ACCESS'), '404');

		$statistics = KunenaForumStatistics::getInstance();
		$statistics->loadAll();
		$this->setProperties($statistics);

		$this->latestMemberLink = KunenaFactory::getUser((int) $this->lastUserId)->getLink();
		$this->userlistUrl = KunenaFactory::getProfile()->getUserListUrl();

		return true;
	}

	protected function prepareDocument()
	{
		$this->setTitle(JText::_('COM_KUNENA_STAT_FORUMSTATS'));
	}
}
