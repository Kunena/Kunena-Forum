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

use Exception;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Input\Input;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\User\User;
use Joomla\Utilities\ArrayHelper;
use Kunena\Forum\Libraries\Access\Access;
use Kunena\Forum\Libraries\Forum\Category\Category;
use Kunena\Forum\Libraries\Forum\Category\CategoryHelper;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Forum\Message\MessageHelper;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\Ban;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * Kunena Users Controller
 *
 * @since   Kunena 2.0
 */
class UsersController extends AdminController
{
	/**
	 * @var    string  The prefix to use with controller messages.
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_KUNENA_USERS';

	/**
	 * @var     null|string
	 * @since   Kunena 6.0
	 */
	protected $baseurl = null;

	/**
	 * Constructor.
	 *
	 * @see     BaseController
	 *
	 * @param   MVCFactoryInterface  $factory  The factory.
	 * @param   CMSApplication       $app      The CMSApplication for the dispatcher
	 * @param   Input                $input    Input
	 *
	 * @param   array                $config   An optional associative array of configuration settings.
	 *
	 * @since   Kunena 2.0
	 *
	 * @throws Exception
	 */
	public function __construct($config = array(), MVCFactoryInterface $factory = null, $app = null, $input = null)
	{
		parent::__construct($config, $factory, $app, $input);

		$this->baseurl = 'administrator/index.php?option=com_kunena&view=users';
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'User', $prefix = 'Administrator', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}

