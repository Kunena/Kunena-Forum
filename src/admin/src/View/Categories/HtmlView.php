<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Views
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\View\Categories;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Object\CMSObject;
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
        $model                 = $this->getModel();
        $this->categories      = $model->getAdminCategories();
        $this->pagination      = $model->getAdminNavigation();
        $this->state           = $model->getState();
        $this->pagesTotal      = 100;
        $this->batchCategories = $model->getBatchCategories();
        $this->filterForm      = $model->getFilterForm();
        $this->activeFilters   = $model->getActiveFilters();

        // Preprocess the list of items to find ordering divisions.
        $this->ordering = [];

        foreach ($this->categories as $item) {
            $this->ordering[$item->parentid][] = $item->id;
        }

        // Check for errors.
        if (\count($errors = $model->getErrors())) {
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
        $toolbar = $this->getDocument()->getToolbar();

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
            ->selector('joomla-dialog-batch')
            ->listCheck(true);

        $canDo = ContentHelper::getActions('com_kunena');

        if ($canDo->get('core.admin') || $canDo->get('core.options')) {
            ToolBarHelper::preferences('com_kunena');
        }

        $helpUrl = 'https://docs.kunena.org/en/setup/sections-categories';
        ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
    }   
}
