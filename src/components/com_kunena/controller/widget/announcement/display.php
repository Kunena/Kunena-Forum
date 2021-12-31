<?php
/**
 * Kunena Component
 * @package         Kunena.Site
 * @subpackage      Controller.Widget
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerWidgetAnnouncementDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerWidgetAnnouncementDisplay extends KunenaControllerDisplay
{
	/**
	 * @var string
	 * @since Kunena
	 */
	protected $name = 'Widget/Announcement';

	/**
	 * @var
	 * @since Kunena
	 */
	public $announcement;

	/**
	 * Prepare announcement box display.
	 *
	 * @return boolean
	 * @throws Exception
	 * @since Kunena
	 */
	protected function before()
	{
		parent::before();

		$config = KunenaConfig::getInstance();

		if (!$config->showannouncement)
		{
			return false;
		}

		$items              = KunenaForumAnnouncementHelper::getAnnouncements();
		$this->announcement = array_pop($items);

		if (!$this->announcement || !$this->announcement->isAuthorised('read'))
		{
			return false;
		}

		$view   = $this->input->getWord('view', 'default');
		$layout = $this->input->getWord('layout', 'default');

		if ($view == 'topic' && $layout != 'default' || $view == 'user' || $view == 'search' || $view == 'announcement' && $layout == 'default')
		{
			return false;
		}

		return true;
	}
}
