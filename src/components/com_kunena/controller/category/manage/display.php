<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Category
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license         http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerApplicationMiscDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerCategoryManageDisplay extends KunenaControllerDisplay
{
	protected $name = 'Category/Manage';

	public $headerText;

	/**
	 * @var KunenaForumCategory
	 * @since Kunena 5.1
	 */
	public $category;

	public $total;

	public $topics;

	/**
	 * @var KunenaPagination
	 * @since Kunena 5.1
	 */
	public $pagination;

	/**
	 * @var KunenaUser
	 * @since Kunena 5.1
	 */
	public $me;

	protected $state = null;

	/**
	 * Prepare category display.
	 *
	 * @return void
	 *
	 * @throws KunenaExceptionAuthorise
	 * @since Kunena 5.1
	 */
	protected function before()
	{
		$this->me = KunenaUserHelper::getMyself();

		if (!$this->me->isAdmin())
		{
			return new KunenaExceptionAuthorise(JText::_('COM_KUNENA_NO_ACCESS'), 403);
		}

		require_once KPATH_SITE . '/models/category.php';
		$catid            = $this->input->getInt('catid');
		$this->categories = array();

		$this->navigation = '';
		$this->state      = $this->app->getUserState('com_kunena.category');
		$header           = JText::_('COM_KUNENA_ADMIN');
		$this->header     = $header;
		$this->ktemplate  = KunenaFactory::getTemplate();
		$this->document   = JFactory::getDocument();
	}

	/**
	 * Prepare document.
	 *
	 * @return void
	 * @since Kunena 5.1
	 */
	protected function prepareDocument()
	{
		$app       = JFactory::getApplication();
		$menu_item = $app->getMenu()->getActive();

		$doc = JFactory::getDocument();
		$doc->setMetaData('robots', 'nofollow, noindex');

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
				$title = JText::_('COM_KUNENA_ADMIN');
				$this->setTitle($title);
			}

			if (!empty($params_keywords))
			{
				$keywords = $params->get('menu-meta_keywords');
				$this->setKeywords($keywords);
			}
			else
			{
				$keywords = JText::_('COM_KUNENA_ADMIN');
				$this->setKeywords($keywords);
			}

			if (!empty($params_description))
			{
				$description = $params->get('menu-meta_description');
				$this->setDescription($description);
			}
			else
			{
				$description = JText::_('COM_KUNENA_ADMIN');
				$this->setDescription($description);
			}
		}
	}
}
