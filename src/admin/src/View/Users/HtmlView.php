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

namespace Kunena\Forum\Administrator\View\Users;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * Users view for Kunena backend
 *
 * @since   Kunena 1.X
 */
class HtmlView extends BaseHtmlView
{
    /**
     * The model state
     *
     * @var    CMSObject
     * @since  6.0
     */
    protected $state;

    /**
     * DisplayDefault
     *
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
        $this->users         = $this->get('items');
        $this->state         = $this->get('State');
        $this->pagination    = $this->get('Pagination');
        $this->modCatList    = $this->get('ModcatsList');
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

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
        ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_USER_MANAGER'), 'users');
        ToolbarHelper::editList('users.edit');

        $toolbar->standardButton(
            'cancel',
            'COM_KUNENA_LOGOUT',
            'users.logout'
        )
            ->listCheck(true);

        /** @var  DropdownButton $dropdown */
        $dropdown = $toolbar->dropdownButton('status-group')
            ->text('JTOOLBAR_CHANGE_STATUS')
            ->toggleSplit(false)
            ->icon('icon-ellipsis-h')
            ->buttonClass('btn btn-action')
            ->listCheck(true);

        $childBar = $dropdown->getChildToolbar();
        $childBar->standardButton('move', 'COM_KUNENA_MOVE_USERMESSAGES', 'users.move')
            ->listCheck(true);
        $childBar->standardButton('trash', 'COM_KUNENA_TRASH_USERMESSAGES', 'users.trashusermessages')
            ->listCheck(true);
        $childBar->standardButton('delete', 'JTOOLBAR_DELETE', 'users.remove')
            ->listCheck(true);
        $childBar->standardButton('delete', 'COM_KUNENA_REMOVE_CATSUBSCRIPTIONS', 'users.removecatsubscriptions')
            ->listCheck(true);
        $childBar->standardButton('delete', 'COM_KUNENA_REMOVE_TOPICSUBSCRIPTIONS', 'users.removetopicsubscriptions')
            ->listCheck(true);

        $childBar->popupButton('batch', 'COM_KUNENA_VIEW_USERS_TOOLBAR_ASSIGN_MODERATORS')
            ->selector('joomla-dialog-moderators')
            ->listCheck(true);
        // $childBar->popupButton('batch', 'COM_KUNENA_VIEW_USERS_TOOLBAR_ASSIGN_MODERATORS')
        //     ->popupType('inline')
        //     ->textHeader(Text::_('COM_KUNENA_VIEW_USERS_TOOLBAR_ASSIGN_MODERATORS'))
        //     ->url('#joomla-dialog-moderators')
        //     ->modalWidth('800px')
        //     ->modalHeight('fit-content')
        //     ->listCheck(true);

        $childBar->popupButton('batch', 'COM_KUNENA_VIEW_USERS_TOOLBAR_SUBSCRIBE_USERS_CATEGORIES')
            ->selector('joomla-dialog-subscribecatsusers')
            ->listCheck(true);
        //    $childBar->popupButton('batch', 'COM_KUNENA_VIEW_USERS_TOOLBAR_SUBSCRIBE_USERS_CATEGORIES')
        //         ->popupType('inline')
        //         ->textHeader(Text::_('COM_KUNENA_VIEW_USERS_TOOLBAR_SUBSCRIBE_USERS_CATEGORIES'))
        //         ->url('#joomla-dialog-subscribecatsusers')
        //         ->modalWidth('800px')
        //         ->modalHeight('fit-content')
        //         ->listCheck(true);

        $helpUrl = 'https://docs.kunena.org/en/manual/backend/users';
        ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
    }

    /**
     * Returns an array of locked filter options.
     *
     * @return  array    The HTML code for the select tag
     *
     * @since   Kunena 6.0
     * 
     * @deprecated Kunena 6.3 will be removed in Kunena 7.0 without replacement
     */
    public function signatureOptions(): array
    {
        // Build the active state filter options.
        $options   = [];
        $options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_FIELD_LABEL_YES'));
        $options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FIELD_LABEL_NO'));

        return $options;
    }

    /**
     * Returns an array of standard published state filter options.
     *
     * @return  array    The HTML code for the select tag
     *
     * @since   Kunena 6.0
     * 
     * @deprecated Kunena 6.3 will be removed in Kunena 7.0 without replacement
     */
    public function blockOptions(): array
    {
        // Build the active state filter options.
        $options   = [];
        $options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FIELD_LABEL_ON'));
        $options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_FIELD_LABEL_OFF'));

        return $options;
    }

    /**
     * Returns an array of type filter options.
     *
     * @return  array    The HTML code for the select tag
     *
     * @since   Kunena 6.0
     * 
     * @deprecated Kunena 6.3 will be removed in Kunena 7.0 without replacement
     */
    public function bannedOptions(): array
    {
        // Build the active state filter options.
        $options   = [];
        $options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_FIELD_LABEL_ON'));
        $options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FIELD_LABEL_OFF'));

        return $options;
    }

    /**
     * Returns an array of standard published state filter options.
     *
     * @return  array   The HTML code for the select tag
     *
     * @since   Kunena 6.0
     * 
     * @deprecated Kunena 6.3 will be removed in Kunena 7.0 without replacement
     */
    public function moderatorOptions(): array
    {
        // Build the active state filter options.
        $options   = [];
        $options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_FIELD_LABEL_YES'));
        $options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FIELD_LABEL_NO'));

        return $options;
    }

    /**
     * Returns an array ranks filter options.
     *
     * @return  array    The HTML code for the select tag
     *
     * @since   Kunena 6.0
     * 
     * @deprecated Kunena 6.3 will be removed in Kunena 7.0 without replacement
     */
    public function ranksOptions(): array
    {
        // Build the active state filter options.
        $options   = [];
        $options[] = HTMLHelper::_('select.option', 'Administrator', Text::_('Administrator'));
        $options[] = HTMLHelper::_('select.option', 'New Member', Text::_('New Member'));

        return $options;
    }
}
