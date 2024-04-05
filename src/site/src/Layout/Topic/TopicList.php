<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.Topic
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\Topic;

\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Layout\KunenaLayout;

/**
 * KunenaLayoutTopicList
 *
 * @since   Kunena 4.0
 */
class TopicList extends KunenaLayout
{
    public $output;

    public $user;

    public $headerText;

    public $pagination;

    public $config;

    public $me;

    public $topics;

    public $allowed;

    public $cache;

    public $catParams;

    public $categorylist;

    public $topic;

    public $state;

    public $embedded;

    public $actions;

    public $ktemplate;

    public $moreUri;

    public $page;

    public $model;

    public $access;

    public $params;

    public $mesIds;

    /**
     * Method to return HTML select list for time filter
     *
     * @param   int|string  $id      Id of the HTML select list
     * @param   string      $attrib  Extra attribute to apply to the list
     *
     * @return  void
     *
     * @since   Kunena 6.0
     */
    public function displayTimeFilter($id = 'filter-time', $attrib = 'class="form-select filter" onchange="this.form.submit()"')
    {
        if (!isset($this->state)) {
            return;
        }

        // Make the select list for time selection
        $timesel[] = HTMLHelper::_('select.option', -1, Text::_('COM_KUNENA_SHOW_ALL'));
        $timesel[] = HTMLHelper::_('select.option', 0, Text::_('COM_KUNENA_SHOW_LASTVISIT'));
        $timesel[] = HTMLHelper::_('select.option', 4, Text::_('COM_KUNENA_SHOW_4_HOURS'));
        $timesel[] = HTMLHelper::_('select.option', 8, Text::_('COM_KUNENA_SHOW_8_HOURS'));
        $timesel[] = HTMLHelper::_('select.option', 12, Text::_('COM_KUNENA_SHOW_12_HOURS'));
        $timesel[] = HTMLHelper::_('select.option', 24, Text::_('COM_KUNENA_SHOW_24_HOURS'));
        $timesel[] = HTMLHelper::_('select.option', 48, Text::_('COM_KUNENA_SHOW_48_HOURS'));
        $timesel[] = HTMLHelper::_('select.option', 168, Text::_('COM_KUNENA_SHOW_WEEK'));
        $timesel[] = HTMLHelper::_('select.option', 720, Text::_('COM_KUNENA_SHOW_MONTH'));
        $timesel[] = HTMLHelper::_('select.option', 8760, Text::_('COM_KUNENA_SHOW_YEAR'));

        echo HTMLHelper::_('select.genericlist', $timesel, 'sel', $attrib, 'value', 'text', $this->state->get('list.time'), $id);
    }
}
