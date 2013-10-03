<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Controllers.Page
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class ComponentKunenaControllerPageAnnouncementDisplay
 */
class ComponentKunenaControllerPageAnnouncementDisplay extends KunenaControllerDisplay
{
	protected $name = 'Page/Announcement';

	public $announcement;

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
