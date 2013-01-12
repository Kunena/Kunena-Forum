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

		JToolBarHelper::publish ();
		JToolBarHelper::unpublish ();
		JToolBarHelper::addNew ();
		JToolBarHelper::editList ();
		JToolBarHelper::deleteList ();
		//JToolBarHelper::back ( JText::_ ( 'Home' ), 'index.php?option=com_kunena' );

		// Joomla 3.0+
		$this->sidebar = '';
		if (0 && version_compare(JVERSION, '3', '>')) {
			// TODO: not implemented in model
			JHtmlSidebar::setAction('index.php?option=com_kunena&view=categories');

			JHtmlSidebar::addFilter(
					JText::_('JOPTION_SELECT_PUBLISHED'),
					'jgrid.published',
					JHtml::_('select.options', PluginsHelper::publishedOptions(), 'value', 'text', $this->state->get('jgrid.published'), true)
			);

			JHtmlSidebar::addFilter(
					JText::_('- Select Type -'),
					'filter_type',
					JHtml::_('select.options', PluginsHelper::typeOptions(), 'value', 'text', $this->state->get('filter.type'))
			);

			JHtmlSidebar::addFilter(
					JText::_('JOPTION_SELECT_ACCESS'),
					'filter_access',
					JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'))
			);

			$this->sidebar = JHtmlSidebar::render();
		}
	}
}

// TODO: what is this?!?
class PluginsHelper
{
	public static $extension = 'com_kunena';

	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 */
	public static function addSubmenu($vName)
	{
		// No submenu for this component.
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return	JObject
	 */
	public static function getActions()
	{
		$user		= JFactory::getUser();
		$result		= new JObject;
		$assetName	= 'com_kunena';

		$actions = JAccess::getActions($assetName);

		foreach ($actions as $action) {
			$result->set($action->name,	$user->authorise($action->name, $assetName));
		}

		return $result;
	}

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return	string			The HTML code for the select tag
	 */
	public static function publishedOptions()
	{
		// Build the active state filter options.
		$options	= array();
		$options[]	= JHtml::_('select.option', '1', 'JENABLED');
		$options[]	= JHtml::_('select.option', '0', 'JDISABLED');

		return $options;
	}

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return	string			The HTML code for the select tag
	 */
	public static function typeOptions()
	{
		// Build the active state filter options.
		$options	= array();
		$options[]	= JHtml::_('select.option', '1', 'Sections');
		$options[]	= JHtml::_('select.option', '0', 'Categories');

		return $options;
	}

}
