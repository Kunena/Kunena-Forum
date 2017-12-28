<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.User
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerUserItemDisplay
 *
 * @since  K4.0
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

		require_once KPATH_SITE . '/models/user.php';
		$this->model = new KunenaModelUser(array(), $this->input);
		$this->model->initialize($this->getOptions(), $this->getOptions()->get('embedded', false));
		$this->state = $this->model->getState();

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
		$doc = JFactory::getDocument();
		$doc->setMetaData('profile:username', $this->profile->getName(), 'property');

		if ($this->profile->getGender() == 1)
		{
			$doc->setMetaData('profile:gender', JText::_('COM_KUNENA_MYPROFILE_GENDER_MALE'), 'property');
		}
		elseif ($this->profile->getGender() == 2)
		{
			$doc->setMetaData('profile:gender', JText::_('COM_KUNENA_MYPROFILE_GENDER_FEMALE'), 'property');
		}
		else
		{
			$doc->setMetaData('profile:gender', JText::_('COM_KUNENA_MYPROFILE_GENDER_UNKNOWN'), 'property');
		}

		$app       = JFactory::getApplication();
		$menu_item = $app->getMenu()->getActive();

		$doc = JFactory::getDocument();
		$config = JFactory::getConfig();
		$robots = $config->get('robots');

		if ($robots == '')
		{
			$doc->setMetaData('robots', 'index, follow');
		}
		elseif ($robots == 'noindex, follow')
		{
			$doc->setMetaData('robots', 'noindex, follow');
		}
		elseif ($robots == 'index, nofollow')
		{
			$doc->setMetaData('robots', 'index, nofollow');
		}
		else
		{
			$doc->setMetaData('robots', 'nofollow, noindex');
		}

		if ($menu_item)
		{
			$params             = $menu_item->params;
			$params_title       = $params->get('page_title');
			$params_keywords    = $params->get('menu-meta_keywords');
			$params_description = $params->get('menu-meta_description');
			$params_robots      = $params->get('robots');

			if (!empty($params_title))
			{
				$title = $params->get('page_title');
				$this->setTitle($title);
			}
			else
			{
				$title = JText::sprintf('COM_KUNENA_VIEW_USER_DEFAULT', $this->profile->getName());
				$this->setTitle($title);
			}

			if (!empty($params_keywords))
			{
				$keywords = $params->get('menu-meta_keywords');
				$this->setKeywords($keywords);
			}
			else
			{
				$keywords = $this->config->board_title . ', ' . $this->profile->getName();
				$this->setKeywords($keywords);
			}

			if (!empty($params_description))
			{
				$description = $params->get('menu-meta_description');
				$this->setDescription($description);
			}
			else
			{
				$description = JText::sprintf('COM_KUNENA_META_PROFILE', $this->profile->getName(),
					$this->config->board_title, $this->profile->getName(), $this->config->board_title);
				$this->setDescription($description);
			}

			if (!empty($params_robots))
			{
				$robots = $params->get('robots');
				$doc->setMetaData('robots', $robots);
			}
		}
	}
}
