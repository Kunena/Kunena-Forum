<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Topic
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Topic\Listing;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicFinder;
use Kunena\Forum\Libraries\Language\KunenaMultilanguage;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicTags;
use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Forum\Libraries\Pagination\KunenaPagination;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Forum\Libraries\User\KunenaUserIgnore;

/**
 * Class ComponentTopicControllerListDisplay
 *
 * @since   Kunena 4.0
 */
abstract class ListDisplay extends KunenaControllerDisplay
{
    /**
     * @var     KunenaUser
     * @since   Kunena 6.0
     */
    public $me;

    /**
     * @var     array|KunenaTopic[]
     * @since   Kunena 6.0
     */
    public $topics;

    /**
     * @var     KunenaPagination
     * @since   Kunena 6.0
     */
    public $pagination;

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    public $headerText;

    public $allowed;

    public $cache;

    public $catParams;

    public $categorylist;

    public $topic;

    public $input;

    /**
     * Values of the filter bar: status, q (subject), author and language.
     *
     * @var     array
     * @since   Kunena 7.2
     */
    public $filters = ['status' => '', 'q' => '', 'author' => '', 'language' => ''];

    /**
     * Show the filter bar above the list.
     *
     * @var     boolean
     * @since   Kunena 7.2
     */
    public $filterBar = false;

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    protected $name = 'Topic/List';

    /**
     * Read the filters of the filter bar (status, subject, author, and the category when the list has none) and
     * apply them to the finder. Shows the filter bar above the list.
     *
     * @param   KunenaTopicFinder  $finder    The finder of the list.
     * @param   boolean            $category  True if the list does not filter on the category itself.
     *
     * @return  void
     *
     * @since   Kunena 7.2
     */
    protected function applyFilters(KunenaTopicFinder $finder, bool $category = false): void
    {
        // The topics of the users whom the logged in user ignores are left out.
        $finder->filterOutAuthors(KunenaUserIgnore::getIgnored(KunenaUserHelper::getMyself()->userid));

        // On a multilingual site the topics are of the language of the page (and of all languages) unless another language, or all
        // languages, was chosen.
        $language = '';

        if (KunenaMultilanguage::isEnabled()) {
            $choice   = $this->input->getString('flang', '');
            $language = $choice === 'all' ? 'all' : (isset(KunenaMultilanguage::getLanguages()[$choice]) ? $choice : KunenaMultilanguage::getCurrent());

            if ($language !== 'all') {
                $finder->filterByLanguage($language);
            }
        }

        // The filters need the reload in the background of the template: without it there is no filter bar, and the
        // filters in an old address do nothing.
        if (!KunenaFactory::getTemplate()->params->get('ajaxTopicList', 1)) {
            $this->filterBar = false;

            return;
        }

        // Not in a list which is shown inside another page (a module, the profile of a user).
        $this->filterBar = !$this->getOptions()->get('embedded', false);
        $this->filters   = [
            'tag'    => KunenaTopicTags::isEnabled() ? $this->input->getInt('ftag', 0) : 0,
            'status' => $this->input->getCmd('fstatus', ''),
            'q'      => trim($this->input->getString('fq', '')),
            'author' => trim($this->input->getString('fauthor', '')),
            'language' => $language,
        ];

        $finder
            ->filterByTag($this->filters['tag'])
            ->filterByStatus($this->filters['status'])
            ->filterBySubject($this->filters['q'])
            ->filterByAuthorName($this->filters['author']);

        $catid = $this->input->getInt('catid', 0);

        if ($category && $catid) {
            $finder->filterByCategories(KunenaCategoryHelper::getCategories([$catid], false, 'read'));
        }
    }

