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

namespace Kunena\Forum\Administrator\View\Mailsqueues;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * Mailsqueues list view for Kunena backend
 *
 * @since   Kunena 7.1
 */
class HtmlView extends BaseHtmlView
{
    /**
     * @var mixed
     * @since   Kunena 7.1
     */
    protected $pagination;

    /**
     * @var mixed
     * @since   Kunena 7.1
     */
    protected $state;

    /**
     * @param   null  $tpl  tpl
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 7.1
     */
    public function display($tpl = null)
    {
        $model               = $this->getModel();
        $this->items         = $model->getItems();
        $this->state         = $model->getState();
        $this->pagination    = $model->getPagination();
        $this->filterForm    = $model->getFilterForm();
        $this->activeFilters = $model->getActiveFilters();

        $this->addToolbar();

        return parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     *
     * @since   Kunena 7.1
     */
    protected function addToolbar(): void
    {
        // Get the toolbar object instance
        $toolbar = $this->getDocument()->getToolbar();

        // Set the title bar text
        ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_ADMIN_MAILSQUEUE_MANAGER'), 'envelope');

        ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'mailsqueues.remove');

        $toolbar->popupButton('purge', 'COM_KUNENA_ADMIN_MAILSQUEUE_PURGE')
            ->selector('joomla-dialog-purge')
            ->listCheck(false);

        $canDo = ContentHelper::getActions('com_kunena');

        if ($canDo->get('core.admin') || $canDo->get('core.options')) {
            ToolBarHelper::preferences('com_kunena');
        }
    }
}
