<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Controller;

defined('_JEXEC') or die();

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Factory;

/**
 * Kunena User Controller
 *
 * @since   Kunena 3.0
 */
class UserController extends FormController
{
	/**
	 * @var     null|string
	 * @since   Kunena 6.0
	 */
	protected $baseurl = null;

	/**
	 * Constructor.
	 *
	 * @param   array                $config   An optional associative array of configuration settings.
	 * @param   MVCFactoryInterface  $factory  The factory.
	 * @param   CMSApplication       $app      The CMSApplication for the dispatcher
	 * @param   Input                $input    Input
	 *
	 * @since   Kunena 2.0
	 *
	 * @see     BaseController
	 * @throws \Exception
	 */
	public function __construct($config = array(), MVCFactoryInterface $factory = null, $app = null, $input = null)
	{
		parent::__construct($config, $factory, $app, $input);

		$this->baseurl = 'administrator/index.php?option=com_kunena&view=users';
	}

	/**
	 * Method to save the form data.
	 *
	 * @return  void
	 *
	 * @since   Kunena 2.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function save()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(\KunenaRoute::_($this->baseurl, false));

			return;
		}

		$this->saveInternal('save');

		$this->setRedirect(\KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Apply
	 *
	 * @return  void
	 *
	 * @since   Kunena 2.0
	 *
	 * @throws  Exception
	 */
	public function apply()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');

			return;
		}

		$this->saveInternal('apply');
	}
	
	/**
	 * Internal method to save an user
	 * 
	 * @param string $type
	 */
	protected function saveInternal($type)
	{
		$newview      = $this->app->input->getString('newview');
		$newrank      = $this->app->input->getString('newrank');
		$signature    = $this->app->input->getString('signature', '');
		$deleteSig    = $this->app->input->getInt('deleteSig');
		$moderator    = $this->app->input->getInt('moderator');
		$uid          = $this->app->input->getInt('uid');
		$deleteAvatar = $this->app->input->getInt('deleteAvatar');
		$neworder     = $this->app->input->getInt('neworder');
		$modCatids    = $moderator ? $this->app->input->get('catid', [], 'array') : [];
		$modCatids    = ArrayHelper::toInteger($modCatids);

		if ($uid)
		{
			$user = \KunenaFactory::getUser($uid);

			// Prepare variables
			if ($deleteSig == 1)
			{
				$user->signature = '';
			}
			else
			{
				$user->signature = $signature;
			}

			$user->personalText = $this->app->input->getString('personaltext', '');
			$birthdate          = $this->app->input->getString('birthdate');

			if ($birthdate)
			{
				$date = Factory::getDate($birthdate);

				$birthdate = $date->format('Y-m-d');
			}

			$user->birthdate = $birthdate;
			$user->location  = trim($this->app->input->getString('location', ''));
			$user->gender    = $this->app->input->getInt('gender', '');
			$this->cleanSocial($user, $this->app);
			$user->websitename  = $this->app->input->getString('websitename', '');
			$user->websiteurl   = $this->app->input->getString('websiteurl', '');
			$user->hideEmail    = $this->app->input->getInt('hidemail');
			$user->showOnline   = $this->app->input->getInt('showonline');
			$user->canSubscribe = $this->app->input->getInt('cansubscribe');
			$user->userListtime = $this->app->input->getInt('userlisttime');
			$user->socialshare  = $this->app->input->getInt('socialshare');
			$user->view         = $newview;
			$user->ordering     = $neworder;
			$user->rank         = $newrank;

			if ($deleteAvatar == 1)
			{
				$user->avatar = '';
			}

			if (!$user->save())
			{
				$this->app->enqueueMessage(Text::_('COM_KUNENA_USER_PROFILE_SAVED_FAILED'), 'error');
			}
			else
			{
				$this->app->enqueueMessage(Text::_('COM_KUNENA_USER_PROFILE_SAVED_SUCCESSFULLY'));
			}

			if (type == 'save')
			{
				$this->setModerate($user, $modCatids);
			}
			else
			{
				// Update moderator rights
				$categories = \KunenaForumCategoryHelper::getCategories(false, false, 'admin');

				foreach ($categories as $category)
				{
					$category->setModerator($user, in_array($category->id, $modCatids));
				}

				// Global moderator is a special case
				if (\KunenaUserHelper::getMyself()->isAdmin())
				{
					\KunenaAccess::getInstance()->setModerator(0, $user, in_array(0, $modCatids));
				}
			}
		}
	}

	/**
	 * Set moderator rights on the user given
	 *
	 * @param   \KunenaUser  $user      user
	 * @param   array       $modCatids modCatids
	 *
	 * @return  boolean
	 *
	 * @since   Kunena 5.1
	 *
	 * @throws  Exception
	 */
	protected function setModerate(\KunenaUser $user, $modCatids)
	{
		// Update moderator rights
		$categories = \KunenaForumCategoryHelper::getCategories(false, false, 'admin');

		foreach ($categories as $category)
		{
			$category->setModerator($user, in_array($category->id, $modCatids));
		}

		// Global moderator is a special case
		if (\KunenaUserHelper::getMyself()->isAdmin())
		{
			\KunenaAccess::getInstance()->setModerator(0, $user, in_array(0, $modCatids));
		}

		return true;
	}

	/**
	 * Clean social items
	 *
	 * @param   \KunenaUser  $user user
	 * @param   Factory     $app  app
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function cleanSocial(&$user, $app)
	{
		foreach ($user->socialButtons() as $key => $social)
		{
			$user->$key = str_replace(' ', '', trim($app->input->getString($key, '')));
		}
	}
}