    /**
     * Prepare topics by pre-loading needed information.
     *
     * @param   array  $userIds  List of additional user Ids to be loaded.
     * @param   array  $mesIds   List of additional message Ids to be loaded.
     *
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    protected function prepareTopics(array $userIds = [], array $mesIds = [])
    {
        // Collect user Ids for avatar prefetch when integrated.
        $lastIds = [];

        if (!$this->topics) {
            return;
        }

        foreach ($this->topics as $topic) {
            $userIds[(int) $topic->first_post_userid] = (int) $topic->first_post_userid;
            $userIds[(int) $topic->last_post_userid]  = (int) $topic->last_post_userid;
            $lastIds[(int) $topic->last_post_id]      = (int) $topic->last_post_id;
        }

        // Prefetch all users/avatars to avoid user by user queries during template iterations.
        if (!empty($userIds)) {
            KunenaUserHelper::loadUsers($userIds);
        }

        $topicIds = array_keys((array) $this->topics);
        KunenaTopicHelper::getUserTopics($topicIds);
        KunenaTopicTags::preload($topicIds);

        // The last posts are needed one by one below: load them with one query.
        KunenaMessageHelper::getMessages($lastIds);

        $mesIds += KunenaTopicHelper::fetchNewStatus((array) $this->topics);

        // Fetch also last post positions when user can see unapproved or deleted posts.
        // TODO: Optimize? Take account of configuration option...
        if ($this->me->isAdmin() || KunenaAccess::getInstance()->getModeratorStatus()) {
            $mesIds += $lastIds;
        }

        // Load position information for all selected messages.
        if ($mesIds) {
            KunenaMessageHelper::loadLocation($mesIds);
        }

        $this->allowed = md5(serialize(KunenaAccess::getInstance()->getAllowedCategories()));
        $options = ['defaultgroup' => 'com_kunena'];
        $this->cache = Factory::getContainer()->get(CacheControllerFactoryInterface::class)->createCacheController('output', $options);

        /*
        if ($cache->start("{$this->ktemplate->name}.common.jump.{$allowed}", 'com_kunena.template'))
        {
            return;
        }*/

        $options    = [];
        $options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_FORUM_TOP'));
        // Todo: fix params
        $this->catParams    = ['sections' => 1, 'catid' => 0];
        $this->categorylist = HTMLHelper::_('select.genericlist', $options, 'catid', 'class="class="form-select fbs" size="1" onchange = "this.form.submit()"', 'value', 'text');

        $this->input = $this->app->getInput();

        // Run events.
        $params = new Registry();
        $params->set('ksource', 'kunena');
        $params->set('kunena_view', 'topic');
        $params->set('kunena_layout', 'list');
        PluginHelper::importPlugin('kunena');
        KunenaParser::prepareContent($content, 'topicList_default');

