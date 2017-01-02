<?php
/**
 * Kunena Component
 * @package       Kunena.Administrator
 * @subpackage    Views
 *
 * @copyright     Copyright (C) 2008 - 2017 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Block Ip view for Kunena backend
 *
 * @since 5.1
 */
class KunenaAdminViewBlockips extends KunenaView
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

		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_KUNENA_A_BLOCKIP_MANAGER'));

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
		$bar = JToolBar::getInstance('toolbar');

		// Set the titlebar text
		JToolBarHelper::title(JText::_('COM_KUNENA') . ': ' . JText::_('COM_KUNENA_A_BLOCKIP_MANAGER'));

	}
}
