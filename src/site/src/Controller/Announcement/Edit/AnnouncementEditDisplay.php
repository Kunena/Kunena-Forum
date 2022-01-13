<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Announcement
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Announcement\Edit;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Forum\Announcement\KunenaAnnouncementHelper;
use Kunena\Forum\Libraries\Route\KunenaRoute;

/**
 * Class ComponentAnnouncementControllerEditDisplay
 *
 * @since   Kunena 4.0
 */
class AnnouncementEditDisplay extends KunenaControllerDisplay
{
	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $announcement;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $name = 'Announcement/Edit';

	/**
	 * Prepare announcement form display.
	 *
	 * @return  void
	 *
	 * @throws  null
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	protected function before()
	{
		parent::before();

		$id = $this->input->getInt('id', null);

		$this->announcement = KunenaAnnouncementHelper::get($id);
		$this->announcement->tryAuthorise($id ? 'edit' : 'create');

		$Itemid = $this->input->getInt('Itemid');

		if (!$Itemid && $this->config->sefRedirect)
		{
			$itemid     = KunenaRoute::fixMissingItemID();
			$controller = BaseController::getInstance("kunena");

			if ($id)
			{
				$controller->setRedirect(KunenaRoute::_("index.php?option=com_kunena&view=announcement&layout=edit&id={$id}&Itemid={$itemid}", false));
			}
			else
			{
				$controller->setRedirect(KunenaRoute::_("index.php?option=com_kunena&view=announcement&layout=create&Itemid={$itemid}", false));
			}

			$controller->redirect();
		}
	}

	/**
	 * Prepare document.
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 6.0
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
