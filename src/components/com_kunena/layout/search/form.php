<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.Search
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/**
 * KunenaLayoutSearchForm
 *
 * @since  K4.0
 */
class KunenaLayoutSearchForm extends KunenaLayout
{
	/**
	 * Method to display the list to choose between posts or titles
	 *
	 * @param   int    $id         Id of the HTML select list
	 * @param   string $attributes Extras attributes to apply to the list
	 *
	 * @return void
	 * @since Kunena
	 */
	public function displayModeList($id, $attributes = 'class="form-control"')
	{
		$options   = array();
		$options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_SEARCH_SEARCH_POSTS'));
		$options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_SEARCH_SEARCH_TITLES'));

		echo HTMLHelper::_('select.genericlist', $options, 'titleonly', $attributes, 'value', 'text', $this->state->get('query.titleonly'), $id);
	}

	/**
	 * Method to get the date list
	 *
	 * @param   int    $id         Id of the HTML select list
	 * @param   string $attributes Extras attributes to apply to the list
	 *
	 * @return void
	 * @since Kunena
	 */
	public function displayDateList($id, $attributes = 'class="form-control"')
	{
		$options   = array();
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
	 * @param   int    $id         Id of the HTML select list
	 * @param   string $attributes Extras attributes to apply to the list
	 *
	 * @return void
	 * @since Kunena
	 */
	public function displayBeforeAfterList($id, $attributes = 'class="form-control"')
	{
		$options   = array();
		$options[] = HTMLHelper::_('select.option', 'after', Text::_('COM_KUNENA_SEARCH_DATE_NEWER'));
		$options[] = HTMLHelper::_('select.option', 'before', Text::_('COM_KUNENA_SEARCH_DATE_OLDER'));

		echo HTMLHelper::_('select.genericlist', $options, 'beforeafter', $attributes, 'value', 'text', $this->state->get('query.beforeafter'), $id);
	}

	/**
	 * Method to display list to choose how to sort the results
	 *
	 * @param   int    $id         Id of the HTML select list
	 * @param   string $attributes Extras attributes to apply to the list
	 *
	 * @return void
	 * @since Kunena
	 */
	public function displaySortByList($id, $attributes = 'class="form-control"')
	{
		$options   = array();
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
	 * @param   int    $id         Id of the HTML select list
	 * @param   string $attributes Extras attributes to apply to the list
	 *
	 * @return void
	 * @since Kunena
	 */
	public function displayOrderList($id, $attributes = 'class="form-control"')
	{
		$options   = array();
		$options[] = HTMLHelper::_('select.option', 'inc', Text::_('COM_KUNENA_SEARCH_SORTBY_INC'));
		$options[] = HTMLHelper::_('select.option', 'dec', Text::_('COM_KUNENA_SEARCH_SORTBY_DEC'));

		echo HTMLHelper::_('select.genericlist', $options, 'order', $attributes, 'value', 'text', $this->state->get('query.order'), $id);
	}

	/**
	 * Method to choose the limit of the list
	 *
	 * @param   int    $id         Id of the HTML select list
	 * @param   string $attributes Extras attributes to apply to the list
	 *
	 * @return void
	 * @since Kunena
	 */
	public function displayLimitList($id, $attributes = 'class="form-control"')
	{
		// Limit value list
		$options   = array();

		if ($this->config->messages_per_page_search != 5 && $this->config->messages_per_page_search != 10 &&
			$this->config->messages_per_page_search != 15 && $this->config->messages_per_page_search != 20)
		{
			$options[] = HTMLHelper::_('select.option', $this->config->messages_per_page_search, Text::sprintf('COM_KUNENA_SEARCH_LIMIT',
				$this->config->messages_per_page_search
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
	 * @param   int    $id         Id of the HTML select list
	 * @param   string $attributes Extras attributes to apply to the list
	 *
	 * @return void
	 * @since Kunena
	 */
	public function displayCategoryList($id, $attributes = 'class="form-control"')
	{
		// Category select list
		$options   = array();
		$options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_SEARCH_SEARCHIN_ALLCATS'));

		$cat_params = array('sections' => true);

		echo HTMLHelper::_(
			'kunenaforum.categorylist', 'catids[]', 0, $options, $cat_params, $attributes, 'value', 'text', $this->state->get('query.catids'), $id
		);
	}
}
