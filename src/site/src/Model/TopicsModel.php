<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Models
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Model;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Model\KunenaModel;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * Topics Model for Kunena
 *
 * @since   Kunena 2.0
 */
class TopicsModel extends KunenaModel
{
    /**
     * @var     array
     * @since   Kunena 6.0
     */
    protected $topics = false;

    /**
     * @var     array
     * @since   Kunena 6.0
     */
    protected $messages = false;

    /**
     * @var     integer
     * @since   Kunena 6.0
     */
    protected $total = 0;

    /**
     * @var     boolean
     * @since   Kunena 6.0
     */
    protected $topicActions = false;

    /**
     * @var     boolean
     * @since   Kunena 6.0
     */
    protected $actionMove = false;

    /**
     * @return  boolean
     *
     * @since   Kunena 6.0
     *
     * @throws  null
     * @throws  Exception
     */
    public function getMessages()
    {
        if ($this->topics === false) {
            $this->getPosts();
        }

        return $this->messages;
    }

    /**
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     * @throws  null
     */
    protected function getPosts()
    {
        $this->topics = [];

        $start = $this->getState('list.start');
        $limit = $this->getState('list.limit');

        // Time will be calculated inside KunenaForumMessageHelper::getLatestMessages()
        $time = $this->getState('list.time');

        $params              = [];
        $params['mode']      = $this->getState('list.mode');
        $params['reverse']   = !$this->getState('list.categories.in');
        $params['starttime'] = $time;
        $params['user']      = $this->getState('user');
        list($this->total, $this->messages) = KunenaMessageHelper::getLatestMessages($this->getState('list.categories'), $start, $limit, $params);

        $topicids = [];

        foreach ($this->messages as $message) {
            $topicids[$message->thread] = $message->thread;
        }

        $authorise = 'read';

        switch ($params['mode']) {
            case 'unapproved':
                $authorise = 'approve';
                break;
            case 'deleted':
                $authorise = 'undelete';
                break;
        }

        $this->topics = KunenaTopicHelper::getTopics($topicids, $authorise);

        $userlist = $postlist = [];

        foreach ($this->messages as $message) {
            $userlist[\intval($message->userid)] = \intval($message->userid);
            $postlist[\intval($message->id)]     = \intval($message->id);
        }

        $this->_common($userlist, $postlist);
    }

    /**
     * @param   array  $userlist  userlist
     * @param   array  $postlist  postlist
     *
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    protected function _common(array $userlist = [], array $postlist = [])
    {
        if ($this->total > 0) {
            // Collect user ids for avatar prefetch when integrated
            $lastpostlist = [];

            foreach ($this->topics as $topic) {
                $userlist[\intval($topic->first_post_userid)] = \intval($topic->first_post_userid);
                $userlist[\intval($topic->last_post_userid)]  = \intval($topic->last_post_userid);
                $lastpostlist[\intval($topic->last_post_id)]  = \intval($topic->last_post_id);
            }

            // Prefetch all users/avatars to avoid user by user queries during template iterations
            if (!empty($userlist)) {
                KunenaUserHelper::loadUsers($userlist);
            }

            KunenaTopicHelper::getUserTopics(array_keys($this->topics));
            $lastreadlist = KunenaTopicHelper::fetchNewStatus($this->topics);

            // Fetch last / new post positions when user can see unapproved or deleted posts
            if ($postlist || $lastreadlist || ($this->me->userid && ($this->me->isAdmin() || KunenaAccess::getInstance()->getModeratorStatus()))) {
                KunenaMessageHelper::loadLocation($postlist + $lastpostlist + $lastreadlist);
            }
        }
    }

    /**
     * @return  integer
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     * @throws  null
     */
    public function getTotal()
    {
        if ($this->topics === false) {
            $this->getTopics();
        }

        return $this->total;
    }

    /**
     * @return  boolean
     *
     * @since   Kunena 6.0
     *
     * @throws  null
     * @throws  Exception
     */
    public function getTopics()
    {
        if ($this->topics === false) {
            $layout = $this->getState('layout');
            $mode   = $this->getState('list.mode');

            if ($mode == 'plugin') {
                $pluginmode = $this->getState('list.modetype');

                if (!empty($pluginmode)) {
                    $total  = 0;
                    $topics = false;

                    PluginHelper::importPlugin('kunena');
                    Factory::getApplication()->triggerEvent('onKunenaGetTopics', [$layout, $pluginmode, &$topics, &$total, $this]);

                    if (!empty($topics)) {
                        $this->topics = $topics;
                        $this->total  = $total;
                        $this->_common();
                    }
                }
            }

            if ($this->topics === false) {
                switch ($layout) {
                    case 'user':
                        $this->getUserTopics();
                        break;
                    default:
                        $this->getRecentTopics();
                }
            }
        }

        return $this->topics;
    }

