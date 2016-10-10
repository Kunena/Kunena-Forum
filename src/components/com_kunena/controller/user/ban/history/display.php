<?php
/**
 * Kunena Component
 * @package         Kunena.Site
 * @subpackage      Controller.User
 *
 * @copyright       Copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license         http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerUserBanHistoryDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerUserBanHistoryDisplay extends KunenaControllerDisplay
{
	protected $name = 'User/Ban/History';

	/**
	 * @var KunenaUser
	 * @since Kunena
	 */
	public $me;

	/**
	 * @var KunenaUser
	 * @since Kunena
	 */
	public $profile;

	/**
	 * @var array|KunenaUserBan[]
	 * @since Kunena
	 */
	public $banHistory;

	public $headerText;

	/**
	 * Prepare ban history.
	 *
	 * @return void
	 *
	 * @throws KunenaExceptionAuthorise
	 * @since Kunena
	 */
	protected function before()
	{
		parent::before();

		$userid = $this->input->getInt('userid');

		$this->me      = KunenaUserHelper::getMyself();
		$this->profile = KunenaUserHelper::get($userid);
		$this->profile->tryAuthorise('ban');

		$this->banHistory = KunenaUserBan::getUserHistory($this->profile->userid);

		$this->headerText = JText::sprintf('COM_KUNENA_BAN_BANHISTORYFOR', $this->profile->getName());
	}

	/**
	 * Prepare document.
	 *
	 * @return void
	 * @since Kunena
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
