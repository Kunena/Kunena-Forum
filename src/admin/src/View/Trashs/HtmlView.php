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

namespace Kunena\Forum\Administrator\View\Trashs;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * Trash view for Kunena backend
 *
 * @since  K1.0
 */
class HtmlView extends BaseHtmlView
{
    /**
     * Filter search
     *
     * @since  6.0
     */
    public $filterSearch;

    /**
     * Filter title
     *
     * @since  6.0
     */
    public $filterTitle;

    /**
     * Filter topic
     *
     * @since  6.0
     */
    public $filterTopic;

    /**
     * Filter category
     *
     * @since  6.0
     */
    public $filterCategory;

    /**
     * Filter ip
     *
     * @since  6.0
     */
    public $filterIp;

    /**
     * Filter author
     *
     * @since  6.0
     */
    public $filterAuthor;

    /**
     * Filter active
     *
     * @since  6.0
     */
    public $filterActive;

    /**
     * The model state
     *
     * @var    CMSObject
     * @since  6.0
     */
    protected $state;

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
        $this->state         = $this->get('State');
        $this->items         = $this->get('Items');
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        $this->pagination    = $this->get('Pagination');

        $layout = $this->getLayout() == 'default' ? 'messages' : $this->getLayout();
        $this->setLayout($layout);

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
        ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_TRASH_MANAGER'), 'trash');

        $toggleLayout = $this->getLayout() == 'messages' ? 'topics' : 'messages';
        $buttonText   = $toggleLayout == 'messages' ? 'COM_KUNENA_TRASH_DISPLAYMESSAGES' : 'COM_KUNENA_TRASH_DISPLAYTOPICS';
        $link         = Route::_('index.php?option=com_kunena&view=trashs&layout=' . $toggleLayout, false);
        $toolbar->link(Text::_($buttonText), $link)
            ->icon('icon-refresh');

        /** @var  DropdownButton $dropdown */
        $dropdown = $toolbar->dropdownButton('status-group')
            ->text('JTOOLBAR_CHANGE_STATUS')
            ->toggleSplit(false)
            ->icon('icon-ellipsis-h')
            ->buttonClass('btn btn-action')
            ->listCheck(true);

        $childBar = $dropdown->getChildToolbar();
        $childBar->confirmButton('trash', 'COM_KUNENA_TRASH_PURGE', 'trashs.purge')
            ->message('COM_KUNENA_PERM_DELETE_ITEMS')
            ->icon('icon-trash')
            ->listCheck(true);
        $childBar->confirmButton('restore', 'COM_KUNENA_TRASH_RESTORE', 'trashs.restore')
            ->message('COM_KUNENA_TRASH_RESTORE_ITEMS')
            ->icon('icon-checkin')
            ->listCheck(true);

        $helpUrl = 'https://docs.kunena.org/en/manual/backend/trashbin';
        ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
    }
}