	/**
	 * Edit
	 *
	 * @return  boolean|void
	 *
	 * @since   Kunena 2.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function edit()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$cid    = $this->input->get('cid', [], 'array');
		$cid    = ArrayHelper::toInteger($cid, []);
		$userid = array_shift($cid);

		if ($userid <= 0)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_PROFILE_NO_USER'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$this->app->setUserState('kunena.user.userid', $userid);

		$this->setRedirect(Route::_("index.php?option=com_kunena&view=user&layout=edit&userid={$userid}", false));
	}

	/**
	 * Trash menu
	 *
	 * @return  void
	 *
	 * @since   Kunena 2.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function trashusermessages()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$cid = $this->input->get('cid', [], 'array');
		$cid = ArrayHelper::toInteger($cid, []);

		if ($cid)
		{
			foreach ($cid as $id)
			{
				list($total, $messages) = MessageHelper::getLatestMessages(false, 0, 0, ['starttime' => '-1', 'user' => $id]);

				foreach ($messages as $mes)
				{
					$mes->publish(KunenaForum::DELETED);
				}
			}
		}
		else
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_PROFILE_NO_USER'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$this->app->enqueueMessage(Text::_('COM_KUNENA_A_USERMES_TRASHED_DONE'));
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Move
	 *
	 * @return  void
	 *
	 * @since   Kunena 2.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function move()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$cid = $this->input->get('cid', [], 'array');
		$cid = ArrayHelper::toInteger($cid, []);

		if (empty($cid))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_PROFILE_NO_USER'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$this->app->setUserState('kunena.usermove.userids', $cid);

		$this->setRedirect(Route::_("index.php?option=com_kunena&view=user&layout=move", false));
	}

	/**
	 * Move Messages
	 *
	 * @return  void
	 *
	 * @since   Kunena 2.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function movemessages()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$catid = $this->app->input->getInt('catid');
		$uids  = (array) $this->app->getUserState('kunena.usermove.userids');

		$error = null;

		if ($uids)
		{
			foreach ($uids as $id)
			{
				list($total, $messages) = MessageHelper::getLatestMessages(false, 0, 0, ['starttime' => '-1', 'user' => $id]);

				foreach ($messages as $object)
				{
					$topic = $object->getTopic();

					if (!$object->isAuthorised('move'))
					{
						$error = $object->getError();
					}
					else
					{
						$target = CategoryHelper::get($catid);

						if (!$topic->move($target, false, false, '', false))
						{
							$error = $topic->getError();
						}
					}
				}
			}
		}
		else
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_PROFILE_NO_USER'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		if ($error)
		{
			$this->app->enqueueMessage($error, 'notice');
		}
		else
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_A_USERMES_MOVED_DONE'));
		}

		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Logout
	 *
	 * @return  void
	 *
	 * @since   Kunena 2.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function logout()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$cid = $this->input->get('cid', [], 'array');
		$cid = ArrayHelper::toInteger($cid, []);
		$id  = array_shift($cid);

		if ($id <= 0)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_PROFILE_NO_USER'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$options = ['clientid' => 0];
		$this->app->logout((int) $id, $options);

		$this->app->enqueueMessage(Text::_('COM_KUNENA_A_USER_LOGOUT_DONE'));
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Remove
	 *
	 * @return  void
	 *
	 * @since   Kunena 2.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function remove()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$cid = $this->input->get('cid', [], 'array');
		$cid = ArrayHelper::toInteger($cid, []);

		if (empty($cid))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_PROFILE_NO_USER'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$users = KunenaUserHelper::loadUsers($cid);

		$my        = Factory::getApplication()->getIdentity();
		$usernames = [];

		foreach ($users as $user)
		{
			if ($my->id == $user->userid)
			{
				$this->app->enqueueMessage(Text::_('COM_KUNENA_USER_ERROR_CANNOT_DELETE_YOURSELF'), 'notice');
				continue;
			}

			$instance = User::getInstance($user->userid);

			if ($instance->authorise('core.admin'))
			{
				$this->app->enqueueMessage(Text::_('COM_KUNENA_USER_ERROR_CANNOT_DELETE_ADMINS'), 'notice');
				continue;
			}

			$result = $user->delete();

			if (!$result)
			{
				$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_USER_DELETE_KUNENA_USER_TABLE_FAILED', $user->userid), 'notice');
				continue;
			}

			// Delete the user too from Joomla!
			$jresult = $instance->delete();

			if (!$jresult)
			{
				$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_USER_DELETE_JOOMLA_USER_TABLE_FAILED', $user->userid), 'notice');
				continue;
			}

			$usernames[] = $user->username;
		}

		if (!empty($usernames))
		{
			$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_USER_DELETE_DONE_SUCCESSFULLY', implode(', ', $usernames)));
		}

		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Ban
	 *
	 * @return  void
	 *
	 * @since   Kunena 2.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function ban()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$cid    = $this->input->get('cid', [], 'array');
		$cid    = ArrayHelper::toInteger($cid, []);
		$userid = array_shift($cid);

		if ($userid <= 0)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_PROFILE_NO_USER'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$ban = Ban::getInstanceByUserid($userid, true);

		if (!$ban->id)
		{
			$ban->ban($userid, null, 0);
			$success = $ban->save();
		}
		else
		{
			$now = new Date;
			$ban->setExpiration($now);
			$success = $ban->save();
		}

		$message = Text::_('COM_KUNENA_USER_BANNED_DONE');

		if (!$success)
		{
			$this->app->enqueueMessage($ban->getError(), 'error');
		}
		else
		{
			$this->app->enqueueMessage($message);
		}

		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Unban
	 *
	 * @return  void
	 *
	 * @since   Kunena 2.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function unban()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$cid    = $this->input->get('cid', [], 'array');
		$cid    = ArrayHelper::toInteger($cid, []);
		$userid = array_shift($cid);

		if ($userid <= 0)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_PROFILE_NO_USER'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$ban = Ban::getInstanceByUserid($userid, true);

		if (!$ban->id)
		{
			$ban->ban($userid, null, 0);
			$success = $ban->save();
		}
		else
		{
			$now = new Date;
			$ban->setExpiration($now);
			$success = $ban->save();
		}

		$message = Text::_('COM_KUNENA_USER_UNBAN_DONE');

		if (!$success)
		{
			$this->app->enqueueMessage($ban->getError(), 'error');
		}
		else
		{
			$this->app->enqueueMessage($message);
		}

		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Set an user as global moderator, works only if you are an admin
	 *
	 * @return  void
	 *
	 * @since   Kunena 5.1
	 *
	 * @throws  null
	 */
	public function moderate()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$modCatids = $this->app->input->get('catid', [], 'array');
		$modCatids = ArrayHelper::toInteger($modCatids);

