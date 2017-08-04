<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - 2017 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

/**
 * Kunena Users Controller
 *
 * @since  2.0
 */
class KunenaAdminControllerUsers extends KunenaController
{
	/**
	 * @var null|string
	 * @since Kunena
	 */
	protected $baseurl = null;

	/**
	 * Construct
	 *
	 * @param   array $config construct
	 *
	 * @since    2.0
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->baseurl = 'administrator/index.php?option=com_kunena&view=users';
	}

	/**
	 * Edit
	 *
	 * @throws Exception
	 *
	 * @return boolean|void
	 *
	 * @since    2.0
	 */
	public function edit()
	{
		if (!\Joomla\CMS\Session\Session::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$cid = \Joomla\CMS\Factory::getApplication()->input->get('cid', array(), 'post', 'array');
		Joomla\Utilities\ArrayHelper::toInteger($cid);
		$userid = array_shift($cid);

		if ($userid <= 0)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_PROFILE_NO_USER'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$this->app->setUserState('kunena.user.userid', $userid);

		$this->setRedirect(JRoute::_("index.php?option=com_kunena&view=user&layout=edit&userid={$userid}", false));
	}

	/**
	 * Save
	 *
	 * @throws Exception
	 *
	 * @return void
	 *
	 * @since    2.0
	 */
	public function save()
	{
		if (!\Joomla\CMS\Session\Session::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$newview      = \Joomla\CMS\Factory::getApplication()->input->getString('newview');
		$newrank      = \Joomla\CMS\Factory::getApplication()->input->getString('newrank');
		$signature    = \Joomla\CMS\Factory::getApplication()->input->getString('signature', '', 'POST', JREQUEST_ALLOWRAW);
		$deleteSig    = \Joomla\CMS\Factory::getApplication()->input->getInt('deleteSig');
		$moderator    = \Joomla\CMS\Factory::getApplication()->input->getInt('moderator');
		$uid          = \Joomla\CMS\Factory::getApplication()->input->getInt('uid');
		$deleteAvatar = \Joomla\CMS\Factory::getApplication()->input->getInt('deleteAvatar');
		$neworder     = \Joomla\CMS\Factory::getApplication()->input->getInt('neworder');
		$modCatids    = $moderator ? \Joomla\CMS\Factory::getApplication()->input->get('catid', array(), 'post', 'array') : array();
		Joomla\Utilities\ArrayHelper::toInteger($modCatids);

		if ($uid)
		{
			$user = KunenaFactory::getUser($uid);

			// Prepare variables
			if ($deleteSig == 1)
			{
				$user->signature = '';
			}
			else
			{
				$user->signature = $signature;
			}

			$user->personalText = \Joomla\CMS\Factory::getApplication()->input->getString('personaltext', '');
			$birthdate                = \Joomla\CMS\Factory::getApplication()->input->getString('birthdate');

			if ($birthdate)
			{
				$date = \Joomla\CMS\Factory::getDate($birthdate);

				$birthdate = $date->format('Y-m-d');
			}

			$user->birthdate   = $birthdate;
			$user->location    = trim(\Joomla\CMS\Factory::getApplication()->input->getString('location', ''));
			$user->gender      = \Joomla\CMS\Factory::getApplication()->input->getInt('gender', '');
			$user->icq         = trim(\Joomla\CMS\Factory::getApplication()->input->getString('icq', ''));
			$user->aim         = trim(\Joomla\CMS\Factory::getApplication()->input->getString('aim', ''));
			$user->yim         = trim(\Joomla\CMS\Factory::getApplication()->input->getString('yim', ''));
			$user->microsoft   = trim(\Joomla\CMS\Factory::getApplication()->input->getString('microsoft', ''));
			$user->skype       = trim(\Joomla\CMS\Factory::getApplication()->input->getString('skype', ''));
			$user->google      = trim(\Joomla\CMS\Factory::getApplication()->input->getString('google', ''));
			$user->twitter     = trim(\Joomla\CMS\Factory::getApplication()->input->getString('twitter', ''));
			$user->facebook    = trim(\Joomla\CMS\Factory::getApplication()->input->getString('facebook', ''));
			$user->myspace     = trim(\Joomla\CMS\Factory::getApplication()->input->getString('myspace', ''));
			$user->linkedin    = trim(\Joomla\CMS\Factory::getApplication()->input->getString('linkedin', ''));
			$user->delicious   = trim(\Joomla\CMS\Factory::getApplication()->input->getString('delicious', ''));
			$user->friendfeed  = trim(\Joomla\CMS\Factory::getApplication()->input->getString('friendfeed', ''));
			$user->digg        = trim(\Joomla\CMS\Factory::getApplication()->input->getString('digg', ''));
			$user->blogspot    = trim(\Joomla\CMS\Factory::getApplication()->input->getString('blogspot', ''));
			$user->flickr      = trim(\Joomla\CMS\Factory::getApplication()->input->getString('flickr', ''));
			$user->bebo        = trim(\Joomla\CMS\Factory::getApplication()->input->getString('bebo', ''));
			$user->instagram   = trim(\Joomla\CMS\Factory::getApplication()->input->getString('instagram', ''));
			$user->qq          = trim(\Joomla\CMS\Factory::getApplication()->input->getString('qq', ''));
			$user->qzone       = trim(\Joomla\CMS\Factory::getApplication()->input->getString('qzone', ''));
			$user->weibo       = trim(\Joomla\CMS\Factory::getApplication()->input->getString('weibo', ''));
			$user->wechat      = trim(\Joomla\CMS\Factory::getApplication()->input->getString('wechat', ''));
			$user->apple       = trim(\Joomla\CMS\Factory::getApplication()->input->getString('apple', ''));
			$user->vk          = trim(\Joomla\CMS\Factory::getApplication()->input->getString('vk', ''));
			$user->telegram    = trim(\Joomla\CMS\Factory::getApplication()->input->getString('telegram', ''));
			$user->whatsapp    = trim(\Joomla\CMS\Factory::getApplication()->input->getString('whatsapp', ''));
			$user->youtube     = trim(\Joomla\CMS\Factory::getApplication()->input->getString('youtube', ''));
			$user->ok          = trim(\Joomla\CMS\Factory::getApplication()->input->getString('ok', ''));
			$user->websitename = \Joomla\CMS\Factory::getApplication()->input->getString('websitename', '');
			$user->websiteurl  = \Joomla\CMS\Factory::getApplication()->input->getString('websiteurl', '');
			$user->hideEmail   = \Joomla\CMS\Factory::getApplication()->input->getString('hidemail');
			$user->showOnline  = \Joomla\CMS\Factory::getApplication()->input->getString('showonline');
			$user->cansubscribe  = \Joomla\CMS\Factory::getApplication()->input->getString('cansubscribe');
			$user->userlisttime  = \Joomla\CMS\Factory::getApplication()->input->getString('userlisttime');
			$user->view     = $newview;
			$user->ordering = $neworder;
			$user->rank     = $newrank;

			if ($deleteAvatar == 1)
			{
				$user->avatar = '';
			}

			if (!$user->save())
			{
				$this->app->enqueueMessage(JText::_('COM_KUNENA_USER_PROFILE_SAVED_FAILED'), 'error');
			}
			else
			{
				$this->app->enqueueMessage(JText::_('COM_KUNENA_USER_PROFILE_SAVED_SUCCESSFULLY'));
			}

			// Update moderator rights
			$categories = KunenaForumCategoryHelper::getCategories(false, false, 'admin');

			foreach ($categories as $category)
			{
				$category->setModerator($user, in_array($category->id, $modCatids));
			}

			// Global moderator is a special case
			if ($this->me->isAdmin())
			{
				KunenaAccess::getInstance()->setModerator(0, $user, in_array(0, $modCatids));
			}
		}

		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Apply
	 *
	 * @throws Exception
	 *
	 * @return void
	 *
	 * @since    2.0
	 */
	public function apply()
	{
		if (!\Joomla\CMS\Session\Session::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');

			return;
		}

		$newview      = \Joomla\CMS\Factory::getApplication()->input->getString('newview');
		$newrank      = \Joomla\CMS\Factory::getApplication()->input->getString('newrank');
		$signature    = \Joomla\CMS\Factory::getApplication()->input->getString('signature', '', 'POST', JREQUEST_ALLOWRAW);
		$deleteSig    = \Joomla\CMS\Factory::getApplication()->input->getInt('deleteSig');
		$moderator    = \Joomla\CMS\Factory::getApplication()->input->getInt('moderator');
		$uid          = \Joomla\CMS\Factory::getApplication()->input->getInt('uid');
		$deleteAvatar = \Joomla\CMS\Factory::getApplication()->input->getInt('deleteAvatar');
		$neworder     = \Joomla\CMS\Factory::getApplication()->input->getInt('neworder');
		$modCatids    = $moderator ? \Joomla\CMS\Factory::getApplication()->input->get('catid', array(), 'post', 'array') : array();
		Joomla\Utilities\ArrayHelper::toInteger($modCatids);

		if ($uid)
		{
			$user = KunenaFactory::getUser($uid);

			// Prepare variables
			if ($deleteSig == 1)
			{
				$user->signature = '';
			}
			else
			{
				$user->signature = $signature;
			}

			$user->personalText = \Joomla\CMS\Factory::getApplication()->input->getString('personaltext', '');
			$birthdate                = \Joomla\CMS\Factory::getApplication()->input->getString('birthdate');

			if ($birthdate)
			{
				$date = \Joomla\CMS\Factory::getDate($birthdate);

				$birthdate = $date->format('Y-m-d');
			}

			$user->birthdate   = $birthdate;
			$user->location    = trim(\Joomla\CMS\Factory::getApplication()->input->getString('location', ''));
			$user->gender      = \Joomla\CMS\Factory::getApplication()->input->getInt('gender', '');
			$user->icq         = trim(\Joomla\CMS\Factory::getApplication()->input->getString('icq', ''));
			$user->aim         = trim(\Joomla\CMS\Factory::getApplication()->input->getString('aim', ''));
			$user->yim         = trim(\Joomla\CMS\Factory::getApplication()->input->getString('yim', ''));
			$user->microsoft   = trim(\Joomla\CMS\Factory::getApplication()->input->getString('microsoft', ''));
			$user->skype       = trim(\Joomla\CMS\Factory::getApplication()->input->getString('skype', ''));
			$user->google      = trim(\Joomla\CMS\Factory::getApplication()->input->getString('google', ''));
			$user->twitter     = trim(\Joomla\CMS\Factory::getApplication()->input->getString('twitter', ''));
			$user->facebook    = trim(\Joomla\CMS\Factory::getApplication()->input->getString('facebook', ''));
			$user->myspace     = trim(\Joomla\CMS\Factory::getApplication()->input->getString('myspace', ''));
			$user->linkedin    = trim(\Joomla\CMS\Factory::getApplication()->input->getString('linkedin', ''));
			$user->delicious   = trim(\Joomla\CMS\Factory::getApplication()->input->getString('delicious', ''));
			$user->friendfeed  = trim(\Joomla\CMS\Factory::getApplication()->input->getString('friendfeed', ''));
			$user->digg        = trim(\Joomla\CMS\Factory::getApplication()->input->getString('digg', ''));
			$user->blogspot    = trim(\Joomla\CMS\Factory::getApplication()->input->getString('blogspot', ''));
			$user->flickr      = trim(\Joomla\CMS\Factory::getApplication()->input->getString('flickr', ''));
			$user->bebo        = trim(\Joomla\CMS\Factory::getApplication()->input->getString('bebo', ''));
			$user->instagram   = trim(\Joomla\CMS\Factory::getApplication()->input->getString('instagram', ''));
			$user->qq          = trim(\Joomla\CMS\Factory::getApplication()->input->getString('qq', ''));
			$user->qzone       = trim(\Joomla\CMS\Factory::getApplication()->input->getString('qzone', ''));
			$user->weibo       = trim(\Joomla\CMS\Factory::getApplication()->input->getString('weibo', ''));
			$user->wechat      = trim(\Joomla\CMS\Factory::getApplication()->input->getString('wechat', ''));
			$user->apple       = trim(\Joomla\CMS\Factory::getApplication()->input->getString('apple', ''));
			$user->vk          = trim(\Joomla\CMS\Factory::getApplication()->input->getString('vk', ''));
			$user->telegram    = trim(\Joomla\CMS\Factory::getApplication()->input->getString('telegram', ''));
			$user->whatsapp    = trim(\Joomla\CMS\Factory::getApplication()->input->getString('whatsapp', ''));
			$user->youtube     = trim(\Joomla\CMS\Factory::getApplication()->input->getString('youtube', ''));
			$user->ok          = trim(\Joomla\CMS\Factory::getApplication()->input->getString('ok', ''));
			$user->websitename = \Joomla\CMS\Factory::getApplication()->input->getString('websitename', '');
			$user->websiteurl  = \Joomla\CMS\Factory::getApplication()->input->getString('websiteurl', '');
			$user->hideEmail   = \Joomla\CMS\Factory::getApplication()->input->getString('hidemail');
			$user->showOnline  = \Joomla\CMS\Factory::getApplication()->input->getString('showonline');
			$user->cansubscribe  = \Joomla\CMS\Factory::getApplication()->input->getString('cansubscribe');
			$user->userlisttime  = \Joomla\CMS\Factory::getApplication()->input->getString('userlisttime');

			$user->view     = $newview;
			$user->ordering = $neworder;
			$user->rank     = $newrank;

			if ($deleteAvatar == 1)
			{
				$user->avatar = '';
			}

			if (!$user->save())
			{
				$this->app->enqueueMessage(JText::_('COM_KUNENA_USER_PROFILE_SAVED_FAILED'), 'error');
			}
			else
			{
				$this->app->enqueueMessage(JText::_('COM_KUNENA_USER_PROFILE_SAVED_SUCCESSFULLY'));
			}

			// Update moderator rights
			$categories = KunenaForumCategoryHelper::getCategories(false, false, 'admin');

			foreach ($categories as $category)
			{
				$category->setModerator($user, in_array($category->id, $modCatids));
			}

			// Global moderator is a special case
			if ($this->me->isAdmin())
			{
				KunenaAccess::getInstance()->setModerator(0, $user, in_array(0, $modCatids));
			}
		}
	}

	/**
	 * Trash menu
	 *
	 * @throws Exception
	 *
	 * @return void
	 *
	 * @since    2.0
	 */
	public function trashusermessages()
	{
		if (!\Joomla\CMS\Session\Session::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$cid = \Joomla\CMS\Factory::getApplication()->input->get('cid', array(), 'post', 'array');
		Joomla\Utilities\ArrayHelper::toInteger($cid);

		if ($cid)
		{
			foreach ($cid as $id)
			{
				list($total, $messages) = KunenaForumMessageHelper::getLatestMessages(false, 0, 0, array('starttime' => '-1', 'user' => $id));

				foreach ($messages as $mes)
				{
					$mes->publish(KunenaForum::DELETED);
				}
			}
		}
		else
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_PROFILE_NO_USER'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$this->app->enqueueMessage(JText::_('COM_KUNENA_A_USERMES_TRASHED_DONE'));
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Move
	 *
	 * @throws Exception
	 *
	 * @return  void
	 *
	 * @since    2.0
	 */
	public function move()
	{
		if (!\Joomla\CMS\Session\Session::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$cid = \Joomla\CMS\Factory::getApplication()->input->get('cid', array(), 'post', 'array');
		Joomla\Utilities\ArrayHelper::toInteger($cid);

		if (empty($cid))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_PROFILE_NO_USER'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$this->app->setUserState('kunena.usermove.userids', $cid);

		$this->setRedirect(JRoute::_("index.php?option=com_kunena&view=user&layout=move", false));
	}

	/**
	 * Move Messages
	 *
	 * @throws Exception
	 *
	 * @return void
	 *
	 * @since    2.0
	 */
	public function movemessages()
	{
		if (!\Joomla\CMS\Session\Session::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$catid = \Joomla\CMS\Factory::getApplication()->input->getInt('catid');
		$uids  = (array) $this->app->getUserState('kunena.usermove.userids');

		$error = null;

		if ($uids)
		{
			foreach ($uids as $id)
			{
				list($total, $messages) = KunenaForumMessageHelper::getLatestMessages(false, 0, 0, array('starttime' => '-1', 'user' => $id));

				foreach ($messages as $object)
				{
					$topic = $object->getTopic();

					if (!$object->authorise('move'))
					{
						$error = $object->getError();
					}
					else
					{
						$target = KunenaForumCategoryHelper::get($catid);

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
			$this->app->enqueueMessage(JText::_('COM_KUNENA_PROFILE_NO_USER'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		if ($error)
		{
			$this->app->enqueueMessage($error, 'notice');
		}
		else
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_A_USERMES_MOVED_DONE'));
		}

		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Logout
	 *
	 * @throws Exception
	 *
	 * @return void
	 *
	 * @since    2.0
	 */
	public function logout()
	{
		if (!\Joomla\CMS\Session\Session::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$cid = \Joomla\CMS\Factory::getApplication()->input->get('cid', array(), 'post', 'array');
		Joomla\Utilities\ArrayHelper::toInteger($cid);
		$id = array_shift($cid);

		if ($id <= 0)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_PROFILE_NO_USER'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$options = array('clientid' => 0);
		$this->app->logout((int) $id, $options);

		$this->app->enqueueMessage(JText::_('COM_KUNENA_A_USER_LOGOUT_DONE'));
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Remove
	 *
	 * @throws Exception
	 *
	 * @return void
	 *
	 * @since    2.0
	 */
	public function remove()
	{
		if (!\Joomla\CMS\Session\Session::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$cid = \Joomla\CMS\Factory::getApplication()->input->get('cid', array(), 'post', 'array');
		Joomla\Utilities\ArrayHelper::toInteger($cid);

		if (empty($cid))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_PROFILE_NO_USER'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$users = KunenaUserHelper::loadUsers($cid);

		$my        = \Joomla\CMS\Factory::getUser();
		$usernames = array();

		foreach ($users as $user)
		{
			if ($my->id == $user->userid)
			{
				$this->app->enqueueMessage(JText::_('COM_KUNENA_USER_ERROR_CANNOT_DELETE_YOURSELF'), 'notice');
				continue;
			}

			$instance = \Joomla\CMS\User\User::getInstance($user->userid);

			if ($instance->authorise('core.admin'))
			{
				$this->app->enqueueMessage(JText::_('COM_KUNENA_USER_ERROR_CANNOT_DELETE_ADMINS'), 'notice');
				continue;
			}

			$result = $user->delete();

			if (!$result)
			{
				$this->app->enqueueMessage(JText::sprintf('COM_KUNENA_USER_DELETE_KUNENA_USER_TABLE_FAILED', $user->userid), 'notice');
				continue;
			}

			// Delete the user too from Joomla!
			$jresult = $instance->delete();

			if (!$jresult)
			{
				$this->app->enqueueMessage(JText::sprintf('COM_KUNENA_USER_DELETE_JOOMLA_USER_TABLE_FAILED', $user->userid), 'notice');
				continue;
			}

			$usernames[] = $user->username;
		}

		if (!empty($usernames))
		{
			$this->app->enqueueMessage(JText::sprintf('COM_KUNENA_USER_DELETE_DONE_SUCCESSFULLY', implode(', ', $usernames)));
		}

		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Ban
	 *
	 * @throws Exception
	 *
	 * @return void
	 *
	 * @since    2.0
	 */
	public function ban()
	{
		if (!\Joomla\CMS\Session\Session::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$cid = \Joomla\CMS\Factory::getApplication()->input->get('cid', array(), 'post', 'array');
		Joomla\Utilities\ArrayHelper::toInteger($cid);
		$userid = array_shift($cid);

		if ($userid <= 0)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_PROFILE_NO_USER'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$ban = KunenaUserBan::getInstanceByUserid($userid, true);

		if (!$ban->id)
		{
			$ban->ban($userid, null, 0);
			$success = $ban->save();
		}
		else
		{
			jimport('joomla.utilities.date');
			$now = new \Joomla\CMS\Date\Date;
			$ban->setExpiration($now);
			$success = $ban->save();
		}

		$message = JText::_('COM_KUNENA_USER_BANNED_DONE');

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
	 * @throws Exception
	 *
	 * @return void
	 *
	 * @since    2.0
	 */
	public function unban()
	{
		if (!\Joomla\CMS\Session\Session::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$cid = \Joomla\CMS\Factory::getApplication()->input->get('cid', array(), 'post', 'array');
		Joomla\Utilities\ArrayHelper::toInteger($cid);
		$userid = array_shift($cid);

		if ($userid <= 0)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_PROFILE_NO_USER'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$ban = KunenaUserBan::getInstanceByUserid($userid, true);

		if (!$ban->id)
		{
			$ban->ban($userid, null, 0);
			$success = $ban->save();
		}
		else
		{
			jimport('joomla.utilities.date');
			$now = new \Joomla\CMS\Date\Date;
			$ban->setExpiration($now);
			$success = $ban->save();
		}

		$message = JText::_('COM_KUNENA_USER_UNBAN_DONE');

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
	 * Block
	 *
	 * @throws Exception
	 *
	 * @return void
	 *
	 * @since    2.0
	 */
	public function block()
	{
		if (!\Joomla\CMS\Session\Session::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$cid = \Joomla\CMS\Factory::getApplication()->input->get('cid', array(), 'post', 'array');
		Joomla\Utilities\ArrayHelper::toInteger($cid);
		$userid = array_shift($cid);

		if ($userid <= 0)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_PROFILE_NO_USER'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$ban = KunenaUserBan::getInstanceByUserid($userid, true);

		if (!$ban->id)
		{
			$ban->ban($userid, null, 1);
			$success = $ban->save();
		}
		else
		{
			jimport('joomla.utilities.date');
			$now = new \Joomla\CMS\Date\Date;
			$ban->setExpiration($now);
			$success = $ban->save();
		}

		$message = JText::_('COM_KUNENA_USER_BLOCKED_DONE');

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
	 * @throws Exception
	 *
	 * @return void
	 *
	 * @since    2.0
	 */
	public function unblock()
	{
		if (!\Joomla\CMS\Session\Session::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$cid = \Joomla\CMS\Factory::getApplication()->input->get('cid', array(), 'post', 'array');
		Joomla\Utilities\ArrayHelper::toInteger($cid);
		$userid = array_shift($cid);

		if ($userid <= 0)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_PROFILE_NO_USER'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$ban = KunenaUserBan::getInstanceByUserid($userid, true);

		if (!$ban->id)
		{
			$ban->ban($userid, null, 1);
			$success = $ban->save();
		}
		else
		{
			jimport('joomla.utilities.date');
			$now = new \Joomla\CMS\Date\Date;
			$ban->setExpiration($now);
			$success = $ban->save();
		}

		$message = JText::_('COM_KUNENA_USER_UNBLOCK_DONE');

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
	 * @throws Exception
	 *
	 * @return void
	 *
	 * @since    2.0
	 */
	public function batch_moderators()
	{
		if (!\Joomla\CMS\Session\Session::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$cid = \Joomla\CMS\Factory::getApplication()->input->get('cid', array(), 'post', 'array');
		Joomla\Utilities\ArrayHelper::toInteger($cid);
		$catids = \Joomla\CMS\Factory::getApplication()->input->get('catid', array(), 'post', 'array');
		Joomla\Utilities\ArrayHelper::toInteger($catids);

		if (empty($cid))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_USERS_BATCH_NO_USERS_SELECTED'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		if (empty($catids))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_USERS_BATCH_NO_CATEGORIES_SELECTED'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		// Update moderator rights
		$categories = KunenaForumCategoryHelper::getCategories(false, false, 'admin');
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
				KunenaAccess::getInstance()->setModerator(0, $user, true);
			}
		}

		$this->app->enqueueMessage(JText::_('COM_KUNENA_USERS_SET_MODERATORS_DONE'));
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Method to just redirect to main manager in case of use of cancel button
	 *
	 * @return void
	 *
	 * @since K4.0
	 */
	public function cancel()
	{
		$this->app->redirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Remove categories subscriptions for the users selected
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 * @since Kunena
	 */
	public function removecatsubscriptions()
	{
		if (!\Joomla\CMS\Session\Session::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$db  = \Joomla\CMS\Factory::getDbo();
		$cid = $this->app->input->get('cid', array(), 'array');

		if (!empty($cid))
		{
			foreach ($cid as $userid)
			{
				$query = $db->getQuery(true);
				$query->update($db->quoteName('#__kunena_user_categories'))->set($db->quoteName('subscribed') . ' = 0')->where($db->quoteName('user_id') . ' = ' . $userid);
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

		$this->app->enqueueMessage(JText::_('COM_KUNENA_USERS_REMOVE_CAT_SUBSCRIPTIONS_DONE'));
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Remove topics subscriptions for the users selected
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 * @since Kunena
	 */
	public function removetopicsubscriptions()
	{
		if (!\Joomla\CMS\Session\Session::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$db  = \Joomla\CMS\Factory::getDBO();
		$cid = $this->app->input->get('cid', array(), 'array');

		if (!empty($cid))
		{
			foreach ($cid as $userid)
			{
				$query = $db->getQuery(true);
				$query->update($db->quoteName('#__kunena_user_topics'))->set($db->quoteName('subscribed') . ' = 0')->where($db->quoteName('user_id') . ' = ' . $userid);
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

		$this->app->enqueueMessage(JText::_('COM_KUNENA_USERS_REMOVE_TOPIC_SUBSCRIPTIONS_DONE'));
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}
}
