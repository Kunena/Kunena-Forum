<?php
/**
 * Kunena Component
 * @package Kunena.Administrator
 * @subpackage Controllers
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/**
 * Kunena Cpanel Controller
 *
 * @since 2.0
 */
class KunenaAdminControllerTools extends KunenaController {
	protected $baseurl = null;

	public function __construct($config = array()) {
		parent::__construct($config);
		$this->baseurl = 'administrator/index.php?option=com_kunena&view=tools';
	}

	function diagnostics() {
		if (!JSession::checkToken('get')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));
			return;
		}

		$fix = JRequest::getCmd('fix');
		$delete = JRequest::getCmd('delete');
		if ($fix) {
			$success = KunenaForumDiagnostics::fix($fix);
			if (!$success) $this->app->enqueueMessage ( JText::sprintf('Failed to fix %s!', $fix), 'error' );
		} elseif ($delete) {
			$success = KunenaForumDiagnostics::delete($delete);
			if (!$success) $this->app->enqueueMessage ( JText::sprintf('Failed to delete %s!', $delete), 'error' );
		}
		$this->setRedirect(KunenaRoute::_($this->baseurl.'&layout=diagnostics', false));
	}

	function prune() {
		if (!JSession::checkToken('post')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));
			return;
		}

		$ids = JRequest::getVar('prune_forum', array(), 'post', 'array'); // Array of integers
		JArrayHelper::toInteger($ids);

		$categories = KunenaForumCategoryHelper::getCategories($ids, false, 'admin');

		if (!$categories) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_CHOOSEFORUMTOPRUNE' ), 'error' );
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));
			return;
		}

		// Convert days to seconds for timestamp functions...
		$prune_days = JRequest::getInt ( 'prune_days', 36500 );
		$prune_date = JFactory::getDate()->toUnix() - ($prune_days * 86400);

		$trashdelete = JRequest::getInt( 'trashdelete', 0);

		$where = array();
		$where[] = " AND tt.last_post_time < {$prune_date}";

		$controloptions = JRequest::getString( 'controloptions', 0);
		if ( $controloptions == 'answered' ) {
			$where[] = 'AND tt.posts>1';
		} elseif ( $controloptions == 'unanswered' ) {
			$where[] = 'AND tt.posts=1';
		} elseif( $controloptions == 'locked' ) {
			$where[] = 'AND tt.locked>0';
		} elseif( $controloptions == 'deleted' ) {
			$where[] = 'AND tt.hold IN (2,3)';
		} elseif( $controloptions == 'unapproved' ) {
			$where[] = 'AND tt.hold=1';
		} elseif( $controloptions == 'shadow' ) {
			$where[] = 'AND tt.moved_id>0';
		} elseif( $controloptions == 'normal' ) {
			$where[] = 'AND tt.locked=0';
		} elseif( $controloptions == 'all' ) {
			// No filtering
		} else {
			$where[] = 'AND 0';
		}

		// Keep sticky topics?
		if (JRequest::getInt( 'keepsticky', 1)) $where[] = ' AND tt.ordering=0';

		$where = implode(' ', $where);

		$params = array(
			'where'=> $where,
		);

		$count = 0;
		foreach ($categories as $category) {
			if ( $trashdelete ) $count += $category->purge($prune_date, $params);
			else $count += $category->trash($prune_date, $params);
		}

		if ( $trashdelete ) $this->app->enqueueMessage ( "" . JText::_('COM_KUNENA_FORUMPRUNEDFOR') . " " . $prune_days . " " . JText::_('COM_KUNENA_PRUNEDAYS') . "; " . JText::_('COM_KUNENA_PRUNEDELETED') . " {$count} " . JText::_('COM_KUNENA_PRUNETHREADS') );
		else $this->app->enqueueMessage ( "" . JText::_('COM_KUNENA_FORUMPRUNEDFOR') . " " . $prune_days . " " . JText::_('COM_KUNENA_PRUNEDAYS') . "; " . JText::_('COM_KUNENA_PRUNETRASHED') . " {$count} " . JText::_('COM_KUNENA_PRUNETHREADS') );
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	function syncusers() {
		$useradd = JRequest::getBool ( 'useradd', 0 );
		$userdel = JRequest::getBool ( 'userdel', 0 );
		$userrename = JRequest::getBool ( 'userrename', 0 );

		$db = JFactory::getDBO ();
		if (!JSession::checkToken('post')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));
			return;
		}

		if ($useradd) {
			$db->setQuery ( "INSERT INTO #__kunena_users (userid, showOnline)
					SELECT a.id AS userid, 1 AS showOnline
					FROM #__users AS a
					LEFT JOIN #__kunena_users AS b ON b.userid=a.id
					WHERE b.userid IS NULL" );
			$db->query ();
			if (KunenaError::checkDatabaseError()) return;
			$this->app->enqueueMessage ( JText::sprintf('COM_KUNENA_SYNC_USERS_ADD_DONE',$db->getAffectedRows ()) );
		}
		if ($userdel) {
			$db->setQuery ( "DELETE a
					FROM #__kunena_users AS a
					LEFT JOIN #__users AS b ON a.userid=b.id
					WHERE b.username IS NULL" );
			$db->query ();
			if (KunenaError::checkDatabaseError()) return;
			$this->app->enqueueMessage ( JText::sprintf('COM_KUNENA_SYNC_USERS_DELETE_DONE',$db->getAffectedRows ()) );
		}
		if ($userrename) {
			$queryName = $this->config->username ? "username" : "name";

			$query = "UPDATE #__kunena_messages AS m
					INNER JOIN #__users AS u
					SET m.name = u.{$queryName}
					WHERE m.userid = u.id";
			$db->setQuery($query);
			$db->query();
			if (KunenaError::checkDatabaseError()) return;

			$this->app->enqueueMessage ( JText::sprintf('COM_KUNENA_SYNC_USERS_RENAME_DONE', $db->getAffectedRows()) );
		}

		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	function recount() {
		$state = $this->app->getUserState ( 'com_kunena.admin.recount', null );

		if ($state === null) {
			// First run: get last message id (if topics were created with <K2.0)
			$query = "SELECT MAX(id) FROM #__kunena_messages";
			$db = JFactory::getDBO();
			$db->setQuery ( $query );
			$state = new StdClass();
			$state->step = 0;
			$state->maxId = (int) $db->loadResult ();
			$state->start = 0;
			$state->reload = 0;

			$state->topics = JRequest::getBool('topics', false);
			$state->usertopics = JRequest::getBool('usertopics', false);
			$state->categories = JRequest::getBool('categories', false);
			$state->users = JRequest::getBool('users', false);
		}

		$this->checkTimeout();
		while (1) {
			$count = mt_rand(4500, 5500);
			switch ($state->step) {
				case 0:
					if ($state->topics) {
						// Update topic statistics
						KunenaForumTopicHelper::recount(false, $state->start, $state->start+$count);
						$state->start += $count;
						//$this->app->enqueueMessage ( JText::sprintf('COM_KUNENA_ADMIN_RECOUNT_TOPICS', min($state->start, $state->maxId), $state->maxId) );
					}
					break;
				case 1:
					if ($state->usertopics) {
						// Update usertopic statistics
						KunenaForumTopicUserHelper::recount(false, $state->start, $state->start+$count);
						$state->start += $count;
						//$this->app->enqueueMessage ( JText::sprintf('COM_KUNENA_ADMIN_RECOUNT_USERTOPICS', min($state->start, $state->maxId), $state->maxId) );
					}
					break;
				case 2:
					if ($state->categories) {
						// Update category statistics
						KunenaForumCategoryHelper::recount();
						KunenaForumCategoryHelper::fixAliases();
						//$this->app->enqueueMessage ( JText::sprintf('COM_KUNENA_ADMIN_RECOUNT_CATEGORY') );
					}
					break;
				case 3:
					if ($state->users) {
						// Update user statistics
						KunenaUserHelper::recount();
						//$this->app->enqueueMessage ( JText::sprintf('COM_KUNENA_ADMIN_RECOUNT_USER') );
					}
					break;
				default:
					$this->app->setUserState ( 'com_kunena.admin.recount', null );
					$this->app->enqueueMessage (JText::_('COM_KUNENA_RECOUNTFORUMS_DONE'));
					$this->setRedirect(KunenaRoute::_($this->baseurl, false));
					return;
			}
			if (!$state->start || $state->start > $state->maxId) {
				$state->step++;
				$state->start = 0;
			}
			if ($this->checkTimeout()) break;
		}
		$state->reload++;
		$this->app->setUserState ( 'com_kunena.admin.recount', $state );
		$this->setRedirect(KunenaRoute::_("{$this->baseurl}&task=recount&i={$state->reload}", false));
	}

	public function trashmenu() {
		require_once(KPATH_ADMIN . '/install/model.php');
		$installer = new KunenaModelInstall();
		$installer->deleteMenu();
		$installer->createMenu();

		$this->app->enqueueMessage ( JText::_('COM_KUNENA_MENU_CREATED') );
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	public function fixlegacy() {
		if (!JSession::checkToken('post')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));
			return;
		}

		$legacy = KunenaMenuFix::getLegacy();
		$errors = KunenaMenuFix::fixLegacy();

		if ($errors) $this->app->enqueueMessage ( JText::sprintf('COM_KUNENA_MENU_FIXED_LEGACY_FAILED', $errors[0] ), 'notice' );
		else $this->app->enqueueMessage ( JText::sprintf('COM_KUNENA_MENU_FIXED_LEGACY', count($legacy) ) );
		$this->setRedirect(KunenaRoute::_($this->baseurl, false));
	}

	public function purgeReStatements() {
		if (!JSession::checkToken('post')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));
			return;
		}

		$re_string = JRequest::getString('re_string', null);

		if ( $re_string != null ) {
			$db	= JFactory::getDBO();
			$query = "UPDATE #__kunena_messages SET subject=TRIM(TRIM(LEADING {$db->quote($re_string)} FROM subject)) WHERE subject LIKE {$db->quote($re_string.'%')}";
			$db->setQuery ( $query );
			$db->Query ();
			KunenaError::checkDatabaseError();

			$count = $db->getAffectedRows ();

			if ( $count > 0 ) {
				$this->app->enqueueMessage ( JText::sprintf('COM_KUNENA_MENU_RE_PURGED', $count, $re_string ) );
				$this->setRedirect(KunenaRoute::_($this->baseurl, false));
			} else {
				$this->app->enqueueMessage ( JText::sprintf('COM_KUNENA_MENU_RE_PURGE_FAILED', $re_string) );
				$this->setRedirect(KunenaRoute::_($this->baseurl, false));
			}
		} else {
			$this->app->enqueueMessage ( JText::_('COM_KUNENA_MENU_RE_PURGE_FORGOT_STATEMENT') );
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));
		}
	}

	public function cleanupIP() {
		if (!JSession::checkToken('post')) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_KUNENA_ERROR_TOKEN' ), 'error' );
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));
			return;
		}

		$cleanup_days = JRequest::getInt('cleanup_ip_days', 365);
		$where ='';
		if ($cleanup_days) {
			$clean_date = JFactory::getDate()->toUnix() - ($cleanup_days * 86400);
			$where = 'WHERE time < '.$clean_date;
		}

		$db	= JFactory::getDBO();
		$query = "UPDATE #__kunena_messages SET ip=NULL {$where};";
		$db->setQuery ( $query );
		$db->Query ();
		KunenaError::checkDatabaseError();

		$count = $db->getAffectedRows ();

		if ( $count > 0 ) {
			$this->app->enqueueMessage ( JText::sprintf('COM_KUNENA_TOOLS_CLEANUP_IP_DONE', $count ) );
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));
		} else {
			$this->app->enqueueMessage ( JText::_('COM_KUNENA_TOOLS_CLEANUP_IP_FAILED') );
			$this->setRedirect(KunenaRoute::_($this->baseurl, false));
		}
	}

	protected function checkTimeout($stop = false) {
		static $start = null;
		if ($stop) $start = 0;
		$time = microtime (true);
		if ($start === null) {
			$start = $time;
			return false;
		}
		if ($time - $start < 14)
			return false;

		return true;
	}

	/**
	 * Method to just redirect to main manager in case of use of cancel button
	 *
	 * @return void
	 *
	 * @since 3.1.0
	 */
	public function cancel()
	{
		$this->app->redirect(KunenaRoute::_($this->baseurl, false));
	}
}