		$cid    = $this->app->input->get('cid', [], 'array');
		$cid    = ArrayHelper::toInteger($cid);
		$userid = array_shift($cid);

		if ($userid <= 0)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_PROFILE_NO_USER'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		if ($userid <= 0)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_PROFILE_NO_USER'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$user = KunenaUserHelper::get($userid);

		$this->setModerate($user, $modCatids);

		$this->app->enqueueMessage(Text::_('COM_KUNENA_USER_MODERATE_DONE'));

		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Unmoderate
	 *
	 * @return  void
	 *
	 * @since   Kunena 5.1
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function unmoderate()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$cid    = $this->app->input->get('cid', [], 'array');
		$cid    = ArrayHelper::toInteger($cid);
		$userid = array_shift($cid);

		if ($userid <= 0)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_PROFILE_NO_USER'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$user     = KunenaUserHelper::get($userid);
		$category = null;

		if ($category instanceof Category)
		{
			$category = $category->id;
		}

		$category = intval($category);

		$usercategory = KunenaUserHelper::get($category, $user);

		if ($usercategory->role == 1)
		{
			$usercategory->role = false;

			if (!$usercategory->params)
			{
				$usercategory->params = '';
			}

			$success = $usercategory->save();

			// Clear role cache
			Access::getInstance()->clearCache();

			// Change user moderator status
			$moderator = Access::getInstance()->getModeratorStatus($user);

			if ($user->moderator != !empty($moderator))
			{
				$user->moderator = intval(!empty($moderator));
				$success         = $user->save();
			}
		}

		$message = Text::_('COM_KUNENA_USER_UNMODERATE_DONE');

		if (!$success)
		{
			$this->app->enqueueMessage($user->getError(), 'error');
		}
		else
		{
			$this->app->enqueueMessage($message);
		}

		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Block
	 *
	 * @return  void
	 *
	 * @since   Kunena 2.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function block()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$cid    = $this->input->get('cid', [], 'array');
		$cid    = ArrayHelper::toInteger($cid, []);
		$userid = array_shift($cid);

		if ($userid <= 0)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_PROFILE_NO_USER'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$ban = Ban::getInstanceByUserid($userid, true);

		if (!$ban->id)
		{
			$ban->ban($userid, null, 1);
			$success = $ban->save();
		}
		else
		{
			$now = new Date;
			$ban->setExpiration($now);
			$success = $ban->save();
		}

		$message = Text::_('COM_KUNENA_USER_BLOCKED_DONE');

		if (!$success)
		{
			$this->app->enqueueMessage($ban->getError(), 'error');
		}
		else
		{
			$this->app->enqueueMessage($message);
		}

		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Unblock
	 *
	 * @return  void
	 *
	 * @since   Kunena 2.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function unblock()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$cid    = $this->input->get('cid', [], 'array');
		$cid    = ArrayHelper::toInteger($cid, []);
		$userid = array_shift($cid);

		if ($userid <= 0)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_PROFILE_NO_USER'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$ban = Ban::getInstanceByUserid($userid, true);

		if (!$ban->id)
		{
			$ban->ban($userid, null, 1);
			$success = $ban->save();
		}
		else
		{
			$now = new Date;
			$ban->setExpiration($now);
			$success = $ban->save();
		}

		$message = Text::_('COM_KUNENA_USER_UNBLOCK_DONE');

