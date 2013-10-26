<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.User
 *
 * @copyright   (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerUserItemDisplay
 *
 * @since  3.1
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

	/**
	 * Load user profile.
	 *
	 * @return void
	 *
	 * @throws KunenaExceptionAuthorise
	 */
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
		$this->profile->tryAuthorise('read');

		// Update profile hits.
		if (!$this->profile->exists() || !$this->profile->isMyself())
		{
			$this->profile->uhits++;
			$this->profile->save();
		}

		$this->headerText = JText::sprintf('COM_KUNENA_VIEW_USER_DEFAULT', $this->profile->getName());
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
