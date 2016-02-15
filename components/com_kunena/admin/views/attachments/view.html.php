<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Administrator
 * @subpackage    Views
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/
defined('_JEXEC') or die ();

/**
 * Attachments view for Kunena backend
 */
class KunenaAdminViewAttachments extends KunenaView
{
	function display($tpl = null)
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

	protected function setToolbar()
	{
		$help_url  = 'https://www.kunena.org/docs/';
		JToolBarHelper::help( 'COM_KUNENA', false, $help_url );
		// Set the titlebar text
		if (version_compare(JVERSION, '3', '>'))
		{
			JToolBarHelper::title(JText::_('COM_KUNENA') . ': ' . JText::_('COM_KUNENA_FILE_MANAGER'), 'folder-open');
		}
		else
		{
			JToolBarHelper::title(JText::_('COM_KUNENA') . ': ' . JText::_('COM_KUNENA_FILE_MANAGER'), 'files');
		}

		JToolBarHelper::spacer();

		if (version_compare(JVERSION, '3', '>'))
		{
			JToolBarHelper::custom('delete', 'trash.png', 'trash_f2.png', 'COM_KUNENA_GEN_DELETE');
		}
		else
		{
			JToolBarHelper::custom('delete', 'delete.png', 'delete_f2.png', 'COM_KUNENA_GEN_DELETE');
		}

		JToolBarHelper::spacer();
	}

	protected function getSortFields()
	{
		$sortFields   = array();
		$sortFields[] = JHtml::_('select.option', 'filename', JText::_('COM_KUNENA_ATTACHMENTS_FIELD_LABEL_TITLE'));
		$sortFields[] = JHtml::_('select.option', 'filetype', JText::_('COM_KUNENA_ATTACHMENTS_FIELD_LABEL_TYPE'));
		$sortFields[] = JHtml::_('select.option', 'size', JText::_('COM_KUNENA_ATTACHMENTS_FIELD_LABEL_SIZE'));
		$sortFields[] = JHtml::_('select.option', 'username', JText::_('COM_KUNENA_ATTACHMENTS_USERNAME'));
		$sortFields[] = JHtml::_('select.option', 'post', JText::_('COM_KUNENA_ATTACHMENTS_FIELD_LABEL_MESSAGE'));
		$sortFields[] = JHtml::_('select.option', 'id', JText::_('JGRID_HEADING_ID'));

		return $sortFields;
	}

	protected function getSortDirectionFields()
	{
		$sortDirection = array();
//		$sortDirection[] = JHtml::_('select.option', 'asc', JText::_('JGLOBAL_ORDER_ASCENDING'));
//		$sortDirection[] = JHtml::_('select.option', 'desc', JText::_('JGLOBAL_ORDER_DESCENDING'));
		// TODO: remove it when J2.5 support is dropped
		$sortDirection[] = JHtml::_('select.option', 'asc', JText::_('COM_KUNENA_FIELD_LABEL_ASCENDING'));
		$sortDirection[] = JHtml::_('select.option', 'desc', JText::_('COM_KUNENA_FIELD_LABEL_DESCENDING'));

		return $sortDirection;
	}
}