		if (!$success)
		{
			$this->app->enqueueMessage($ban->getError(), 'error');
		}
		else
		{
			$this->app->enqueueMessage($message);
		}

		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Batch Moderators
	 *
	 * @return  void
	 *
	 * @since   Kunena 2.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function batch_moderators()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$cid    = $this->input->get('cid', [], 'array');
		$cid    = ArrayHelper::toInteger($cid, []);
		$catids = $this->input->get('catid', [], 'array');
		$catids = ArrayHelper::toInteger($catids, []);

		if (empty($cid))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_USERS_BATCH_NO_USERS_SELECTED'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		if (empty($catids))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_USERS_BATCH_NO_CATEGORIES_SELECTED'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		// Update moderator rights
		$categories = CategoryHelper::getCategories(false, false, 'admin');
		$users      = KunenaUserHelper::loadUsers($cid);

		foreach ($users as $user)
		{
			foreach ($categories as $category)
			{
				if (in_array($category->id, $catids))
				{
					$category->setModerator($user, true);
				}
			}

			// Global moderator is a special case
			if ($this->me->isAdmin() && in_array(0, $catids))
			{
				Access::getInstance()->setModerator(0, $user, true);
			}
		}

		$this->app->enqueueMessage(Text::_('COM_KUNENA_USERS_SET_MODERATORS_DONE'));
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Method to just redirect to main manager in case of use of cancel button
	 *
	 * @return  void
	 *
	 * @since   Kunena 4.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function cancel()
	{
		$this->app->redirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Remove categories subscriptions for the users selected
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function removecatsubscriptions()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$db  = Factory::getDbo();
		$cid = $this->input->get('cid', [], 'array');
		$cid = ArrayHelper::toInteger($cid, []);

		if (!empty($cid))
		{
			foreach ($cid as $userid)
			{
				$query = $db->getQuery(true);
				$query->update($db->quoteName('#__kunena_user_categories'))
					->set($db->quoteName('subscribed') . ' = 0')
					->where($db->quoteName('user_id') . ' = ' . $userid);
				$db->setQuery($query);

				try
				{
					$db->execute();
				}
				catch (Exception $e)
				{
					$e->getMessage();
				}
			}
		}

		$this->app->enqueueMessage(Text::_('COM_KUNENA_USERS_REMOVE_CAT_SUBSCRIPTIONS_DONE'));
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Remove topics subscriptions for the users selected
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function removetopicsubscriptions()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$db  = Factory::getDBO();
		$cid = $this->input->get('cid', [], 'array');
		$cid = ArrayHelper::toInteger($cid, []);

		if (!empty($cid))
		{
			foreach ($cid as $userid)
			{
				$query = $db->getQuery(true);
				$query->update($db->quoteName('#__kunena_user_topics'))
					->set($db->quoteName('subscribed') . ' = 0')
					->where($db->quoteName('user_id') . ' = ' . $userid);
				$db->setQuery($query);

				try
				{
					$db->execute();
				}
				catch (Exception $e)
				{
					$e->getMessage();
				}
			}
		}

		$this->app->enqueueMessage(Text::_('COM_KUNENA_USERS_REMOVE_TOPIC_SUBSCRIPTIONS_DONE'));
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Subscribe users to categories selected
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws  Exception
	 * @throws  null
	 */
	public function subscribeuserstocategories()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$userids = $this->app->input->get('cid', [], 'array');
		$userids = ArrayHelper::toInteger($userids);
		$catids  = $this->app->input->get('catid', [], 'array');
		$catids  = ArrayHelper::toInteger($catids);

		if (empty($userids))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_USERS_BATCH_NO_USERS_SELECTED'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		if (empty($catids))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_USERS_BATCH_NO_CATEGORIES_SELECTED'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$categories = CategoryHelper::getCategories($catids);

		foreach ($userids as $userid)
		{
			foreach ($categories as $category)
			{
				$category->subscribe(true, $userid);
			}
		}

		$this->app->enqueueMessage(Text::_('COM_KUNENA_USERS_ADD_CATEGORIES_SUBSCRIPTIONS_DONE'));
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}
}
