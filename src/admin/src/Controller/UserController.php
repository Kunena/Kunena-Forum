<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Controller;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

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
	 * @param   MVCFactoryInterface|null  $factory  The factory.
	 * @param   null                      $app      The CMSApplication for the dispatcher
	 * @param   null                      $input    Input
	 *
	 * @param   array                     $config   An optional associative array of configuration settings.
	 *
	 * @throws Exception
	 * @since   Kunena 2.0
	 *
	 * @see     BaseController
	 */
	public function __construct($config = [], MVCFactoryInterface $factory = null, $app = null, $input = null)
	{
		parent::__construct($config, $factory, $app, $input);

		$this->baseurl = 'administrator/index.php?option=com_kunena&view=users';
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   null  $key     key
	 * @param   null  $urlVar  url var
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 2.0
	 */
	public function save($key = null, $urlVar = null): void
	{
		if (!Session::checkToken())
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$this->saveInternal('save');

		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Internal method to save an user
	 *
	 * @param   string  $type  type
	 *
	 * @return  void
	 *
	 * @throws Exception
	 * @since   Kunena 6.0
	 */
	protected function saveInternal(string $type)
	{
		$newView      = $this->app->input->getString('newView');
		$newRank      = $this->app->input->getString('newRank');
		$signature    = $this->app->input->getString('signature');
		$deleteSig    = $this->app->input->getInt('deleteSig');
		$moderator    = $this->app->input->getInt('moderator');
		$uid          = $this->app->input->getInt('uid');
		$deleteAvatar = $this->app->input->getInt('deleteAvatar');
		$newOrder     = $this->app->input->getInt('newOrder');
		$modCatids    = $moderator ? $this->app->input->get('catid', [], 'array') : [];
		$modCatids    = ArrayHelper::toInteger($modCatids);
		KunenaFactory::loadLanguage('com_kunena.controllers', 'admin');

		if ($uid)
		{
			$user = KunenaFactory::getUser($uid);

			// Prepare variables
			if ($deleteSig === 1)
			{
				$user->signature = '';
			}
			else
			{
				$user->signature = $signature;
			}

			$user->personalText = $this->app->input->getString('personalText');
			$birthdate          = $this->app->input->getString('birthdate');

			if ($birthdate)
			{
				$date = Factory::getDate($birthdate);

				$birthdate = $date->format('Y-m-d');
			}

			$user->birthdate = $birthdate;
			$user->location  = trim($this->app->input->getString('location'));
			$user->gender    = $this->app->input->getInt('gender', '');
			$this->cleanSocial($user, $this->app);
			$user->websitename  = $this->app->input->getString('websitename');
			$user->websiteurl   = $this->app->input->getString('websiteurl');
			$user->hideEmail    = $this->app->input->getInt('hidemail');
			$user->showOnline   = $this->app->input->getInt('showonline');
			$user->canSubscribe = $this->app->input->getInt('cansubscribe');
			$user->userListtime = $this->app->input->getInt('userlisttime');
			$user->socialshare  = $this->app->input->getInt('socialshare');
			$user->view         = $newView;
			$user->ordering     = $newOrder;
			$user->rank         = $newRank;

			if ($deleteAvatar === 1)
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

			if ($type === 'save')
			{
				$this->setModerate($user, $modCatids);
			}
			else
			{
				// Update moderator rights
				$categories = KunenaCategoryHelper::getCategories(false, false, 'admin');

				foreach ($categories as $category)
				{
					$category->setModerator($user, \in_array($category->id, $modCatids, true));
				}

				// Global moderator is a special case
				if (KunenaUserHelper::getMyself()->isAdmin())
				{
					KunenaAccess::getInstance()->setModerator((object) [], $user, \in_array(0, $modCatids, true));
				}

				$this->setRedirect(KunenaRoute::_("administrator/index.php?option=com_kunena&view=user&layout=edit&userid={$uid}", false));
			}
		}
	}

	/**
	 * Clean social items
	 *
	 * @param   KunenaUser      $user  user
	 * @param   CMSApplication  $app   app
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function cleanSocial(KunenaUser $user, CMSApplication $app): void
	{
		foreach ($user->socialButtons() as $key => $social)
		{
			$user->$key = str_replace(' ', '', trim($app->input->getString($key, '')));
		}
	}

	/**
	 * Set moderator rights on the user given
	 *
	 * @param   KunenaUser  $user       KunenaUser object
	 * @param   array       $modCatids  modCatids
	 *
	 * @return  boolean
	 *
	 * @throws Exception
	 * @since   Kunena 5.1
	 */
	protected function setModerate(KunenaUser $user, array $modCatids): bool
	{
		// Update moderator rights
		$categories = KunenaCategoryHelper::getCategories(false, false, 'admin');

		foreach ($categories as $category)
		{
			$category->setModerator($user, \in_array($category->id, $modCatids));
		}

		// Global moderator is a special case
		if (KunenaUserHelper::getMyself()->isAdmin())
		{
			KunenaAccess::getInstance()->setModerator(0, $user, \in_array(0, $modCatids));
		}

		return true;
	}

	/**
	 * Apply
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   Kunena 2.0
	 */
	public function apply(): void
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');

			return;
		}

		$this->saveInternal('apply');
	}
}
