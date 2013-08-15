<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Layout.Announcement.List
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

class KunenaLayoutAnnouncementList extends KunenaLayout
{
	function getPaginationObject($maxpages) {
		$pagination = new KunenaPagination($this->total, $this->state->get('list.start'), $this->state->get('list.limit'));
		$pagination->setDisplayedPages($maxpages);

		return $pagination;
	}

	function displayItems() {
		$this->row = 0;
		$this->k = 0;
		foreach ($this->announcements as $this->announcement) {
			$this->displayItem();
		}
	}

	function displayItem() {
		$this->k= 1 - $this->k;
		echo $this->subLayout('Announcement/Row')->setProperties($this->getProperties());
		$this->row++;
	}

	function displayField($name, $mode=null) {
		return $this->announcement->displayField($name, $mode);
	}
}
