<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Administrator
 * @subpackage    Views
 *
 * @copyright     Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * Block Ip view for Kunena backend
 *
 * @since 5.1
 */
class KunenaAdminViewBlockips extends KunenaView
{
	/**
	 * @param   null  $tpl  tpl
	 *
	 * @since Kunena
	 * @throws Exception
	 */
	public function displayDefault($tpl = null)
	{
		$this->state      = $this->get('state');
		$this->group      = $this->state->get('group');
		$this->items      = $this->get('items');
		$this->pagination = $this->get('Pagination');

		$document = Factory::getApplication()->getDocument();
		$document->setTitle(Text::_('COM_KUNENA_A_BLOCKIP_MANAGER'));

		$this->setToolbar();
		$this->display();
	}

	/**
	 * Set the toolbar on log manager
	 *
	 * @since Kunena
	 */
	protected function setToolbar()
	{
		// Get the toolbar object instance
		$bar = Joomla\CMS\Toolbar\Toolbar::getInstance('toolbar');

		// Set the titlebar text
		ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_A_BLOCKIP_MANAGER'));
	}
}
