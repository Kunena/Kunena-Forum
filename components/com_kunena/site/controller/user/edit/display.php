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
 * Class ComponentKunenaControllerUserEditDisplay
 */
class ComponentKunenaControllerUserEditDisplay extends KunenaControllerDisplay
{
	protected $name = 'User/Edit';

	/**
	 * @var jUser
	 */
	public $user;
	/**
	 * @var KunenaUser
	 */
	public $profile;

	protected function before()
	{
		parent::before();

		// If profile integration is disabled, this view doesn't exist.
		$integration = KunenaFactory::getProfile();
		if (get_class($integration) == 'KunenaProfileNone')
		{
			throw new KunenaExceptionAuthorise(JText::_('COM_KUNENA_PROFILE_DISABLED'), 404);
		}

		$userid = $this->input->getInt('userid');

		$this->user = JFactory::getUser($userid);
		$this->profile = KunenaUserHelper::get($userid);

		// TODO: authorise action...
		if ($this->user->guest || !$this->profile->isMyself())
		{
			throw new KunenaExceptionAuthorise(JText::sprintf('COM_KUNENA_VIEW_USER_EDIT_AUTH_FAILED', $this->profile->getName()), 403);
		}

		$this->headerText = JText::sprintf('COM_KUNENA_VIEW_USER_DEFAULT', $this->profile->getName());
	}

	protected function prepareDocument()
	{
		$this->setTitle($this->headerText);
	}
}
