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
 * Class ComponentKunenaControllerUserItemDisplay
 */
class ComponentKunenaControllerUserItemDisplay extends KunenaControllerDisplay
{
	protected $name = 'User/Item';

	/**
	 * @var KunenaUser
	 */
	public $me;
	/**
	 * @var JUser
	 */
	public $user;
	/**
	 * @var KunenaUser
	 */
	public $profile;
	public $headerText;
	public $tabs;

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

		$this->me = KunenaUserHelper::getMyself();
		$this->user = JFactory::getUser($userid);
		$this->profile = KunenaUserHelper::get($userid);

		// TODO: authorise action...
		if ($this->user->guest || (!$this->me->exists() && !$this->config->pubprofile)) {
			throw new KunenaExceptionAuthorise(JText::_('COM_KUNENA_PROFILEPAGE_NOT_ALLOWED_FOR_GUESTS'), 403);
		}

		// Update profile hits.
		if (!$this->profile->exists() || !$this->profile->isMyself()) {
			$this->profile->uhits++;
			$this->profile->save();
		}

		$this->headerText = JText::sprintf('COM_KUNENA_VIEW_USER_DEFAULT', $this->profile->getName());
	}

	protected function prepareDocument()
	{
		$this->setTitle($this->headerText);
	}
}
