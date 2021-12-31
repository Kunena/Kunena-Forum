<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Table\Table;

/**
 * Kunena User Controller
 *
 * @since  2.0
 */
class KunenaControllerUser extends KunenaController
{
	/**
	 * @param   bool  $cachable   cachable
	 * @param   bool  $urlparams  urlparams
	 *
	 * @return \Joomla\CMS\MVC\Controller\BaseController|void
	 * @since Kunena
	 * @throws Exception
	 * @throws null
	 */
	public function display($cachable = false, $urlparams = false)
	{
		// Redirect profile to integrated component if profile integration is turned on
		$redirect = 1;
		$active   = $this->app->getMenu()->getActive();

		if (!empty($active))
		{
			$params   = $active->params;
			$redirect = $params->get('integration', 1);
		}

		if ($redirect && $this->app->input->getCmd('format', 'html') == 'html')
		{
			$profileIntegration = KunenaFactory::getProfile();
			$layout             = $this->app->input->getCmd('layout', 'default');

			if ($profileIntegration instanceof KunenaProfileKunena)
			{
				// Continue
			}
			elseif ($layout == 'default')
			{
				$url = $this->me->getUrl(false);
			}
			elseif ($layout == 'list')
			{
				$url = $profileIntegration->getUserListURL('', false);
			}

			if (!empty($url))
			{
				$this->setRedirect($url);

				return;
			}
		}

		$layout = $this->app->input->getCmd('layout', 'default');

		if ($layout == 'list')
		{
			if (!KunenaFactory::getConfig()->userlist_allowed && Factory::getUser()->guest)
			{
				throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), '401');
			}
		}

		// Else the user does not exists.
		if (!$this->me)
		{
			throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_USER_UNKNOWN'), 404);
		}

		parent::display();
	}

	/**
	 * @since Kunena
	 * @throws Exception
	 * @throws null
	 */
	public function search()
	{
		$model = $this->getModel('user');

		$uri = new Joomla\CMS\Uri\Uri('index.php?option=com_kunena&view=user&layout=list');

		$state      = $model->getState();
		$search     = $state->get('list.search');
		$limitstart = $state->get('list.start');

		if ($search)
		{
			$uri->setVar('search', $search);
		}

		if ($limitstart)
		{
			$uri->setVar('limitstart', $search);
		}

		$this->setRedirect(KunenaRoute::_($uri, false));
	}

	/**
	 * @since Kunena
	 * @throws Exception
	 * @throws null
	 */
	public function change()
	{
		if (!Session::checkToken('get'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$layout = $this->app->input->getString('topic_layout', 'default');
		$this->me->setTopicLayout($layout);
		$this->setRedirectBack();
	}

	/**
	 * @since Kunena
	 * @throws Exception
	 * @throws null
	 */
	public function karmaup()
	{
		$this->karma(1);
	}

	/**
	 * @since Kunena
	 * @throws Exception
	 * @throws null
	 */
	public function karmadown()
	{
		$this->karma(-1);
	}

	/**
	 * @return array|null
	 * @since Kunena
	 * @throws Exception
	 */
	public function save()
	{
		$return = null;
		$errors = 0;
		$userid = $this->app->input->getInt('userid');

		if (!Session::checkToken('post'))
		{
			throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_ERROR_TOKEN'), 403);
		}

		// Check permission
		$moderator = KunenaUserHelper::getMyself()->isModerator();
		$my        = Factory::getUser();

		if (!$moderator)
		{
			if ($userid != $my->id)
			{
				throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_ERROR_TOKEN'), 403);
			}
		}

		// Make sure that the user exists.
		if (!$this->me->exists())
		{
			throw new KunenaExceptionAuthorise(Text::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'), 403);
		}

		if (!$userid)
		{
			$this->user = Factory::getUser();
		}
		else
		{
			$this->user = Factory::getUser($userid);
		}

		$success = $this->saveUser();

		if (!$success)
		{
			$errors++;
			$this->app->enqueueMessage(Text::_('COM_KUNENA_PROFILE_ACCOUNT_NOT_SAVED'), 'error');
		}

		// Save Kunena user.
		$this->saveProfile();
		$this->saveSettings();
		$success = $this->user->save();

		try
		{
			$success;
		}
		catch (\Exception $e)
		{
			$errors++;
			$this->app->enqueueMessage($e->getMessage(), 'error');
		}

		\Joomla\CMS\Plugin\PluginHelper::importPlugin('system');

		Factory::getApplication()->triggerEvent('OnAfterKunenaProfileUpdate', array($this->user, $success));

		if ($errors)
		{
			throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_PROFILE_SAVE_ERROR'), 500);
		}

		if ($this->user->userid == $this->me->userid)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_PROFILE_SAVED'));
			$edited_by_moderator = 0;
		}
		else
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_PROFILE_SAVED_BY_MODERATOR'));
			$edited_by_moderator = 1;
		}

		if ($this->config->log_moderation)
		{
			$log = KunenaLog::LOG_USER_EDIT;

			KunenaLog::log(
				KunenaLog::TYPE_ACTION,
				$log,
				array(
					'edited_by_moderator' => $edited_by_moderator,
				),
				null,
				null,
				$this->user
			);
		}

		if ($return)
		{
			return $return;
		}
	}

	/**
	 * @since Kunena
	 * @throws Exception
	 * @throws null
	 */
	public function ban()
	{
		$user = KunenaFactory::getUser($this->app->input->getInt('userid', 0));

		if (!$user->exists() || !Session::checkToken('post'))
		{
			$this->setRedirect($user->getUrl(false), Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');

			return;
		}

		$ban = KunenaUserBan::getInstanceByUserid($user->userid, true);

		try
		{
			$ban->canBan();
		}
		catch (Exception $e)
		{
			$this->setRedirect($user->getUrl(false), $e->getMessage(), 'error');

			return;
		}

		$ip             = $this->app->input->getString('ip', '');
		$banlevel       = $this->app->input->getInt('banlevel', 0);
		$expiration     = $this->app->input->getString('expiration', '');
		$reason_private = $this->app->input->getString('reason_private', '');
		$reason_public  = $this->app->input->getString('reason_public', '');
		$comment        = $this->app->input->getString('comment', '');

		$banDelPosts     = $this->app->input->getString('bandelposts', '');
		$banDelPostsPerm = $this->app->input->getString('bandelpostsperm', '');
		$DelAvatar       = $this->app->input->getString('delavatar', '');
		$DelSignature    = $this->app->input->getString('delsignature', '');
		$DelProfileInfo  = $this->app->input->getString('delprofileinfo', '');

		$delban = $this->app->input->getString('delban', '');

		if (!$ban->id)
		{
			$ban->ban($user->userid, $ip, $banlevel, $expiration, $reason_private, $reason_public, $comment);
			$success = $ban->save();

			// Send report to stopforumspam
			$this->report($user->userid);
		}
		else
		{
			if ($delban)
			{
				$ban->unBan($comment);
				$success = $ban->save();
			}
			else
			{
				$ban->blocked = $banlevel;
				$ban->setExpiration($expiration, $comment);
				$ban->setReason($reason_public, $reason_private);
				$success = $ban->save();
			}
		}

		if ($banlevel)
		{
			if ($ban->isEnabled())
			{
				$this->app->logout($user->userid);
				$message = Text::_('COM_KUNENA_USER_BLOCKED_DONE');
				$log     = KunenaLog::LOG_USER_BLOCK;
			}
			else
			{
				$message = Text::_('COM_KUNENA_USER_UNBLOCKED_DONE');
				$log     = KunenaLog::LOG_USER_UNBLOCK;
			}
		}
		else
		{
			if ($ban->isEnabled())
			{
				$message = Text::_('COM_KUNENA_USER_BANNED_DONE');
				$log     = KunenaLog::LOG_USER_BAN;
			}
			else
			{
				$message = Text::_('COM_KUNENA_USER_UNBANNED_DONE');
				$log     = KunenaLog::LOG_USER_UNBAN;
			}
		}

		try
		{
			$success;
		}
		catch (\Exception $e)
		{
			$this->app->enqueueMessage($e->getMessage(), 'error');
		}

		if ($success)
		{
			if ($this->config->log_moderation)
			{
				KunenaLog::log(
					KunenaLog::TYPE_MODERATION,
					$log,
					array(
						'expiration'     => $delban ? 'NOW' : $expiration,
						'reason_private' => $reason_private,
						'reason_public'  => $reason_public,
						'comment'        => $comment,
						'options'        => array(
							'resetProfile'   => (bool) $DelProfileInfo,
							'resetSignature' => (bool) $DelSignature || $DelProfileInfo,
							'deleteAvatar'   => (bool) $DelAvatar || $DelProfileInfo,
							'deletePosts'    => (bool) $banDelPosts,
						),
					),
					null,
					null,
					$user
				);

				KunenaUserHelper::recountBanned();
			}

			$this->app->enqueueMessage($message);
		}

		if (!empty($DelAvatar) || !empty($DelProfileInfo))
		{
			$avatar_deleted = '';

			// Delete avatar from file system
			if (is_file(JPATH_ROOT . '/media/kunena/avatars/' . $user->avatar) && !stristr($user->avatar, 'gallery/'))
			{
				KunenaFile::delete(JPATH_ROOT . '/media/kunena/avatars/' . $user->avatar);
				$avatar_deleted = Text::_('COM_KUNENA_MODERATE_DELETED_BAD_AVATAR_FILESYSTEM');
			}

			$user->avatar = '';
			$user->save();
			$this->app->enqueueMessage(Text::_('COM_KUNENA_MODERATE_DELETED_BAD_AVATAR') . $avatar_deleted);
		}

		if (!empty($DelProfileInfo))
		{
			$user->personalText     = '';
			$user->birthdate        = '1000-01-01';
			$user->location         = '';
			$user->gender           = 0;
			$user->icq              = '';
			$user->yim              = '';
			$user->microsoft        = '';
			$user->skype            = '';
			$user->google           = '';
			$user->twitter          = '';
			$user->facebook         = '';
			$user->myspace          = '';
			$user->linkedin         = '';
			$user->linkedin_company = '';
			$user->delicious        = '';
			$user->friendfeed       = '';
			$user->digg             = '';
			$user->blogspot         = '';
			$user->flickr           = '';
			$user->bebo             = '';
			$user->instagram        = '';
			$user->qqsocial         = '';
			$user->qzone            = '';
			$user->whatsapp         = '';
			$user->youtube          = '';
			$user->ok               = '';
			$user->weibo            = '';
			$user->wechat           = '';
			$user->apple            = '';
			$user->vk               = '';
			$user->telegram         = '';
			$user->websitename      = '';
			$user->websiteurl       = '';
			$user->signature        = '';
			$user->save();
			$this->app->enqueueMessage(Text::_('COM_KUNENA_MODERATE_DELETED_BAD_PROFILEINFO'));
		}
		elseif (!empty($DelSignature))
		{
			$user->signature = '';
			$user->save();
			$this->app->enqueueMessage(Text::_('COM_KUNENA_MODERATE_DELETED_BAD_SIGNATURE'));
		}

		if (!empty($banDelPosts))
		{
			$params = array('starttime' => '-1', 'nolimit' => -1, 'user' => $user->userid, 'mode' => 'unapproved');

			list($total, $messages) = KunenaForumMessageHelper::getLatestMessages(false, 0, 0, $params);

			$parmas_recent = array('starttime' => '-1', 'nolimit' => -1, 'user' => $user->userid);

			list($total, $messages_recent) = KunenaForumMessageHelper::getLatestMessages(false, 0, 0, $parmas_recent);

			$messages = array_merge($messages_recent, $messages);

			foreach ($messages as $mes)
			{
				$mes->publish(KunenaForum::DELETED);
			}

			$this->app->enqueueMessage(Text::_('COM_KUNENA_MODERATE_DELETED_BAD_MESSAGES'));
		}

		if (!empty($banDelPostsPerm))
		{
			$params = array('starttime' => '-1', 'nolimit' => -1, 'user' => $user->userid, 'mode' => 'unapproved');

			list($total, $messages) = KunenaForumMessageHelper::getLatestMessages(false, 0, 0, $params);

			$parmas_recent = array('starttime' => '-1', 'nolimit' => -1, 'user' => $user->userid);

			list($total, $messages_recent) = KunenaForumMessageHelper::getLatestMessages(false, 0, 0, $parmas_recent);

			$messages = array_merge($messages_recent, $messages);

			foreach ($messages as $mes)
			{
				$mes->delete();
			}

			$this->app->enqueueMessage(Text::_('COM_KUNENA_MODERATE_DELETED_PERM_BAD_MESSAGES'));
		}

		$this->setRedirect($user->getUrl(false));
	}

	/**
	 * @since Kunena
	 * @throws Exception
	 */
	public function cancel()
	{
		$user = KunenaFactory::getUser();
		$this->setRedirect($user->getUrl(false));
	}

	/**
	 * @since Kunena
	 * @throws Exception
	 * @throws null
	 */
	public function login()
	{
		if (!Factory::getUser()->guest || !Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$input  = $this->app->input;
		$method = $input->getMethod();

		$username  = $input->$method->get('username', '', 'USERNAME');
		$password  = $input->$method->get('password', '', 'RAW');
		$remember  = $this->input->getBool('remember', false);
		$secretkey = $input->$method->get('secretkey', '', 'RAW');

		$login = KunenaLogin::getInstance();
		$error = $login->loginUser($username, $password, $remember, $secretkey);

		// Get the return url from the request and validate that it is internal.
		$return = base64_decode($input->post->get('return', '', 'BASE64'));

		if (!$error && $return && Uri::isInternal($return))
		{
			// Redirect the user.
			$this->setRedirect(Route::_($return, false));

			return;
		}

		$this->setRedirectBack();
	}

	/**
	 * @since Kunena
	 * @throws Exception
	 * @throws null
	 */
	public function logout()
	{
		if (!Session::checkToken('request'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$login = KunenaLogin::getInstance();

		if (!Factory::getUser()->guest)
		{
			$login->logoutUser();
		}

		// Get the return url from the request and validate that it is internal.
		$return = base64_decode($this->app->input->getBase64('return'));

		if ($return && Uri::isInternal($return))
		{
			// Redirect the user.
			$this->setRedirect(Route::_($return, false));

			return;
		}

		$this->setRedirectBack();
	}

	/**
	 * Save online status for user
	 *
	 * @return void
	 * @since Kunena
	 * @throws Exception
	 * @throws null
	 */
	public function status()
	{
		if (!Session::checkToken('request'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$status     = $this->app->input->getInt('status', 0);
		$me         = KunenaUserHelper::getMyself();
		$me->status = $status;

		try
		{
			$me->save();
		}
		catch (\Exception $e)
		{
			$this->app->enqueueMessage($e->getMessage(), 'error');
		}

		if ($me->save())
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_STATUS_SAVED'));
		}

		$this->setRedirectBack();
	}

	/**
	 * Set online status text for user
	 *
	 * @return void
	 * @since Kunena
	 * @throws Exception
	 * @throws null
	 */
	public function statusText()
	{
		if (!Session::checkToken('request'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$status_text     = $this->app->input->post->getString('status_text', null);
		$me              = KunenaUserHelper::getMyself();
		$me->status_text = $status_text;

		try
		{
			$me->save();
		}
		catch (\Exception $e)
		{
			$this->app->enqueueMessage($e->getMessage(), 'error');
		}

		if ($me->save())
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_STATUS_SAVED'));
		}

		$this->setRedirectBack();
	}

	// Internal functions:

	/**
	 * @param $karmaDelta
	 *
	 * @since Kunena
	 * @throws Exception
	 * @throws null
	 */
	protected function karma($karmaDelta)
	{
		if (!Session::checkToken('get'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		// 14400 seconds = 6 hours
		$karma_delay = '14400';

		$userid = $this->app->input->getInt('userid', 0);

		$target = KunenaFactory::getUser($userid);

		if (!$this->config->showkarma || !$this->me->exists() || !$target->exists() || $karmaDelta == 0)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_USER_ERROR_KARMA'), 'error');
			$this->setRedirectBack();

			return;
		}

		$now = Factory::getDate()->toUnix();

		if ($this->me->karma_time!==0)
		{
			if (!$this->me->isModerator() && $now - $this->me->karma_time < $karma_delay)
			{
				$this->app->enqueueMessage(Text::_('COM_KUNENA_KARMA_WAIT'), 'notice');
				$this->setRedirectBack();

				return;
			}
		}

		if ($karmaDelta > 0)
		{
			if ($this->me->userid == $target->userid)
			{
				$this->app->enqueueMessage(Text::_('COM_KUNENA_KARMA_SELF_INCREASE'), 'notice');
				$karmaDelta = -10;
			}
			else
			{
				$this->app->enqueueMessage(Text::_('COM_KUNENA_KARMA_INCREASED'));
			}
		}
		else
		{
			if ($this->me->userid == $target->userid)
			{
				$this->app->enqueueMessage(Text::_('COM_KUNENA_KARMA_SELF_DECREASE'), 'notice');
			}
			else
			{
				$this->app->enqueueMessage(Text::_('COM_KUNENA_KARMA_DECREASED'));
			}
		}

		$this->me->karma_time = $now;

		if ($this->me->userid != $target->userid && !$this->me->save())
		{
			$this->app->enqueueMessage($this->me->getError(), 'notice');
			$this->setRedirectBack();

			return;
		}

		$target->karma += $karmaDelta;

		try
		{
			$target->save();
		}
		catch (\Exception $e)
		{
			$this->app->enqueueMessage($e->getMessage(), 'notice');
			$this->setRedirectBack();

			return;
		}

		// Activity integration
		$activity = KunenaFactory::getActivityIntegration();
		$activity->onAfterKarma($target->userid, $this->me->userid, $karmaDelta);
		$this->setRedirectBack();
	}

	/**
	 * @return boolean
	 *
	 * @since Kunena
	 * @throws Exception
	 */
	protected function saveUser()
	{
		// We only allow users to edit few fields
		$allow = array('name', 'email', 'password', 'password2', 'params');

		if (\Joomla\CMS\Component\ComponentHelper::getParams('com_users')->get('change_login_name', 1))
		{
			$allow[] = 'username';
		}

		// Clean request
		$post           = $this->app->input->post->getArray();
		$post_password  = $this->app->input->post->get('password', '', 'raw');
		$post_password2 = $this->app->input->post->get('password2', '', 'raw');

		if (empty($post_password) || empty($post_password2))
		{
			unset($post['password'], $post['password2']);
		}
		else
		{
			// If we have parameters from com_users, use those instead.
			// Some of these may be empty for legacy reasons.
			$params = ComponentHelper::getParams('com_users');

			// Do a password safety check.
			if ($post_password != $post_password2)
			{
				$this->app->enqueueMessage(Text::_('COM_KUNENA_PROFILE_PASSWORD_MISMATCH'), 'notice');

				return false;
			}

			if (strlen($post_password) < $params->get('minimum_length'))
			{
				$this->app->enqueueMessage(Text::_('COM_KUNENA_PROFILE_PASSWORD_NOT_MINIMUM'), 'notice');

				return false;
			}

			$value = $post_password;

			if (!empty($params))
			{
				$minimumLengthp    = $params->get('minimum_length');
				$minimumIntegersp  = $params->get('minimum_integers');
				$minimumSymbolsp   = $params->get('minimum_symbols');
				$minimumUppercasep = $params->get('minimum_uppercase');
				$meterp            = $params->get('meter');
				$thresholdp        = $params->get('threshold');

				empty($minimumLengthp) ?: $minimumLength = (int) $minimumLengthp;
				empty($minimumIntegersp) ?: $minimumIntegers = (int) $minimumIntegersp;
				empty($minimumSymbolsp) ?: $minimumSymbols = (int) $minimumSymbolsp;
				empty($minimumUppercasep) ?: $minimumUppercase = (int) $minimumUppercasep;
				empty($meterp) ?: $meter = $meterp;
				empty($thresholdp) ?: $threshold = $thresholdp;
			}

			// If the field is empty and not required, the field is valid.
			$valueLength = strlen($value);

			// Load language file of com_users component
			Factory::getLanguage()->load('com_users');

			// We set a maximum length to prevent abuse since it is unfiltered.
			if ($valueLength > 4096)
			{
				$this->app->enqueueMessage(Text::_('COM_USERS_MSG_PASSWORD_TOO_LONG'), 'warning');
			}

			// We don't allow white space inside passwords
			$valueTrim = trim($value);

			// Set a variable to check if any errors are made in password
			$validPassword = true;

			if (strlen($valueTrim) != $valueLength)
			{
				$this->app->enqueueMessage(
					Text::_('COM_USERS_MSG_SPACES_IN_PASSWORD'),
					'warning'
				);

				$validPassword = false;
			}

			// Minimum number of integers required
			if (!empty($minimumIntegers))
			{
				$nInts = preg_match_all('/[0-9]/', $value, $imatch);

				if ($nInts < $minimumIntegers)
				{
					$this->app->enqueueMessage(
						Text::plural('COM_USERS_MSG_NOT_ENOUGH_INTEGERS_N', $minimumIntegers),
						'warning'
					);

					$validPassword = false;
				}
			}

			// Minimum number of symbols required
			if (!empty($minimumSymbols))
			{
				$nsymbols = preg_match_all('[\W]', $value, $smatch);

				if ($nsymbols < $minimumSymbols)
				{
					$this->app->enqueueMessage(
						Text::plural('COM_USERS_MSG_NOT_ENOUGH_SYMBOLS_N', $minimumSymbols),
						'warning'
					);

					$validPassword = false;
				}
			}

			// Minimum number of upper case ASCII characters required
			if (!empty($minimumUppercase))
			{
				$nUppercase = preg_match_all('/[A-Z]/', $value, $umatch);

				if ($nUppercase < $minimumUppercase)
				{
					$this->app->enqueueMessage(
						Text::plural('COM_USERS_MSG_NOT_ENOUGH_UPPERCASE_LETTERS_N', $minimumUppercase),
						'warning'
					);

					$validPassword = false;
				}
			}

			// Minimum length option
			if (!empty($minimumLength))
			{
				if (strlen((string) $value) < $minimumLength)
				{
					$this->app->enqueueMessage(
						Text::plural('COM_USERS_MSG_PASSWORD_TOO_SHORT_N', $minimumLength),
						'warning'
					);

					$validPassword = false;
				}
			}

			// If valid has violated any rules above return false.
			if (!$validPassword)
			{
				return false;
			}
		}

		$post = array_intersect_key($post, array_flip($allow));

		if (empty($post))
		{
			return true;
		}

		$username = $this->user->get('username');
		$user     = new Joomla\CMS\User\User($this->user->id);

		// Bind the form fields to the user table and save.
		if (!$user->bind($post) || !$user->save(true))
		{
			Factory::getApplication()->enqueueMessage($user->getError(), 'error');

			return false;
		}

		// Reload the user.
		if (KunenaUserHelper::getMyself()->userid == $this->user->id)
		{
			$this->user->load($this->user->id);
			$session = Factory::getSession();
			$session->set('user', $this->user);

			// Update session if username has been changed
			if ($username && $username != $this->user->username)
			{
				$table = Table::getInstance('session', 'JTable');
				$table->load($session->getId());

				$table->username = $this->user->username;
				$table->store();
			}
		}

		return true;
	}

	protected function saveProfile()
	{
		$input  = $this->app->input;
		$method = $input->getMethod();
		$user   = KunenaFactory::getUser($input->$method->get('userid', 0, 'int'));

		if ($this->config->signature)
		{
			$signature = $input->$method->get('signature', '', 'raw');

			if ($this->me->checkUserAllowedLinksImages() && $signature != $user->signature)
			{
				$signature = preg_replace('/\[url=(.*?)\](.*?)\[\/url\]/su', '', $signature);
				$signature = preg_replace('/\[img=(.*?)\](.*?)\[\/img\]/su', '', $signature);

				// When the bbcode urls and images are removed just remove the others links
				$signature = preg_replace('/(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)(#?[\w \.-]*)(\??[\w \.-]*)(\=?[\w \.-]*)/i', '', $signature);

				$this->app->enqueueMessage(Text::_('COM_KUNENA_PROFILE_SAVED_WITHOUT_LINKS_IMAGES'));
			}

			$user->signature = $signature;
		}

		$user->personalText = $input->$method->get('personaltext', '', 'string');
		$birthdate          = $input->$method->get('birthdate', '', 'string');

		if ($birthdate)
		{
			if ($birthdate == '11/30/-0001')
			{
				$birthdate = '1901/01/01';
			}

			$date = Factory::getDate($birthdate);

			$birthdate = $date->format('Y-m-d');
		}

		$user->birthdate        = $birthdate;
		$user->location         = trim($input->$method->get('location', '', 'string'));
		$user->gender           = $input->$method->get('gender', 0, 'int');
		$user->icq              = str_replace(' ', '', trim($input->$method->get('icq', '', 'string')));
		$user->yim              = str_replace(' ', '', trim($input->$method->get('yim', '', 'string')));
		$user->microsoft        = str_replace(' ', '', trim($input->$method->get('microsoft', '', 'string')));
		$user->skype            = str_replace(' ', '', trim($input->$method->get('skype', '', 'string')));
		$user->google           = str_replace(' ', '', trim($input->$method->get('google', '', 'string')));
		$user->twitter          = str_replace(' ', '', trim($input->$method->get('twitter', '', 'string')));
		$user->facebook         = str_replace(' ', '', trim($input->$method->get('facebook', '', 'string')));
		$user->myspace          = str_replace(' ', '', trim($input->$method->get('myspace', '', 'string')));
		$user->linkedin         = str_replace(' ', '', trim($input->$method->get('linkedin', '', 'string')));
		$user->linkedin_company = str_replace(' ', '', trim($input->$method->get('linkedin_company', '', 'string')));
		$user->delicious        = str_replace(' ', '', trim($input->$method->get('delicious', '', 'string')));
		$user->friendfeed       = str_replace(' ', '', trim($input->$method->get('friendfeed', '', 'string')));
		$user->digg             = str_replace(' ', '', trim($input->$method->get('digg', '', 'string')));
		$user->blogspot         = str_replace(' ', '', trim($input->$method->get('blogspot', '', 'string')));
		$user->flickr           = str_replace(' ', '', trim($input->$method->get('flickr', '', 'string')));
		$user->bebo             = str_replace(' ', '', trim($input->$method->get('bebo', '', 'string')));
		$user->instagram        = str_replace(' ', '', trim($input->$method->get('instagram', '', 'string')));
		$user->qqsocial         = str_replace(' ', '', trim($input->$method->get('qqsocial', '', 'string')));
		$user->qzone            = str_replace(' ', '', trim($input->$method->get('qzone', '', 'string')));
		$user->weibo            = str_replace(' ', '', trim($input->$method->get('weibo', '', 'string')));
		$user->wechat           = str_replace(' ', '', trim($input->$method->get('wechat', '', 'string')));
		$user->apple            = str_replace(' ', '', trim($input->$method->get('apple', '', 'string')));
		$user->vk               = str_replace(' ', '', trim($input->$method->get('vk', '', 'string')));
		$user->whatsapp         = str_replace(' ', '', trim($input->$method->get('whatsapp', '', 'string')));
		$user->telegram         = str_replace(' ', '', trim($input->$method->get('telegram', '', 'string')));
		$user->youtube          = str_replace(' ', '', trim($input->$method->get('youtube', '', 'string')));
		$user->ok               = str_replace(' ', '', trim($input->$method->get('ok', '', 'string')));
		$user->websitename      = $input->$method->get('websitename', '', 'string');
		$user->websiteurl       = $input->$method->get('websiteurl', '', 'string');

		// Save avatar from gallery
		$avatar_gallery = $input->$method->get('avatar_gallery', '', 'string');

		if (!empty($avatar_gallery))
		{
			$user->avatar = $avatar_gallery;
		}
	}

	/**
	 * Delete previoulsy uplaoded avatars from filesystem
	 *
	 * @return void
	 * @since Kunena
	 * @throws Exception
	 */
	protected function deleteOldAvatars()
	{
		$user = KunenaFactory::getUser($this->app->input->getInt('userid', 0));

		if (preg_match('|^users/|', $user->avatar))
		{
			// Delete old uploaded avatars:
			if (is_dir(KPATH_MEDIA . '/avatars/resized'))
			{
				$deletelist = KunenaFolder::folders(KPATH_MEDIA . '/avatars/resized', '.', false, true);

				foreach ($deletelist as $delete)
				{
					if (is_file($delete . '/' . $user->avatar))
					{
						KunenaFile::delete($delete . '/' . $user->avatar);
					}
				}
			}

			if (is_file(KPATH_MEDIA . '/avatars/' . $user->avatar))
			{
				KunenaFile::delete(KPATH_MEDIA . '/avatars/' . $user->avatar);
			}
		}
	}

	/**
	 * Upload avatar with AJAX.
	 *
	 * @since 5.1
	 * @throws null
	 */
	public function upload()
	{
		// Only support JSON requests.
		if ($this->input->getWord('format', 'html') != 'json')
		{
			throw new RuntimeException(Text::_('Bad Request'), 400);
		}

		$upload = KunenaUpload::getInstance();
		$user   = KunenaFactory::getUser($this->app->input->getInt('userid', 0));

		// We are converting all exceptions into JSON.
		try
		{
			$caption = $this->input->getString('caption');
			$options = array(
				'filename'   => $this->input->getString('filename'),
				'size'       => $this->input->getInt('size'),
				'mime'       => $this->input->getString('mime'),
				'hash'       => $this->input->getString('hash'),
				'chunkStart' => $this->input->getInt('chunkStart', 0),
				'chunkEnd'   => $this->input->getInt('chunkEnd', 0),
				'image_type' => 'avatar',
			);

			// Upload!
			$this->config->avatartypes = strtolower($this->config->avatartypes);
			$upload->addExtensions(explode(',', $this->config->avatartypes));
			$response = (object) $upload->ajaxUpload($options);

			if (!empty($response->completed))
			{
				$this->deleteOldAvatars();

				// We have it all, lets update the avatar in user table
				$uploadFile = $upload->getProtectedFile();
				list($basename, $extension) = $upload->splitFilename();

				KunenaFile::copy($uploadFile, KPATH_MEDIA . '/avatars/users/avatar' . $user->userid . '.' . $extension);

				KunenaPath::setPermissions(KPATH_MEDIA . '/avatars/users/avatar' . $user->userid . '.' . $extension);

				// Save in the table KunenaUser
				$kuser            = $user;
				$kuser->avatar    = 'users/avatar' . $user->userid . '.' . $extension;
				$kuser->timestamp = round(microtime(true));
				$kuser->save();
			}
		}
		catch (Exception $response)
		{
			$upload->cleanup();

			// Use the exception as the response.
		}

		header('Content-type: application/json');
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");

		if (ob_get_length())
		{
			ob_end_clean();
		}

		echo $upload->ajaxResponse($response);

		jexit();
	}

	/**
	 * Remove avatar with AJAX
	 *
	 * @since 5.1
	 * @throws Exception
	 */
	public function removeAvatar()
	{
		// Only support JSON requests.
		if ($this->input->getWord('format', 'html') != 'json')
		{
			throw new RuntimeException(Text::_('Bad Request'), 400);
		}

		if (!Session::checkToken('request'))
		{
			throw new RuntimeException(Text::_('Forbidden'), 403);
		}

		$success = array();
		$kuser   = KunenaFactory::getUser($this->app->input->getInt('userid', 0));

		if (KunenaUserHelper::getMyself()->userid == $kuser->userid || KunenaUserHelper::getMyself()->isAdmin() || KunenaUserHelper::getMyself()->isModerator())
		{
			$this->deleteOldAvatars();

			// Save in the table KunenaUser
			$kuser->avatar = '';
			$success       = $kuser->save();
		}
		else
		{
			throw new RuntimeException(Text::_('Forbidden'), 403);
		}

		header('Content-type: application/json');
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");

		if (ob_get_length())
		{
			ob_end_clean();
		}

		echo json_encode($success);

		jexit();
	}

	/**
	 * Get avatar attached to a profile with AJAX.
	 *
	 * @return void
	 * @since 5.1
	 * @throws Exception
	 */
	public function loadAvatar()
	{
		// Only support JSON requests.
		if ($this->input->getWord('format', 'html') != 'json')
		{
			throw new RuntimeException(Text::_('Bad Request'), 400);
		}

		if (!Session::checkToken('request'))
		{
			throw new RuntimeException(Text::_('Forbidden'), 403);
		}

		$userid = $this->input->getInt('userid');
		$kuser  = KunenaFactory::getUser($userid);

		$avatar       = new stdClass;
		$avatar->name = $kuser->avatar;

		if (!empty($kuser->avatar))
		{
			$avatar->path = Uri::root() . 'media/kunena/avatars/' . $kuser->avatar;
		}
		else
		{
			$avatar->path = Uri::root() . 'media/kunena/avatars/' . KunenaConfig::getInstance()->defaultavatar;
		}

		header('Content-type: application/json');
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");

		if (ob_get_length())
		{
			ob_end_clean();
		}

		echo json_encode($avatar);

		jexit();
	}

	protected function saveSettings()
	{
		$this->user = KunenaFactory::getUser($this->app->input->getInt('userid', 0));

		if ($this->app->input->get('hidemail', null) === null)
		{
			return;
		}

		$this->user->ordering     = $this->app->input->getInt('messageordering', '');
		$this->user->hideEmail    = $this->app->input->getInt('hidemail', '');
		$this->user->showOnline   = $this->app->input->getInt('showonline', '');
		$this->user->canSubscribe = $this->app->input->getInt('cansubscribe', '');
		$this->user->userListtime = $this->app->input->getInt('userlisttime', '');
		$this->user->socialshare  = $this->app->input->getInt('socialshare', '');
	}

	public function delfile()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirectBack();

			return;
		}

		$cid = $this->app->input->get('cid', array(), 'post', 'array');
		$cid = ArrayHelper::toInteger($cid);

		if (!empty($cid))
		{
			$number = 0;

			foreach ($cid as $id)
			{
				$attachment  = KunenaAttachmentHelper::get($id);
				$message     = $attachment->getMessage();
				$attachments = array($attachment->id, 1);
				$attach      = array();
				$removeList  = array_keys(array_diff_key($attachments, $attach));
				$removeList  = ArrayHelper::toInteger($removeList);
				$message->removeAttachments($removeList);

				$topic = $message->getTopic();

				if ($attachment->isAuthorised('delete') && $attachment->delete())
				{
					if ($attachment->inline)
					{
						$find             = array('/\[attachment='.$id.'\](.*?)\[\/attachment\]/su');
						$replace          = '';
						$text             = preg_replace($find, $replace, $message->message);
						$message->message = $text;
					}

					$message->save();

					if ($topic->attachments > 0)
					{
						$topic->attachments = $topic->attachments - 1;
						$topic->save(false);
					}

					$number++;
				}
			}

			if ($number > 0)
			{
				$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_ATTACHMENTS_DELETE_SUCCESSFULLY', $number));
				$this->setRedirectBack();

				return;
			}
			else
			{
				$this->app->enqueueMessage(Text::_('COM_KUNENA_ATTACHMENTS_DELETE_FAILED'), 'error');
				$this->setRedirectBack();

				return;
			}
		}

		$this->app->enqueueMessage(Text::_('COM_KUNENA_ATTACHMENTS_NO_ATTACHMENTS_SELECTED'), 'error');
		$this->setRedirectBack();
	}

	/**
	 * Reports a user to stopforumspam.com
	 *
	 * @param   int     $user      user
	 * @param   string  $evidence  evidence
	 *
	 * @return boolean
	 * @since Kunena
	 */
	protected function report($userid = 0, $evidence = null)
	{
		if (!$this->config->stopforumspam_key || !$userid)
		{
			return false;
		}

		$spammer = Factory::getUser($userid);

		// TODO: remove this query by getting the ip of user by an another way
		$db = Factory::getDBO();
		$db->setQuery("SELECT ip FROM #__kunena_messages WHERE userid=" . $userid . " GROUP BY ip ORDER BY `time` DESC", 0, 1);
		$ip = $db->loadResult();

		if (!empty($ip))
		{
			$options = new Joomla\Registry\Registry;

			try
			{
				$transport = new Joomla\CMS\Http\Transport\StreamTransport($options);
			}
			catch (Exception $e)
			{
				$this->app->enqueueMessage($e->getMessage(), 'error');

				return false;
			}

			// Create a 'stream' transport.
			$http = new Joomla\CMS\Http\Http($options, $transport);

			$data = 'username=' . $spammer->username . '&ip_addr=' . $ip . '&email=' . $spammer->email . '&api_key=' .
				$this->config->stopforumspam_key . '&evidence=' . $evidence;

			$response = $http->post('https://www.stopforumspam.com/add', $data);

			if ($response->code == '200')
			{
				if ($this->config->log_moderation)
				{
					$log = KunenaLog::LOG_USER_REPORT_STOPFORUMSPAM;

					KunenaLog::log(
						KunenaLog::TYPE_ACTION,
						$log,
						array(
							'user_ip_reported' => $data['ip'],
							'username_reported' => $data['username'],
							'email_reported' => $data['email'],
						),
						null,
						null,
						null
						);
				}

				// Report accepted. There is no need to display the reason
				$this->app->enqueueMessage(Text::_('COM_KUNENA_STOPFORUMSPAM_REPORT_SUCCESS'));

				return true;
			}
			else
			{
				// Report failed or refused
				$reasons = array();
				preg_match('/<p>.*<\/p>/', $response->body, $reasons);

				// Stopforumspam returns only one reason, which is reasons[0], but we need to strip out the html tags before using it
				$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_STOPFORUMSPAM_REPORT_FAILED', strip_tags($reasons[0])), 'error');

				return false;
			}
		}
		else
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_STOPFORUMSPAM_REPORT_NO_IP_GIVEN'), 'error');
		}
	}
}
