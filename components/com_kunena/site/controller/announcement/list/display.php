<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Controllers.Announcement
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class ComponentKunenaControllerAnnouncementListDisplay
 */
class ComponentKunenaControllerAnnouncementListDisplay extends KunenaControllerDisplay
{
	protected $name = 'Announcement/List';

	public $total;
	public $pagination;
	public $announcements;

	protected function before()
	{
		parent::before();

		$limit = $this->input->getInt('limit', 0);
		if ($limit < 1 || $limit > 100) $limit = 20;

		$limitstart = $this->input->getInt('limitstart', 0);
		if ($limitstart < 0) $limitstart = 0;

		$moderator = KunenaUserHelper::getMyself()->isModerator();
		$this->total = KunenaForumAnnouncementHelper::getCount(!$moderator);
		$this->pagination = new JPagination($this->total, $limitstart, $limit);
		$this->announcements = KunenaForumAnnouncementHelper::getAnnouncements($this->pagination->limitstart,
			$this->pagination->limit, !$moderator);

		return true;
	}

	protected function prepareDocument()
	{
		$this->setTitle(JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS'));
	}
}
