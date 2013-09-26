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

class ComponentKunenaControllerAnnouncementItemDisplay extends KunenaControllerDisplay
{
	public $layout;

	protected function display() {
		if (!$this->announcement->authorise('read')) {
			$content = KunenaLayout::factory('Page/Custom')
				->set('header', JText::_('COM_KUNENA_ACCESS_DENIED'))
				->set('body', $this->announcement->getError());

			return $content;
		}

		// Display layout with given parameters.
		$content = KunenaLayout::factory('Announcement/Item')->setProperties($this->getProperties());

		return $content;
	}

	protected function before() {
		parent::before();

		$id = $this->input->getInt('id', null);

		$this->layout = $this->input->getCmd('layout', 'default');
		$this->announcement = KunenaForumAnnouncementHelper::get($id);

		return true;
	}
}
