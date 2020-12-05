<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Views
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\View\Ranks;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Kunena\Forum\Libraries\Template\KunenaTemplate;
use function defined;

/**
 * About view for Kunena ranks backend
 *
 * @since   Kunena 1.X
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * @var mixed
	 * @since version
	 */
	private $pagination;
	private $state;
	/**
	 * @var mixed
	 * @since version
	 */
	private $filterActive;
	/**
	 * @var mixed
	 * @since version
	 */
	private $listDirection;
	/**
	 * @var mixed
	 * @since version
	 */
	private $listOrdering;
	/**
	 * @var mixed
	 * @since version
	 */
	private $filterActive;
	/**
	 * @var mixed
	 * @since version
	 */
	private $filterMinPostCount;
	/**
	 * @var mixed
	 * @since version
	 */
	private $filterSpecial;
	/**
	 * @var mixed
	 * @since version
	 */
	private $filterTitle;
	/**
	 * @var mixed
	 * @since version
	 */
	private $filterSearch;
	/**
	 * @var array
	 * @since version
	 */
	private $sortDirectionFields;
	/**
	 * @var array
	 * @since version
	 */
	private $sortFields;
	/**
	 * @var KunenaTemplate|\KunenaTemplateaurelia
	 * @since version
	 */
	private $ktemplate;
	/**
	 * @var mixed
	 * @since version
	 */
	private $pagination;
	/**
	 * @var mixed
	 * @since version
	 */
	private $state;
	/**
	 * @var mixed
	 * @since version
	 */
	private $items;

	/**
	 * Returns an array of standard published state filter options.
	 *
	 * @return  array  The HTML code for the select tag
	 *
	 * @since   Kunena 6.0
	 */
	public static function specialOptions(): array
	{
		// Build the active state filter options.
		$options   = [];
		$options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_FIELD_LABEL_YES'));
		$options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FIELD_LABEL_NO'));

		return $options;
	}

	/**
	 * @param   null  $tpl  tpl
	 *
	 * @return  mixed
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
		$this->ktemplate  = KunenaTemplate::getInstance();

		$this->sortFields          = $this->getSortFields();
		$this->sortDirectionFields = $this->getSortDirectionFields();

		$this->filterSearch       = $this->escape($this->state->get('filter.search'));
		$this->filterTitle        = $this->escape($this->state->get('filter.title'));
		$this->filterSpecial      = $this->escape($this->state->get('filter.special'));
		$this->filterMinPostCount = $this->escape($this->state->get('filter.min'));
		$this->filterActive       = $this->escape($this->state->get('filter.active'));
		$this->listOrdering       = $this->escape($this->state->get('list.ordering'));
		$this->listDirection      = $this->escape($this->state->get('list.direction'));

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
		$sortFields[] = HTMLHelper::_('select.option', 'title', Text::_('JGLOBAL_TITLE'));
		$sortFields[] = HTMLHelper::_('select.option', 'special', Text::_('COM_KUNENA_RANKS_SPECIAL'));
		$sortFields[] = HTMLHelper::_('select.option', 'min', Text::_('COM_KUNENA_RANKSMIN'));
		$sortFields[] = HTMLHelper::_('select.option', 'id', Text::_('JGRID_HEADING_ID'));

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

		ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_RANK_MANAGER'), 'star-2');

		ToolbarHelper::spacer();
		ToolbarHelper::addNew('add', 'COM_KUNENA_NEW_RANK');
		ToolbarHelper::editList();
		ToolbarHelper::divider();
		ToolbarHelper::deleteList();
		ToolbarHelper::spacer();
		$help_url = 'https://docs.kunena.org/en/manual/backend/ranks/add-rank';
		ToolbarHelper::help('COM_KUNENA', false, $help_url);
	}
}
