<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.Search
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\Search;

\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Layout\KunenaLayout;

/**
 * KunenaLayoutSearchForm
 *
 * @since   Kunena 4.0
 */
class SearchForm extends KunenaLayout
{
    /**
     * @var     object
     * @since   Kunena 6.0
     */
    public $state;

    /**
     * Method to display the list to choose between posts or titles
     *
     * @param   string  $id          Id of the HTML select list
     * @param   string  $attributes  Extras attributes to apply to the list
     *
     * @return  void
     *
     * @since   Kunena 6.0
     */
    public function displayModeList(string $id, string $attributes = 'class="form-select mb-3"')
    {
        $options   = [];
        $options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_SEARCH_SEARCH_POSTS'));
        $options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_SEARCH_SEARCH_TITLES'));

        echo HTMLHelper::_('select.genericlist', $options, 'titleonly', $attributes, 'value', 'text', $this->state->get('query.titleonly'), $id);
    }

    /**
     * Method to get the date list
     *
     * @param   string  $id          Id of the HTML select list
     * @param   string  $attributes  Extras attributes to apply to the list
     *
     * @return  void
     *
     * @since   Kunena 6.0
     */
    public function displayDateList(string $id, string $attributes = 'class="form-select mb-3"')
    {
        $options   = [];
        $options[] = HTMLHelper::_('select.option', 'lastvisit', Text::_('COM_KUNENA_SEARCH_DATE_LASTVISIT'));
        $options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_SEARCH_DATE_YESTERDAY'));
        $options[] = HTMLHelper::_('select.option', '7', Text::_('COM_KUNENA_SEARCH_DATE_WEEK'));
        $options[] = HTMLHelper::_('select.option', '14', Text::_('COM_KUNENA_SEARCH_DATE_2WEEKS'));
        $options[] = HTMLHelper::_('select.option', '30', Text::_('COM_KUNENA_SEARCH_DATE_MONTH'));
        $options[] = HTMLHelper::_('select.option', '90', Text::_('COM_KUNENA_SEARCH_DATE_3MONTHS'));
        $options[] = HTMLHelper::_('select.option', '180', Text::_('COM_KUNENA_SEARCH_DATE_6MONTHS'));
        $options[] = HTMLHelper::_('select.option', '365', Text::_('COM_KUNENA_SEARCH_DATE_YEAR'));
        $options[] = HTMLHelper::_('select.option', 'all', Text::_('COM_KUNENA_SEARCH_DATE_ANY'));

        echo HTMLHelper::_('select.genericlist', $options, 'searchdate', $attributes, 'value', 'text', $this->state->get('query.searchdate'), $id);
    }

    /**
     * Method to display list to choose into the new dates or the older dates
     *
     * @param   string  $id          Id of the HTML select list
     * @param   string  $attributes  Extras attributes to apply to the list
     *
     * @return  void
     *
     * @since   Kunena 6.0
     */
    public function displayBeforeAfterList(string $id, string $attributes = 'class="form-select mb-3"')
    {
        $options   = [];
        $options[] = HTMLHelper::_('select.option', 'after', Text::_('COM_KUNENA_SEARCH_DATE_NEWER'));
        $options[] = HTMLHelper::_('select.option', 'before', Text::_('COM_KUNENA_SEARCH_DATE_OLDER'));

        echo HTMLHelper::_('select.genericlist', $options, 'beforeafter', $attributes, 'value', 'text', $this->state->get('query.beforeafter'), $id);
    }

    /**
     * Method to display list to choose how to sort the results
     *
     * @param   string  $id          Id of the HTML select list
     * @param   string  $attributes  Extras attributes to apply to the list
     *
     * @return  void
     *
     * @since   Kunena 6.0
     */
    public function displaySortByList(string $id, string $attributes = 'class="form-select mb-3"')
    {
        $options   = [];
        $options[] = HTMLHelper::_('select.option', 'title', Text::_('COM_KUNENA_SEARCH_SORTBY_TITLE'));

        // $options[] = HTMLHelper::_('select.option',  'replycount', Text::_('COM_KUNENA_SEARCH_SORTBY_POSTS'));
        $options[] = HTMLHelper::_('select.option', 'views', Text::_('COM_KUNENA_SEARCH_SORTBY_VIEWS'));

        // $options[] = HTMLHelper::_('select.option',  'threadstart', Text::_('COM_KUNENA_SEARCH_SORTBY_START'));
        $options[] = HTMLHelper::_('select.option', 'lastpost', Text::_('COM_KUNENA_SEARCH_SORTBY_POST'));

        // $options[] = HTMLHelper::_('select.option',  'postusername', Text::_('COM_KUNENA_SEARCH_SORTBY_USER'));
        $options[] = HTMLHelper::_('select.option', 'forum', Text::_('COM_KUNENA_CATEGORY'));

        echo HTMLHelper::_('select.genericlist', $options, 'sortby', $attributes, 'value', 'text', $this->state->get('query.sortby'), $id);
    }

