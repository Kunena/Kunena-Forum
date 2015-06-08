<?php
/**
 * Kunena Component
 *
 * @package       Kunena.Administrator
 * @subpackage    Views
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          http://www.kunena.org
 **/
defined('_JEXEC') or die ();

/**
 * About view for Kunena smilies backend
 */
class KunenaAdminViewSmilies extends KunenaView
{
	function display($tpl = null)
	{
		$this->setToolbar();
		$this->items      = $this->get('Items');
		$this->state      = $this->get('State');
		$this->pagination = $this->get('Pagination');

		$this->sortFields          = $this->getSortFields();
		$this->sortDirectionFields = $this->getSortDirectionFields();

		$this->filterSearch   = $this->escape($this->state->get('filter.search'));
		$this->filterCode     = $this->escape($this->state->get('filter.code'));
		$this->filterLocation = $this->escape($this->state->get('filter.location'));
		$this->filterActive   = $this->escape($this->state->get('filter.active'));
		$this->listOrdering   = $this->escape($this->state->get('list.ordering'));
		$this->listDirection  = $this->escape($this->state->get('list.direction'));

		return parent::display($tpl);
	}

	protected function setToolbar()
	{
		$this->filterActive = $this->escape($this->state->get('filter.active'));
		$this->pagination   = $this->get('Pagination');

		if (version_compare(JVERSION, '3', '>'))
		{
			JToolBarHelper::title(JText::_('COM_KUNENA') . ': ' . JText::_('COM_KUNENA_EMOTICON_MANAGER'), 'thumbs-up');
		}
		else
		{
			JToolBarHelper::title(JText::_('COM_KUNENA') . ': ' . JText::_('COM_KUNENA_EMOTICON_MANAGER'), 'smilies');
		}

		JToolBarHelper::spacer();
		JToolBarHelper::addNew('add', 'COM_KUNENA_NEW_SMILIE');

		//TODO: Implement flag to hide options, personal preference option.
		//if($this->filterActive || $this->pagination->total > 0) {
		JToolBarHelper::editList();
		JToolBarHelper::divider();
		JToolBarHelper::deleteList();
		//}
		JToolBarHelper::spacer();
		$help_url  = 'http://www.kunena.org/docs/Smiley_management';
		JToolBarHelper::help( 'COM_KUNENA', false, $help_url );
	}

	protected function getSortFields()
	{
		$sortFields   = array();
		$sortFields[] = JHtml::_('select.option', 'code', JText::_('COM_KUNENA_EMOTICONS_CODE'));
		$sortFields[] = JHtml::_('select.option', 'location', JText::_('COM_KUNENA_EMOTICONS_URL'));
		$sortFields[] = JHtml::_('select.option', 'id', JText::_('COM_KUNENA_EMOTICONS_FIELD_LABEL_ID'));

		return $sortFields;
	}

	protected function getSortDirectionFields()
	{
		$sortDirection = array();
		//$sortDirection[] = JHtml::_('select.option', 'asc', JText::_('JGLOBAL_ORDER_ASCENDING'));
		//$sortDirection[] = JHtml::_('select.option', 'desc', JText::_('JGLOBAL_ORDER_DESCENDING'));
		// TODO: remove it when J2.5 support is dropped
		$sortDirection[] = JHtml::_('select.option', 'asc', JText::_('COM_KUNENA_FIELD_LABEL_ASCENDING'));
		$sortDirection[] = JHtml::_('select.option', 'desc', JText::_('COM_KUNENA_FIELD_LABEL_DESCENDING'));

		return $sortDirection;
	}
}
