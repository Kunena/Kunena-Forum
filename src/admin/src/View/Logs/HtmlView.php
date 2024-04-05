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
        $this->state         = $this->get('state');
        $this->group         = $this->state->get('group');
        $this->items         = $this->get('items');
        $this->pagination    = $this->get('Pagination');
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

        $document = Factory::getApplication()->getDocument();
        $document->setTitle(Text::_('Forum Logs'));

        $this->addToolbar();

        return parent::display($tpl);
    }

    /**
     * @return  array
     *
     * @since   Kunena 6.0
     * 
     * @deprecated Kunena 6.3 will be removed in Kunena 7.0 without replacement
     */
    protected function getFilterTypeFields(): array
    {
        $filterFields   = [];
        $filterFields[] = HTMLHelper::_('select.option', 1, 'MOD');
        $filterFields[] = HTMLHelper::_('select.option', 2, 'ACT');
        $filterFields[] = HTMLHelper::_('select.option', 3, 'ERR');
        $filterFields[] = HTMLHelper::_('select.option', 4, 'REP');

        return $filterFields;
    }

    /**
     * @return  array
     *
     * @since   Kunena 6.0
     * 
     * @deprecated Kunena 6.3 will be removed in Kunena 7.0 without replacement
     */
    protected function getFilterOperationFields(): array
    {
        $filterFields = [];

        $reflection = new ReflectionClass('Kunena\Forum\Libraries\Log\KunenaLog');

        $constants = $reflection->getConstants();
        ksort($constants);

        foreach ($constants as $key => $value) {
            if (strpos($key, 'LOG_') === 0) {
                $filterFields[] = HTMLHelper::_('select.option', $key, Text::_("COM_KUNENA_{$value}"));
            }
        }

        return $filterFields;
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
        $toolbar = Toolbar::getInstance();

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

        $helpUrl = 'https://docs.kunena.org/en/manual/backend/users';
        ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
    }

    /**
     * @param   integer  $id  id
     *
     * @return string
     *
     * @since   Kunena 6.0
     * 
     * @deprecated Kunena 6.3 will be removed in Kunena 7.0 without replacement
     */
    public function getType(int $id): string
    {
        static $types = [1 => 'MOD', 2 => 'ACT', 3 => 'ERR', 4 => 'REP'];

        return isset($types[$id]) ? $types[$id] : '???';
    }

    /**
     * @param   string  $name  name
     *
     * @return  string
     *
     * @since   Kunena 6.0
     * 
     * @deprecated Kunena 6.3 will be removed in Kunena 7.0 without replacement
     */
    public function getGroupCheckbox(string $name): string
    {
        $checked = isset($this->group[$name]) ? ' checked="checked"' : '';

        return '<input type="checkbox" name="group_' . $name . '" value="1" title="Group By" ' . $checked . ' class="filter form-control" />';
    }
}
