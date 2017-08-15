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
 * @since       K1.5
 */
class KunenaAdminViewPlugin extends KunenaView
{
	protected $item = null;

	protected $form = null;

	protected $state = null;

	/**
	 * Display the view
	 *
	 * @param   null  $tpl
	 *
	 * @return bool
	 *
	 * @throws Exception
	 */
	public function displayEdit($tpl = null)
	{
		$this->state = $this->get('State');
		$this->item  = $this->get('Item');
		$this->form  = $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		$this->addToolbar();
		$this->display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return bool
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);
		JToolbarHelper::title(JText::_('COM_KUNENA') . ': ' . JText::_('COM_KUNENA_PLUGIN_MANAGER'), 'pluginsmanager');
		JToolbarHelper::spacer();
		JToolbarHelper::apply('apply');
		JToolbarHelper::save('save');
		JToolbarHelper::cancel('cancel', 'JTOOLBAR_CLOSE');
		JToolbarHelper::spacer();

		$help_url  = 'https://docs.kunena.org/en/manual/backend/plugins';
		JToolBarHelper::help('COM_KUNENA', false, $help_url);
	}
}
