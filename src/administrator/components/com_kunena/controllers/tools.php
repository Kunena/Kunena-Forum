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
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;

/**
 * Kunena Cpanel Controller
 *
 * @since  2.0
 */
class KunenaAdminControllerTools extends KunenaController
{
	/**
	 * @var null|string
	 *
	 * @since    2.0
	 */
	protected $baseurl = null;

	/**
	 * Construct
	 *
	 * @param   array $config config
	 *
	 * @throws Exception
	 * @since    2.0
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->baseurl = 'administrator/index.php?option=com_kunena&view=tools';
	}

	/**
	 * Diagnotics
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @since    2.0
	 * @throws null
	 */
	public function diagnostics()
	{
		if (!Session::checkToken('get'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$fix    = $this->app->input->getCmd('fix');
		$delete = $this->app->input->getCmd('delete');

		if ($fix)
		{
			$success = KunenaForumDiagnostics::fix($fix);

			if (!$success)
			{
				$this->app->enqueueMessage(Text::sprintf('Failed to fix %s!', $fix), 'error');
			}
		}
		elseif ($delete)
		{
			$success = KunenaForumDiagnostics::delete($delete);

			if (!$success)
			{
				$this->app->enqueueMessage(Text::sprintf('Failed to delete %s!', $delete), 'error');
			}
		}

		$this->setRedirect(KunenaRoute::_($this->baseurl . '&layout=diagnostics', false));
	}

	/**
	 * Prune
	 *
	 * @throws Exception
	 *
	 * @return void
	 *
	 * @since    2.0
	 * @throws null
	 */
	public function prune()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$ids = $this->app->input->get('prune_forum', array(), 'post', 'array');
		$ids = ArrayHelper::toInteger($ids);

		$categories = KunenaForumCategoryHelper::getCategories($ids, false, 'admin');

		if (!$categories)
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_CHOOSEFORUMTOPRUNE'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		// Convert days to seconds for timestamp functions...
		$prune_days = $this->app->input->getInt('prune_days', 36500);
		$prune_date = Factory::getDate()->toUnix() - ($prune_days * 86400);

		$trashdelete = $this->app->input->getInt('trashdelete', 0);

		$where   = array();
		$where[] = " AND tt.last_post_time < {$prune_date}";

		$controloptions = $this->app->input->getString('controloptions', 0);

		if ($controloptions == 'answered')
		{
			$where[] = 'AND tt.posts>1';
		}
		elseif ($controloptions == 'unanswered')
		{
			$where[] = 'AND tt.posts=1';
		}
		elseif ($controloptions == 'locked')
		{
			$where[] = 'AND tt.locked>0';
		}
		elseif ($controloptions == 'deleted')
		{
			$where[] = 'AND tt.hold IN (2,3)';
		}
		elseif ($controloptions == 'unapproved')
		{
			$where[] = 'AND tt.hold=1';
		}
		elseif ($controloptions == 'shadow')
		{
			$where[] = 'AND tt.moved_id>0';
		}
		elseif ($controloptions == 'normal')
		{
			$where[] = 'AND tt.locked=0';
		}
		elseif ($controloptions == 'all')
		{
			// No filtering
		}
		else
		{
			$where[] = 'AND 0';
		}

		// Keep sticky topics?
		if ($this->app->input->getInt('keepsticky', 1))
		{
			$where[] = ' AND tt.ordering=0';
		}

		$where = implode(' ', $where);

		$params = array(
			'where' => $where,
		);

		$count = 0;

		foreach ($categories as $category)
		{
			if ($trashdelete)
			{
				$count += $category->purge($prune_date, $params);
			}
			else
			{
				$count += $category->trash($prune_date, $params);
			}
		}

		if ($trashdelete)
		{
			$this->app->enqueueMessage("" . Text::_('COM_KUNENA_FORUMPRUNEDFOR') . " " . $prune_days . " "
				. Text::_('COM_KUNENA_PRUNEDAYS') . "; " . Text::_('COM_KUNENA_PRUNEDELETED') . " {$count} " . Text::_('COM_KUNENA_PRUNETHREADS')
			);
		}
		else
		{
			$this->app->enqueueMessage("" . Text::_('COM_KUNENA_FORUMPRUNEDFOR') . " " . $prune_days . " "
				. Text::_('COM_KUNENA_PRUNEDAYS') . "; " . Text::_('COM_KUNENA_PRUNETRASHED') . " {$count} " . Text::_('COM_KUNENA_PRUNETHREADS')
			);
		}

		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Sync Users
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @since    2.0
	 * @throws null
	 */
	public function syncusers()
	{
		$useradd     = $this->app->input->getBool('useradd', 0);
		$userdel     = $this->app->input->getBool('userdel', 0);
		$userrename  = $this->app->input->getBool('userrename', 0);
		$userdellife = $this->app->input->getBool('userdellife', 0);

		$db = Factory::getDBO();

		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		if ($useradd)
		{
			$db->setQuery(
				"INSERT INTO #__kunena_users (userid, showOnline)
					SELECT a.id AS userid, 1 AS showOnline
					FROM #__users AS a
					LEFT JOIN #__kunena_users AS b ON b.userid=a.id
					WHERE b.userid IS NULL"
			);

			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				$this->app->enqueueMessage($e->getMessage());

				return;
			}

			$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_SYNC_USERS_ADD_DONE', $db->getAffectedRows()));
		}

		if ($userdel)
		{
			$db->setQuery(
				"DELETE a
					FROM #__kunena_users AS a
					LEFT JOIN #__users AS b ON a.userid=b.id
					WHERE b.username IS NULL"
			);

			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				$this->app->enqueueMessage($e->getMessage());

				return;
			}

			$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_SYNC_USERS_DELETE_DONE', $db->getAffectedRows()));
		}

		if ($userdellife)
		{
			$db->setQuery(
				"DELETE a
			FROM #__kunena_users AS a
			LEFT JOIN #__users AS b ON a.userid=b.id
			WHERE banned='0000-00-00 00:00:00'"
			);
			$db->execute();

			$db->setQuery(
				"DELETE a
			FROM #__users AS a
			WHERE block='1'"
			);

			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				$this->app->enqueueMessage($e->getMessage());

				return;
			}

			$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_SYNC_USERS_DELETE_DONE', $db->getAffectedRows()));
		}

