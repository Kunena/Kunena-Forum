<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.Search
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerSearchResultsDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerSearchResultsDisplay extends KunenaControllerDisplay
{
	protected $name = 'Search/Results';

	/**
	 * @var KunenaModelSearch
	 */
	public $model;

	/**
	 * @var int
	 */
	public $total;

	public $data = array();

	/**
	 * Prepare search results display.
	 *
	 * @return void
	 */
	protected function before()
	{
		parent::before();

		require_once KPATH_SITE . '/models/search.php';
		$this->model = new KunenaModelSearch(array(), $this->input);
		$this->model->initialize($this->getOptions(), $this->getOptions()->get('embedded', false));
		$this->state = $this->model->getState();

		$this->me = KunenaUserHelper::getMyself();
		$this->message_ordering = $this->me->getMessageOrdering();

		$this->searchwords = $this->model->getSearchWords();
		$this->isModerator = ($this->me->isAdmin() || KunenaAccess::getInstance()->getModeratorStatus());

		$this->results = array();
		$this->total = $this->model->getTotal();
		$this->results = $this->model->getResults();

		$doc = JFactory::getDocument();
		$doc->setMetaData('robots', 'follow, noindex');

		foreach ($doc->_links as $key => $value)
		{
			if (is_array($value))
			{
				if (array_key_exists('relation', $value))
				{
					if ($value['relation'] == 'canonical')
					{
						$canonicalUrl = 'index.php?option=com_kunena&view=search';
						$doc->_links[$canonicalUrl] = $value;
						unset($doc->_links[$key]);
						break;
					}
				}
			}
		}

		$this->pagination = new KunenaPagination(
			$this->total,
			$this->state->get('list.start'),
			$this->state->get('list.limit')
		);

		$this->error = $this->model->getError();
	}

	/**
	 * Prepare document.
	 *
	 * @return void
	 */
	protected function prepareDocument()
	{
		$app       = JFactory::getApplication();
		$menu_item = $app->getMenu()->getActive();

		if ($menu_item)
		{
			$params             = $menu_item->params;
			$params_title       = $params->get('page_title');
			$params_keywords    = $params->get('menu-meta_keywords');
			$params_description = $params->get('menu-meta_description');

			if (!empty($params_title))
			{
				$title = $params->get('page_title');
				$this->setTitle($title);
			}
			else
			{
				$this->setTitle(JText::_('COM_KUNENA_SEARCH_ADVSEARCH'));
			}

			if (!empty($params_keywords))
			{
				$keywords = $params->get('menu-meta_keywords');
				$this->setKeywords($keywords);
			}
			else
			{
				$keywords = $this->config->board_title . ', ' . JText::_('COM_KUNENA_SEARCH_ADVSEARCH') . ', ' . $this->searchwords;
				$this->setKeywords($keywords);
			}

			if (!empty($params_description))
			{
				$description = $params->get('menu-meta_description');
				$this->setDescription($description);
			}
			else
			{
				$description = JText::_('COM_KUNENA_SEARCH_ADVSEARCH') . ': ' . $this->config->board_title;
				$this->setDescription($description);
			}
		}
	}
}
