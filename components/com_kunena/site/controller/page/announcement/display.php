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

class ComponentKunenaControllerPageAnnouncementDisplay extends KunenaControllerDisplay
{
	public $announcement;

	protected function display()
	{
		// TODO: add caching.
		// Display layout with given parameters.
		$content = KunenaLayout::factory('Page/Announcement')
			->set('announcement', $this->announcement);

		return $content;
	}

	protected function before()
	{
		parent::before();

		$config = KunenaConfig::getInstance();
		if (!$config->showannouncement)
		{
			return false;
		}
		$items = KunenaForumAnnouncementHelper::getAnnouncements();
		$this->announcement = array_pop($items);
		if (!$this->announcement || !$this->announcement->authorise('read'))
		{
			return false;
		}

		return true;
	}
}
