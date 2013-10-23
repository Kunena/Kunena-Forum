<?php
/**
 * Kunena Component
 * @package Kunena.Site
 * @subpackage Controllers.User
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Class ComponentKunenaControllerUserBanFormDisplay
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

	protected function before()
	{
		parent::before();

		$userid = $this->input->getInt('userid');

		$this->profile = KunenaUserHelper::get($userid);
		$this->banInfo = KunenaUserBan::getInstanceByUserid($userid, true);

		if (!$this->banInfo->canBan()) {
			throw new KunenaExceptionAuthorise($this->banInfo->getError(), 403);
		}

		$this->headerText = $this->banInfo->exists() ? JText::_('COM_KUNENA_BAN_EDIT') : JText::_('COM_KUNENA_BAN_NEW');
	}

	protected function prepareDocument()
	{
		$this->setTitle($this->headerText);
	}
}
