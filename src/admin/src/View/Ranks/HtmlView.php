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

namespace Kunena\Forum\Administrator\View\Ranks;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\HTML\Helpers\Sidebar;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Kunena\Forum\Libraries\Template\KunenaTemplate;

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
    protected $pagination;

    /**
     * @var mixed
     * @since version
     */
    protected $state;

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
        $model               = $this->getModel();
        $this->items         = $model->getItems();
        $this->state         = $model->getState();
        $this->pagination    = $model->getPagination();
        $this->filterForm    = $model->getFilterForm();
        $this->activeFilters = $model->getActiveFilters();
        $this->ktemplate     = KunenaTemplate::getInstance();

        // Check for errors.
        if (\count($errors = $model->getErrors())) {
            throw new Exception(implode("\n", $errors));
        }

        $this->addToolbar();

        $this->sidebar = Sidebar::render();

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

        ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_RANK_MANAGER'), 'star-2');

        $toolbar->addNew('rank.add', 'COM_KUNENA_NEW_RANK');
        ToolbarHelper::editList('ranks.edit');
        ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'ranks.remove');

        $canDo = ContentHelper::getActions('com_kunena');

        if ($canDo->get('core.admin') || $canDo->get('core.options')) {
            ToolBarHelper::preferences('com_kunena');
        }

        $helpUrl = 'https://docs.kunena.org/en/manual/backend/ranks/add-rank';
        ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
    }
}
