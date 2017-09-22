<?php
/**
 * Kunena Component
 * @package       Kunena.Administrator
 * @subpackage    Views
 *
 * @copyright     Copyright (C) 2008 - 2017 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Email view for Kunena backend
 *
 * @since 5.0
 */
class KunenaAdminViewEmail extends KunenaView
{
	/**
	 * @param   null $tpl
	 *
	 * @since Kunena
	 */
	public function displayDefault($tpl = null)
	{
		$this->state      = $this->get('state');
		$this->group      = $this->state->get('group');
		$this->items      = $this->get('items');
		$this->pagination = $this->get('Pagination');

		$document = \Joomla\CMS\Factory::getDocument();
		$document->setTitle(JText::_('COM_KUNENA_A_EMAIL_MANAGER'));

		$this->setToolbar();
		$this->display();
	}

	/**
	 * Set the toolbar on log manager
	 * @since Kunena
	 */
	protected function setToolbar()
	{
		// Get the toolbar object instance
		$bar = \Joomla\CMS\Toolbar\Toolbar::getInstance('toolbar');

		// Set the titlebar text
		JToolbarHelper::title(JText::_('COM_KUNENA') . ': ' . JText::_('COM_KUNENA_A_EMAIL_MANAGER'));
	}
}
