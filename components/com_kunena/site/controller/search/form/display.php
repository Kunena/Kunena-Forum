<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.Search
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
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
		$app = JFactory::getApplication();
		$menu_item   = $app->getMenu()->getActive(); // get the active item
		$params = $menu_item->params; // get the params
		$params_title = $params->get('page_title');
		$params_keywords = $params->get('menu-meta_keywords');
		$params_description = $params->get('menu-description');

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
			$description = JText::_('COM_KUNENA_SEARCH_ADVSEARCH') . ': ' . $this->config->board_title ;
			$this->setDescription($description);
		}
	}
}
