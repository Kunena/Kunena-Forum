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

class KunenaLayoutAnnouncementItem extends KunenaLayout
{
	public $buttons;

	public function getActions() {
		$this->buttons = array();
		if ($this->announcement->authorise('edit'))
			$this->buttons['edit'] = $this->getButton($this->announcement->getUri('edit'), 'edit', 'announcement', 'moderation');
		if ($this->announcement->authorise('delete'))
			$this->buttons['delete'] = $this->getButton($this->announcement->getTaskUri('delete'), 'delete', 'announcement', 'permanent');
		if ($this->buttons)
			$this->buttons['cpanel'] = $this->getButton(KunenaForumAnnouncementHelper::getUri('list'), 'list', 'announcement', 'communication');

		return $this->buttons;
	}
}
