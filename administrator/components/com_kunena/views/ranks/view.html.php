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
 * About view for Kunena ranks backend
 */
class KunenaAdminViewRanks extends KunenaView {
	function displayDefault() {
		$this->setToolBarDefault();
		$this->ranks = $this->get('Ranks');
		$this->state = $this->get('state');
		$this->navigation = $this->get ( 'AdminNavigation' );
		$this->display ();
	}

	function displayAdd() {
		$this->displayEdit();
	}

	function displayEdit() {
		$this->setToolBarEdit();
		$this->state = $this->get('state');
		$this->rank_selected = $this->get('rank');
		$this->rankpath = $this->ktemplate->getRankPath();
		$this->listranks = $this->get('Rankspaths');
		$this->display ();
	}

	protected function setToolBarDefault() {
		JToolBarHelper::title ( JText::_('COM_KUNENA').': '.JText::_('COM_KUNENA_RANK_MANAGER'), 'kunena.png' );
		JToolBarHelper::spacer();
		JToolBarHelper::addNew('add', 'COM_KUNENA_NEW_RANK');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('edit', 'edit.png', 'edit_f2.png', 'COM_KUNENA_EDIT');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('delete', 'delete.png', 'delete_f2.png', 'COM_KUNENA_GEN_DELETE');
		JToolBarHelper::spacer();
	}

	protected function setToolBarEdit() {
		JToolBarHelper::title ( JText::_('COM_KUNENA'), 'kunena.png' );
		JToolBarHelper::spacer();
		JToolBarHelper::save('save');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('ranks');
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
	public static function specialOptions()
	{
		// Build the active state filter options.
		$options	= array();
		$options[]	= JHtml::_('select.option', '1', 'Yes');
		$options[]	= JHtml::_('select.option', '0', 'No');

		return $options;
	}
}