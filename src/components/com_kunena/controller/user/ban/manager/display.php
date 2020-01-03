<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.User
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

/**
 * Class ComponentKunenaControllerUserBanManagerDisplay
 *
 * @since   Kunena 4.0
 */
class ComponentKunenaControllerUserBanManagerDisplay extends KunenaControllerDisplay
{
	/**
	 * @var string
	 * @since   Kunena 6.0
	 */
	protected $name = 'User/Ban/Manager';

	/**
	 * @var KunenaUser
	 * @since   Kunena 6.0
	 */
	public $me;

	/**
	 * @var KunenaUser
	 * @since   Kunena 6.0
	 */
	public $profile;

	/**
	 * @var KunenaUserBan
	 * @since   Kunena 6.0
	 */
	public $userBans;

	/**
	 * @var
	 * @since   Kunena 6.0
	 */
	public $headerText;

	/**
	 * Prepare ban manager.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
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

		$this->headerText = Text::_('COM_KUNENA_BAN_BANMANAGER');
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
