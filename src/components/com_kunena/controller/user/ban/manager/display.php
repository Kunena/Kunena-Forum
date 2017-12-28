<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.User
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerUserBanManagerDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerUserBanManagerDisplay extends KunenaControllerDisplay
{
	protected $name = 'User/Ban/Manager';

	/**
	 * @var KunenaUser
	 */
	public $me;

	/**
	 * @var KunenaUser
	 */
	public $profile;

	/**
	 * @var KunenaUserBan
	 */
	public $userBans;

	public $headerText;

	/**
	 * Prepare ban manager.
	 *
	 * @return void
	 */
	protected function before()
	{
		parent::before();

		$this->me = KunenaUserHelper::getMyself();

		// TODO: add authorisation
		// TODO: add pagination
		$this->userBans = KunenaUserBan::getBannedUsers(0, 50);

		if (!empty($this->userBans))
		{
			KunenaUserHelper::loadUsers(array_keys($this->userBans));
		}

		$this->headerText = JText::_('COM_KUNENA_BAN_BANMANAGER');
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
				$this->setTitle($this->headerText);
			}

			if (!empty($params_keywords))
			{
				$keywords = $params->get('menu-meta_keywords');
				$this->setKeywords($keywords);
			}
			else
			{
				$this->setKeywords($this->headerText);
			}

			if (!empty($params_description))
			{
				$description = $params->get('menu-meta_description');
				$this->setDescription($description);
			}
			else
			{
				$this->setDescription($this->headerText);
			}
		}
	}
}
