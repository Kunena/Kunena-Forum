<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Views
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
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
		$this->announcement = $this->get ( 'Announcement' );

		if (!$this->announcement->authorise('read')) {
			$this->setError($this->announcement->getError());
		}

		$this->showdate = $this->announcement->showdate;

		$this->_prepareDocument();

		$errors = $this->getErrors();
		if ($errors) {
			$this->displayNoAccess($errors);
			return;
		}

		$this->display();
	}

	function displayCreate($tpl = null) {
		$this->announcement = $this->get ( 'NewAnnouncement' );

		if (!$this->announcement->authorise('create')) {
			$this->setError($this->announcement->getError());
		}

		$this->returnUrl = KunenaForumAnnouncementHelper::getUri('list');

		$this->_prepareDocument();

		$errors = $this->getErrors();
		if ($errors) {
			$this->displayNoAccess($errors);
			return;
		}

		$this->display();
	}

	function displayEdit($tpl = null) {
		$this->announcement = $this->get ( 'Announcement' );

		if (!$this->announcement->authorise('edit')) {
			$this->setError($this->announcement->getError());
		}

		$this->returnUrl = KunenaForumAnnouncementHelper::getUri('list');

		$this->_prepareDocument();

		$errors = $this->getErrors();
		if ($errors) {
			$this->displayNoAccess($errors);
			return;
		}

		$this->display();
	}

	function displayList($tpl = null) {
		$this->announcements = $this->get ( 'Announcements' );
		$new = new KunenaForumAnnouncement;

		$this->actions = array();
		if ($new->authorise('create')) $this->actions['add'] = $new->getUri('create');
		if ($this->actions) $this->actions['cpanel'] = KunenaForumAnnouncementHelper::getUri('list');

		$this->announcementActions = $this->get ( 'announcementActions' );

		$this->_prepareDocument();

		$this->total = $this->get('Total');

		$errors = $this->getErrors();
		if ($errors) {
			$this->displayNoAccess($errors);
			return;
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

	function displayActions() {
		$this->buttons = array();
		if ($this->announcement->authorise('edit'))
			$this->buttons['edit'] = $this->getButton($this->announcement->getUri('edit'), 'edit', 'announcement', 'moderation');
		if ($this->announcement->authorise('delete'))
			$this->buttons['delete'] = $this->getButton($this->announcement->getTaskUri('delete'), 'delete', 'announcement', 'permanent');
		if ($this->buttons)
			$this->buttons['cpanel'] = $this->getButton(KunenaForumAnnouncementHelper::getUri('list'), 'list', 'announcement', 'communication');

		$contents = $this->loadTemplateFile('actions');
		return $contents;
	}

	function displayField($name, $mode=null) {
		return $this->announcement->displayField($name, $mode);
	}

	function displayInput($name, $attributes='', $id=null) {
		switch ($name) {
			case 'id':
				return '<input type="hidden" name="id" value="'.intval($this->announcement->id).'" />';
			case 'title':
				return '<input type="text" name="title" '.$attributes.' value="'.$this->escape($this->announcement->title).'"/>';
			case 'sdescription':
				return '<textarea name="sdescription" '.$attributes.'>'.$this->escape($this->announcement->sdescription).'</textarea>';
			case 'description':
				return '<textarea name="description" '.$attributes.'>'.$this->escape($this->announcement->description).'</textarea>';
			case 'created':
				return JHtml::_('calendar', $this->escape($this->announcement->created), 'created', $id);
			case 'showdate':
				$options	= array();
				$options[]	= JHtml::_('select.option',  '0', JText::_('COM_KUNENA_NO') );
				$options[]	= JHtml::_('select.option',  '1', JText::_('COM_KUNENA_YES') );
				return JHtml::_('select.genericlist',  $options, 'showdate', $attributes, 'value', 'text', $this->announcement->showdate, $id );
			case 'published':
				$options	= array();
				$options[]	= JHtml::_('select.option',  '0', JText::_('COM_KUNENA_NO') );
				$options[]	= JHtml::_('select.option',  '1', JText::_('COM_KUNENA_YES') );
				return JHtml::_('select.genericlist',  $options, 'published', $attributes, 'value', 'text', $this->announcement->published, $id );
		}
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

	function getPagination($maxpages) {
		$pagination = new KunenaPagination($this->total, $this->state->get('list.start'), $this->state->get('list.limit'));
		$pagination->setDisplayedPages($maxpages);

		return $pagination->getPagesLinks();
	}

	protected function _prepareDocument(){
		$this->setTitle(JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS'));

		// TODO: set keywords and description
	}
}
