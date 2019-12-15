<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.User
 *
 * @copyright       Copyright (C) 2008 - 2019 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

/**
 * Class ComponentKunenaControllerUserBanHistoryDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerUserBanHistoryDisplay extends KunenaControllerDisplay
{
	/**
	 * @var string
	 * @since Kunena
	 */
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

	/**
	 * @var
	 * @since Kunena
	 */
	public $headerText;

	/**
	 * Prepare ban history.
	 *
	 * @return void
	 *
	 * @since Kunena
	 * @throws null
	 */
	protected function before()
	{
		parent::before();

		$userid = $this->input->getInt('userid');

		$this->me      = KunenaUserHelper::getMyself();
		$this->profile = KunenaUserHelper::get($userid);
		$this->profile->tryAuthorise('ban');

		$this->banHistory = KunenaUserBan::getUserHistory($this->profile->userid);

		$this->headerText = Text::sprintf('COM_KUNENA_BAN_BANHISTORYFOR', $this->profile->getName());
	}

	/**
	 * Prepare document.
	 *
	 * @return void
	 * @since Kunena
	 * @throws Exception
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