    /**
     * Method to display list to choose the order
     *
     * @param   string  $id          Id of the HTML select list
     * @param   string  $attributes  Extras attributes to apply to the list
     *
     * @return  void
     *
     * @since   Kunena 6.0
     */
    public function displayOrderList(string $id, string $attributes = 'class="form-select mb-3"')
    {
        $options   = [];
        $options[] = HTMLHelper::_('select.option', 'inc', Text::_('COM_KUNENA_SEARCH_SORTBY_INC'));
        $options[] = HTMLHelper::_('select.option', 'dec', Text::_('COM_KUNENA_SEARCH_SORTBY_DEC'));

        echo HTMLHelper::_('select.genericlist', $options, 'order', $attributes, 'value', 'text', $this->state->get('query.order'), $id);
    }

    /**
     * Method to choose the limit of the list
     *
     * @param   string  $id          Id of the HTML select list
     * @param   string  $attributes  Extras attributes to apply to the list
     *
     * @return  void
     *
     * @since   Kunena 6.0
     */
    public function displayLimitList(string $id, string $attributes = 'class="form-select mb-3"')
    {
        // Limit value list
        $options = [];

        if (
            $this->config->messagesPerPageSearch != 5 && $this->config->messagesPerPageSearch != 10
            && $this->config->messagesPerPageSearch != 15 && $this->config->messagesPerPageSearch != 20
        ) {
            $options[] = HTMLHelper::_(
                'select.option',
                $this->config->messagesPerPageSearch,
                Text::sprintf(
                    'COM_KUNENA_SEARCH_LIMIT',
                    $this->config->messagesPerPageSearch
                )
            );
        }

        $options[] = HTMLHelper::_('select.option', '5', Text::_('COM_KUNENA_SEARCH_LIMIT5'));
        $options[] = HTMLHelper::_('select.option', '10', Text::_('COM_KUNENA_SEARCH_LIMIT10'));
        $options[] = HTMLHelper::_('select.option', '15', Text::_('COM_KUNENA_SEARCH_LIMIT15'));
        $options[] = HTMLHelper::_('select.option', '20', Text::_('COM_KUNENA_SEARCH_LIMIT20'));

        echo HTMLHelper::_('select.genericlist', $options, 'limit', $attributes, 'value', 'text', $this->state->get('list.limit'), $id);
    }

    /**
     * Method to display list of categories
     *
     * @param   string  $id          Id of the HTML select list
     * @param   string  $attributes  Extras attributes to apply to the list
     *
     * @return  void
     *
     * @since   Kunena 6.0
     */
    public function displayCategoryList(string $id, string $attributes = 'class="form-select"')
    {
        // Category select list
        $options   = [];
        $options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_SEARCH_SEARCHIN_ALLCATS'));

        $catParams = ['sections' => true];

        echo HTMLHelper::_(
            'kunenaforum.categorylist',
            'catids[]',
            0,
            $options,
            $catParams,
            $attributes,
            'value',
            'text',
            $this->state->get('query.catids'),
            $id
        );
    }

    /**
     * Method to create an input in function of name given
     *
     * @param   string  $name        Name of input to create
     * @param   string  $attributes  Attibutes to be added to input
     *
     * @return  string
     *
     * @since   Kunena 6.0
     */
    public function displayInput(string $name, string $attributes = '')
    {
        switch ($name) {
            case 'searchatdate':
                return '<input type="text" class="form-select" name="searchatdate" data-date-format="yyy-dd-mm" placeholder="yyy-dd-mm" value="' . $this->state->get('query.searchatdate') . '">' . $attributes;
        }

        return '';
    }
}
