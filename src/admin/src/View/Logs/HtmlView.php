<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Administrator
 * @subpackage    Views
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\View\Logs;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use ReflectionClass;

/**
 * Logs view for Kunena backend
 *
 * @since 5.0
 */
class HtmlView extends BaseHtmlView
{
    /**
     * @var  void
     *
     * @since   Kunena 6.0
     */
    protected $group;

    /**
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function displayClean(): void
    {
        $this->setToolBarClean();
        $this->display();
    }

    /**
     * @return  void
     *
     * @since   Kunena 6.0
     */
    protected function setToolbarClean(): void
    {
        // Set the title bar text
        ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_LOG_MANAGER'), 'users');
        ToolbarHelper::custom('logs.clean', 'delete.png', 'delete_f2.png', 'COM_KUNENA_CLEAN_LOGS_ENTRIES', false);
        ToolbarHelper::cancel();
    }

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
        $model               = $this->getModel();
        $this->state         = $model->getstate();
        $this->group         = $this->state->get('group');
        $this->items         = $model->getItems();
        $this->pagination    = $model->getPagination();
        $this->filterForm    = $model->getFilterForm();
        $this->activeFilters = $model->getActiveFilters();

        $document = Factory::getApplication()->getDocument();
        $document->setTitle(Text::_('Forum Logs'));

        $this->addToolbar();

        return parent::display($tpl);
    }
    
    /**
     * Get the log type
     * 
     * @param   integer  $id  id
     *
     * @return string
     *
     * @since   Kunena 6.0
     *
     */
    public function getType(int $id): string
    {
        static $types = [1 => 'MOD', 2 => 'ACT', 3 => 'ERR', 4 => 'REP'];
        
        return isset($types[$id]) ? $types[$id] : '???';
    }
    

    /**
     * Set the toolbar on log manager
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
        ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_LOG_MANAGER'), 'users');
        $toolbar->popupButton('cleanentries', 'COM_KUNENA_LOG_CLEAN_ENTRIES')
            ->selector('joomla-dialog-clean')
            ->listCheck(false);
        // $toolbar->popupButton('cleanentries', 'COM_KUNENA_LOG_CLEAN_ENTRIES')
        //     ->popupType('inline')
        //     ->textHeader(Text::_('COM_KUNENA_LOG_CLEAN_ENTRIES'))
        //     ->url('#joomla-dialog-clean')
        //     ->modalWidth('800px')
        //     ->modalHeight('fit-content');

        $canDo = ContentHelper::getActions('com_kunena');

        if ($canDo->get('core.admin') || $canDo->get('core.options')) {
            ToolBarHelper::preferences('com_kunena');
        }

        $helpUrl = 'https://docs.kunena.org/en/manual/backend/users';
        ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
    }
}
