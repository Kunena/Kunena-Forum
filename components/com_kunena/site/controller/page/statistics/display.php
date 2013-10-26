<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.Page
 *
 * @copyright   (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerPageStatisticsDisplay
 *
 * @since  3.1
 */
class ComponentKunenaControllerPageStatisticsDisplay extends KunenaControllerDisplay
{
	protected $name = 'Page/Statistics';

	public $config;

	public $latestMemberLink;

	public $statisticsUrl;

	public $userlistUrl;

	/**
	 * Prepare statistics box display.
	 *
	 * @return bool
	 */
	protected function before()
	{
		parent::before();

		$this->config = KunenaConfig::getInstance();

		if (!$this->config->get('showstats'))
		{
			return false;
		}

		$statistics = KunenaForumStatistics::getInstance();
		$statistics->loadGeneral();
		$this->setProperties($statistics);

		$this->latestMemberLink = KunenaFactory::getUser(intval($this->lastUserId))->getLink();
		$this->statisticsUrl = KunenaRoute::_('index.php?option=com_kunena&view=statistics');
		$this->userlistUrl = KunenaFactory::getProfile()->getUserListUrl();

		return true;
	}
}