		if ($userrename)
		{
			$queryName = $this->config->username ? "username" : "name";

			$query = "UPDATE #__kunena_messages AS m
					INNER JOIN #__users AS u
					SET m.name = u.{$queryName}
					WHERE m.userid = u.id";
			$db->setQuery($query);

			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				$this->app->enqueueMessage($e->getMessage());

				return;
			}

			$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_SYNC_USERS_RENAME_DONE', $db->getAffectedRows()));
		}

		if ($userdellife)
		{
			$db->setQuery("DELETE a FROM #__kunena_users AS a LEFT JOIN #__users AS b ON a.userid=b.id WHERE banned='1000-01-01 00:00:00'");
			$db->execute();

			$db->setQuery("DELETE a FROM #__users AS a WHERE block='1'");

			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				$this->app->enqueueMessage($e->getMessage());

				return;
			}

			$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_SYNC_USERS_DELETE_DONE', $db->getAffectedRows()));
		}

		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Begin category recount.
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @since    2.0
	 * @throws null
	 */
	public function recount()
	{
		$ajax = $this->input->getWord('format', 'html') == 'json';

		if (!Session::checkToken())
		{
			$this->setResponse(
				array(
					'success' => false,
					'header'  => 'An Error Occurred',
					'message' => 'Please see more details below.',
					'error'   => Text::_('COM_KUNENA_ERROR_TOKEN'),
				),
				$ajax
			);
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$state = $this->app->getUserState('com_kunena.admin.recount', null);

		if ($state === null)
		{
			// First run: get last message id (if topics were created with <K2.0)
			$state          = new StdClass;
			$state->step    = 0;
			$state->start   = 0;
			$state->current = 0;
			$state->reload  = 0;

			$db    = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select('MAX(thread)')->from('#__kunena_messages');
			$db->setQuery($query);

			// Topic count
			$state->maxId = (int) $db->loadResult();
			$state->total = $state->maxId * 2 + 10000;

			$state->topics     = $this->input->getBool('topics', false);
			$state->usertopics = $this->input->getBool('usertopics', false);
			$state->categories = $this->input->getBool('categories', false);
			$state->users      = $this->input->getBool('users', false);
			$state->polls      = $this->input->getBool('polls', false);

			$this->app->setUserState('com_kunena.admin.recount', $state);

			$msg = Text::_('COM_KUNENA_AJAX_INIT');
		}
		else
		{
			$msg = Text::_('COM_KUNENA_AJAX_RECOUNT_CONTINUE');
		}

		$token    = Session::getFormToken() . '=1';
		$redirect = KunenaRoute::_("{$this->baseurl}&task=dorecount&i={$state->reload}&{$token}", false);
		$this->setResponse(
			array(
				'success' => true,
				'status'  => sprintf("%2.1f%%", 99 * $state->current / ($state->total + 1)),
				'header'  => Text::_('COM_KUNENA_AJAX_RECOUNT_WAIT'),
				'message' => $msg,
				'href'    => $redirect,
			),
			$ajax
		);
	}

	/**
	 * Set proper response for both AJAX and traditional calls.
	 *
	 * @param   $response
	 * @param   $ajax
	 *
	 * @return void
	 *
	 * @since    2.0
	 */
	protected function setResponse($response, $ajax)
	{
		if (!$ajax)
		{
			if (!empty($response['error']))
			{
				$this->setMessage($response['error'], 'error');
			}

			if (!empty($response['href']))
			{
				$this->setRedirect($response['href']);
			}
		}
		else
		{
			if (ob_get_length())
			{
				ob_end_clean();
			}

			header('Content-type: application/json');
			echo json_encode($response);
			flush();
			jexit();
		}
	}

	/**
	 * Perform recount on statistics in smaller chunks.
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since    2.0
	 * @throws null
	 */
	public function dorecount()
	{
		$ajax = $this->input->getWord('format', 'html') == 'json';

		if (!Session::checkToken('request'))
		{
			$this->setResponse(
				array(
					'success' => false,
					'header'  => Text::_('COM_KUNENA_AJAX_ERROR'),
					'message' => Text::_('COM_KUNENA_AJAX_DETAILS_BELOW'),
					'error'   => Text::_('COM_KUNENA_ERROR_TOKEN'),
				),
				$ajax
			);
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$state = $this->app->getUserState('com_kunena.admin.recount', null);

		try
		{
			$this->checkTimeout();

			while (1)
			{
				// Topic count per run.
				// TODO: count isn't accurate as it can overflow total.
				$count = mt_rand(4500, 5500);

				switch ($state->step)
				{
					case 0:
						if ($state->topics)
						{
							// Update topic statistics
							KunenaAttachmentHelper::cleanup();
							KunenaForumTopicHelper::recount(false, $state->start, $state->start + $count);
							$state->start += $count;
							$msg          = Text::sprintf(
								'COM_KUNENA_ADMIN_RECOUNT_TOPICS_X',
								round(min(100 * $state->start / $state->maxId + 1, 100)) . '%'
							);
						}
						break;
					case 1:
						if ($state->usertopics)
						{
							// Update user's topic statistics
							KunenaForumTopicUserHelper::recount(false, $state->start, $state->start + $count);
							$state->start += $count;
							$msg          = Text::sprintf(
								'COM_KUNENA_ADMIN_RECOUNT_USERTOPICS_X',
								round(min(100 * $state->start / $state->maxId + 1, 100)) . '%'
							);
						}
						break;
					case 2:
						if ($state->categories)
						{
							// Update category statistics
							KunenaForumCategoryHelper::recount();
							KunenaForumCategoryHelper::fixAliases();
							$msg = Text::sprintf('COM_KUNENA_ADMIN_RECOUNT_CATEGORIES_X', '100%');
						}
						break;
					case 3:
						if ($state->users)
						{
							// Update user statistics
							KunenaForumMessageThankyouHelper::recountThankyou();
							KunenaUserHelper::recount();
							KunenaUserHelper::recountPostsNull();
							$msg = Text::sprintf('COM_KUNENA_ADMIN_RECOUNT_USERS_X', '100%');
						}
						break;
					case 4:
						if ($state->polls)
						{
							// Update user statistics
							KunenaForumTopicPollHelper::recount();
							$msg = Text::sprintf('COM_KUNENA_ADMIN_RECOUNT_POLLS_X', '100%');
						}
						break;
					default:
						$header = Text::_('COM_KUNENA_RECOUNTFORUMS_DONE');
						$msg    = Text::_('COM_KUNENA_AJAX_REQUESTED_RECOUNTED');
						$this->app->setUserState('com_kunena.admin.recount', null);
						$this->setResponse(
							array(
								'success' => true,
								'status'  => '100%',
								'header'  => $header,
								'message' => $msg,
							),
							$ajax
						);
						$this->setRedirect(KunenaRoute::_($this->baseurl, false), $header);

						return;
				}

				$state->current = min($state->current + $count, $state->total);

				if (!$state->start || $state->start > $state->maxId)
				{
					$state->step++;
					$state->start = 0;
				}

				if ($this->checkTimeout())
				{
					break;
				}
			}

			$state->reload++;
			$this->app->setUserState('com_kunena.admin.recount', $state);
		}
		catch (Exception $e)
		{
			if (!$ajax)
			{
				throw $e;
			}

			$this->setResponse(
				array(
					'success' => false,
					'status'  => sprintf("%2.1f%%", 99 * $state->current / ($state->total + 1)),
					'header'  => Text::_('COM_KUNENA_AJAX_ERROR'),
					'message' => Text::_('COM_KUNENA_AJAX_DETAILS_BELOW'),
					'error'   => $e->getMessage(),
				),
				$ajax
			);
		}

		$token    = Session::getFormToken() . '=1';
		$redirect = KunenaRoute::_("{$this->baseurl}&task=dorecount&i={$state->reload}&{$token}", false);
		$this->setResponse(
			array(
				'success' => true,
				'status'  => sprintf("%2.1f%%", 99 * $state->current / ($state->total + 1)),
				'header'  => Text::_('COM_KUNENA_AJAX_RECOUNT_WAIT'),
				'message' => $msg,
				'href'    => $redirect,
			), $ajax
		);
	}

	/**
	 * Check timeout
	 *
	 * @param   bool $stop stop
	 *
	 * @return boolean
	 *
	 * @since    2.0
	 */
	protected function checkTimeout($stop = false)
	{
		static $start = null;

		if ($stop)
		{
			$start = 0;
		}

		$time = microtime(true);

		if ($start === null)
		{
			$start = $time;

			return false;
		}

		if ($time - $start < 14)
		{
			return false;
		}

		return true;
	}

	/**
	 * Trash Menu
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @since    2.0
	 * @throws null
	 */
	public function trashmenu()
	{
		require_once KPATH_ADMIN . '/install/model.php';

		$installer = new KunenaModelInstall;
		$installer->deleteMenu();
		$installer->createMenu();

		$this->app->enqueueMessage(Text::_('COM_KUNENA_MENU_CREATED'));
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Fix Legacy
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @since    2.0
	 * @throws null
	 */
	public function fixlegacy()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$legacy = KunenaMenuFix::getLegacy();
		$errors = KunenaMenuFix::fixLegacy();

		if ($errors)
		{
			$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_MENU_FIXED_LEGACY_FAILED', $errors[0]), 'notice');
		}
		else
		{
			$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_MENU_FIXED_LEGACY', count($legacy)));
		}

		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Purge restatements
	 *
	 * @return  void
	 *
	 * @throws Exception
	 * @since    2.0
	 * @throws null
	 */
	public function purgeReStatements()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$re_string = $this->app->input->getString('re_string', null);

		if ($re_string != null)
		{
			$db    = Factory::getDbo();
			$query = "UPDATE #__kunena_messages SET subject=TRIM(TRIM(LEADING {$db->quote($re_string)} FROM subject)) WHERE subject LIKE {$db->quote($re_string . '%')}";
			$db->setQuery($query);

			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				$this->app->enqueueMessage($e->getMessage());

				return;
			}

			$count = $db->getAffectedRows();

			if ($count > 0)
			{
				$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_MENU_RE_PURGED', $count, $re_string));
				$this->setRedirect(KunenaRoute::_($this->baseurl, false));
			}
			else
			{
				$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_MENU_RE_PURGE_FAILED', $re_string));
				$this->setRedirect(KunenaRoute::_($this->baseurl, false));
			}
		}
		else
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_MENU_RE_PURGE_FORGOT_STATEMENT'));
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));
		}
	}

	/**
	 * Clean ip
	 *
	 * @throws Exception
	 *
	 * @return void
	 *
	 * @since    2.0
	 * @throws null
	 */
	public function cleanupIP()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$cleanup_days = $this->app->input->getInt('cleanup_ip_days', 365);
		$where        = '';

		if ($cleanup_days)
		{
			$clean_date = Factory::getDate()->toUnix() - ($cleanup_days * 86400);
			$where      = 'WHERE time < ' . $clean_date;
		}

		$db    = Factory::getDbo();
		$query = "UPDATE #__kunena_messages SET ip=NULL {$where};";
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			$this->app->enqueueMessage($e->getMessage());

			return;
		}

		$count = $db->getAffectedRows();

		$deleteipusers = $this->app->input->getBool('deleteipusers', 0);

		if ($deleteipusers)
		{
			$db    = Factory::getDbo();
			$query = "UPDATE #__kunena_users SET ip = NULL;";
			$db->setQuery($query);

			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				$this->app->enqueueMessage($e->getMessage());

				return;
			}

			$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_TOOLS_CLEANUP_IP_USERS_DONE', $count));
		}

		if ($count > 0)
		{
			$this->app->enqueueMessage(Text::sprintf('COM_KUNENA_TOOLS_CLEANUP_IP_DONE', $count));
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));
		}
		else
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_TOOLS_CLEANUP_IP_FAILED'));
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));
		}
	}

	/**
	 * Method to just redirect to main manager in case of use of cancel button
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @since K4.0
	 * @throws null
	 */
	public function cancel()
	{
		$this->app->redirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Method to completely remove kunena by checking before if the user is a super-administrator
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @since K4.0
	 * @throws null
	 */
	public function uninstall()
	{
		if (!Session::checkToken('post'))
		{
			$this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		// Check if the user has the super-administrator rights
		$username = $this->app->input->getString('username');
		$password = $this->app->input->getString('password');
		$code     = $this->app->input->getInt('secretkey');

		$login = KunenaLogin::getInstance();

		if ($login->isTFAEnabled())
		{
			if (empty($code) || $code == 0)
			{
				$this->app->enqueueMessage(Text::_('COM_KUNENA_TOOLS_UNINSTALL_LOGIN_SECRETKEY_INVALID'));
				$this->setRedirect(KunenaRoute::_($this->baseurl, false));
			}
		}

		$error = $login->loginUser($username, $password, 0, null);

		$user = Factory::getUser(\Joomla\CMS\User\UserHelper::getUserId($username));

		$isroot = $user->authorise('core.admin');

		if (!$error && $isroot)
		{
			$this->app->setUserState('com_kunena.uninstall.allowed', true);

			$this->setRedirect(KunenaRoute::_('administrator/index.php?option=com_kunena&view=uninstall&' . Session::getFormToken() . '=1', false));

			return;
		}

		$this->app->enqueueMessage(Text::_('COM_KUNENA_TOOLS_UNINSTALL_LOGIN_FAILED'));
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * System Report
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @since    2.0
	 * @throws null
	 */
	public function systemreport()
	{
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}
}
