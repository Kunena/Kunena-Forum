<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Administrator
 * @subpackage  Controllers
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

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
	 * @param   array  $config  config
	 *
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
	 * @since    2.0
	 */
	public function diagnostics()
	{
		if (!JSession::checkToken('get'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$fix    = JFactory::getApplication()->input->getCmd('fix');
		$delete = JFactory::getApplication()->input->getCmd('delete');

		if ($fix)
		{
			$success = KunenaForumDiagnostics::fix($fix);

			if (!$success)
			{
				$this->app->enqueueMessage(JText::sprintf('Failed to fix %s!', $fix), 'error');
			}
		}
		elseif ($delete)
		{
			$success = KunenaForumDiagnostics::delete($delete);

			if (!$success)
			{
				$this->app->enqueueMessage(JText::sprintf('Failed to delete %s!', $delete), 'error');
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
	 */
	public function prune()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$ids = JFactory::getApplication()->input->get('prune_forum', array(), 'post', 'array');
		Joomla\Utilities\ArrayHelper::toInteger($ids);

		$categories = KunenaForumCategoryHelper::getCategories($ids, false, 'admin');

		if (!$categories)
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_CHOOSEFORUMTOPRUNE'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		// Convert days to seconds for timestamp functions...
		$prune_days = JFactory::getApplication()->input->getInt('prune_days', 36500);
		$prune_date = JFactory::getDate()->toUnix() - ($prune_days * 86400);

		$trashdelete = JFactory::getApplication()->input->getInt('trashdelete', 0);

		$where   = array();
		$where[] = " AND tt.last_post_time < {$prune_date}";

		$controloptions = JFactory::getApplication()->input->getString('controloptions', 0);

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
		if (JFactory::getApplication()->input->getInt('keepsticky', 1))
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
			$this->app->enqueueMessage("" . JText::_('COM_KUNENA_FORUMPRUNEDFOR') . " " . $prune_days . " "
				. JText::_('COM_KUNENA_PRUNEDAYS') . "; " . JText::_('COM_KUNENA_PRUNEDELETED') . " {$count} " . JText::_('COM_KUNENA_PRUNETHREADS'));
		}
		else
		{
			$this->app->enqueueMessage("" . JText::_('COM_KUNENA_FORUMPRUNEDFOR') . " " . $prune_days . " "
				. JText::_('COM_KUNENA_PRUNEDAYS') . "; " . JText::_('COM_KUNENA_PRUNETRASHED') . " {$count} " . JText::_('COM_KUNENA_PRUNETHREADS'));
		}

		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Sync Users
	 *
	 * @return void
	 *
	 * @since    2.0
	 */
	public function syncusers()
	{
		$useradd     = JFactory::getApplication()->input->getBool('useradd', 0);
		$userdel     = JFactory::getApplication()->input->getBool('userdel', 0);
		$userrename  = JFactory::getApplication()->input->getBool('userrename', 0);
		$userdellife = JFactory::getApplication()->input->getBool('userdellife', 0);

		$db = JFactory::getDBO();

		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
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
					WHERE b.userid IS NULL");

			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage());

				return;
			}

			$this->app->enqueueMessage(JText::sprintf('COM_KUNENA_SYNC_USERS_ADD_DONE', $db->getAffectedRows()));
		}

		if ($userdel)
		{
			$db->setQuery(
					"DELETE a
					FROM #__kunena_users AS a
					LEFT JOIN #__users AS b ON a.userid=b.id
					WHERE b.username IS NULL");

			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage());

				return;
			}

			$this->app->enqueueMessage(JText::sprintf('COM_KUNENA_SYNC_USERS_DELETE_DONE', $db->getAffectedRows()));
		}

		if ($userdellife)
		{
			$db->setQuery(
			"DELETE a
			FROM #__kunena_users AS a
			LEFT JOIN #__users AS b ON a.userid=b.id
			WHERE banned='0000-00-00 00:00:00'");
			$db->execute();

			$db->setQuery(
			"DELETE a
			FROM #__users AS a
			WHERE block='1'");

			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage());

				return;
			}

			$this->app->enqueueMessage(JText::sprintf('COM_KUNENA_SYNC_USERS_DELETE_DONE', $db->getAffectedRows()));
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
				JFactory::getApplication()->enqueueMessage($e->getMessage());

				return;
			}

			$this->app->enqueueMessage(JText::sprintf('COM_KUNENA_SYNC_USERS_RENAME_DONE', $db->getAffectedRows()));
		}

		if ($userdellife)
		{
			$db->setQuery("DELETE a FROM #__kunena_users AS a LEFT JOIN #__users AS b ON a.userid=b.id WHERE banned='0000-00-00 00:00:00'");
			$db->execute();

			$db->setQuery("DELETE a FROM #__users AS a WHERE block='1'");

			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage());

				return;
			}

			$this->app->enqueueMessage(JText::sprintf('COM_KUNENA_SYNC_USERS_DELETE_DONE', $db->getAffectedRows()));
		}

		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Begin category recount.
	 *
	 * @return void
	 *
	 * @since    2.0
	 */
	public function recount()
	{
		$ajax = $this->input->getWord('format', 'html') == 'json';

		if (!JSession::checkToken())
		{
			$this->setResponse(
				array(
					'success' => false,
					'header'  => 'An Error Occurred',
					'message' => 'Please see more details below.',
					'error'   => JText::_('COM_KUNENA_ERROR_TOKEN')
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

			$db    = JFactory::getDbo();
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

			$msg = JText::_('COM_KUNENA_AJAX_INIT');
		}
		else
		{
			$msg = JText::_('COM_KUNENA_AJAX_RECOUNT_CONTINUE');
		}

		$token    = JSession::getFormToken() . '=1';
		$redirect = KunenaRoute::_("{$this->baseurl}&task=dorecount&i={$state->reload}&{$token}", false);
		$this->setResponse(
			array(
				'success' => true,
				'status'  => sprintf("%2.1f%%", 99 * $state->current / ($state->total + 1)),
				'header'  => JText::_('COM_KUNENA_AJAX_RECOUNT_WAIT'),
				'message' => $msg,
				'href'    => $redirect
			),
			$ajax
		);
	}

	/**
	 * Perform recount on statistics in smaller chunks.
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since    2.0
	 */
	public function dorecount()
	{
		$ajax = $this->input->getWord('format', 'html') == 'json';

		if (!JSession::checkToken('request'))
		{
			$this->setResponse(
				array(
					'success' => false,
					'header'  => JText::_('COM_KUNENA_AJAX_ERROR'),
					'message' => JText::_('COM_KUNENA_AJAX_DETAILS_BELOW'),
					'error'   => JText::_('COM_KUNENA_ERROR_TOKEN')
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
							$msg = JText::sprintf(
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
							$msg = JText::sprintf(
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
							$msg = JText::sprintf('COM_KUNENA_ADMIN_RECOUNT_CATEGORIES_X', '100%');
						}
						break;
					case 3:
						if ($state->users)
						{
							// Update user statistics
							KunenaUserHelper::recount();
							KunenaForumMessageThankyouHelper::recountThankyou();
							KunenaUserHelper::recountPostsNull();
							$msg = JText::sprintf('COM_KUNENA_ADMIN_RECOUNT_USERS_X', '100%');
						}
						break;
					case 4:
						if ($state->polls)
						{
							// Update user statistics
							KunenaForumTopicPollHelper::recount();
							$msg = JText::sprintf('COM_KUNENA_ADMIN_RECOUNT_POLLS_X', '100%');
						}
						break;
					default:
						$header = JText::_('COM_KUNENA_RECOUNTFORUMS_DONE');
						$msg    = JText::_('COM_KUNENA_AJAX_REQUESTED_RECOUNTED');
						$this->app->setUserState('com_kunena.admin.recount', null);
						$this->setResponse(
							array(
								'success' => true,
								'status'  => '100%',
								'header'  => $header,
								'message' => $msg
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
					'header'  => JText::_('COM_KUNENA_AJAX_ERROR'),
					'message' => JText::_('COM_KUNENA_AJAX_DETAILS_BELOW'),
					'error'   => $e->getMessage()
				),
				$ajax
			);
		}

		$token    = JSession::getFormToken() . '=1';
		$redirect = KunenaRoute::_("{$this->baseurl}&task=dorecount&i={$state->reload}&{$token}", false);
		$this->setResponse(
			array(
				'success' => true,
				'status'  => sprintf("%2.1f%%", 99 * $state->current / ($state->total + 1)),
				'header'  => JText::_('COM_KUNENA_AJAX_RECOUNT_WAIT'),
				'message' => $msg,
				'href'    => $redirect
			), $ajax
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
			while (@ob_end_clean())
			{
			}

			header('Content-type: application/json');
			echo json_encode($response);
			flush();
			jexit();
		}
	}

	/**
	 * Trash Menu
	 *
	 * @return void
	 *
	 * @since    2.0
	 */
	public function trashmenu()
	{
		require_once (KPATH_ADMIN . '/install/model.php');

		$installer = new KunenaModelInstall;
		$installer->deleteMenu();
		$installer->createMenu();

		$this->app->enqueueMessage(JText::_('COM_KUNENA_MENU_CREATED'));
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Fix Legacy
	 *
	 * @return void
	 *
	 * @since    2.0
	 */
	public function fixlegacy()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$legacy = KunenaMenuFix::getLegacy();
		$errors = KunenaMenuFix::fixLegacy();

		if ($errors)
		{
			$this->app->enqueueMessage(JText::sprintf('COM_KUNENA_MENU_FIXED_LEGACY_FAILED', $errors[0]), 'notice');
		}
		else
		{
			$this->app->enqueueMessage(JText::sprintf('COM_KUNENA_MENU_FIXED_LEGACY', count($legacy)));
		}

		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * Purge restatements
	 *
	 * @return  void
	 *
	 * @since    2.0
	 */
	public function purgeReStatements()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$re_string = JFactory::getApplication()->input->getString('re_string', null);

		if ($re_string != null)
		{
			$db    = JFactory::getDbo();
			$query = "UPDATE #__kunena_messages SET subject=TRIM(TRIM(LEADING {$db->quote($re_string)} FROM subject)) WHERE subject LIKE {$db->quote($re_string.'%')}";
			$db->setQuery($query);

			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage());

				return;
			}

			$count = $db->getAffectedRows();

			if ($count > 0)
			{
				$this->app->enqueueMessage(JText::sprintf('COM_KUNENA_MENU_RE_PURGED', $count, $re_string));
				$this->setRedirect(KunenaRoute::_($this->baseurl, false));
			}
			else
			{
				$this->app->enqueueMessage(JText::sprintf('COM_KUNENA_MENU_RE_PURGE_FAILED', $re_string));
				$this->setRedirect(KunenaRoute::_($this->baseurl, false));
			}
		}
		else
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_MENU_RE_PURGE_FORGOT_STATEMENT'));
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
	 */
	public function cleanupIP()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		$cleanup_days = JFactory::getApplication()->input->getInt('cleanup_ip_days', 365);
		$where        = '';

		if ($cleanup_days)
		{
			$clean_date = JFactory::getDate()->toUnix() - ($cleanup_days * 86400);
			$where      = 'WHERE time < ' . $clean_date;
		}

		$db    = JFactory::getDbo();
		$query = "UPDATE #__kunena_messages SET ip=NULL {$where};";
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage());

			return;
		}

		$count = $db->getAffectedRows();

		if ($count > 0)
		{
			$this->app->enqueueMessage(JText::sprintf('COM_KUNENA_TOOLS_CLEANUP_IP_DONE', $count));
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));
		}
		else
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_TOOLS_CLEANUP_IP_FAILED'));
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));
		}
	}

	/**
	 * Check timeout
	 *
	 * @param   bool  $stop  stop
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
	 * Method to completely remove kunena by checking before if the user is a super-administrator
	 *
	 * @return void
	 *
	 * @since K4.0
	 */
	public function uninstall()
	{
		if (!JSession::checkToken('post'))
		{
			$this->app->enqueueMessage(JText::_('COM_KUNENA_ERROR_TOKEN'), 'error');
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));

			return;
		}

		// Check if the user has the super-administrator rights
		$username = $this->app->input->getString('username');
		$password = $this->app->input->getString('password');
		$code = $this->app->input->getInt('secretkey');

		$login = KunenaLogin::getInstance();

		if ($login->isTFAEnabled())
		{
			if (empty($code) || $code == 0)
			{
				$this->app->enqueueMessage(JText::_('COM_KUNENA_TOOLS_UNINSTALL_LOGIN_SECRETKEY_INVALID'));
				$this->setRedirect(KunenaRoute::_($this->baseurl, false));
			}
		}

		$error = $login->loginUser($username, $password, 0, null, $code);

		$user = JFactory::getUser(JUserHelper::getUserId($username));

		$isroot = $user->authorise('core.admin');

		if (!$error && $isroot)
		{
			$this->app->setUserState('com_kunena.uninstall.allowed', true);

			$this->setRedirect(KunenaRoute::_('administrator/index.php?option=com_kunena&view=uninstall&' . JSession::getFormToken() . '=1', false));

			return;
		}

		$this->app->enqueueMessage(JText::_('COM_KUNENA_TOOLS_UNINSTALL_LOGIN_FAILED'));
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	/**
	 * System Report
	 *
	 * @return void
	 *
	 * @since    2.0
	 */
	public function systemreport()
	{
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}
}
