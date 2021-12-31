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
 * Attachments view for Kunena backend
 * @since Kunena
 */
class KunenaAdminViewAttachments extends KunenaView
{
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

		$this->filterSearch     = $this->escape($this->state->get('list.search'));
		$this->filterTitle      = $this->escape($this->state->get('filter.title'));
		$this->filterType       = $this->escape($this->state->get('filter.type'));
		$this->filterSize       = $this->escape($this->state->get('filter.size'));
		$this->filterDimensions = $this->escape($this->state->get('filter.dims'));
		$this->filterUsername   = $this->escape($this->state->get('filter.username'));
		$this->filterPost       = $this->escape($this->state->get('filter.post'));
		$this->filterActive     = $this->escape($this->state->get('filter.active'));
		$this->listOrdering     = $this->escape($this->state->get('list.ordering'));
		$this->listDirection    = $this->escape($this->state->get('list.direction'));

		return parent::display($tpl);

	}

	/**
	 * @since Kunena
	 */
	protected function setToolbar()
	{
		$help_url = 'https://docs.kunena.org/en/manual/backend/attachments';
		JToolbarHelper::help('COM_KUNENA', false, $help_url);
		JToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_FILE_MANAGER'), 'folder-open');
		JToolbarHelper::spacer();
		JToolbarHelper::custom('delete', 'trash.png', 'trash_f2.png', 'COM_KUNENA_GEN_DELETE');

		JToolbarHelper::spacer();
	}

	/**
	 * Returns an array of review filter options.
	 *
	 * @return    array
	 * @since Kunena
	 */
	protected function getSortFields()
	{
		$sortFields   = array();
		$sortFields[] = HTMLHelper::_('select.option', 'filename', Text::_('COM_KUNENA_ATTACHMENTS_FIELD_LABEL_TITLE'));
		$sortFields[] = HTMLHelper::_('select.option', 'filetype', Text::_('COM_KUNENA_ATTACHMENTS_FIELD_LABEL_TYPE'));
		$sortFields[] = HTMLHelper::_('select.option', 'size', Text::_('COM_KUNENA_ATTACHMENTS_FIELD_LABEL_SIZE'));
		$sortFields[] = HTMLHelper::_('select.option', 'username', Text::_('COM_KUNENA_ATTACHMENTS_USERNAME'));
		$sortFields[] = HTMLHelper::_('select.option', 'post', Text::_('COM_KUNENA_ATTACHMENTS_FIELD_LABEL_MESSAGE'));
		$sortFields[] = HTMLHelper::_('select.option', 'id', Text::_('JGRID_HEADING_ID'));

		return $sortFields;
	}

	/**
	 * Returns an array of review filter options.
	 * @since Kunena
	 *
	 * @return    array
	 */
	protected function getSortDirectionFields()
	{
		$sortDirection   = array();
		$sortDirection[] = HTMLHelper::_('select.option', 'asc', Text::_('JGLOBAL_ORDER_ASCENDING'));
		$sortDirection[] = HTMLHelper::_('select.option', 'desc', Text::_('JGLOBAL_ORDER_DESCENDING'));

		return $sortDirection;
	}
}
