<?php
/**
 * Kunena Component
 * @package         Kunena.Site
 * @subpackage      Controller.Widget
 *
 * @copyright       Copyright (C) 2008 - 2017 Kunena Team. All rights reserved.
 * @license         http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerWidgetStatisticsDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerWidgetStatisticsDisplay extends KunenaControllerDisplay
{
	/**
	 * @var string
	 * @since Kunena
	 */
	protected $name = 'Widget/Statistics';

	/**
	 * @var
	 * @since Kunena
	 */
	public $config;

	/**
	 * @var
	 * @since Kunena
	 */
	public $latestMemberLink;

	/**
	 * @var
	 * @since Kunena
	 */
	public $statisticsUrl;

	/**
	 * Prepare statistics box display.
	 *
	 * @return boolean
	 * @since Kunena
	 */
	protected function before()
	{
		parent::before();

		$this->config = KunenaConfig::getInstance();

		if (!$this->config->get('showstats') || (!$this->config->statslink_allowed && !KunenaUserHelper::get()->exists()))
		{
			throw new KunenaExceptionAuthorise(JText::_('COM_KUNENA_NO_ACCESS'), '404');
		}

		$statistics = KunenaForumStatistics::getInstance();
		$statistics->loadGeneral();
		$this->setProperties($statistics);

		$this->latestMemberLink = KunenaFactory::getUser(intval($this->lastUserId))->getLink(null, null, '');
		$this->statisticsUrl    = KunenaFactory::getProfile()->getStatisticsURL();

		return true;
	}
}
