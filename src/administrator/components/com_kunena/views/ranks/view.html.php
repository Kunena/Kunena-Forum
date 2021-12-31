<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Views
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/**
 * About view for Kunena ranks backend
 *
 * @since  K1.X
 */
class KunenaAdminViewRanks extends KunenaView
{
	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return    array    The HTML code for the select tag
	 * @since Kunena
	 */
	public static function specialOptions()
	{
		// Build the active state filter options.
		$options   = array();
		$options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_FIELD_LABEL_YES'));
		$options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FIELD_LABEL_NO'));

		return $options;
	}

	/**
	 * @param   null $tpl tpl
	 *
	 * @return mixed
	 * @since Kunena
	 */
	public function display($tpl = null)
	{
		$this->setToolbar();
		$this->items      = $this->get('Items');
		$this->state      = $this->get('state');
		$this->pagination = $this->get('Pagination');

		$this->sortFields          = $this->getSortFields();
		$this->sortDirectionFields = $this->getSortDirectionFields();

		$this->filterSearch       = $this->escape($this->state->get('filter.search'));
		$this->filterTitle        = $this->escape($this->state->get('filter.title'));
		$this->filterSpecial      = $this->escape($this->state->get('filter.special'));
		$this->filterMinPostCount = $this->escape($this->state->get('filter.min'));
		$this->filterActive       = $this->escape($this->state->get('filter.active'));
		$this->listOrdering       = $this->escape($this->state->get('list.ordering'));
		$this->listDirection      = $this->escape($this->state->get('list.direction'));

		return parent::display($tpl);
	}

	/**
	 * @since Kunena
	 */
	protected function setToolbar()
	{
		$this->filterActive = $this->escape($this->state->get('filter.active'));
		$this->pagination   = $this->get('Pagination');

		JToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_RANK_MANAGER'), 'star-2');

		JToolbarHelper::spacer();
		JToolbarHelper::addNew('add', 'COM_KUNENA_NEW_RANK');
		JToolbarHelper::editList();
		JToolbarHelper::divider();
		JToolbarHelper::deleteList();
		JToolbarHelper::spacer();
		$help_url = 'https://docs.kunena.org/en/manual/backend/ranks/add-rank';
		JToolbarHelper::help('COM_KUNENA', false, $help_url);
	}

	/**
	 * @return array
	 * @since Kunena
	 */
	protected function getSortFields()
	{
		$sortFields   = array();
		$sortFields[] = HTMLHelper::_('select.option', 'title', Text::_('JGLOBAL_TITLE'));
		$sortFields[] = HTMLHelper::_('select.option', 'special', Text::_('COM_KUNENA_RANKS_SPECIAL'));
		$sortFields[] = HTMLHelper::_('select.option', 'min', Text::_('COM_KUNENA_RANKSMIN'));
		$sortFields[] = HTMLHelper::_('select.option', 'id', Text::_('JGRID_HEADING_ID'));

		return $sortFields;
	}

	/**
	 * @return array
	 * @since Kunena
	 */
	protected function getSortDirectionFields()
	{
		$sortDirection   = array();
		$sortDirection[] = HTMLHelper::_('select.option', 'asc', Text::_('JGLOBAL_ORDER_ASCENDING'));
		$sortDirection[] = HTMLHelper::_('select.option', 'desc', Text::_('JGLOBAL_ORDER_DESCENDING'));

		return $sortDirection;
	}
}
