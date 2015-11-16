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
 * Class ComponentKunenaControllerUserBanHistoryDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerUserBanHistoryDisplay extends KunenaControllerDisplay
{
	protected $name = 'User/Ban/History';

	/**
	 * @var KunenaUser
	 */
	public $me;

	/**
	 * @var KunenaUser
	 */
	public $profile;

	/**
	 * @var array|KunenaUserBan[]
	 */
	public $banHistory;

	public $headerText;

	/**
	 * Prepare ban history.
	 *
	 * @return void
	 *
	 * @throws KunenaExceptionAuthorise
	 */
	protected function before()
	{
		parent::before();

		$userid = $this->input->getInt('userid');

		$this->me = KunenaUserHelper::getMyself();
		$this->profile = KunenaUserHelper::get($userid);
		$this->profile->tryAuthorise('ban');

		$this->banHistory = KunenaUserBan::getUserHistory($this->profile->userid);

		$this->headerText = JText::sprintf('COM_KUNENA_BAN_BANHISTORYFOR', $this->profile->getName());
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
