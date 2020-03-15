<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Search
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Search\Form;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Kunena\Forum\Libraries\Access\Access;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Forum\Site\Model\SearchModel;
use function defined;

/**
 * Class ComponentSearchControllerFormDisplay
 *
 * @since   Kunena 4.0
 */
class ComponentSearchControllerFormDisplay extends KunenaControllerDisplay
{
	/**
	 * @var     SearchModel
	 * @since   Kunena 6.0
	 */
	public $model;
	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $name = 'Search/Form';

	/**
	 * Prepare search form display.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  null
	 * @throws  Exception
	 */
	protected function before()
	{
		parent::before();

		$this->model = new SearchModel([], $this->input);
		$this->model->initialize($this->getOptions(), $this->getOptions()->get('embedded', false));
		$this->state = $this->model->getState();

		$Itemid = Factory::getApplication()->input->getCmd('Itemid');

		if (!$Itemid && $this->config->sef_redirect)
		{
			if ($this->config->search_id)
			{
				$itemidfix = $this->config->search_id;
			}
			else
			{
				$menu      = $this->app->getMenu();
				$getid     = $menu->getItem(KunenaRoute::getItemID("index.php?option=com_kunena&view=search"));
				$itemidfix = $getid->id;
			}

			if (!$itemidfix)
			{
				$itemidfix = KunenaRoute::fixMissingItemID();
			}

			$controller = BaseController::getInstance("kunena");
			$controller->setRedirect(KunenaRoute::_("index.php?option=com_kunena&view=search&Itemid={$itemidfix}", false));
			$controller->redirect();
		}

		$this->me = KunenaUserHelper::getMyself();

		$this->isModerator = ($this->me->isAdmin() || Access::getInstance()->getModeratorStatus());
		$this->error       = $this->model->getError();
	}

	/**
	 * Prepare document.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 */
	protected function prepareDocument()
	{
		$menu_item = $this->app->getMenu()->getActive();

		$config = Factory::getConfig();
		$robots = $config->get('robots');

		if ($robots == 'noindex, follow')
		{
			$this->setMetaData('robots', 'noindex, follow');
		}
		elseif ($robots == 'index, nofollow')
		{
			$this->setMetaData('robots', 'index, nofollow');
		}
		elseif ($robots == 'noindex, nofollow')
		{
			$this->setMetaData('robots', 'noindex, nofollow');
		}
		else
		{
			$this->setMetaData('robots', 'index, follow');
		}

		if ($menu_item)
		{
			$params             = $menu_item->getParams();
			$params_title       = $params->get('page_title');
			$params_description = $params->get('menu-meta_description');
			$params_robots      = $params->get('robots');

			if (!empty($params_title))
			{
				$title = $params->get('page_title');
				$this->setTitle($title);
			}
			else
			{
				$this->setTitle(Text::_('COM_KUNENA_SEARCH_ADVSEARCH'));
			}

			if (!empty($params_description))
			{
				$description = $params->get('menu-meta_description');
				$this->setDescription($description);
			}
			else
			{
				$description = Text::_('COM_KUNENA_SEARCH_ADVSEARCH') . ': ' . $this->config->board_title;
				$this->setDescription($description);
			}

			if (!empty($params_robots))
			{
				$robots = $params->get('robots');
				$this->setMetaData('robots', $robots);
			}
		}
	}
}