        // @todo: this has never worked as $this->topics does not exist here
        // should be removed, commented out for now
        // $prepareEvent = new KunenaPrepareEvent('onKunenaPrepare', [
        //     'context' => 'kunena.topic.list',
        //     'subject' => $this->topic,
        //     'params'  => $params,
        //     'page'    => 0
        // ]);
        // $this->app->getDispatcher()->dispatch('onKunenaPrepare', $prepareEvent);
    }

    /**
     * Prepare document.
     *
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    protected function prepareDocument()
    {
        $page       = $this->pagination->pagesCurrent;
        $total      = $this->pagination->pagesTotal;
        $headerText = $this->headerText . ($total > 1 && $page > 1 ? " - " . Text::_('COM_KUNENA_PAGES') . " {$page}" : '');

        $menu_item = $this->app->getMenu()->getActive();

        $robots = $this->app->get('robots');

        if ($robots == 'noindex, follow') {
            $this->setMetaData('robots', 'noindex, follow');
        } elseif ($robots == 'index, nofollow') {
            $this->setMetaData('robots', 'index, nofollow');
        } elseif ($robots == 'noindex, nofollow') {
            $this->setMetaData('robots', 'noindex, nofollow');
        } else {
            $this->setMetaData('robots', 'index, follow');
        }

        if ($menu_item) {
            $params             = $menu_item->getParams();
            $params_title       = $params->get('page_title');
            $params_description = $params->get('menu-meta_description');
            $params_robots      = $params->get('robots');

            if (!empty($params_title)) {
                $title = $params->get('page_title') . ($total > 1 && $page > 1 ? " - " . Text::_('COM_KUNENA_PAGES') . " {$page}" : '');
                $this->setTitle($title);
            } else {
                $this->setTitle($headerText);
            }

            if (!empty($params_description)) {
                $description = $params->get('menu-meta_description') . ($total > 1 && $page > 1 ? " - " . Text::_('COM_KUNENA_PAGES') . " {$page}" : '');
                $this->setDescription($description);
            } else {
                $description = Text::_('COM_KUNENA_THREADS_IN_FORUM') . ': ' . $this->config->boardTitle . ($total > 1 && $page > 1 ? " - " . Text::_('COM_KUNENA_PAGES') . " {$page}" : '');
                $this->setDescription($description);
            }

            if (!empty($params_robots)) {
                $robots = $params->get('robots');
                $this->setMetaData('robots', $robots);
            }
        }
    }

    /**
     * Get Topic Actions.
     *
     * @param   array  $topics   topics
     * @param   array  $actions  actions
     *
     * @return  array|void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    protected function getTopicActions(array $topics, $actions = ['delete', 'approve', 'undelete', 'move', 'permdelete'])
    {
        if (!$actions) {
            return;
        }

        $options                = [];
        $options['none']        = HTMLHelper::_('select.option', 'none', Text::_('COM_KUNENA_BULK_CHOOSE_ACTION'));
        $options['unsubscribe'] = HTMLHelper::_('select.option', 'unsubscribe', Text::_('COM_KUNENA_UNSUBSCRIBE_SELECTED'));
        $options['unfavorite']  = HTMLHelper::_('select.option', 'unfavorite', Text::_('COM_KUNENA_UNFAVORITE_SELECTED'));
        $options['move']        = HTMLHelper::_('select.option', 'move', Text::_('COM_KUNENA_MOVE_SELECTED'));
        $options['approve']     = HTMLHelper::_('select.option', 'approve', Text::_('COM_KUNENA_APPROVE_SELECTED'));
        $options['delete']      = HTMLHelper::_('select.option', 'delete', Text::_('COM_KUNENA_DELETE_SELECTED'));
        $options['permdelete']  = HTMLHelper::_('select.option', 'permdel', Text::_('COM_KUNENA_BUTTON_PERMDELETE_LONG'));
        $options['undelete']    = HTMLHelper::_('select.option', 'restore', Text::_('COM_KUNENA_BUTTON_UNDELETE_LONG'));

        // Only display actions that are available to user.
        $actions = array_combine($actions, array_fill(0, \count($actions), false));
        array_unshift($actions, $options['none']);

        foreach ($topics as $topic) {
            foreach ($actions as $action => $value) {
                if ($value !== false) {
                    continue;
                }

                switch ($action) {
                    case 'unsubscribe':
                    case 'unfavorite':
                        $actions[$action] = isset($options[$action]) ? $options[$action] : false;
                        break;
                    default:
                        $actions[$action] = isset($options[$action]) && $topic->isAuthorised($action) ? $options[$action] : false;
                }
            }
        }

        $actions = array_filter($actions, function ($item) {
            return !empty($item);
        });

        if (\count($actions) == 1) {
            return;
        }

        return $actions;
    }

    /**
     * Get Message Actions.
     *
     * @param   array  $messages  messages
     * @param   array  $actions   actions
     *
     * @return  array|void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    protected function getMessageActions(array $messages, $actions = ['approve', 'undelete', 'delete', 'move', 'permdelete'])
    {
        if (!$actions) {
            return;
        }

        $options               = [];
        $options['none']       = HTMLHelper::_('select.option', 'none', Text::_('COM_KUNENA_BULK_CHOOSE_ACTION'));
        $options['approve']    = HTMLHelper::_('select.option', 'approve_posts', Text::_('COM_KUNENA_APPROVE_SELECTED'));
        $options['delete']     = HTMLHelper::_('select.option', 'delete_posts', Text::_('COM_KUNENA_DELETE_SELECTED'));
        $options['move']       = HTMLHelper::_('select.option', 'move', Text::_('COM_KUNENA_MOVE_SELECTED'));
        $options['permdelete'] = HTMLHelper::_('select.option', 'permdel_posts', Text::_('COM_KUNENA_BUTTON_PERMDELETE_LONG'));
        $options['undelete']   = HTMLHelper::_('select.option', 'restore_posts', Text::_('COM_KUNENA_BUTTON_UNDELETE_LONG'));

        // Only display actions that are available to user.
        $actions = array_combine($actions, array_fill(0, \count($actions), false));
        array_unshift($actions, $options['none']);

        foreach ($messages as $message) {
            foreach ($actions as $action => $value) {
                if ($value !== false) {
                    continue;
                }

                $actions[$action] = isset($options[$action]) && $message->isAuthorised($action) ? $options[$action] : false;
            }
        }

        $actions = array_filter($actions, function ($item) {
            return !empty($item);
        });

        if (\count($actions) == 1) {
            return;
        }

        return $actions;
    }
}
