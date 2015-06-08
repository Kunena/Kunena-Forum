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
 * Class ComponentKunenaControllerUserBanFormDisplay
 *
 * @since  K4.0
 */
class ComponentKunenaControllerUserBanFormDisplay extends KunenaControllerDisplay
{
	protected $name = 'User/Ban/Form';

	/**
	 * @var KunenaUser
	 */
	public $profile;

	/**
	 * @var KunenaUserBan
	 */
	public $banInfo;

	public $headerText;

	/**
	 * Prepare ban form.
	 *
	 * @return void
	 *
	 * @throws KunenaExceptionAuthorise
	 */
	protected function before()
	{
		parent::before();

		$userid = $this->input->getInt('userid');

		$this->profile = KunenaUserHelper::get($userid);
		$this->profile->tryAuthorise('ban');

		$this->banInfo = KunenaUserBan::getInstanceByUserid($userid, true);

		$this->headerText = $this->banInfo->exists() ? JText::_('COM_KUNENA_BAN_EDIT') : JText::_('COM_KUNENA_BAN_NEW');
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
