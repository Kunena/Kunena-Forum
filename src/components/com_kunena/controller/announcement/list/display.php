<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Announcement
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Announement\Kunenalist;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Forum\Announcement\AnnouncementHelper;
use Kunena\Forum\Libraries\Pagination\Pagination;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use function defined;

/**
 * Class ComponentAnnouncementControllerListDisplay
 *
 * @since   Kunena 4.0
 */
class ComponentAnnouncementControllerListDisplay extends KunenaControllerDisplay
{
	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $name = 'Announcement/List';

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $announcements;

	/**
	 * @var     boolean
	 * @since   Kunena 6.0
	 */
	public $pagination;

	/**
	 * Prepare announcement list display.
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

		$limit = $this->input->getInt('limit', 0);

		$Itemid = $this->input->getInt('Itemid');

		if (!$Itemid && $this->config->sef_redirect)
		{
			$itemid     = KunenaRoute::fixMissingItemID();
			$controller = BaseController::getInstance("kunena");
			$controller->setRedirect(\Kunena\Forum\Libraries\Route\KunenaRoute::_("index.php?option=com_kunena&view=announcement&layout=list&Itemid={$itemid}", false));
			$controller->redirect();
		}

		if ($limit < 1 || $limit > 100)
		{
			$limit = 20;
		}

		$limitstart = $this->input->getInt('limitstart', 0);

		if ($limitstart < 0)
		{
			$limitstart = 0;
		}

		$moderator           = \Kunena\Forum\Libraries\User\KunenaUserHelper::getMyself()->isModerator();
		$this->pagination    = new Pagination(AnnouncementHelper::getCount(!$moderator), $limitstart, $limit);
		$this->announcements = AnnouncementHelper::getAnnouncements(
			$this->pagination->limitstart,
			$this->pagination->limit,
			!$moderator
		);
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

		if ($menu_item)
		{
			$params             = $menu_item->getParams();
			$params_title       = $params->get('page_title');
			$params_description = $params->get('menu-meta_description');

			if (!empty($params_title))
			{
				$title = $params->get('page_title');
				$this->setTitle($title);
			}
			else
			{
				$this->setTitle(Text::_('COM_KUNENA_ANN_ANNOUNCEMENTS'));
			}

			if (!empty($params_description))
			{
				$description = $params->get('menu-meta_description');
				$this->setDescription($description);
			}
			else
			{
				$this->setDescription(Text::_('COM_KUNENA_ANN_ANNOUNCEMENTS'));
			}
		}
	}
}