    /**
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     * @throws  null
     */
    protected function getUserTopics()
    {
        $limitstart = $this->getState('list.start');
        $limit      = $this->getState('list.limit');

        $latestCategory   = $this->getState('list.categories');
        $latestCategoryIn = $this->getState('list.categories.in');

        $started       = false;
        $posts         = false;
        $favorites     = false;
        $subscriptions = false;

        // Set order by
        $orderby = "tt.last_post_time DESC";

        switch ($this->getState('list.mode')) {
            case 'posted':
                $posts   = true;
                $orderby = "ut.last_post_id DESC";
                break;
            case 'started':
                $started = true;
                break;
            case 'favorites':
                $favorites = true;
                break;
            case 'subscriptions':
                $subscriptions = true;
                break;
            default:
                $posts         = true;
                $favorites     = true;
                $subscriptions = true;
                $orderby       = "ut.favorite DESC, tt.last_post_time DESC";
                break;
        }

        $params = [
            'reverse'    => !$latestCategoryIn,
            'orderby'    => $orderby,
            'hold'       => 0,
            'user'       => $this->getState('user'),
            'started'    => $started,
            'posted'     => $posts,
            'favorited'  => $favorites,
            'subscribed' => $subscriptions, ];

        list($this->total, $this->topics) = KunenaTopicHelper::getLatestTopics($latestCategory, $limitstart, $limit, $params);

        $this->_common();
    }

    /**
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     * @throws  null
     */
    protected function getRecentTopics()
    {
        $limitstart = $this->getState('list.start');
        $limit      = $this->getState('list.limit');
        $time       = $this->getState('list.time');

        if (Factory::getDocument()->getType() != 'feed') {
            if ($time < 0) {
                $time = 0;
            } elseif ($time == 0) {
                $time = KunenaFactory::getSession()->lasttime;
            } else {
                $time = Factory::getDate()->toUnix() - ((int) $time * 3600);
            }
        } else {
            $time = new \DateTime($time . ' ago');
            $time = $time->getTimestamp();
        }

        $latestCategory   = $this->getState('list.categories');
        $latestCategoryIn = $this->getState('list.categories.in');

        $hold     = 0;
        $where    = '';
        $lastpost = true;

        // Reset topics.
        $this->total  = 0;
        $this->topics = [];

        switch ($this->getState('list.mode')) {
            case 'topics':
                $lastpost = false;
                break;
            case 'sticky':
                $where = 'AND tt.ordering>0';
                break;
            case 'locked':
                $where = 'AND tt.locked>0';
                break;
            case 'noreplies':
                $where = 'AND tt.posts=1';
                break;
            case 'unapproved':
                $allowed = KunenaCategoryHelper::getCategories(false, false, 'topic.approve');

                if (empty($allowed)) {
                    return;
                }

                $allowed = implode(',', array_keys($allowed));
                $hold    = '1';
                $where   = "AND tt.category_id IN ({$allowed})";
                break;
            case 'deleted':
                $allowed = KunenaCategoryHelper::getCategories(false, false, 'topic.undelete');

                if (empty($allowed)) {
                    return;
                }

                $allowed = implode(',', array_keys($allowed));
                $hold    = '2';
                $where   = "AND tt.category_id IN ({$allowed})";
                break;
            case 'replies':
            default:
                break;
        }

        $params = [
            'reverse'   => !$latestCategoryIn,
            'exclude'   => $this->setState('list.categories.exclude', 0),
            'orderby'   => $lastpost ? 'tt.last_post_time DESC' : 'tt.first_post_time DESC',
            'starttime' => $time,
            'hold'      => $hold,
            'where'     => $where, ];

        list($this->total, $this->topics) = KunenaTopicHelper::getLatestTopics($latestCategory, $limitstart, $limit, $params);

        $this->_common();
    }

