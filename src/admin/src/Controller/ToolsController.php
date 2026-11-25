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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\Utilities\ArrayHelper;
use Kunena\Forum\Libraries\Attachment\KunenaAttachmentHelper;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Event\KunenaBeforeDeleteSocialsEvent;
use Kunena\Forum\Libraries\Event\KunenaBeforeModifySocialsEvent;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\KunenaDiagnostics;
use Kunena\Forum\Libraries\Forum\Message\Thankyou\KunenaMessageThankyouHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Forum\Topic\Poll\KunenaPollHelper;
use Kunena\Forum\Libraries\Forum\Topic\User\KunenaTopicUserHelper;
use Kunena\Forum\Libraries\Install\KunenaModelInstall;
use Kunena\Forum\Libraries\Install\KunenaObsoleteFiles;
use Kunena\Forum\Libraries\Menu\KunenaMenuFix;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Tools\KunenaCacheFolders;
use Kunena\Forum\Libraries\Tools\KunenaOrphanFiles;
use Kunena\Forum\Libraries\Tools\KunenaUserCleanup;
use Kunena\Forum\Libraries\User\KunenaAccountActionPolicy;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Forum\Libraries\User\KunenaUserSocials;
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
    protected mixed $baseurl = null;

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
        // The messages of the tools of Kunena 8.0 are with their pages.
        $lang->load('com_kunena.views', KPATH_ADMIN);
        $this->baseurl = 'administrator/index.php?option=com_kunena&view=tools';
    }

    /**
     * Diagnostics
     *
     * @return  void
     *
     * @since   Kunena 2.0
     *
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

                $this->setRedirect(KunenaRoute::_($this->baseurl, false));

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

                $this->setRedirect(KunenaRoute::_($this->baseurl, false));

                return;
            }

            $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_SYNC_USERS_DELETE_DONE', $db->getAffectedRows()), 'success');
        }

        if ($userDelLife) {
            // Lifetime bans are stored with the maximum expiration date; temporary bans must be kept. Only the members whose Joomla
            // account is blocked are deleted, the account and the profile: the profiles of all the members banned for life were
            // deleted, also of those whose account stays (a ban of the forum does not block it), and the ban is in the profile.
            $query = $db->createQuery()
                ->select($db->quoteName('ku.userid'))
                ->from($db->quoteName('#__kunena_users', 'ku'))
                ->innerJoin($db->quoteName('#__users', 'u') . ' ON ' . $db->quoteName('u.id') . ' = ' . $db->quoteName('ku.userid'))
                ->where($db->quoteName('ku.banned') . ' = ' . $db->quote('9999-12-31 23:59:59'))
                ->where($db->quoteName('u.block') . ' = 1');
            $db->setQuery($query);

            try {
                $lifeBanned = array_map('intval', (array) $db->loadColumn());
            } catch (RuntimeException $e) {
                $this->app->enqueueMessage($e->getMessage(), 'error');

                $this->setRedirect(KunenaRoute::_($this->baseurl, false));

                return;
            }

            $deleted = 0;

            if ($lifeBanned) {
                $idList = implode(',', $lifeBanned);

                $query = $db->createQuery()
                    ->delete($db->quoteName('#__kunena_users'))
                    ->where($db->quoteName('userid') . ' IN (' . $idList . ')');
                $db->setQuery($query);

                try {
                    $db->execute();
                } catch (RuntimeException $e) {
                    $this->app->enqueueMessage($e->getMessage(), 'error');

                    $this->setRedirect(KunenaRoute::_($this->baseurl, false));

                    return;
                }

                // Only remove the Joomla accounts of the lifetime banned users, not every blocked account
                $query = $db->createQuery()
                    ->delete($db->quoteName('#__users'))
                    ->where($db->quoteName('id') . ' IN (' . $idList . ')')
                    ->where($db->quoteName('block') . ' = 1');

                $db->setQuery($query);

                try {
                    $db->execute();
                } catch (RuntimeException $e) {
                    $this->app->enqueueMessage($e->getMessage(), 'error');

                    $this->setRedirect(KunenaRoute::_($this->baseurl, false));

                    return;
                }

                $deleted = $db->getAffectedRows();

                // The groups of the deleted accounts (they stayed in the table of the groups of the users).
                $db->setQuery(
                    $db->createQuery()
                        ->delete($db->quoteName('#__user_usergroup_map'))
                        ->where($db->quoteName('user_id') . ' IN (' . $idList . ')')
                )->execute();
            }

            $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_SYNC_USERS_DELETE_DONE', $deleted), 'success');
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

                $this->setRedirect(KunenaRoute::_($this->baseurl, false));

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

                $this->setRedirect(KunenaRoute::_($this->baseurl, false));

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

                $this->setRedirect(KunenaRoute::_($this->baseurl, false));

                return;
            }

            $affectedRows += $db->getAffectedRows();

            $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_SYNC_USERS_RENAME_DONE', $affectedRows), 'success');
        }

        $this->setRedirect(KunenaRoute::_($this->baseurl, false));
    }

    /**
     * Begin category recount.
     *
     * @return  void
     *
     * @since   Kunena 2.0
     *
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
                'status'  => \sprintf("%2.1f%%", 99 * $state->current / ($state->total + 1)),
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
            // JSON with its content type: the browser does not show it as HTML (psalm.xml, TaintedHtml).
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

        if ($state === null) {
            // The recount has not been started (or it has already finished): nothing to continue.
            $this->setResponse(
                [
                    'success' => false,
                    'header'  => Text::_('COM_KUNENA_AJAX_ERROR'),
                    'message' => Text::_('COM_KUNENA_AJAX_DETAILS_BELOW'),
                    'error'   => Text::_('COM_KUNENA_AJAX_ERROR'),
                ],
                $ajax
            );
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

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
                                round(min(100 * $state->start / ($state->maxId + 1), 100)) . '%'
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
                                round(min(100 * $state->start / ($state->maxId + 1), 100)) . '%'
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
                    'status'  => \sprintf("%2.1f%%", 99 * $state->current / ($state->total + 1)),
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
                'status'  => \sprintf("%2.1f%%", 99 * $state->current / ($state->total + 1)),
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
     * @throws  Exception
     */
    public function trashMenu(): void
    {
        // The menu of the forum is deleted and made new: not for a request without the token of the form (a link or a picture
        // with this address which an administrator opens).
        if (!Session::checkToken()) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

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

                $this->setRedirect(KunenaRoute::_($this->baseurl, false));

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
     * @throws  Exception
     */
    public function cleanupIP(): void
    {
        if (!Session::checkToken()) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        $cleanupDays = trim($this->input->getString('cleanup_ip_days', ''));

        // An empty or invalid value would otherwise remove the IP of every message.
        if (!ctype_digit($cleanupDays)) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_TOOLS_CLEANUP_IP_DAYS_REQUIRED'), 'notice');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        $cleanupDays = (int) $cleanupDays;

        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->update($db->quoteName('#__kunena_messages'))
            ->set('ip=NULL')
            ->where($db->quoteName('ip') . ' IS NOT NULL');

        if ($cleanupDays) {
            $cleanDate = Factory::getDate()->toUnix() - ($cleanupDays * 86400);
            $query->where($db->quoteName('time') . ' < ' . $cleanDate);
        }

        $db->setQuery($query);

        try {
            $db->execute();
        } catch (RuntimeException $e) {
            $this->app->enqueueMessage($e->getMessage(), 'error');

            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        $count = $db->getAffectedRows();

        if ($count > 0) {
            $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_TOOLS_CLEANUP_IP_DONE', $count), 'success');
        } else {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_TOOLS_CLEANUP_IP_FAILED'), 'notice');
        }

        $deleteIpUsers = $this->input->getBool('deleteIpUsers', 0);

        if ($deleteIpUsers) {
            $query = $db->createQuery()
                ->update($db->quoteName('#__kunena_users'))
                ->set('ip=NULL')
                ->where($db->quoteName('ip') . ' IS NOT NULL');
            $db->setQuery($query);

            try {
                $db->execute();
            } catch (RuntimeException $e) {
                $this->app->enqueueMessage($e->getMessage(), 'error');

                $this->setRedirect(KunenaRoute::_($this->baseurl, false));

                return;
            }

            $usersCount = $db->getAffectedRows();

            if ($usersCount > 0) {
                $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_TOOLS_CLEANUP_IP_USERS_DONE', $usersCount), 'success');
            } else {
                $this->app->enqueueMessage(Text::_('COM_KUNENA_TOOLS_CLEANUP_IP_USERS_NONE'), 'notice');
            }
        }

        $this->setRedirect(KunenaRoute::_($this->baseurl, false));
    }

    /**
     * Set up column language in case the updates has failed
     *
     * @return  void
     *
     * @since   Kunena 7.0.2
     *
     * @throws  Exception
     */
    public function setupcolumnlanguage(): void
    {
        if (!Session::checkToken('request')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

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
                echo htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
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
     * @throws  Exception
     */
    public function setupsocials(): void
    {
        if (!Session::checkToken('request')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        $db     = Factory::getContainer()->get('DatabaseDriver');

        $query = 'SHOW COLUMNS FROM ' . $db->quoteName('#__kunena_users') . " LIKE 'socials';";
        $db->setQuery($query);
        $columnSocials = $db->loadResult();

        if (!$columnSocials) {
            // Create the column socials
            $query = 'ALTER TABLE ' . $db->quoteName('#__kunena_users') . ' ADD `socials` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL AFTER `ip`';
            $query = $db->setQuery($query);

            try {
                $db->execute();
            } catch (Exception $e) {
                echo htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
            }

            // Move values of old socials columns to the JSON in column socials
            $listOldSocialsColumns = ['userid', 'facebook', 'myspace', 'linkedin', 'linkedin_company', 'digg', 'skype', 'yim', 'google', 'github', 'microsoft', 'blogspot', 'flickr', 'bebo', 'instagram', 'qqsocial', 'qzone', 'weibo', 'wechat',
                'vk', 'telegram', 'apple', 'vimeo', 'whatsapp', 'youtube', 'ok', 'pinterest', 'reddit', 'icq'];

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

            $listOldSocialsColumnsForImport = array_diff($listOldSocialsColumns, ['icq']);

            // The social websites of every user go into the column socials through the object of the socials, as the update to 6.4
            // moves them. The JSON was written by hand without escaping (a value with a quote made the column no JSON), only for the
            // users who were never banned (the others lost their socials with the old columns, removed below), and the column
            // twitter was read as $value->twiter. Two websites have a new name: the value of the new name goes first.
            $renamed = ['twitter' => 'x_social', 'bsky_app' => 'bluesky_app'];
            $limit   = 100;
            $offset  = 0;

            do {
                $query = $db->createQuery()
                    ->select($db->quoteName($listOldSocialsColumnsForImport))
                    ->from($db->quoteName('#__kunena_users'))
                    ->order($db->quoteName('userid'))
                    ->setLimit($limit, $offset);
                $rows = (array) $db->setQuery($query)->loadObjectList();

                foreach ($rows as $row) {
                    $socials = KunenaUserSocials::getInstance((int) $row->userid, false);

                    foreach (get_object_vars($row) as $column => $value) {
                        $key = $renamed[$column] ?? $column;

                        if ($column === 'userid' || (string) $value === '' || !isset($socials->$key) || (isset($renamed[$column]) && !empty($row->$key))) {
                            continue;
                        }

                        $socials->$key->value = (string) $value;
                    }

                    $socials->save();
                }

                $offset += $limit;
            } while (\count($rows) === $limit);

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
                        $query = $db->createQuery();
                        $query = 'ALTER TABLE ' . $db->quoteName('#__kunena_users') . ' DROP ' . $column . ';';
                        $db->setQuery($query);
                        $db->execute();
                    }
                }
            }

            $this->app->enqueueMessage(Text::_('COM_KUNENA_TOOLS_SOCIALS_HAS_BEEN_CREATED_AND_SOCIAL_DATA_IMPORTED'), 'message');
            $this->app->redirect(KunenaRoute::_($this->baseurl, false));
        } else {
            // Socials already set up: still remove leftover legacy columns which are never imported
            foreach (['icq'] as $column) {
                $query = 'SHOW COLUMNS FROM ' . $db->quoteName('#__kunena_users') . ' LIKE ' . $db->quote($column);
                $db->setQuery($query);

                if ($db->loadResult()) {
                    $db->setQuery('ALTER TABLE ' . $db->quoteName('#__kunena_users') . ' DROP ' . $db->quoteName($column));
                    $db->execute();
                }
            }
            $this->app->enqueueMessage(Text::_('COM_KUNENA_TOOLS_SOCIALS_ALREADY_EXISTS_NOTHING_TO_DO'), 'message');
            $this->app->redirect(KunenaRoute::_($this->baseurl, false));
        }
    }

    /**
     * Method to call the plugin modifysocials to send to it the data of socials to change
     *
     * @return  void
     *
     * @since   Kunena 7.1.0
     *
     * @throws  Exception
     */
    public function modifysocials()
    {
        if (!Session::checkToken('request')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->app->redirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        if (!PluginHelper::isEnabled('system', 'modifysocials')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_TOOLS_SOCIALS_PLUGIN_MODIFYSOCIALS_PLUGIN_NOT_ENABLED'), 'message');
            $this->app->redirect(KunenaRoute::_($this->baseurl, false));
        }

        $name       = $this->input->getString('name');
        $profileURL = $this->input->getString('profileurl', '##VALUE##');
        $noURL      = $this->input->getInt('nourl', 0);
        $fa         = $this->input->getString('fa');

        $socials = ['name' => $name, 'profileURL' => $profileURL, 'noURL' => $noURL, 'fa' => $fa];

        $dispatcher = Factory::getApplication()->getDispatcher();
        PluginHelper::importPlugin('modifysocials');

        $result = $dispatcher->dispatch(
            'onKunenaBeforeModifySocials',
            new KunenaBeforeModifySocialsEvent(
                'onKunenaBeforeModifySocials',
                [
                    'socials' => $socials,
                ]
            )
        )->getArgument('result', []);

        $this->app->enqueueMessage(Text::_('COM_KUNENA_TOOLS_SOCIALS_HAS_BEEN_CHANGED'), 'message');
        if (!empty($result[0])) {
            $this->app->enqueueMessage($result[0], 'message');
        }
        $this->app->redirect(KunenaRoute::_($this->baseurl, false));

    }

    /**
     * Method to call the plugin modifysocials to handle the deletion of socials network selected by the user
     *
     * @return  void
     *
     * @since   Kunena 7.1.0
     *
     * @throws  Exception
     */
    public function deletesocials()
    {
        if (!Session::checkToken('request')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->app->redirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        if (!PluginHelper::isEnabled('system', 'modifysocials')) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_TOOLS_SOCIALS_PLUGIN_MODIFYSOCIALS_PLUGIN_NOT_ENABLED'), 'message');
            $this->app->redirect(KunenaRoute::_($this->baseurl, false));
        }

        $socialsToDelete = $this->input->get('socials', [], 'array');

        $socials = ['socialsToDelete' => $socialsToDelete];

        $dispatcher = Factory::getApplication()->getDispatcher();
        PluginHelper::importPlugin('modifysocials');

        $result = $dispatcher->dispatch(
            'onKunenaBeforeDeleteSocials',
            new KunenaBeforeDeleteSocialsEvent(
                'onKunenaBeforeDeleteSocials',
                [
                    'socials' => $socials,
                ]
            )
        )->getArgument('result', []);

        $this->app->enqueueMessage(Text::_('COM_KUNENA_TOOLS_SOCIALS_SELECTED_HAS_BEEN_DELETED'), 'message');
        if (!empty($result[0])) {
            $this->app->enqueueMessage($result[0], 'message');
        }
        $this->app->redirect(KunenaRoute::_($this->baseurl, false));
    }

    /**
     * Method to just redirect to main manager in case of use of cancel button
     *
     * @param   string|null  $key  key
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
     * @throws  Exception
     */
    public function systemReport(): void
    {
        $this->setRedirect(KunenaRoute::_($this->baseurl, false));
    }

    /**
     * Deletes the chosen files which Kunena no longer ships (tool "Prune old files"). Only an administrator of Kunena, and only
     * the files of the list which can be deleted (KunenaObsoleteFiles): the paths of the request are checked against it.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     *
     * @throws  Exception
     */
    public function pruneFiles(): void
    {
        $back = KunenaRoute::_($this->baseurl . '&layout=prunefiles', false);

        if (!Session::checkToken()) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirect($back);

            return;
        }

        if (!$this->app->getIdentity()->authorise('core.admin', 'com_kunena')) {
            $this->app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
            $this->setRedirect($back);

            return;
        }

        $paths = array_values(array_filter((array) $this->input->post->get('files', [], 'array'), 'is_string'));

        if (!$paths) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_NONE_CHOSEN'), 'warning');
            $this->setRedirect($back);

            return;
        }

        $result = KunenaObsoleteFiles::deleteOnSite($paths);

        if ($result['deleted']) {
            $this->app->enqueueMessage(Text::plural('COM_KUNENA_TOOLS_PRUNE_FILES_N_DELETED', \count($result['deleted'])), 'success');
        }

        if ($result['skipped']) {
            $this->app->enqueueMessage(Text::plural('COM_KUNENA_TOOLS_PRUNE_FILES_N_SKIPPED', \count($result['skipped'])), 'warning');
        }

        $this->setRedirect($back);
    }

    /**
     * Deletes the orphaned files of a folder (tool "Orphaned files"): the attachments or the avatars which no row names. The
     * list is made again on the server: no path of the request is trusted.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     *
     * @throws  Exception
     */
    public function deleteOrphans(): void
    {
        if (!$this->toolAllowed('orphanfiles')) {
            return;
        }

        $folder = $this->input->post->getCmd('scope') === 'avatars' ? KunenaOrphanFiles::AVATARS : KunenaOrphanFiles::ATTACHMENTS;
        $result = KunenaOrphanFiles::deleteOnSite($folder);

        $this->app->enqueueMessage(
            Text::plural('COM_KUNENA_TOOLS_ORPHANS_N_DELETED', $result['deleted'], HTMLHelper::_('number.bytes', $result['bytes'])),
            $result['deleted'] ? 'success' : 'info'
        );

        if ($result['failed']) {
            $this->app->enqueueMessage(Text::plural('COM_KUNENA_TOOLS_ORPHANS_N_FAILED', $result['failed']), 'warning');
        }
    }

    /**
     * Deletes the attachments whose file is missing (tool "Orphaned files"): only the chosen ids which are missing now.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     *
     * @throws  Exception
     */
    public function deleteMissingAttachments(): void
    {
        if (!$this->toolAllowed('orphanfiles')) {
            return;
        }

        $ids     = ArrayHelper::toInteger((array) $this->input->post->get('ids', [], 'array'));
        $deleted = $ids ? KunenaOrphanFiles::deleteMissingOnSite($ids) : 0;

        $this->app->enqueueMessage(Text::plural('COM_KUNENA_TOOLS_ORPHANS_MISSING_N_DELETED', $deleted), $deleted ? 'success' : 'info');
    }

    /**
     * Empties the caches of Kunena (tool "Empty the caches").
     *
     * @return  void
     *
     * @since   Kunena 8.0
     *
     * @throws  Exception
     */
    public function emptyCaches(): void
    {
        if (!$this->toolAllowed('caches')) {
            return;
        }

        $names  = array_values(array_filter((array) $this->input->post->get('folders', [], 'array'), 'is_string'));
        $result = KunenaCacheFolders::emptyOnSite($names);

        $this->app->enqueueMessage(
            Text::plural('COM_KUNENA_TOOLS_CACHES_N_EMPTIED', $result['files'], HTMLHelper::_('number.bytes', $result['bytes'])),
            'success'
        );
    }

    /**
     * Cleans up a user in one step (tool "Clean up a user"): his messages, attachments, profile and account.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     *
     * @throws  Exception
     */
    public function cleanupUser(): void
    {
        $userid = $this->input->post->getInt('userid', 0);

        if (!$this->toolAllowed('usercleanup&userid=' . $userid)) {
            return;
        }

        $options = KunenaUserCleanup::options($this->input->post->getArray());

        if (!KunenaUserCleanup::hasAction($options)) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_TOOLS_USERCLEANUP_NOTHING_CHOSEN'), 'warning');

            return;
        }

        if (!KunenaUserCleanup::summary($userid)) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_PROFILE_NO_USER'), 'error');

            return;
        }

        $action = $options['account'] === KunenaUserCleanup::ACCOUNT_BLOCK ? KunenaAccountActionPolicy::BLOCK : KunenaAccountActionPolicy::BAN;

        if (!$this->accountActionAllowed($action, $userid)) {
            return;
        }

        $done = KunenaUserCleanup::apply($userid, $options);

        if ($options['messages'] !== KunenaUserCleanup::MESSAGES_KEEP) {
            $key = $options['messages'] === KunenaUserCleanup::MESSAGES_DELETE ? 'COM_KUNENA_TOOLS_USERCLEANUP_N_DELETED' : 'COM_KUNENA_TOOLS_USERCLEANUP_N_TRASHED';
            $this->app->enqueueMessage(Text::plural($key, $done['messages']), 'success');
        }

        if ($options['attachments']) {
            $this->app->enqueueMessage(Text::plural('COM_KUNENA_TOOLS_USERCLEANUP_N_ATTACHMENTS', $done['attachments']), 'success');
        }

        if ($options['profile']) {
            $this->app->enqueueMessage(
                Text::_($done['profile'] ? 'COM_KUNENA_TOOLS_USERCLEANUP_PROFILE_DONE' : 'COM_KUNENA_TOOLS_USERCLEANUP_PROFILE_FAILED'),
                $done['profile'] ? 'success' : 'warning'
            );
        }

        if ($options['account'] !== KunenaUserCleanup::ACCOUNT_KEEP) {
            $ok = $done['account'] === $options['account'];
            $this->app->enqueueMessage(
                Text::_($ok ? ($options['account'] === KunenaUserCleanup::ACCOUNT_BLOCK ? 'COM_KUNENA_USER_BLOCKED_DONE' : 'COM_KUNENA_USER_BANNED_DONE') : 'COM_KUNENA_TOOLS_USERCLEANUP_ACCOUNT_FAILED'),
                $ok ? 'success' : 'warning'
            );
        }
    }

    /**
     * The checks of a tool which changes the site: the token of the form and an administrator of Kunena. Sets the redirect back
     * to the layout of the tool in any case.
     *
     * @param   string  $layout  The layout of the tool, with its query when it has one.
     *
     * @return  boolean  True when the tool may go on.
     *
     * @since   Kunena 8.0
     *
     * @throws  Exception
     */
    private function toolAllowed(string $layout): bool
    {
        $this->setRedirect(KunenaRoute::_($this->baseurl . '&layout=' . $layout, false));

        if (!Session::checkToken()) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');

            return false;
        }

        if (!$this->app->getIdentity()->authorise('core.admin', 'com_kunena')) {
            $this->app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');

            return false;
        }

        return true;
    }

    /**
     * May the current user do this to the account (KunenaAccountActionPolicy)? The reason is shown when not.
     *
     * @param   string  $action    The action (KunenaAccountActionPolicy).
     * @param   int     $targetId  The user.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     *
     * @throws  Exception
     */
    private function accountActionAllowed(string $action, int $targetId): bool
    {
        $me     = $this->app->getIdentity();
        $target = Factory::getContainer()->get(UserFactoryInterface::class)->loadUserById($targetId);
        $reason = KunenaAccountActionPolicy::refusal(
            $action,
            (int) $me->id,
            $targetId,
            (bool) $target->authorise('core.admin'),
            KunenaUserHelper::get($targetId)->isAdmin(),
            (bool) $me->authorise('core.admin'),
            static fn (string $right): bool => (bool) $me->authorise($right, 'com_users')
        );

        if ($reason === null) {
            return true;
        }

        if ($reason === KunenaAccountActionPolicy::SELF) {
            $message = Text::_('COM_KUNENA_LIB_USER_BAN_ERROR_YOURSELF');
        } elseif ($reason === KunenaAccountActionPolicy::ADMIN) {
            $message = Text::sprintf('COM_KUNENA_LIB_USER_BAN_ERROR_ADMIN', KunenaUserHelper::get($targetId)->getName());
        } else {
            $message = Text::_('JERROR_ALERTNOAUTHOR');
        }

        $this->app->enqueueMessage($message, 'error');

        return false;
    }
}
