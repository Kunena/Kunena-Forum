<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.User
 *
 * @copyright   (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
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
		$this->setTitle($this->headerText);
	}
}
