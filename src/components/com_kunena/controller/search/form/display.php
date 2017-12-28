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
 * Class ComponentKunenaControllerSearchFormDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerSearchFormDisplay extends KunenaControllerDisplay
{
	protected $name = 'Search/Form';

	/**
	 * @var KunenaModelSearch
	 */
	public $model;

	/**
	 * Prepare search form display.
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

		$this->isModerator = ($this->me->isAdmin() || KunenaAccess::getInstance()->getModeratorStatus());
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

		$doc = JFactory::getDocument();
		$config = JFactory::getConfig();
		$robots = $config->get('robots');

		if ($robots == '')
		{
			$doc->setMetaData('robots', 'index, follow');
		}
		elseif ($robots == 'noindex, follow')
		{
			$doc->setMetaData('robots', 'noindex, follow');
		}
		elseif ($robots == 'index, nofollow')
		{
			$doc->setMetaData('robots', 'index, nofollow');
		}
		else
		{
			$doc->setMetaData('robots', 'nofollow, noindex');
		}

		if ($menu_item)
		{
			$params             = $menu_item->params;
			$params_title       = $params->get('page_title');
			$params_keywords    = $params->get('menu-meta_keywords');
			$params_description = $params->get('menu-meta_description');
			$params_robots      = $params->get('robots');

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
				$keywords = $this->config->board_title . ', ' . JText::_('COM_KUNENA_SEARCH_ADVSEARCH');
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

			if (!empty($params_robots))
			{
				$robots = $params->get('robots');
				$doc->setMetaData('robots', $robots);
			}
		}
	}
}