    /**
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     * @throws  null
     */
    public function getTopicActions()
    {
        if ($this->topics === false) {
            $this->getTopics();
        }

        $delete = $approve = $undelete = $move = $permdelete = false;

        foreach ($this->topics as $topic) {
            if (!$delete && $topic->isAuthorised('delete')) {
                $delete = true;
            }

            if (!$approve && $topic->isAuthorised('approve')) {
                $approve = true;
            }

            if (!$undelete && $topic->isAuthorised('undelete')) {
                $undelete = true;
            }

            if (!$move && $topic->isAuthorised('move')) {
                $move = $this->actionMove = true;
            }

            if (!$permdelete && $topic->isAuthorised('permdelete')) {
                $permdelete = true;
            }
        }

        $actionDropdown[] = HTMLHelper::_('select.option', 'none', Text::_('COM_KUNENA_BULK_CHOOSE_ACTION'));

        if ($this->getState('list.mode') == 'subscriptions') {
            $actionDropdown[] = HTMLHelper::_('select.option', 'unsubscribe', Text::_('COM_KUNENA_UNSUBSCRIBE_SELECTED'));
        }

        if ($this->getState('list.mode') == 'favorites') {
            $actionDropdown[] = HTMLHelper::_('select.option', 'unfavorite', Text::_('COM_KUNENA_UNFAVORITE_SELECTED'));
        }

        if ($move) {
            $actionDropdown[] = HTMLHelper::_('select.option', 'move', Text::_('COM_KUNENA_MOVE_SELECTED'));
        }

        if ($approve) {
            $actionDropdown[] = HTMLHelper::_('select.option', 'approve', Text::_('COM_KUNENA_APPROVE_SELECTED'));
        }

        if ($delete) {
            $actionDropdown[] = HTMLHelper::_('select.option', 'delete', Text::_('COM_KUNENA_DELETE_SELECTED'));
        }

        if ($permdelete) {
            $actionDropdown[] = HTMLHelper::_('select.option', 'permdel', Text::_('COM_KUNENA_BUTTON_PERMDELETE_LONG'));
        }

        if ($undelete) {
            $actionDropdown[] = HTMLHelper::_('select.option', 'restore', Text::_('COM_KUNENA_BUTTON_UNDELETE_LONG'));
        }

        if (\count($actionDropdown) == 1) {
            return;
        }

        return $actionDropdown;
    }

    /**
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  null
     * @throws  Exception
     */
    public function getPostActions()
    {
        if ($this->messages === false) {
            $this->getPosts();
        }

        $delete = $approve = $undelete = $permdelete = false;

        foreach ($this->messages as $message) {
            if (!$delete && $message->isAuthorised('delete')) {
                $delete = true;
            }

            if (!$approve && $message->isAuthorised('approve')) {
                $approve = true;
            }

            if (!$undelete && $message->isAuthorised('undelete')) {
                $undelete = true;
            }

            if (!$permdelete && $message->isAuthorised('permdelete')) {
                $permdelete = true;
            }
        }

        $actionDropdown[] = HTMLHelper::_('select.option', 'none', Text::_('COM_KUNENA_BULK_CHOOSE_ACTION'));

        if ($approve) {
            $actionDropdown[] = HTMLHelper::_('select.option', 'approve_posts', Text::_('COM_KUNENA_APPROVE_SELECTED'));
        }

        if ($delete) {
            $actionDropdown[] = HTMLHelper::_('select.option', 'delete_posts', Text::_('COM_KUNENA_DELETE_SELECTED'));
        }

        if ($permdelete) {
            $actionDropdown[] = HTMLHelper::_('select.option', 'permdel_posts', Text::_('COM_KUNENA_BUTTON_PERMDELETE_LONG'));
        }

        if ($undelete) {
            $actionDropdown[] = HTMLHelper::_('select.option', 'restore_posts', Text::_('COM_KUNENA_BUTTON_UNDELETE_LONG'));
        }

        if (\count($actionDropdown) == 1) {
            return;
        }

        return $actionDropdown;
    }

