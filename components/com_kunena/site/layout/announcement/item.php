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
	public function __construct($name, SplPriorityQueue $paths = null) {
		parent::__construct($name, $paths);
		$this->template = KunenaFactory::getTemplate();
	}

	public function displayField($name, $mode=null) {
		return $this->announcement->displayField($name, $mode);
	}

	public function displayActions() {
		$this->buttons = array();
		if ($this->announcement->authorise('edit'))
			$this->buttons['edit'] = $this->getButton($this->announcement->getUri('edit'), 'edit', 'announcement', 'moderation');
		if ($this->announcement->authorise('delete'))
			$this->buttons['delete'] = $this->getButton($this->announcement->getTaskUri('delete'), 'delete', 'announcement', 'permanent');
		if ($this->buttons)
			$this->buttons['cpanel'] = $this->getButton(KunenaForumAnnouncementHelper::getUri('list'), 'list', 'announcement', 'communication');

		$contents = $this->subLayout('Announcement/Item/Actions')->setProperties($this->getProperties());
		return $contents;
	}
}
