<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Views
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

jimport ( 'joomla.cache.handler.output' );

/**
 * Announcement view
 */
class KunenaViewAnnouncement extends KunenaView {
	function displayDefault($tpl = null) {
		$this->assignRef ( 'announcement', $this->get ( 'Announcement' ) );

		if (!$this->announcement->authorise('read')) {
			$this->setError($this->announcement->getError());
		}

		$new = new KunenaForumAnnouncement;

		$this->actions = array();
		if ($this->announcement->authorise('edit')) $this->actions['edit'] = $this->announcement->getUrl('edit', 'object');
		if ($this->announcement->authorise('delete')) $this->actions['delete'] = $this->announcement->getTaskUrl('delete', 'object');
		if ($new->authorise('create')) $this->actions['add'] = $new->getUrl('create', 'object');
		if ($this->actions) $this->actions['cpanel'] = KunenaForumAnnouncementHelper::getUrl('list', 'object');

		$this->showdate = $this->announcement->showdate;

		$this->setTitle(JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS') . ' - ' . $this->config->board_title);

		$errors = $this->getErrors();
		if ($errors) {
			return $this->displayNoAccess($errors);
		}

		$this->display();
	}

	function displayCreate($tpl = null) {
		$this->assignRef ( 'announcement', $this->get ( 'NewAnnouncement' ) );

		if (!$this->announcement->authorise('create')) {
			$this->setError($this->announcement->getError());
		}

		$this->returnUrl = KunenaForumAnnouncementHelper::getUrl('list', 'object');

		$this->setTitle(JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS') . ' - ' . $this->config->board_title);

		$errors = $this->getErrors();
		if ($errors) {
			return $this->displayNoAccess($errors);
		}

		$this->display();
	}

	function displayEdit($tpl = null) {
		$this->assignRef ( 'announcement', $this->get ( 'Announcement' ) );

		if (!$this->announcement->authorise('edit')) {
			$this->setError($this->announcement->getError());
		}

		$this->returnUrl = KunenaForumAnnouncementHelper::getUrl('list', 'object');

		$this->setTitle(JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS') . ' - ' . $this->config->board_title);

		$errors = $this->getErrors();
		if ($errors) {
			return $this->displayNoAccess($errors);
		}

		$this->display();
	}

	function displayList($tpl = null) {
		$this->assignRef ( 'announcements', $this->get ( 'Announcements' ) );
		$new = new KunenaForumAnnouncement;

		$this->actions = array();
		if ($new->authorise('create')) $this->actions['add'] = $new->getUrl('create', 'object');
		if ($this->actions) $this->actions['cpanel'] = KunenaForumAnnouncementHelper::getUrl('list', 'object');

		$this->setTitle(JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS') . ' - ' . $this->config->board_title);

		$errors = $this->getErrors();
		if ($errors) {
			return $this->displayNoAccess($errors);
		}

		$this->display();
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
		echo $this->loadTemplateFile ( 'item' );
		$this->row++;
	}
	function canPublish() {
		return $this->announcement->authorise('edit');
	}
	function canEdit() {
		return $this->announcement->authorise('edit');
	}
	function canDelete() {
		return $this->announcement->authorise('delete');
	}
}