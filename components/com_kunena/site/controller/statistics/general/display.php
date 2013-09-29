<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Controller.Statistics.Whoisonline
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class ComponentKunenaControllerStatisticsGeneralDisplay extends KunenaControllerDisplay
{
	protected function display() {
		// Display layout with given parameters.
		$content = KunenaLayout::factory('Statistics/General')->setProperties($this->getProperties());

		return $content;
	}

	protected function before() {
		parent::before();

		$this->config = KunenaConfig::getInstance();

		$statistics = KunenaForumStatistics::getInstance();
		$statistics->loadAll();
		$this->setProperties($statistics);

		$this->latestMemberLink = KunenaFactory::getUser((int) $this->lastUserId)->getLink();
		$this->userlistUrl = KunenaFactory::getProfile()->getUserListUrl();

		return true;
	}
}
