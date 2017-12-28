<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Site
 * @subpackage  Layout.Search
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * KunenaLayoutSearchForm
 *
 * @since  K4.0
 *
 */
class KunenaLayoutSearchForm extends KunenaLayout
{
	/**
	 * Method to display the list to choose between posts or titles
	 *
	 * @param   int    $id         Id of the HTML select list
	 * @param   string  $attributes  Extras attributes to apply to the list
	 *
	 * @return void
	 */
	public function displayModeList($id, $attributes = 'class="form-control"')
	{
		$options   = array();
		$options[] = JHtml::_('select.option', '0', JText::_('COM_KUNENA_SEARCH_SEARCH_POSTS'));
		$options[] = JHtml::_('select.option', '1', JText::_('COM_KUNENA_SEARCH_SEARCH_TITLES'));
		echo JHtml::_('select.genericlist', $options, 'titleonly', $attributes, 'value', 'text', $this->state->get('query.titleonly'), $id);
	}

	/**
	 * Method to get the date list
	 *
	 * @param   int    $id         Id of the HTML select list
	 * @param   string $attributes Extras attributes to apply to the list
	 *
	 * @return void
	 */
	public function displayDateList($id, $attributes = 'class="form-control"')
	{
		$options   = array();
		$options[] = JHtml::_('select.option', 'lastvisit', JText::_('COM_KUNENA_SEARCH_DATE_LASTVISIT'));
		$options[] = JHtml::_('select.option', '1', JText::_('COM_KUNENA_SEARCH_DATE_YESTERDAY'));
		$options[] = JHtml::_('select.option', '7', JText::_('COM_KUNENA_SEARCH_DATE_WEEK'));
		$options[] = JHtml::_('select.option', '14', JText::_('COM_KUNENA_SEARCH_DATE_2WEEKS'));
		$options[] = JHtml::_('select.option', '30', JText::_('COM_KUNENA_SEARCH_DATE_MONTH'));
		$options[] = JHtml::_('select.option', '90', JText::_('COM_KUNENA_SEARCH_DATE_3MONTHS'));
		$options[] = JHtml::_('select.option', '180', JText::_('COM_KUNENA_SEARCH_DATE_6MONTHS'));
		$options[] = JHtml::_('select.option', '365', JText::_('COM_KUNENA_SEARCH_DATE_YEAR'));
		$options[] = JHtml::_('select.option', 'all', JText::_('COM_KUNENA_SEARCH_DATE_ANY'));
		echo JHtml::_('select.genericlist', $options, 'searchdate', $attributes, 'value', 'text', $this->state->get('query.searchdate'), $id);
	}

	/**
	 * Method to display list to choose into the new dates or the older dates
	 *
	 * @param   int    $id         Id of the HTML select list
	 * @param   string $attributes Extras attributes to apply to the list
	 *
	 * @return void
	 */
	public function displayBeforeAfterList($id, $attributes = 'class="form-control"')
	{
		$options   = array();
		$options[] = JHtml::_('select.option', 'after', JText::_('COM_KUNENA_SEARCH_DATE_NEWER'));
		$options[] = JHtml::_('select.option', 'before', JText::_('COM_KUNENA_SEARCH_DATE_OLDER'));
		echo JHtml::_('select.genericlist', $options, 'beforeafter', $attributes, 'value', 'text', $this->state->get('query.beforeafter'), $id);
	}

	/**
	 * Method to display list to choose how to sort the results
	 *
	 * @param   int    $id         Id of the HTML select list
	 * @param   string $attributes Extras attributes to apply to the list
	 *
	 * @return void
	 */
	public function displaySortByList($id, $attributes = 'class="form-control"')
	{
		$options   = array();
		$options[] = JHtml::_('select.option', 'title', JText::_('COM_KUNENA_SEARCH_SORTBY_TITLE'));

		// $options[] = JHtml::_('select.option',  'replycount', JText::_('COM_KUNENA_SEARCH_SORTBY_POSTS'));
		$options[] = JHtml::_('select.option', 'views', JText::_('COM_KUNENA_SEARCH_SORTBY_VIEWS'));

		// $options[] = JHtml::_('select.option',  'threadstart', JText::_('COM_KUNENA_SEARCH_SORTBY_START'));
		$options[] = JHtml::_('select.option', 'lastpost', JText::_('COM_KUNENA_SEARCH_SORTBY_POST'));

		// $options[] = JHtml::_('select.option',  'postusername', JText::_('COM_KUNENA_SEARCH_SORTBY_USER'));
		$options[] = JHtml::_('select.option', 'forum', JText::_('COM_KUNENA_CATEGORY'));
		echo JHtml::_('select.genericlist', $options, 'sortby', $attributes, 'value', 'text', $this->state->get('query.sortby'), $id);
	}

	/**
	 * Method to display list to choose the order
	 *
	 * @param   int    $id         Id of the HTML select list
	 * @param   string $attributes Extras attributes to apply to the list
	 *
	 * @return void
	 */
	public function displayOrderList($id, $attributes = 'class="form-control"')
	{
		$options   = array();
		$options[] = JHtml::_('select.option', 'inc', JText::_('COM_KUNENA_SEARCH_SORTBY_INC'));
		$options[] = JHtml::_('select.option', 'dec', JText::_('COM_KUNENA_SEARCH_SORTBY_DEC'));
		echo JHtml::_('select.genericlist', $options, 'order', $attributes, 'value', 'text', $this->state->get('query.order'), $id);
	}

	/**
	 * Method to choose the limit of the list
	 *
	 * @param   int    $id         Id of the HTML select list
	 * @param   string $attributes Extras attributes to apply to the list
	 *
	 * @return void
	 */
	public function displayLimitList($id, $attributes = 'class="form-control"')
	{
		// Limit value list
		$options   = array();
		$options[] = JHtml::_('select.option', $this->config->messages_per_page_search, JText::sprintf('COM_KUNENA_SEARCH_LIMIT',
			$this->config->messages_per_page_search));
		$options[] = JHtml::_('select.option', '5', JText::_('COM_KUNENA_SEARCH_LIMIT5'));
		$options[] = JHtml::_('select.option', '10', JText::_('COM_KUNENA_SEARCH_LIMIT10'));
		$options[] = JHtml::_('select.option', '15', JText::_('COM_KUNENA_SEARCH_LIMIT15'));
		$options[] = JHtml::_('select.option', '20', JText::_('COM_KUNENA_SEARCH_LIMIT20'));
		echo JHtml::_('select.genericlist', $options, 'limit', $attributes, 'value', 'text', $this->state->get('list.limit'), $id);
	}

	/**
	 * Method to display list of categories
	 *
	 * @param   int    $id         Id of the HTML select list
	 * @param   string $attributes Extras attributes to apply to the list
	 *
	 * @return void
	 */
	public function displayCategoryList($id, $attributes = 'class="form-control"')
	{
		// Category select list
		$options   = array();
		$options[] = JHtml::_('select.option', '0', JText::_('COM_KUNENA_SEARCH_SEARCHIN_ALLCATS'));

		$cat_params = array('sections' => true);
		echo JHtml::_(
			'kunenaforum.categorylist', 'catids[]', 0, $options, $cat_params, $attributes, 'value', 'text', $this->state->get('query.catids'), $id
		);
	}
}
