<?php
/**
 * Kunena Component
 * @package Kunena.Administrator
 * @subpackage Views
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * About view for Kunena backend
 */
class KunenaAdminViewCategories extends KunenaView {
	function displayCreate() {
		return $this->displayEdit();
	}

	function displayEdit() {
		$this->category = $this->get ( 'AdminCategory' );
		// FIXME: better access control and gracefully handle no rights
		// Prevent fatal error if no rights:
		if (!$this->category) return;
		$this->options = $this->get ( 'AdminOptions' );
		$this->moderators = $this->get ( 'AdminModerators' );
		$this->setToolBarEdit();
		$this->display();
	}

	function displayDefault() {
		$this->categories = $this->get ( 'AdminCategories' );
		$this->navigation = $this->get ( 'AdminNavigation' );

		// Preprocess the list of items to find ordering divisions.
		$this->ordering = array();
		foreach ($this->categories as &$item) {
			$this->ordering[$item->parent_id][] = $item->id;
		}
		$this->setToolBarDefault();
		$this->sortFields = $this->getSortFields();
		$this->sortDirectionFields = $this->getSortDirectionFields();

		$this->user = JFactory::getUser();
		$this->me = KunenaUserHelper::getMyself();
		$this->userId		= $this->user->get('id');
		$this->filterSearch = $this->escape($this->state->get('filter.search'));
		$this->filterPublished = $this->escape($this->state->get('filter.published'));
		$this->filterTitle = $this->escape($this->state->get('filter.title'));
		$this->filterType	= $this->escape($this->state->get('filter.type'));
		$this->filterAccess = $this->escape($this->state->get('filter.access'));
		$this->filterLocked = $this->escape($this->state->get('filter.locked'));
		$this->filterReview = $this->escape($this->state->get('filter.review'));
		$this->filterAnonymous = $this->escape($this->state->get('filter.anonymous'));
		$this->listOrdering = $this->escape($this->state->get('list.ordering'));
		$this->listDirection = $this->escape($this->state->get('list.direction'));
		$this->saveOrder 	= ($this->listOrdering == 'a.ordering' && $this->listDirection == 'asc');
		$this->saveOrderingUrl = 'index.php?option=com_kunena&view=categories&task=saveOrderAjax&tmpl=component';
		$this->display();
	}

	protected function setToolBarEdit() {
		// Set the titlebar text
		JToolBarHelper::title ( JText::_('COM_KUNENA'), 'categories' );
		JToolBarHelper::apply('apply');
		JToolBarHelper::save('save');
		JToolBarHelper::save2new('save2new');

		// If an existing item, can save to a copy.
		if ($this->category->exists()) {
			JToolBarHelper::save2copy('save2copy');
		}
		JToolBarHelper::cancel();
	}
	protected function setToolBarDefault() {
		JToolBarHelper::title ( JText::_('COM_KUNENA').': '.JText::_('COM_KUNENA_CATEGORY_MANAGER'), 'categories');
		//TODO STRING
		JToolBarHelper::addNew ('add', 'New Category');
		JToolBarHelper::editList ();
		JToolBarHelper::publish ();
		JToolBarHelper::unpublish ();
		JToolBarHelper::deleteList ();
		//JToolBarHelper::back ( JText::_ ( 'Home' ), 'index.php?option=com_kunena' );
	}

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return	string	The HTML code for the select tag
	 */
	public function publishedOptions() {
		// Build the active state filter options.
		$options	= array();
		$options[]	= JHtml::_('select.option', '1', JText::_('COM_KUNENA_FIELD_LABEL_ON'));
		$options[]	= JHtml::_('select.option', '0', JText::_('COM_KUNENA_FIELD_LABEL_OFF'));

		return $options;
	}

	/**
	 * Returns an array of locked filter options.
	 *
	 * @return	string	The HTML code for the select tag
	 */
	public function lockOptions() {
		// Build the active state filter options.
		$options	= array();
		$options[]	= JHtml::_('select.option', '1', JText::_('COM_KUNENA_FIELD_LABEL_ON'));
		$options[]	= JHtml::_('select.option', '0', JText::_('COM_KUNENA_FIELD_LABEL_OFF'));

		return $options;
	}

	/**
	 * Returns an array of review filter options.
	 *
	 * @return	string	The HTML code for the select tag
	 */
	public function reviewOptions() {
		// Build the active state filter options.
		$options	= array();
		$options[]	= JHtml::_('select.option', '1', JText::_('COM_KUNENA_FIELD_LABEL_ON'));
		$options[]	= JHtml::_('select.option', '0', JText::_('COM_KUNENA_FIELD_LABEL_OFF'));

		return $options;
	}

	/**
	 * Returns an array of type filter options.
	 *
	 * @return	string	The HTML code for the select tag
	 */
	public function anonymousOptions() {
		// Build the active state filter options.
		$options	= array();
		$options[]	= JHtml::_('select.option', '1', JText::_('COM_KUNENA_FIELD_LABEL_ON'));
		$options[]	= JHtml::_('select.option', '0', JText::_('COM_KUNENA_FIELD_LABEL_OFF'));

		return $options;
	}

	protected function getSortFields() {
		$sortFields = array();
		$sortFields[] = JHtml::_('select.option', 'p.published', JText::_('JSTATUS'));
		$sortFields[] = JHtml::_('select.option', 'p.title', JText::_('JGLOBAL_TITLE'));
		$sortFields[] = JHtml::_('select.option', 'p.access', JText::_('COM_KUNENA_CATEGORIES_LABEL_ACCESS'));
		$sortFields[] = JHtml::_('select.option', 'p.locked', JText::_('COM_KUNENA_LOCKED'));
		$sortFields[] = JHtml::_('select.option', 'p.review', JText::_('COM_KUNENA_REVIEW'));
		$sortFields[] = JHtml::_('select.option', 'p.anonymous', JText::_('COM_KUNENA_CATEGORY_ANONYMOUS'));
		$sortFields[] = JHtml::_('select.option', 'p.id', JText::_('JGRID_HEADING_ID'));

		return $sortFields;
	}

	protected function getSortDirectionFields() {
		$sortDirection = array();
		$sortDirection[] = JHtml::_('select.option', 'asc', JText::_('JGLOBAL_ORDER_ASCENDING'));
		$sortDirection[] = JHtml::_('select.option', 'desc', JText::_('JGLOBAL_ORDER_DESCENDING'));

		return $sortDirection;
	}
}
