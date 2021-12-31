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
 * Class ComponentKunenaControllerAnnouncementEditDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerAnnouncementEditDisplay extends KunenaControllerDisplay
{
	/**
	 * @var string
	 * @since Kunena
	 */
	protected $name = 'Announcement/Edit';

	/**
	 * @var string
	 * @since Kunena
	 */
	public $announcement;

	/**
	 * Prepare announcement form display.
	 *
	 * @return void
	 * @throws Exception
	 * @throws null
	 * @since Kunena
	 */
	protected function before()
	{
		parent::before();

		$id = $this->input->getInt('id', null);

		$this->announcement = KunenaForumAnnouncementHelper::get($id);
		$this->announcement->tryAuthorise($id ? 'edit' : 'create');

		$Itemid = $this->input->getInt('Itemid');

		if (!$Itemid && KunenaConfig::getInstance()->sef_redirect)
		{
			$itemid     = KunenaRoute::fixMissingItemID();
			$controller = BaseController::getInstance("kunena");

			if ($id)
			{
				$controller->setRedirect(KunenaRoute::_("index.php?option=com_kunena&view=announcement&layout=edit&&id={$id}&Itemid={$itemid}", false));
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
