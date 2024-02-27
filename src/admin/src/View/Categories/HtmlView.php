<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Views
 *
 * @copyright       Copyright (C) 2008 - 2024 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\View\Categories;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * About view for Kunena backend
 *
 * @since   Kunena 6.0
 */
class HtmlView extends BaseHtmlView
{
    /**
     * @var     array|KunenaCategory[]
     * @since   Kunena 6.0
     */
    public $categories = [];

    public $sortFields;

    public $ordering;

    public $saveOrder;

    /**
     * @var     array|KunenaCategory[]
     * @since   Kunena 6.0
     */
    public $batchCategories;

    /**
     * The model state
     *
     * @var    CMSObject
     * @since  Kunena 6.0
     */
    protected $state;

    /**
     * @var mixed
     * @since version
     */
    protected $pagination;

    /**
     * @param   null  $tpl  tpl
     *
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws Exception
     */
    public function display($tpl = null)
    {
        $this->categories      = $this->get('AdminCategories');
        $this->pagination      = $this->get('AdminNavigation');
        $this->state           = $this->get('State');
        $this->pagesTotal      = 100;
        $this->batchCategories = $this->get('BatchCategories');
        $this->filterForm      = $this->get('FilterForm');
        $this->activeFilters   = $this->get('ActiveFilters');

        // Preprocess the list of items to find ordering divisions.
        $this->ordering = [];

        foreach ($this->categories as $item) {
            $this->ordering[$item->parentid][] = $item->id;
        }

        // Check for errors.
        if (\count($errors = $this->get('Errors'))) {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        $this->user = Factory::getApplication()->getIdentity();
        $this->me   = KunenaUserHelper::getMyself();

        $this->addToolbar();

        return parent::display($tpl);
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
        // Get the toolbar object instance
        $toolbar = Toolbar::getInstance();

        // Set the title bar text
        ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_CATEGORY_MANAGER'), 'list-view');

        $toolbar->addNew('categories.add', 'COM_KUNENA_NEW_CATEGORY');
        $toolbar->edit('categories.edit')->listCheck(true);

        /** @var  DropdownButton $dropdown */
        $dropdown = $toolbar->dropdownButton('status-group')
            ->text('JTOOLBAR_CHANGE_STATUS')
            ->toggleSplit(false)
            ->icon('icon-ellipsis-h')
            ->buttonClass('btn btn-action')
            ->listCheck(true);

        $childBar = $dropdown->getChildToolbar();
        $childBar->publish('categories.publish')->listCheck(true);
        $childBar->unpublish('categories.unpublish')->listCheck(true);
        $childBar->delete('categories.delete', 'COM_KUNENA_CATEGORY_TOOLBAR_DELETE_CATEGORY')
            ->message('COM_KUNENA_CATEGORIES_CONFIRM_DELETE_BODY_MODAL')
            ->listCheck(true);
        $childBar->popupButton('batch', 'JTOOLBAR_BATCH')
            ->popupType('inline')
            ->textHeader(Text::_('JTOOLBAR_BATCH'))
            ->url('#joomla-dialog-batch')
            ->modalWidth('800px')
            ->modalHeight('fit-content')
            ->listCheck(true);

        $helpUrl = 'https://docs.kunena.org/en/setup/sections-categories';
        ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
    }

    /**
     * Returns an array of standard published state filter options.
     *
     * @return  array The HTML code for the select tag
     *
     * @since   Kunena 6.0
     * 
     * @deprecated Kunena 6.3 will be removed in Kunena 7.0 without replacement
     */
    public function publishedOptions(): array
    {
        // Build the active state filter options.
        $options   = [];
        $options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_FIELD_LABEL_ON'));
        $options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FIELD_LABEL_OFF'));

        return $options;
    }

    /**
     * Returns an array of locked filter options.
     *
     * @return  array  The HTML code for the select tag
     *
     * @since   Kunena 6.0
     * 
     * @deprecated Kunena 6.3 will be removed in Kunena 7.0 without replacement
     */
    public function lockOptions(): array
    {
        // Build the active state filter options.
        $options   = [];
        $options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_FIELD_LABEL_ON'));
        $options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FIELD_LABEL_OFF'));

        return $options;
    }

    /**
     * Returns an array of review filter options.
     *
     * @return  array The HTML code for the select tag
     *
     * @since   Kunena 6.0
     * 
     * @deprecated Kunena 6.3 will be removed in Kunena 7.0 without replacement
     */
    public function reviewOptions(): array
    {
        // Build the active state filter options.
        $options   = [];
        $options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_FIELD_LABEL_ON'));
        $options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FIELD_LABEL_OFF'));

        return $options;
    }

    /**
     * Returns an array of review filter options.
     *
     * @return  array
     *
     * @since   Kunena 6.0
     * 
     * @deprecated Kunena 6.3 will be removed in Kunena 7.0 without replacement
     */
    public function allowPollsOptions(): array
    {
        // Build the active state filter options.
        $options   = [];
        $options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_FIELD_LABEL_ON'));
        $options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FIELD_LABEL_OFF'));

        return $options;
    }

    /**
     * Returns an array of type filter options.
     *
     * @return  array  The HTML code for the select tag
     * @since   Kunena 6.0
     * 
     * @deprecated Kunena 6.3 will be removed in Kunena 7.0 without replacement
     */
    public function anonymousOptions(): array
    {
        // Build the active state filter options.
        $options   = [];
        $options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_FIELD_LABEL_ON'));
        $options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FIELD_LABEL_OFF'));

        return $options;
    }
}
