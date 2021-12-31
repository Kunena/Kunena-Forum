<?php
/**
 * Kunena Component
 * @package       Kunena.Administrator
 * @subpackage    Views
 *
 * @copyright     Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * Labels view for Kunena backend
 *
 * @since 5.0
 */
class KunenaAdminViewLabels extends KunenaView
{
	/**
	 * @param   null $tpl tpl
	 *
	 * @since Kunena
	 */
	public function displayDefault($tpl = null)
	{
		$this->state      = $this->get('state');
		$this->group      = $this->state->get('group');
		$this->items      = $this->get('items');
		$this->pagination = $this->get('Pagination');

		$document = Factory::getDocument();
		$document->setTitle(Text::_('Forum Labels'));

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
		JToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_A_LABELS_MANAGER'));

	}
}
