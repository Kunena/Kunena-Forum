<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Controller;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;
use Kunena\Forum\Libraries\Attachment\KunenaAttachmentHelper;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Event\KunenaBeforeModifySocialsEvent;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\KunenaDiagnostics;
use Kunena\Forum\Libraries\Forum\Message\Thankyou\KunenaMessageThankyouHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Forum\Topic\Poll\KunenaPollHelper;
use Kunena\Forum\Libraries\Forum\Topic\User\KunenaTopicUserHelper;
use Kunena\Forum\Libraries\Install\KunenaModelInstall;
use Kunena\Forum\Libraries\Menu\KunenaMenuFix;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use RuntimeException;
use StdClass;

/**
 * Kunena Cpanel Controller
 *
 * @since   Kunena 2.0
 */
class ToolsController extends FormController
{
    /**
     * @var     null|string
     * @since   Kunena 2.0
     */
    protected $baseurl = null;

    /**
     * Construct
     *
     * @param   array  $config  config
     *
     * @since   Kunena 2.0
     *
     * @throws  Exception
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
        $lang = $this->app->getLanguage();
        $lang->load('com_kunena.controllers', KPATH_ADMIN);
        $this->baseurl = 'administrator/index.php?option=com_kunena&view=tools';
    }

    /**
     * Diagnostics
     *
     * @return  void
     *
     * @since   Kunena 2.0
     *
     * @throws  null
     * @throws  Exception
     */
    public function diagnostics(): void
    {
        if (!Session::checkToken('get')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        $fix    = $this->input->getCmd('fix');
        $delete = $this->input->getCmd('delete');

        if ($fix) {
            $success = KunenaDiagnostics::fix($fix);

            if (!$success) {
                $this->app->enqueueMessage(Text::sprintf('Failed to fix %s!', $fix), 'error');
            }
        } elseif ($delete) {
            $success = KunenaDiagnostics::delete($delete);

            if (!$success) {
                $this->app->enqueueMessage(Text::sprintf('Failed to delete %s!', $delete), 'error');
            }
        }

        $this->setRedirect(KunenaRoute::_($this->baseurl . '&layout=diagnostics', false));
    }

    /**
     * Prune
     *
     * @return  void
     *
     * @since   Kunena 2.0
     *
     * @throws  null
     * @throws  Exception
     */
    public function prune(): void
    {
        if (!Session::checkToken()) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        $ids = $this->input->get('prune_forum', [], 'array');
        $ids = ArrayHelper::toInteger($ids);

        $categories = KunenaCategoryHelper::getCategories($ids, false, 'admin');

        if (!$categories) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_CHOOSEFORUMTOPRUNE'), 'error');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        // Convert days to seconds for timestamp functions...
        $pruneDays = $this->input->getInt('pruneDays', 36500);
        $pruneDate = Factory::getDate()->toUnix() - ($pruneDays * 86400);

        $trashDelete = $this->input->getInt('trashDelete', 0);

        $where   = [];
        $where[] = " AND tt.last_post_time < {$pruneDate}";

        $controlOptions = $this->input->getString('controlOptions', 0);

        if ($controlOptions == 'answered') {
            $where[] = 'AND tt.posts>1';
        } elseif ($controlOptions == 'unanswered') {
            $where[] = 'AND tt.posts=1';
        } elseif ($controlOptions == 'locked') {
            $where[] = 'AND tt.locked>0';
        } elseif ($controlOptions == 'deleted') {
            $where[] = 'AND tt.hold IN (2,3)';
        } elseif ($controlOptions == 'unapproved') {
            $where[] = 'AND tt.hold=1';
        } elseif ($controlOptions == 'shadow') {
            $where[] = 'AND tt.moved_id>0';
        } elseif ($controlOptions == 'normal') {
            $where[] = 'AND tt.locked=0';
        } elseif ($controlOptions == 'all') {
            // No filtering
            $where[] = '';
        } else {
            $where[] = 'AND 0';
        }

        // Keep sticky topics?
        if ($this->input->getInt('keepsticky', 1)) {
            $where[] = ' AND tt.ordering=0';
        }

        $where = implode(' ', $where);

        $params = [
            'where' => $where,
        ];

        $count = 0;

        foreach ($categories as $category) {
            if ($trashDelete) {
                $count += $category->purge($pruneDate, $params);
            } else {
                $count += $category->trash($pruneDate, $params);
            }
        }

        if ($trashDelete) {
            $this->app->enqueueMessage(
                "" . Text::_('COM_KUNENA_FORUMPRUNEDFOR') . " " . $pruneDays . " "
                    . Text::_('COM_KUNENA_PRUNEDAYS') . "; " . Text::_('COM_KUNENA_PRUNEDELETED') . " {$count} " . Text::_('COM_KUNENA_PRUNETHREADS'),
                'success'
            );
        } else {
            $this->app->enqueueMessage(
                "" . Text::_('COM_KUNENA_FORUMPRUNEDFOR') . " " . $pruneDays . " "
                    . Text::_('COM_KUNENA_PRUNEDAYS') . "; " . Text::_('COM_KUNENA_PRUNETRASHED') . " {$count} " . Text::_('COM_KUNENA_PRUNETHREADS'),
                'success'
            );
        }

        $this->setRedirect(KunenaRoute::_($this->baseurl, false));
    }

    /**
     * Sync Users
     *
     * @return  void
     *
     * @since   Kunena 2.0
     *
     * @throws  null
     * @throws  Exception
     */
    public function syncUsers(): void
    {
        $userAdd     = $this->input->getBool('userAdd', 0);
        $userDel     = $this->input->getBool('userDel', 0);
        $userRename  = $this->input->getBool('userRename', 0);
        $userDelLife = $this->input->getBool('userDelLife', 0);

        $db = Factory::getContainer()->get('DatabaseDriver');

        if (!Session::checkToken()) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        if ($userAdd) {
            // Create a SELECT query to get the users that need to be added
            $selectQuery = $db->createQuery();
            $selectQuery->select($db->quoteName('ju.id') . ' AS ' . $db->quoteName('userid'))
                ->select('1 AS ' . $db->quoteName('showOnline'))
                ->from($db->quoteName('#__users', 'ju'))
                ->join('LEFT', $db->quoteName('#__kunena_users', 'ku') . ' ON ' . $db->quoteName('ku.userid') . '=' . $db->quoteName('ju.id'))
                ->where($db->quoteName('ku.userid') . ' IS NULL');

            // Create the INSERT query using the SELECT query
            $query = $db->createQuery();
            $query->insert($db->quoteName('#__kunena_users'))
                ->columns([$db->quoteName('userid'), $db->quoteName('showOnline')])
                ->values($selectQuery);
            $db->setQuery($query);

            try {
                $db->execute();
            } catch (RuntimeException $e) {
                $this->app->enqueueMessage($e->getMessage(), 'error');

                return;
            }

            $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_SYNC_USERS_ADD_DONE', $db->getAffectedRows()), 'success');
        }

        if ($userDel) {
            // TODO: need to find a way to make this query working with DatabaseQuery
            $db->createQuery();
            $db->setQuery(
                "DELETE a
				FROM #__kunena_users AS a
				LEFT JOIN #__users AS b ON a.userid=b.id
				WHERE b.username IS NULL"
                );

            try {
                $db->execute();
            } catch (RuntimeException $e) {
                $this->app->enqueueMessage($e->getMessage(), 'error');

                return;
            }

            $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_SYNC_USERS_DELETE_DONE', $db->getAffectedRows()), 'success');
        }

        if ($userDelLife) {
            $now = Factory::getDate();

            // TODO: need to find a way to make this query working with DatabaseQuery
            $db->createQuery();
            $db->setQuery("DELETE a FROM #__kunena_users AS a LEFT JOIN #__users AS b ON a.userid=b.id WHERE banned > " . $db->quote($now->toSql()));

            try {
                $db->execute();
            } catch (RuntimeException $e) {
                $this->app->enqueueMessage($e->getMessage(), 'error');

                return;
            }

            $query = $db->createQuery()
                ->delete($db->quoteName('#__users'))
                ->where('block=\'1\'');

            $db->setQuery($query);

            try {
                $db->execute();
            } catch (RuntimeException $e) {
                $this->app->enqueueMessage($e->getMessage(), 'error');

                return;
            }

            $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_SYNC_USERS_DELETE_DONE', $db->getAffectedRows()), 'success');
        }

        if ($userRename) {
            $queryName = KunenaConfig::getInstance()->username ? "username" : "name";

            $query = $db->createQuery()
                ->update($db->quoteName('#__kunena_messages', 'm'))
                ->innerJoin($db->quoteName('#__users', 'u'))
                ->set($db->quoteName('m.name') . ' = ' . $db->quoteName('u.' . $queryName))
                ->where($db->quoteName('m.userid') . ' = ' . $db->quoteName('u.id'));

            $db->setQuery($query);

            try {
                $db->execute();
            } catch (RuntimeException $e) {
                $this->app->enqueueMessage($e->getMessage(), 'error');

                return;
            }

            $affectedRows = $db->getAffectedRows();

            $query = $db->createQuery()
                ->update($db->quoteName('#__kunena_topics', 't'))
                ->innerJoin($db->quoteName('#__users', 'u'))
                ->set($db->quoteName('t.first_post_guest_name') . ' = ' . $db->quoteName('u.' . $queryName))
                ->where($db->quoteName('t.first_post_userid') . ' = ' . $db->quoteName('u.id'));

            $db->setQuery($query);

            try {
                $db->execute();
            } catch (RuntimeException $e) {
                $this->app->enqueueMessage($e->getMessage(), 'error');

                return;
            }

            $affectedRows += $db->getAffectedRows();

            $query = $db->createQuery()
                ->update($db->quoteName('#__kunena_topics', 't'))
                ->innerJoin($db->quoteName('#__users', 'u'))
                ->set($db->quoteName('t.last_post_guest_name') . ' = ' . $db->quoteName('u.' . $queryName))
                ->where($db->quoteName('t.last_post_userid') . ' = ' . $db->quoteName('u.id'));

            $db->setQuery($query);

            try {
                $db->execute();
            } catch (RuntimeException $e) {
                $this->app->enqueueMessage($e->getMessage(), 'error');

                return;
            }

            $affectedRows += $db->getAffectedRows();

            $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_SYNC_USERS_RENAME_DONE', $affectedRows), 'success');
        }

        $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_SYNC_USERS_RENAME_DONE', $db->getAffectedRows()), 'success');

        $this->setRedirect(KunenaRoute::_($this->baseurl, false));
    }

    /**
     * Begin category recount.
     *
     * @return  void
     *
     * @since   Kunena 2.0
     *
     * @throws  null
     * @throws  Exception
     */
    public function recount(): void
    {
        $ajax = $this->input->getWord('format', 'html') == 'json';

        if (!Session::checkToken()) {
            $this->setResponse(
                [
                    'success' => false,
                    'header'  => 'An Error Occurred',
                    'message' => 'Please see more details below.',
                    'error'   => Text::_('COM_KUNENA_ERROR_TOKEN'),
                ],
                $ajax
            );
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        $state = $this->app->getUserState('com_kunena.admin.recount');

        if ($state === null) {
            // First run: get last message id (if topics were created with <K2.0)
            $state          = new StdClass();
            $state->step    = 0;
            $state->start   = 0;
            $state->current = 0;
            $state->reload  = 0;

            $db    = Factory::getContainer()->get('DatabaseDriver');
            $query = $db->createQuery();
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
        } else {
            $msg = Text::_('COM_KUNENA_AJAX_RECOUNT_CONTINUE');
        }

        $token    = Session::getFormToken() . '=1';
        $redirect = KunenaRoute::_("{$this->baseurl}&task=tools.doRecount&i={$state->reload}&{$token}", false);
        $this->setResponse(
            [
                'success' => true,
                'status'  => sprintf("%2.1f%%", 99 * $state->current / ($state->total + 1)),
                'header'  => Text::_('COM_KUNENA_AJAX_RECOUNT_WAIT'),
                'message' => $msg,
                'href'    => $redirect,
            ],
            $ajax
        );
    }

    /**
     * Set proper response for both AJAX and traditional calls.
     *
     * @param   array  $response  response
     * @param   bool   $ajax      ajax
     *
     * @return  void
     *
     * @since   Kunena 2.0
     */
    protected function setResponse(array $response, bool $ajax): void
    {
        if (!$ajax) {
            if (!empty($response['error'])) {
                $this->setMessage($response['error'], 'error');
            }

            if (!empty($response['href'])) {
                $this->setRedirect($response['href']);
            }
        } else {
            while (@ob_end_clean()) {
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
     * @return  void
     *
     * @since   Kunena 2.0
     *
     * @throws  null
     * @throws  Exception
     */
    public function doRecount(): void
    {
        $ajax = $this->input->getWord('format', 'html') == 'json';

        if (!Session::checkToken('request')) {
            $this->setResponse(
                [
                    'success' => false,
                    'header'  => Text::_('COM_KUNENA_AJAX_ERROR'),
                    'message' => Text::_('COM_KUNENA_AJAX_DETAILS_BELOW'),
                    'error'   => Text::_('COM_KUNENA_ERROR_TOKEN'),
                ],
                $ajax
            );
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        $state = $this->app->getUserState('com_kunena.admin.recount', null);

        try {
            $this->checkTimeout();

            while (1) {
                // Topic count per run.
                // TODO: count isn't accurate as it can overflow total.
                $count = mt_rand(4500, 5500);

                switch ($state->step) {
                    case 0:
                        if ($state->topics) {
                            // Update topic statistics
                            KunenaAttachmentHelper::cleanup();
                            KunenaTopicHelper::recount(false, $state->start, $state->start + $count);
                            $state->start += $count;
                            $msg          = Text::sprintf(
                                'COM_KUNENA_ADMIN_RECOUNT_TOPICS_X',
                                round(min(100 * $state->start / $state->maxId + 1, 100)) . '%'
                            );
                        }
                        break;
                    case 1:
                        if ($state->usertopics) {
                            // Update user's topic statistics
                            KunenaTopicUserHelper::recount(false, $state->start, $state->start + $count);
                            $state->start += $count;
                            $msg          = Text::sprintf(
                                'COM_KUNENA_ADMIN_RECOUNT_USERTOPICS_X',
                                round(min(100 * $state->start / $state->maxId + 1, 100)) . '%'
                            );
                        }
                        break;
                    case 2:
                        if ($state->categories) {
                            // Update category statistics
                            KunenaCategoryHelper::recountBatch();
                            KunenaCategoryHelper::fixAliases();
                            $msg = Text::sprintf('COM_KUNENA_ADMIN_RECOUNT_CATEGORIES_X', '100%');
                        }
                        break;
                    case 3:
                        if ($state->users) {
                            // Update user statistics
                            KunenaMessageThankyouHelper::recountThankyou();
                            KunenaUserHelper::recount();
                            KunenaUserHelper::recountPostsNull();
                            $msg = Text::sprintf('COM_KUNENA_ADMIN_RECOUNT_USERS_X', '100%');
                        }
                        break;
                    case 4:
                        if ($state->polls) {
                            // Update user statistics
                            KunenaPollHelper::recount();
                            $msg = Text::sprintf('COM_KUNENA_ADMIN_RECOUNT_POLLS_X', '100%');
                        }
                        break;
                    default:
                        $header = Text::_('COM_KUNENA_RECOUNTFORUMS_DONE');
                        $msg    = Text::_('COM_KUNENA_AJAX_REQUESTED_RECOUNTED');
                        $this->app->setUserState('com_kunena.admin.recount', null);
                        $this->setResponse(
                            [
                                'success' => true,
                                'status'  => '100%',
                                'header'  => $header,
                                'message' => $msg,
                            ],
                            $ajax
                        );
                        $this->setRedirect(KunenaRoute::_($this->baseurl, false), $header);

                        return;
                }

                $state->current = min($state->current + $count, $state->total);

                if (!$state->start || $state->start > $state->maxId) {
                    $state->step++;
                    $state->start = 0;
                }

                if ($this->checkTimeout()) {
                    break;
                }
            }

            $state->reload++;
            $this->app->setUserState('com_kunena.admin.recount', $state);
        } catch (Exception $e) {
            if (!$ajax) {
                throw $e;
            }

            $this->setResponse(
                [
                    'success' => false,
                    'status'  => sprintf("%2.1f%%", 99 * $state->current / ($state->total + 1)),
                    'header'  => Text::_('COM_KUNENA_AJAX_ERROR'),
                    'message' => Text::_('COM_KUNENA_AJAX_DETAILS_BELOW'),
                    'error'   => $e->getMessage(),
                ],
                $ajax
            );
        }

        $token    = Session::getFormToken() . '=1';
        $redirect = KunenaRoute::_("{$this->baseurl}&task=tools.doRecount&i={$state->reload}&{$token}", false);
        $this->setResponse(
            [
                'success' => true,
                'status'  => sprintf("%2.1f%%", 99 * $state->current / ($state->total + 1)),
                'header'  => Text::_('COM_KUNENA_AJAX_RECOUNT_WAIT'),
                'message' => $msg,
                'href'    => $redirect,
            ],
            $ajax
        );
    }

    /**
     * Check timeout
     *
     * @param   bool  $stop  stop
     *
     * @return  boolean
     *
     * @since   Kunena 2.0
     */
    protected function checkTimeout($stop = false): bool
    {
        static $start = null;

        if ($stop) {
            $start = 0;
        }

        $time = microtime(true);

        if ($start === null) {
            $start = $time;

            return false;
        }

        if ($time - $start < 14) {
            return false;
        }

        return true;
    }

    /**
     * Trash Menu
     *
     * @return  void
     *
     * @since   Kunena 2.0
     *
     * @throws  null
     * @throws  Exception
     */
    public function trashMenu(): void
    {
        $installer = new KunenaModelInstall();
        $installer->deleteMenu();
        $installer->createMenu();

        $this->app->enqueueMessage(Text::_('COM_KUNENA_MENU_CREATED'), 'success');
        $this->setRedirect(KunenaRoute::_($this->baseurl, false));
    }

    /**
     * Fix Legacy
     *
     * @return  void
     *
     * @since   Kunena 2.0
     *
     * @throws  null
     * @throws  Exception
     */
    public function fixLegacy(): void
    {
        if (!Session::checkToken()) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        $legacy = KunenaMenuFix::getLegacy();
        $errors = KunenaMenuFix::fixLegacy();

        if ($errors) {
            $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_MENU_FIXED_LEGACY_FAILED', $errors[0]), 'error');
        } else {
            $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_MENU_FIXED_LEGACY', \count($legacy)), 'success');
        }

        $this->setRedirect(KunenaRoute::_($this->baseurl, false));
    }

    /**
     * Purge restatements
     *
     * @return  void
     *
     * @since   Kunena 2.0
     *
     * @throws  null
     * @throws  Exception
     */
    public function purgeReStatements(): void
    {
        if (!Session::checkToken()) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        $reString = $this->input->getString('reString');

        if ($reString != null) {
            $db = Factory::getContainer()->get('DatabaseDriver');

            $query = $db->createQuery()
                ->update($db->quoteName('#__kunena_messages'))
                ->set("subject=TRIM(TRIM(LEADING {$db->quote($reString)} FROM subject))")
                ->where("subject LIKE {$db->quote($reString . '%')}");

            $db->setQuery($query);

            try {
                $db->execute();
            } catch (RuntimeException $e) {
                $this->app->enqueueMessage($e->getMessage(), 'error');

                return;
            }

            $count = $db->getAffectedRows();

            if ($count > 0) {
                $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_MENU_RE_PURGED', $count, $reString), 'success');
                $this->setRedirect(KunenaRoute::_($this->baseurl, false));
            } else {
                $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_MENU_RE_PURGE_FAILED', $reString), 'error');
                $this->setRedirect(KunenaRoute::_($this->baseurl, false));
            }
        } else {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_MENU_RE_PURGE_FORGOT_STATEMENT'), 'warning');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));
        }
    }

    /**
     * Clean ip
     *
     * @return  void
     *
     * @since   Kunena 2.0
     *
     * @throws  null
     * @throws  Exception
     */
    public function cleanupIP(): void
    {
        if (!Session::checkToken()) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        $cleanupDays = $this->input->getInt('cleanup_ip_days', 365);
        $where       = '';

        if ($cleanupDays) {
            $cleanDate = Factory::getDate()->toUnix() - ($cleanupDays * 86400);
            $where     = 'time < ' . $cleanDate;
        }

        $db = Factory::getContainer()->get('DatabaseDriver');

        if (!empty($where)) {
            $query = $db->createQuery()
                ->update($db->quoteName('#__kunena_messages'))->set('ip=NULL')->where($where);
        } else {
            $query = $db->createQuery()
                ->update($db->quoteName('#__kunena_messages'))->set('ip=NULL');
        }

        $db->setQuery($query);

        try {
            $db->execute();
        } catch (RuntimeException $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');

            return;
        }

        $count = $db->getAffectedRows();

        $deleteIpUsers = $this->input->getBool('deleteIpUsers', 0);

        if ($deleteIpUsers) {
            $db    = Factory::getContainer()->get('DatabaseDriver');
            $query = $db->createQuery()
                ->update($db->quoteName('#__kunena_users'))
                ->set('ip=NULL');
            $db->setQuery($query);

            try {
                $db->execute();
            } catch (RuntimeException $e) {
                $this->app->enqueueMessage($e->getMessage(), 'error');

                return;
            }

            $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_TOOLS_CLEANUP_IP_USERS_DONE', $count), 'success');
        }

        if ($count > 0) {
            $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_TOOLS_CLEANUP_IP_DONE', $count), 'success');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));
        } else {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_TOOLS_CLEANUP_IP_FAILED'), 'error');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));
        }
    }
    
    /**
     * Set up column language in case the updates has failed
     *
     * @return  void
     *
     * @since   Kunena 7.0.2
     *
     * @throws  null
     * @throws  Exception
     */
    public function setupcolumnlanguage(): void
    {
        $db     = Factory::getContainer()->get('DatabaseDriver');
        
        $query = 'SHOW COLUMNS FROM ' . $db->quoteName('#__kunena_users') . " LIKE 'language';";
        $db->setQuery($query);
        $columnLanguage = $db->loadResult();
        
        if (!$columnLanguage) {
            $query = 'ALTER TABLE ' . $db->quoteName('#__kunena_users') . ' ADD `language` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL AFTER `ip`';
            $db->setQuery($query);
            
            try {
                $db->execute();
            } catch (Exception $e) {
                echo $e->getMessage();
            }
            
            $this->app->enqueueMessage(Text::_('COM_KUNENA_TOOLS_COLUMN_LANGUAGE_HAS_BEEN_CREATED'), 'message');
            $this->app->redirect(KunenaRoute::_($this->baseurl, false));
        }
        
        $this->app->enqueueMessage(Text::_('COM_KUNENA_TOOLS_COLUMN_LANGUAGE_EXIST_ALREADY_NOTHING_TO_DO'), 'message');
        $this->app->redirect(KunenaRoute::_($this->baseurl, false));
    }
    
    /**
     * Set up socials in case the updates has failed
     *
     * @return  void
     *
     * @since   Kunena 7.0.2
     *
     * @throws  null
     * @throws  Exception
     */
    public function setupsocials(): void
    {
        $db     = Factory::getContainer()->get('DatabaseDriver');
        
        $query = 'SHOW COLUMNS FROM ' . $db->quoteName('#__kunena_users') . " LIKE 'socials';";
        $db->setQuery($query);
        $columnSocials = $db->loadResult();
        
        if (!$columnSocials) {
            // Create the column socials
            $query = "ALTER TABLE ' . $db->quoteName('#__kunena_users') . ' ADD `socials` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL AFTER `ip`";
            $query = $db->setQuery($query);
            
            try {
                $db->execute();
            } catch (Exception $e) {
                echo $e->getMessage();
            }
            
            // Move values of old socials columns to the JSON in column socials
            $listOldSocialsColumns = ['userid', 'facebook', 'myspace', 'linkedin', 'linkedin_company', 'digg', 'skype', 'yim', 'google', 'github', 'microsoft', 'blogspot', 'flickr', 'bebo', 'instagram', 'qqsocial', 'qzone', 'weibo', 'wechat',
                'vk', 'telegram', 'apple', 'vimeo', 'whatsapp', 'youtube', 'ok', 'pinterest', 'reddit'];
            
            // Check if column twitter exist to import value from it just in case
            $query = 'SHOW COLUMNS FROM ' . $db->quoteName('#__kunena_users') . ' LIKE ' . $db->quote('twitter');
            $db->setQuery($query);
            $columnTwitter = $db->loadResult();
            
            if ($columnTwitter) {
                $listOldSocialsColumns[] = 'twitter';
            }
            
            // Check if column x_social exist to import value from it just in case
            $query = 'SHOW COLUMNS FROM ' . $db->quoteName('#__kunena_users') . ' LIKE ' . $db->quote('x_social');
            $db->setQuery($query);
            $columnX_social = $db->loadResult();
            
            if ($columnX_social) {
                $listOldSocialsColumns[] = 'x_social';
            }
            
            // Check if column bsky_app exist to import value from it just in case
            $query = 'SHOW COLUMNS FROM ' . $db->quoteName('#__kunena_users') . ' LIKE ' . $db->quote('bsky_app');
            $db->setQuery($query);
            $columnBsky = $db->loadResult();
            
            if ($columnBsky) {
                $listOldSocialsColumns[] = 'bsky_app';
            }
            
            // Check if column bluesky_app exist to import value from it just in case
            $query = 'SHOW COLUMNS FROM ' . $db->quoteName('#__kunena_users') . ' LIKE ' . $db->quote('bluesky_app');
            $db->setQuery($query);
            $columnBluesky = $db->loadResult();
            
            if ($columnBluesky) {
                $listOldSocialsColumns[] = 'bluesky_app';
            }
            
            // Check if column delicious exist to import value from it just in case
            $query = 'SHOW COLUMNS FROM ' . $db->quoteName('#__kunena_users') . ' LIKE ' . $db->quote('delicious');
            $db->setQuery($query);
            $columnDelicious = $db->loadResult();
            
            if ($columnDelicious) {
                $listOldSocialsColumns[] = 'delicious';
            }
            
            // Prepare the JSON for each user with putting the old value of each social
            // Set the content in the column socials
            $limit = 100;
            $offset = 0;
            
            do {
                $query  = $db->createQuery()
                    ->select($db->quoteName($listOldSocialsColumns))
                    ->from($db->quoteName('#__kunena_users'))
                    ->where($db->quoteName('banned') . '= ' . $db->quote('1000-01-01 00:00:00'))
                    ->setLimit($limit, $offset);
                $db->setQuery($query);
                $valuesOldColumns = $db->loadObjectList();
                
                if (!empty($valuesOldColumns)) {
                    foreach ($valuesOldColumns as $value) {
                        $X_social = '';
                        if (isset($value->x_social)) {
                            $X_social = $value->x_social;
                        } elseif (isset($value->twitter)) {
                            $X_social = $value->twiter;
                        }
                        
                        $bluesky_app = '';
                        if (isset($value->bsky_app)) {
                            $bluesky_app = $value->bsky_app;
                        } elseif (isset($value->bluesky_app)) {
                            $bluesky_app = $value->bluesky_app;
                        }
                        
                        $fields = array(
                            $db->quoteName('socials') . ' = ' . $db->quote('{
                            "x_social": {
                            "value": "' . $X_social . '",
                            "url": "https://x.com/##VALUE##",
                            "title": "COM_KUNENA_MYPROFILE_X_SOCIAL",
                            "nourl": 0,
                            "fa": "fa-brands fa-x-twitter"
                            },
                            "facebook": {
                            "value": "' . $value->facebook . '",
                            "url": "https://www.facebook.com/##VALUE##",
                            "title": "COM_KUNENA_MYPROFILE_FACEBOOK",
                            "nourl": 0,
                            "fa": "fa-brands fa-facebook"
                            },
                            "myspace": {
                            "value": "' . $value->myspace . '",
                            "url": "https://www.myspace.com/##VALUE##",
                            "title": "COM_KUNENA_MYPROFILE_MYSPACE",
                            "nourl": 0,
                            "fa": "fa-solid fa-square-share-nodes"
                            },
                            "linkedin": {
                            "value": "' . $value->linkedin . '",
                            "url": "https://www.linkedin.com/in/##VALUE##",
                            "title": "COM_KUNENA_MYPROFILE_LINKEDIN",
                            "nourl": 0,
                            "fa": "fa-brands fa-linkedin"
                            },
                            "linkedin_company": {
                            "value": "' . $value->linkedin_company . '",
                            "url": "https://www.linkedin.com/company/##VALUE##",
                            "title": "COM_KUNENA_MYPROFILE_LINKEDIN",
                            "nourl": 0,
                            "fa": "fa-brands fa-linkedin"
                            },
                            "digg": {
                            "value": "' . $value->digg . '",
                            "url": "https://www.digg.com/users/##VALUE##",
                            "title": "COM_KUNENA_MYPROFILE_DIGG",
                            "nourl": 0,
                            "fa": "fa-brands fa-digg"
                            },
                            "skype": {
                            "value": "' . $value->skype . '",
                            "url": "skype:##VALUE##",
                            "title": "COM_KUNENA_MYPROFILE_SKYPE",
                            "nourl": 0,
                            "fa": "fa-brands fa-skype"
                            },
                            "yim": {
                            "value": "' . $value->yim . '",
                            "url": "##VALUE##",
                            "title": "COM_KUNENA_MYPROFILE_YIM",
                            "nourl": 1,
                            "fa": "fa-brands fa-yahoo"
                            },
                            "google": {
                            "value": "' . $value->google . '",
                            "url": "##VALUE##",
                            "title": "COM_KUNENA_MYPROFILE_GOOGLE",
                            "nourl": 1,
                            "fa": "fa-brands fa-google"
                            },
                            "github": {
                            "value": "' . $value->github . '",
                            "url": "https://www.github.com/##VALUE##",
                            "title": "COM_KUNENA_MYPROFILE_GITHUB",
                            "nourl": 0,
                            "fa": "fa-brands fa-github"
                            },
                            "microsoft": {
                            "value": "' . $value->microsoft . '",
                            "url": "##VALUE##",
                            "title": "COM_KUNENA_MYPROFILE_MICROSOFT",
                            "nourl": 1,
                            "fa": "fa-brands fa-microsoft"
                            },
                            "blogspot": {
                            "value": "' . $value->blogspot . '",
                            "url": "https://##VALUE##.blogspot.com/",
                            "title": "COM_KUNENA_MYPROFILE_BLOGSPOT",
                            "nourl": 0,
                            "fa": "fa-solid fa-square-share-nodes"
                            },
                            "flickr": {
                            "value": "' . $value->flickr . '",
                            "url": "https://www.flickr.com/##VALUE##",
                            "title": "COM_KUNENA_MYPROFILE_FLICKR",
                            "nourl": 0,
                            "fa": "fa-brands fa-flickr"
                            },
                            "instagram": {
                            "value": "' . $value->bebo . '",
                            "url": "https://www.instagram.com/##VALUE##/",
                            "title": "COM_KUNENA_MYPROFILE_INSTAGRAM",
                            "nourl": 0,
                            "fa": "fa-brands fa-instagram"
                            },
                            "qqsocial": {
                            "value": "' . $value->instagram . '",
                            "url": "##VALUE##",
                            "title": "COM_KUNENA_MYPROFILE_QQSOCIAL",
                            "nourl": 1,
                            "fa": "fa-solid fa-square-share-nodes"
                            },
                            "qzone": {
                            "value": "' . $value->qqsocial . '",
                            "url": "##VALUE##",
                            "title": "COM_KUNENA_MYPROFILE_QZONE",
                            "nourl": 1,
                            "fa": "fa-solid fa-square-share-nodes"
                            },
                            "weibo": {
                            "value": "' . $value->qzone . '",
                            "url": "##VALUE##",
                            "title": "COM_KUNENA_MYPROFILE_WEIBO",
                            "nourl": 1,
                            "fa": "fa-brands fa-weibo"
                            },
                            "wechat": {
                            "value": "' . $value->weibo . '",
                            "url": "##VALUE##",
                            "title": "COM_KUNENA_MYPROFILE_WECHAT",
                            "nourl": 1,
                            "fa": "fa-solid fa-square-share-nodes"
                            },
                            "vk": {
                            "value": "' . $value->wechat . '",
                            "url": "https://vk.com/##VALUE##",
                            "title": "COM_KUNENA_MYPROFILE_VK",
                            "nourl": 0,
                            "fa": "fa-brands fa-vk"
                            },
                            "telegram": {
                            "value": "' . $value->vk . '",
                            "url": "https://t.me/##VALUE##",
                            "title": "COM_KUNENA_MYPROFILE_TELEGRAM",
                            "nourl": 0,
                            "fa": "fa-brands fa-telegram"
                            },
                            "apple": {
                            "value": "' . $value->telegram . '",
                            "url": "##VALUE##",
                            "title": "COM_KUNENA_MYPROFILE_APPLE",
                            "nourl": 1,
                            "fa": "fa-brands fa-apple"
                            },
                            "vimeo": {
                            "value": "' . $value->apple . '",
                            "url": "https://vimeo.com/##VALUE##",
                            "title": "COM_KUNENA_MYPROFILE_VIMEO",
                            "nourl": 0,
                            "fa": "fa-brands fa-vimeo"
                            },
                            "whatsapp": {
                            "value": "' . $value->vimeo . '",
                            "url": "https://wa.me/##VALUE##",
                            "title": "COM_KUNENA_MYPROFILE_WHATSAPP",
                            "nourl": 0,
                            "fa": "fa-brands fa-whatsapp"
                            },
                            "youtube": {
                            "value": "' . $value->whatsapp . '",
                            "url": "https://www.youtube.com/##VALUE##",
                            "title": "COM_KUNENA_MYPROFILE_YOUTUBE",
                            "nourl": 0,
                            "fa": "fa-brands fa-youtube"
                            },
                            "ok": {
                            "value": "' . $value->youtube . '",
                            "url": "https://ok.ru/##VALUE##",
                            "title": "COM_KUNENA_MYPROFILE_OK",
                            "nourl": 0,
                            "fa": "fa-solid fa-square-share-nodes"
                            },
                            "pinterest": {
                            "value": "' . $value->ok . '",
                            "url": "https://pinterest.com/##VALUE##",
                            "title": "COM_KUNENA_MYPROFILE_PINTEREST",
                            "nourl": 0,
                            "fa": "fa-brands fa-pinterest"
                            },
                            "reddit": {
                            "value": "' . $value->pinterest . '",
                            "url": "https://www.reddit.com/user/##VALUE##",
                            "title": "COM_KUNENA_MYPROFILE_REDDIT",
                            "nourl": 0,
                            "fa": "fa-brands fa-reddit"
                            },
                            "bluesky_app": {
                            "value": "' . $value->reddit . '",
                            "url": "https://bsky.app/##VALUE##",
                            "title": "COM_KUNENA_MYPROFILE_BLUESKY_APP",
                            "nourl": 0,
                            "fa": "fa-brands fa-bluesky"
                            },
                            "threads": {
                            "value": "' . $bluesky_app . '",
                            "url": "https://www.threads.net/@##VALUE##",
                            "title": "COM_KUNENA_MYPROFILE_THREADS_APP",
                            "nourl": 0,
                            "fa": "fa-brands fa-threads"
                            }
                            }')
                        );
                        
                        $conditions = array(
                            $db->quoteName('userid') . ' = ' . $value->userid
                        );
                        
                        $query = $db->getQuery(true);
                        $query->update($db->quoteName('#__kunena_users'))->set($fields)->where($conditions);
                        $db->setQuery($query);
                        
                        try {
                            $db->execute();
                        } catch (Exception $e) {
                            echo $e->getMessage();
                        }
                    }
                    
                    $offset += $limit;
                } else {
                    break;
                }
            }  while (true);          
            
            // Check if column socialshare exist to remove the column
            $query = 'SHOW COLUMNS FROM ' . $db->quoteName('#__kunena_users') . ' LIKE ' . $db->quote('socialshare');
            $db->setQuery($query);
            $columnSocialshare = $db->loadResult();
            
            if ($columnSocialshare) {
                $listOldSocialsColumns[] = 'socialshare';
            }
            
            // Remove old socials columns            
            foreach ($listOldSocialsColumns as $column) {
                if ($column != 'userid') {
                    $query = "SHOW COLUMNS FROM " . $db->quoteName('#__kunena_users') . " LIKE '{$column}';";
                    $db->setQuery($query);
                    $column = $db->loadResult();
                 
                    if ($column) {
                        $query = $db->getQuery(true);
                        $query = 'ALTER TABLE ' . $db->quoteName('#__kunena_users') . ' DROP ' . $column . ';';
                        $db->setQuery($query);
                        $db->execute();
                    }
                }
            }

            $this->app->enqueueMessage(Text::_('COM_KUNENA_TOOLS_SOCIALS_HAS_BEEN_CREATED_AND_SOCIAL_DATA_IMPORTED'), 'message');
            $this->app->redirect(KunenaRoute::_($this->baseurl, false));
        } else {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_TOOLS_SOCIALS_ALREADY_EXISTS_NOTHING_TO_DO'), 'message');
            $this->app->redirect(KunenaRoute::_($this->baseurl, false));
        }
    }
    
    /**
     * Method to call the plugin modifysocials to send to it the data of socials to change
     *
     * @param   null  $key  key
     *
     * @return  void
     *
     * @since   Kunena 7.1.0
     *
     * @throws  Exception
     */
    public function modifysocials()
    {
        if (!PluginHelper::isEnabled('system', 'modifysocials')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_TOOLS_SOCIALS_PLUGIN_MODIFYSOCIALS_PLUGIN_NOT_ENABLED'), 'message');
            $this->app->redirect(KunenaRoute::_($this->baseurl, false));
        }
        
        $name = $this->input->getString('name');
        $profileURL = $this->input->getString('profileurl', '##VALUE##');
        $noURL = $this->input->getInt('nourl', 0);
        $fa = $this->input->getString('fa');
        
        $socials = ['name' => $name, 'profileURL' => $profileURL, 'noURL' => $noURL, 'fa' => $fa];
 
        $dispatcher = Factory::getApplication()->getDispatcher();
        PluginHelper::importPlugin('modifysocials');
        
        $result = $dispatcher->dispatch(
            'onKunenaBeforeModifySocials',
            new KunenaBeforeModifySocialsEvent(
                'onKunenaBeforeModifySocials', [
                    'socials' => $socials,
                ]))->getArgument('result', []);
       
       $this->app->enqueueMessage(Text::_('COM_KUNENA_TOOLS_SOCIALS_HAS_BEEN_CHANGED'), 'message');
       $this->app->enqueueMessage($result[0], 'message');
       $this->app->redirect(KunenaRoute::_($this->baseurl, false));

    }

    /**
     * Method to just redirect to main manager in case of use of cancel button
     *
     * @param   null  $key  key
     *
     * @return  void
     *
     * @since   Kunena 4.0
     *
     * @throws  Exception
     */
    public function cancel($key = null)
    {
        $this->app->redirect(KunenaRoute::_($this->baseurl, false));
    }

    /**
     * System Report
     *
     * @return  void
     *
     * @since   Kunena 2.0
     *
     * @throws  null
     * @throws  Exception
     */
    public function systemReport(): void
    {
        $this->setRedirect(KunenaRoute::_($this->baseurl, false));
    }
}
