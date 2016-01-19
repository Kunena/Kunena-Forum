<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.Widget
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerWidgetAnnouncementDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerWidgetAnnouncementDisplay extends KunenaControllerDisplay
{
	protected $name = 'Widget/Announcement';

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

		$view = $this->input->getWord('view', 'default');
		$layout = $this->input->getWord('layout', 'default');

		if ($view == 'topic' && $layout != 'default'  || $view == 'user' || $view == 'search' || $view == 'announcement' && $layout == 'default') {
			return false;
		}

		return true;
	}
}
