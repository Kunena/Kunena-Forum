<?php
/**
 * Kunena Component
 * @package         Kunena.Site
 * @subpackage      Controller.Announcement
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;

/**
 * Class ComponentKunenaControllerAnnouncementListDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerAnnouncementListDisplay extends KunenaControllerDisplay
{
	/**
	 * @var string
	 * @since Kunena
	 */
	protected $name = 'Announcement/List';

	/**
	 * @var string
	 * @since Kunena
	 */
	public $announcements;

	/**
	 * @var boolean
	 * @since Kunena
	 */
	public $pagination;

	/**
	 * Prepare announcement list display.
	 *
	 * @return void
	 * @throws Exception
	 * @throws null
	 * @since Kunena
	 */
	protected function before()
	{
		parent::before();

		$limit = $this->input->getInt('limit', 0);

		$Itemid = $this->input->getInt('Itemid');

		if (!$Itemid && KunenaConfig::getInstance()->sef_redirect)
		{
			$itemid     = KunenaRoute::fixMissingItemID();
			$controller = BaseController::getInstance("kunena");
			$controller->setRedirect(KunenaRoute::_("index.php?option=com_kunena&view=announcement&layout=list&Itemid={$itemid}", false));
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

		$moderator           = KunenaUserHelper::getMyself()->isModerator();
		$this->pagination    = new KunenaPagination(KunenaForumAnnouncementHelper::getCount(!$moderator), $limitstart, $limit);
		$this->announcements = KunenaForumAnnouncementHelper::getAnnouncements(
			$this->pagination->limitstart,
			$this->pagination->limit,
			!$moderator
		);
	}

	/**
	 * Prepare document.
	 *
	 * @return void
	 * @throws Exception
	 * @since Kunena
	 */
	protected function prepareDocument()
	{
		$app       = Factory::getApplication();
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
				$this->setTitle(Text::_('COM_KUNENA_ANN_ANNOUNCEMENTS'));
			}

			if (!empty($params_keywords))
			{
				$keywords = $params->get('menu-meta_keywords');
				$this->setKeywords($keywords);
			}
			else
			{
				$this->setKeywords(Text::_('COM_KUNENA_ANN_ANNOUNCEMENTS'));
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
