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
		$this->setToolBarDefault();
		$this->display();
	}

	protected function setToolBarEdit() {
		// Set the titlebar text
		JToolBarHelper::title ( JText::_('COM_KUNENA'), 'kunena.png' );
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
		JToolBarHelper::title ( JText::_('COM_KUNENA').': '.JText::_('COM_KUNENA_CATEGORY_MANAGER'));
		//TODO STRING
		JToolBarHelper::addNew ('add', 'New Category');
		JToolBarHelper::editList ();
		JToolBarHelper::publish ();
		JToolBarHelper::unpublish ();
		JToolBarHelper::deleteList ();
		//JToolBarHelper::back ( JText::_ ( 'Home' ), 'index.php?option=com_kunena' );

		// Joomla 3.0+
		$this->sidebar = '';
		if ( version_compare(JVERSION, '3', '>')) {
			JHtmlSidebar::setAction('index.php?option=com_kunena&view=categories');

			$publishedOptions	= array();
			$publishedOptions[]	= JHtml::_('select.option', '1', 'JENABLED');
			$publishedOptions[]	= JHtml::_('select.option', '0', 'JDISABLED');

			JHtmlSidebar::addFilter(
					JText::_('JOPTION_SELECT_PUBLISHED'),
					'jgrid.published',
					JHtml::_('select.options', $publishedOptions, 'value', 'text', $this->state->get('jgrid.published'), true)
			);

			$typeOptions	= array();
			$typeOptions[]	= JHtml::_('select.option', '1', 'Sections');
			$typeOptions[]	= JHtml::_('select.option', '2', 'Categories');

			JHtmlSidebar::addFilter(
					JText::_('- Select Type -'),
					'filter_type',
					JHtml::_('select.options', $typeOptions, 'value', 'text', $this->state->get('filter.type'))
			);

			JHtmlSidebar::addFilter(
					JText::_('JOPTION_SELECT_ACCESS'),
					'filter_access',
					JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'))
			);

			$this->sidebar = JHtmlSidebar::render();
		}
	}

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return	string			The HTML code for the select tag
	 */
	public function publishedOptions()
	{
		// Build the active state filter options.
		$options	= array();
		$options[]	= JHtml::_('select.option', '1', 'On');
		$options[]	= JHtml::_('select.option', '0', 'Off');

		return $options;
	}

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return	string			The HTML code for the select tag
	 */
	public function typeOptions()
	{
		// Build the active state filter options.
		$options	= array();
		$options[]	= JHtml::_('select.option', '1', 'Sections');
		$options[]	= JHtml::_('select.option', '0', 'Categories');

		return $options;
	}

	/**
	 * Returns an array of locked filter options.
	 *
	 * @return	string			The HTML code for the select tag
	 */
	public function lockOptions()
	{
		// Build the active state filter options.
		$options	= array();
		$options[]	= JHtml::_('select.option', '1', 'On');
		$options[]	= JHtml::_('select.option', '0', 'Off');

		return $options;
	}

	/**
	 * Returns an array of review filter options.
	 *
	 * @return	string			The HTML code for the select tag
	 */
	public function reviewOptions()
	{
		// Build the active state filter options.
		$options	= array();
		$options[]	= JHtml::_('select.option', '1', 'On');
		$options[]	= JHtml::_('select.option', '0', 'Off');

		return $options;
	}

	/**
	 * Returns an array of type filter options.
	 *
	 * @return	string			The HTML code for the select tag
	 */
	public function anonymousOptions()
	{
		// Build the active state filter options.
		$options	= array();
		$options[]	= JHtml::_('select.option', '1', 'On');
		$options[]	= JHtml::_('select.option', '0', 'Off');

		return $options;
	}
}
