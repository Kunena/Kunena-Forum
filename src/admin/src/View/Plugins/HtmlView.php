<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_plugins
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Kunena\Forum\Administrator\View\Plugins;

\defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * View class for a list of plugins.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_plugins
 *
 * @since       K1.5
 */
class HtmlView extends BaseHtmlView
{
    /**
     * Form object for search filters
     *
     * @var    Form
     * @since  4.0.0
     */
    public $filterForm;

    /**
     * The active search filters
     *
     * @var    array
     * @since  4.0.0
     */
    public $activeFilters;

    /**
     * An array of items
     *
     * @var  array
     * @since  4.0.0
     */
    protected $items;

    /**
     * The pagination object
     *
     * @var    Pagination
     * @since  4.0.0
     */
    protected $pagination;

    /**
     * The model state
     *
     * @var    CMSObject
     * @since  6.0
     */
    protected $state;

    /**
     * Display the view
     *
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
        $this->items         = $this->get('Items');
        $this->state         = $this->get('state');
        $this->pagination    = $this->get('Pagination');
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

        $lang = Factory::getApplication()->getLanguage();

        foreach ($this->items as $item) {
            $source    = JPATH_PLUGINS . '/' . $item->folder . '/' . $item->element;
            $extension = 'plg_' . $item->folder . '_' . $item->element;
            $lang->load($extension . '.sys', JPATH_ADMINISTRATOR, null, false, false)
                || $lang->load($extension . '.sys', $source, null, false, false)
                || $lang->load($extension . '.sys', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
                || $lang->load($extension . '.sys', $source, $lang->getDefault(), false, false);
            $item->name = Text::_($item->name);
        }

        $this->user = Factory::getApplication()->getIdentity();

        // Check for errors.
        if (\count($errors = $this->get('Errors'))) {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        $this->addToolbar();

        return parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void The HTML code for the select tag
     *
     * @since   K1.6
     */
    protected function addToolbar(): void
    {
        // Get the toolbar object instance
        $toolbar = $this->getDocument()->getToolbar();

        // Set the title bar text
        ToolbarHelper::title(Text::_('COM_KUNENA') . ': ' . Text::_('COM_KUNENA_PLUGIN_MANAGER'), 'puzzle');

        $canDo   = ContentHelper::getActions('com_plugins');
        $toolbar = $this->getDocument()->getToolbar();

        ToolbarHelper::title(Text::_('COM_PLUGINS_MANAGER_PLUGINS'), 'plug plugin');

        if ($canDo->get('core.edit.state')) {
            $toolbar->publish('plugins.publish', 'JTOOLBAR_ENABLE')->listCheck(true);
            $toolbar->unpublish('plugins.unpublish', 'JTOOLBAR_DISABLE')->listCheck(true);
            $toolbar->checkin('plugins.checkin');
        }

        $helpUrl = 'https://docs.kunena.org/en/manual/backend/plugins';
        ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
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
    public function publishedOptions(): array
    {
        // Build the active state filter options.
        $options   = [];
        $options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_FIELD_LABEL_ON'));
        $options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FIELD_LABEL_OFF'));

        return $options;
    }
}
