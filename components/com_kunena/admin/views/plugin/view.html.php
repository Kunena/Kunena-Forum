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
class KunenaAdminViewPlugin extends KunenaView
{
	protected $item = null;

	protected $form = null;

	protected $state = null;

	/**
	 * Display the view
	 */
	public function displayEdit($tpl = null)
	{
		$this->state = $this->get('State');
		$this->item  = $this->get('Item');
		$this->form  = $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));

			return;
		}

		$this->addToolbar();
		$this->display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		//$canDo = PluginsHelper::getActions();

		JToolbarHelper::title(JText::_('COM_KUNENA') . ': ' . JText::_('COM_KUNENA_PLUGIN_MANAGER'), 'pluginsmanager');
		JToolbarHelper::spacer();

		// If not checked out, can save the item.
		//if ($canDo->get('core.edit'))
		//{
		JToolbarHelper::apply('apply');
		JToolbarHelper::save('save');
		//}
		JToolbarHelper::cancel('cancel', 'JTOOLBAR_CLOSE');
		JToolbarHelper::spacer();

		$help_url  = 'http://www.kunena.org/docs/';
		JToolBarHelper::help( 'COM_KUNENA', false, $help_url );
	}
}
