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

namespace Kunena\Forum\Administrator\View\Attachments;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * Attachments view for Kunena backend
 *
 * @since   Kunena 6.0
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * @param   null  $tpl  tpl
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	public function display($tpl = null)
	{
		$this->items      = $this->get('Items');
		$this->state      = $this->get('state');
		$this->pagination = $this->get('Pagination');

		$this->sortFields          = $this->getSortFields();
		$this->sortDirectionFields = $this->getSortDirectionFields();

		// Check for errors.
		if (\count($errors = $this->get('Errors')))
		{
			throw new GenericDataException(implode("\n", $errors), 500);
		}

		$this->filter             = new \stdClass;
		$this->filter->Search     = $this->escape($this->state->get('filter.search'));
		$this->filter->Username   = $this->escape($this->state->get('filter.username'));
		$this->filter->Title      = $this->escape($this->state->get('filter.title'));
		$this->filter->Type       = $this->escape($this->state->get('filter.type'));
		$this->filter->Size       = $this->escape($this->state->get('filter.size'));
		$this->filter->Dimensions = $this->escape($this->state->get('filter.dims'));
		$this->filter->Post       = $this->escape($this->state->get('filter.post'));
		$this->filter->Active     = $this->escape($this->state->get('filter.active'));

		$this->list            = new \stdClass;
		$this->list->Ordering  = $this->escape($this->state->get('list.ordering'));
		$this->list->Direction = $this->escape($this->state->get('list.direction'));

		$this->addToolbar();

		return parent::display($tpl);
	}

	/**
	 * Returns an array of review filter options.
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected function getSortFields(): array
	{
		$sortFields   = [];
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
	 *
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected function getSortDirectionFields(): array
	{
		$sortDirection   = [];
		$sortDirection[] = HTMLHelper::_('select.option', 'asc', Text::_('JGLOBAL_ORDER_ASCENDING'));
		$sortDirection[] = HTMLHelper::_('select.option', 'desc', Text::_('JGLOBAL_ORDER_DESCENDING'));

		return $sortDirection;
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function addToolbar(): void
	{
		$helpUrl = 'https://docs.kunena.org/en/manual/backend/attachments';
		ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
		ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_FILE_MANAGER'), 'folder-open');
		ToolbarHelper::spacer();
		ToolbarHelper::custom('attachments.delete', 'trash.png', 'trash_f2.png', 'COM_KUNENA_GEN_DELETE');

		ToolbarHelper::spacer();
	}
}
