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
 * Class ComponentKunenaControllerPageAnnouncementDisplay
 *
 * @since  3.1
 */
class ComponentKunenaControllerPageAnnouncementDisplay extends KunenaControllerDisplay
{
	protected $name = 'Page/Announcement';

	public $announcement;

	/**
	 * Prepare announcement box display.
	 *
	 * @return bool
	 */
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
