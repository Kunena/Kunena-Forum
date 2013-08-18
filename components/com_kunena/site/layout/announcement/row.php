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

class KunenaLayoutAnnouncementRow extends KunenaLayout
{
	public function canPublish() {
		return $this->announcement->authorise('edit');
	}
	public function canEdit() {
		return $this->announcement->authorise('edit');
	}
	public function canDelete() {
		return $this->announcement->authorise('delete');
	}

	public function displayField($name, $mode=null) {
		return $this->announcement->displayField($name, $mode);
	}
}
