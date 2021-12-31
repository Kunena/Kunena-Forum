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
 * About view for Kunena smilies backend
 *
 * @since  K1.X
 */
class KunenaAdminViewSmilies extends KunenaView
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

	/**
	 * @since Kunena
	 */
	protected function setToolbar()
	{
		$this->filterActive = $this->escape($this->state->get('filter.active'));
		$this->pagination   = $this->get('Pagination');

		JToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_EMOTICON_MANAGER'), 'thumbs-up');
		JToolbarHelper::spacer();
		JToolbarHelper::addNew('add', 'COM_KUNENA_NEW_SMILIE');
		JToolbarHelper::editList();
		JToolbarHelper::divider();
		JToolbarHelper::deleteList();
		JToolbarHelper::spacer();
		$help_url = 'https://docs.kunena.org/en/manual/backend/emoticons/new-emoticon';
		JToolbarHelper::help('COM_KUNENA', false, $help_url);
	}

	/**
	 * @return array
	 * @since Kunena
	 */
	protected function getSortFields()
	{
		$sortFields   = array();
		$sortFields[] = HTMLHelper::_('select.option', 'code', Text::_('COM_KUNENA_EMOTICONS_CODE'));
		$sortFields[] = HTMLHelper::_('select.option', 'location', Text::_('COM_KUNENA_EMOTICONS_URL'));
		$sortFields[] = HTMLHelper::_('select.option', 'id', Text::_('COM_KUNENA_EMOTICONS_FIELD_LABEL_ID'));

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
