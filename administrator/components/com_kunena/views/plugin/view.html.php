<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_plugins
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * View to edit a plugin.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_plugins
 * @since       1.5
 */
class KunenaAdminViewPlugin extends JViewLegacy
{
	protected $item;

	protected $form;

	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user  = JFactory::getUser();
		//$canDo = PluginsHelper::getActions();

		JToolbarHelper::title( JText::_('COM_KUNENA').': '.JText::_('COM_KUNENA_PLUGINS_MANAGER'), 'pluginsmanager');
		JToolbarHelper::spacer();
		// If not checked out, can save the item.
		//if ($canDo->get('core.edit'))
		//{
			JToolbarHelper::apply('plugin.apply');
			JToolbarHelper::save('plugin.save');
		//}
		JToolbarHelper::cancel('plugin.cancel', 'JTOOLBAR_CLOSE');
		JToolbarHelper::spacer();
	}
}
