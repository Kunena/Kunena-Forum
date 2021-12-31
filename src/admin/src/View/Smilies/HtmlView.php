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

namespace Kunena\Forum\Administrator\View\Smilies;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Kunena\Forum\Libraries\Template\KunenaTemplate;

/**
 * About view for Kunena smilies backend
 *
 * @since   Kunena 1.X
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * @var mixed
	 * @since version
	 */
	protected $pagination;

	/**
	 * @var mixed
	 * @since version
	 */
	protected $state;

	/**
	 * @var mixed
	 * @since version
	 */
	protected $filterActive;

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
		$this->state      = $this->get('State');
		$this->pagination = $this->get('Pagination');
		$this->ktemplate  = KunenaTemplate::getInstance();

		$this->sortFields          = $this->getSortFields();
		$this->sortDirectionFields = $this->getSortDirectionFields();

		$this->filter           = new \stdClass;
		$this->filter->Search   = $this->escape($this->state->get('filter.search'));
		$this->filter->Code     = $this->escape($this->state->get('filter.code'));
		$this->filter->Active   = $this->escape($this->state->get('filter.active'));
		$this->filter->Location = $this->escape($this->state->get('filter.location'));

		$this->list            = new \stdClass;
		$this->list->Ordering  = $this->escape($this->state->get('list.ordering'));
		$this->list->Direction = $this->escape($this->state->get('list.direction'));

		$this->addToolbar();

		return parent::display($tpl);
	}

	/**
	 * @return  array
	 *
	 * @since   Kunena 6.0
	 */
	protected function getSortFields(): array
	{
		$sortFields   = [];
		$sortFields[] = HTMLHelper::_('select.option', 'code', Text::_('COM_KUNENA_EMOTICONS_CODE'));
		$sortFields[] = HTMLHelper::_('select.option', 'location', Text::_('COM_KUNENA_EMOTICONS_URL'));
		$sortFields[] = HTMLHelper::_('select.option', 'id', Text::_('COM_KUNENA_EMOTICONS_FIELD_LABEL_ID'));

		return $sortFields;
	}

	/**
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
		$this->filterActive = $this->escape($this->state->get('filter.active'));
		$this->pagination   = $this->get('Pagination');

		ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_EMOTICON_MANAGER'), 'thumbs-up');
		ToolbarHelper::spacer();
		ToolbarHelper::addNew('smilies.add', 'COM_KUNENA_NEW_SMILIE');
		ToolbarHelper::editList('smilies.edit');
		ToolbarHelper::divider();
		ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'smilies.remove');
		ToolbarHelper::spacer();
		$helpUrl = 'https://docs.kunena.org/en/manual/backend/emoticons/new-emoticon';
		ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
	}
}
