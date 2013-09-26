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

class ComponentKunenaControllerAnnouncementListDisplay extends KunenaControllerDisplay
{
	public $layout;

	protected function display() {
		// Display layout with given parameters.
		$content = KunenaLayout::factory('Announcement/List')->setProperties($this->getProperties());

		return $content;
	}

	protected function before() {
		$this->layout = $this->input->getCmd('layout', 'default');

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
}