    /**
     * @return  boolean
     *
     * @since   Kunena 6.0
     */
    public function getActionMove()
    {
        return $this->actionMove;
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @param   null  $ordering
     * @param   null  $direction
     *
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws Exception
     */
    protected function populateState($ordering = null, $direction = null)
    {
        $params = $this->getParameters();
        $this->setState('params', $params);

        $format = $this->getWord('format', 'html');
        $this->setState('format', $format);

        $active = $this->app->getMenu()->getActive();
        $active = $active ? (int) $active->id : 0;
        $layout = $this->getWord('layout', 'default');
        $this->setState('layout', $layout);

        $display = $this->getUserStateFromRequest('com_kunena.users_display', 'display', 'topics');
        $this->setState('display', $display);

        $userid = $this->getInt('userid', -1);

        if ($userid < 0) {
            $userid = $this->me->userid;
        } elseif ($userid > 0) {
            $userid = KunenaFactory::getUser($userid)->userid;
        } else {
            $userid = 0;
        }

        $this->setState('user', $userid);

        $mode = $this->getWord('mode', 'default');
        $this->setState('list.mode', $mode);

        $modetype = $this->getWord('modetype', '');
        $this->setState('list.modetype', $modetype);

        $catid = $this->getInt('catid');
        $this->setState('list.categories.exclude', 0);

        if ($catid) {
            $latestCategory   = [$catid];
            $latestCategoryIn = true;

            // Check if the category is in excluded list
            if (!empty($this->config->rssExcludedCategories)) {
                $cat_excluded = explode(',', $this->config->rssExcludedCategories);

                if (\in_array($catid, $cat_excluded)) {
                    $latestCategory   = $this->config->rssExcludedCategories;
                    $latestCategoryIn = 0;
                    $this->setState('list.categories.exclude', 1);
                }
            }
        } else {
            if (Factory::getApplication()->getDocument()->getType() != 'feed') {
                // Get configuration the categories selected from kunena latest module params when installed
                $klatestCategory   = $params->get('topics_categories_klatest', []);
                // Get configuration the categories selected from the active menu item
                $latestCategory   = $params->get('topics_categories', []);
                $latestCategoryIn = $params->get('topics_catselection', '');

                /*
                 * Check if topics_catselection is set on "Use Global"=empty, "Show Categories"=1 or "Hide Categories"=0 then if selected "Show Categories" or "Hide Categories",
                 * get the list of categories from the menu item with topics_categories.
                 * From Kunena 6.1 in Kunena menus the default value of topics_catselection for option "Use Global" should be set to 2 instead of empty
                 */
                if ((empty($latestCategoryIn) || $latestCategoryIn==2) && count($klatestCategory) == 0) {
                    if($this->config->latestCategory==0) {
                        $latestCategory = false;
                    }
                    else {
                        $latestCategory = explode(',', $this->config->latestCategory);
                    }

                    $latestCategoryIn = $this->config->latestCategoryIn;
                } elseif (count($klatestCategory) > 0) {
                    $latestCategory = $klatestCategory;
                } else {
                    // Make sure that category list is an array when it's different to zero.
                    if ($latestCategory !=0 || !empty($latestCategory)){
                        if (!\is_array($latestCategory)) {
                            $latestCategory = explode(',', $latestCategory);
                        }
                    } else {
                        $latestCategory = array();
                    }
                }
            } else {
                // Use RSS configuration.
                if (!empty($this->config->rssExcludedCategories)) {
                    $latestCategory   = $this->config->rssExcludedCategories;
                    $latestCategoryIn = 0;
                } else {
                    $latestCategory   = $this->config->rssIncludedCategories;
                    $latestCategoryIn = 1;
                }
            }
        }

        $this->setState('list.categories', $latestCategory);
        $this->setState('list.categories.in', $latestCategoryIn);

        // Selection time.
        if (Factory::getApplication()->getDocument()->getType() != 'feed') {
            // Selection time from user state / menu item / url parameter / configuration.
            if (!$this->me->exists() || $this->me->exists() && $this->me->userListtime == -2) {
                $value = $this->getUserStateFromRequest(
                    "com_kunena.topics_{$active}_{$layout}_{$mode}_{$userid}_{$catid}_list_time",
                    'sel',
                    $params->get('topics_time', $this->config->showListTime),
                    'int'
                );
                $this->setState('list.time', (int) $value);
            }

            if ($this->me->exists() && $this->me->userListtime != -2) {
                $value = $this->getUserStateFromRequest("com_kunena.topics_{$active}_{$layout}_{$mode}_{$userid}_{$catid}_list_time", 'sel', $this->me->userListtime, 'int');
                $this->setState('list.time', (int) $value);
            }
        } else {
            // Selection time.
            $value = $this->getString('sel', $this->config->rssTimeLimit);
            $this->setState('list.time', $value);
        }

        // List state information
        $value        = $this->getUserStateFromRequest("com_kunena.topics_{$active}_{$layout}_{$mode}_{$userid}_{$catid}_list_limit", 'limit', 0, 'int');
        $defaultlimit = $format != 'feed' ? $this->config->threadsPerPage : $this->config->rssLimit;

        if ($value < 1 || $value > 100) {
            $value = $defaultlimit;
        }

        $this->setState('list.limit', $value);

        $value = $this->getUserStateFromRequest("com_kunena.topics_{$active}_{$layout}_{$mode}_{$userid}_{$catid}_list_ordering", 'filter_order', 'id', 'cmd');
        $this->setState('list.ordering', $value);

        $value = $this->getUserStateFromRequest("com_kunena.topics_{$active}_{$layout}_{$mode}_{$userid}_{$catid}_list_start", 'limitstart', 0, 'int');

        // $value = $this->getInt ( 'limitstart', 0 );
        $this->setState('list.start', $value);

        $value = $this->getUserStateFromRequest("com_kunena.topics_{$active}_{$layout}_{$mode}_{$userid}_{$catid}_list_direction", 'filter_order_Dir', 'desc', 'word');

        if ($value != 'asc') {
            $value = 'desc';
        }

        $this->setState('list.direction', $value);
    }
}
