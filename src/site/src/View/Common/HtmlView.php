<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Views
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\View\Common;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Announcement\KunenaAnnouncementHelper;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\KunenaStatistics;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Forum\Libraries\Login\KunenaLogin;
use Kunena\Forum\Libraries\Menu\KunenaMenuHelper;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Forum\Libraries\View\KunenaView;
use StdClass;

/**
 * Common view
 *
 * @since   Kunena 6.0
 */
class HtmlView extends KunenaView
{
    /**
     * @var     integer
     * @since   Kunena 6.0
     */
    public $catid = 0;

    /**
     * @var     boolean
     * @since   Kunena 6.0
     */
    public $offline = false;

    public $ktemplate;

    public $app;

    public $me;

    public $state;

    public $config;

    public $memberCount;

    public $lastUserId;

    /**
     * @var array
     * @since version
     */
    public $pathway;

    public $header;

    /**
     * @param   null  $layout  layout
     * @param   null  $tpl     tpl
     *
     * @return  mixed|void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function display($layout = null, $tpl = null)
    {
        $this->state = $this->get('State');

        if ($this->config->boardOffline && !$this->me->isAdmin()) {
            $this->offline = true;
        }

        if ($this->app->scope == 'com_kunena') {
            if (!$layout) {
                throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_PAGE'), 404);
            }
        }

        return $this->displayLayout($layout, $tpl);
    }

    /**
     * @param   null  $tpl  tpl
     *
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function displayDefault($tpl = null)
    {
        $this->header = $this->escape($this->header);

        if (empty($this->html)) {
            $this->body = KunenaParser::parseBBCode($this->body);
        }

        $result = $this->loadTemplateFile($tpl);

        echo $result;
    }

    /**
     * @param   null  $tpl  tpl
     *
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function displayAnnouncement($tpl = null)
    {
        if ($this->offline) {
            return;
        }

        if ($this->config->showAnnouncement > 0) {
            $items        = KunenaAnnouncementHelper::getAnnouncements();
            $announcement = array_pop($items);

            if (!$announcement) {
                echo ' ';

                return;
            }

            $cache    = Factory::getCache('com_kunena', 'output');
            $annCache = $cache->get('announcement', 'global');

            if (!$annCache) {
                $cache->remove("{$this->ktemplate->name}.common.announcement", 'com_kunena.template');
            }

            if ($cache->start("{$this->ktemplate->name}.common.announcement", 'com_kunena.template')) {
                return;
            }

            if ($announcement && $announcement->isAuthorised('read')) {
                $result = $this->loadTemplateFile($tpl);

                echo $result;
            } else {
                echo ' ';
            }

            $cache->store($announcement->id, 'announcement', 'global');
            $cache->end();
        } else {
            echo ' ';
        }
    }

    /**
     * @param   null  $tpl  tpl
     *
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function displayForumJump($tpl = null)
    {
        if ($this->offline) {
            return;
        }

        $allowed = md5(serialize(KunenaAccess::getInstance()->getAllowedCategories()));
        $cache   = Factory::getCache('com_kunena', 'output');

        if ($cache->start("{$this->ktemplate->name}.common.jump.{$allowed}", 'com_kunena.template')) {
            return;
        }

        $result = $this->loadTemplateFile($tpl);

        echo $result;

        $cache->end();
    }

    /**
     * @param   null  $tpl  tpl
     *
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  null
     * @throws  Exception
     */
    public function displayBreadcrumb($tpl = null)
    {
        if ($this->offline) {
            return;
        }

        $catid  = $this->app->input->getInt('catid', 0);
        $id     = $this->app->input->getInt('id', 0);
        $view   = $this->app->input->getWord('view', 'default');
        $layout = $this->app->input->getWord('layout', 'default');

        $this->breadcrumb = $pathway = $this->app->getPathway();
        $active     = $this->app->getMenu()->getActive();

        if (empty($this->pathway)) {
            KunenaFactory::loadLanguage('com_kunena.sys', 'admin');

            if ($catid) {
                $parents         = KunenaCategoryHelper::getParents($catid);
                $parents[$catid] = KunenaCategoryHelper::get($catid);

                // Remove categories from pathway if menu item contains/excludes them
                if (!empty($active->query['catid']) && isset($parents[$active->query['catid']])) {
                    $curcatid = $active->query['catid'];

                    while (($item = array_shift($parents)) !== null) {
                        if ($item->id == $curcatid) {
                            break;
                        }
                    }
                }

                foreach ($parents as $parent) {
                    $pathway->addItem($this->escape($parent->name), KunenaRoute::normalize("index.php?option=com_kunena&view=category&catid={$parent->id}"));
                }
            }

            if ($view == 'announcement') {
                $pathway->addItem(Text::_('COM_KUNENA_ANN_ANNOUNCEMENTS'), KunenaRoute::normalize("index.php?option=com_kunena&view=announcement&layout=list"));
            } elseif ($id) {
                $topic = KunenaTopicHelper::get($id);
                $pathway->addItem($this->escape($topic->subject), KunenaRoute::normalize("index.php?option=com_kunena&view=category&catid={$catid}&id={$topic->id}"));
            }

            if ($view == 'topic') {
                $active_layout = (!empty($active->query['view']) && $active->query['view'] == 'topic' && !empty($active->query['layout'])) ? $active->query['layout'] : '';

                switch ($layout) {
                    case 'create':
                        if ($active_layout != 'create') {
                            $pathway->addItem($this->escape(Text::_('COM_KUNENA_NEW')));
                        }
                        break;
                    case 'reply':
                        if ($active_layout != 'reply') {
                            $pathway->addItem($this->escape(Text::_('COM_KUNENA_BUTTON_MESSAGE_REPLY')));
                        }
                        break;
                    case 'edit':
                        if ($active_layout != 'edit') {
                            $pathway->addItem($this->escape(Text::_('COM_KUNENA_EDIT')));
                        }
                        break;
                }
            }
        }

        $this->pathway = [];

        foreach ($pathway->getPathway() as $pitem) {
            $item       = new StdClass();
            $item->name = $this->escape($pitem->name);
            $item->link = KunenaRoute::_($pitem->link);

            if ($item->link) {
                $this->pathway[] = $item;
            }
        }

        $result = $this->loadTemplateFile($tpl, ['pathway' => $this->pathway]);

        echo $result;
    }

    /**
     * @param   null  $tpl  tpl
     *
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function displayWhosonline($tpl = null)
    {
        if ($this->offline) {
            return;
        }

        $moderator = \intval($this->me->isModerator()) + \intval($this->me->isAdmin());
        $cache     = Factory::getCache('com_kunena', 'output');

        if ($cache->start("{$this->ktemplate->name}.common.whosonline.{$moderator}", "com_kunena.template")) {
            return;
        }

        $users = KunenaUserHelper::getOnlineUsers();
        KunenaUserHelper::loadUsers(array_keys($users));
        $onlineusers = KunenaUserHelper::getOnlineCount();

        $who = '<strong>' . $onlineusers['user'] . ' </strong>';

        if ($onlineusers['user'] == 1) {
            $who .= Text::_('COM_KUNENA_WHO_ONLINE_MEMBER') . '&nbsp;';
        } else {
            $who .= Text::_('COM_KUNENA_WHO_ONLINE_MEMBERS') . '&nbsp;';
        }

        $who .= Text::_('COM_KUNENA_WHO_AND');
        $who .= '<strong> ' . $onlineusers['guest'] . ' </strong>';

        if ($onlineusers['guest'] == 1) {
            $who .= Text::_('COM_KUNENA_WHO_ONLINE_GUEST') . '&nbsp;';
        } else {
            $who .= Text::_('COM_KUNENA_WHO_ONLINE_GUESTS') . '&nbsp;';
        }

        $who           .= Text::_('COM_KUNENA_WHO_ONLINE_NOW');

        $onlineList = [];
        $hiddenList = [];

        foreach ($users as $userid => $usertime) {
            $user = KunenaUserHelper::get($userid);

            if (!$user->showOnline) {
                if ($moderator) {
                    $hiddenList[$user->getName()] = $user;
                }
            } else {
                $onlineList[$user->getName()] = $user;
            }
        }

        ksort($onlineList);
        ksort($hiddenList);

        // Fall back to old template file.
        $result = $this->loadTemplateFile($tpl);

        echo $result;

        $cache->end();
    }

    /**
     * @param   string  $action  action
     * @param   bool    $xhtml   xhtml
     *
     * @return  string
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function getUserlistURL($action = '', $xhtml = true)
    {
        $profile = KunenaFactory::getProfile();

        return $profile->getUserListURL($action, $xhtml);
    }

    /**
     * @param   null  $tpl  tpl
     *
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     * @throws  null
     */
    public function displayStatistics($tpl = null)
    {
        if ($this->offline) {
            return;
        }

        $cache = Factory::getCache('com_kunena', 'output');

        if ($cache->start("{$this->ktemplate->name}.common.statistics", 'com_kunena.template')) {
            return;
        }

        $kunenaStats = KunenaStatistics::getInstance();
        $kunenaStats->loadGeneral();

        $this->latestMemberLink = KunenaFactory::getUser(\intval($this->lastUserId))->getLink();
        $this->statisticsUrl    = KunenaRoute::_('index.php?option=com_kunena&view=statistics');
        $this->statisticsLink   = $this->getStatsLink($this->config->boardTitle . ' ' . Text::_('COM_KUNENA_STAT_FORUMSTATS'), '');
        $this->usercountLink    = $this->getUserlistLink('', $this->memberCount);
        $this->userlistLink     = $this->getUserlistLink('', Text::_('COM_KUNENA_STAT_USERLIST') . ' &raquo;');
        $this->moreLink         = $this->getStatsLink(Text::_('COM_KUNENA_STAT_MORE_ABOUT_STATS') . ' &raquo;');

        $result = $this->loadTemplateFile($tpl);

        echo $result;
        $cache->end();
    }

    /**
     * @param   string  $name   name
     * @param   string  $class  class
     * @param   string  $rel    rel
     *
     * @return  boolean|string
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     * @throws  null
     */
    public function getStatsLink($name, $class = '', $rel = 'follow')
    {
        $my = KunenaFactory::getUser();

        if (KunenaFactory::getConfig()->statsLinkAllowed == 0 && $my->userid == 0) {
            return false;
        }

        return '<a href="' . KunenaRoute::_('index.php?option=com_kunena&view=statistics') . '" rel="' . $rel . '" class="' . $class . '">' . $name . '</a>';
    }

    /**
     * @param   string  $action  action
     * @param   string  $name    name
     * @param   string  $rel     rel
     * @param   string  $class   class
     *
     * @return  boolean|string
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function getUserlistLink($action, $name, $rel = 'nofollow', $class = '')
    {
        $my = KunenaFactory::getUser();

        if ($name == $this->memberCount) {
            $link = KunenaFactory::getProfile()->getUserListURL($action);

            if ($link) {
                return '<a href="' . $link . '" rel="' . $rel . '" class="' . $class . '">' . $name . '</a>';
            } else {
                return $name;
            }
        } elseif ($my->userid == 0 && !KunenaFactory::getConfig()->userlistAllowed) {
            return false;
        } else {
            $link = KunenaFactory::getProfile()->getUserListURL($action);

            return '<a href="' . $link . '" rel="' . $rel . '" class="' . $class . '">' . $name . '</a>';
        }
    }

    /**
     * @param   null  $tpl  tpl
     *
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     * @throws  null
     */
    public function displayFooter($tpl = null)
    {
        if ($this->offline) {
            return;
        }

        $catid = 0;

        if ($this->config->enableRss) {
            if ($catid > 0) {
                $category = KunenaCategoryHelper::get($catid);

                if ($category->pubAccess == 0 && $category->parent) {
                    $rss_params = '&catid=' . (int) $catid;
                }
            } else {
                $rss_params = '';
            }

            if (isset($rss_params)) {
                $document = Factory::getApplication()->getDocument();
                $document->addCustomTag('<link rel="alternate" type="application/rss+xml" data-bs-toggle="tooltip" title="' . Text::_('COM_KUNENA_LISTCAT_RSS') . '" href="' . $this->getRSSURL($rss_params) . '" />');
            }
        }

        $result = $this->loadTemplateFile($tpl);

        echo $result;
    }

    /**
     * Method to get Kunena URL RSS feed by taking config option to define the data to display
     *
     * @param   string       $params  Add extras params to the URL
     * @param   bool|string  $xhtml   Replace & by & for XML compilance.
     *
     * @return  string
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     * @throws  null
     */
    private function getRSSURL($params = '', $xhtml = true)
    {
        $mode = KunenaFactory::getConfig()->rssType;

        if (!empty(KunenaFactory::getConfig()->rssFeedBurnerUrl)) {
            return KunenaFactory::getConfig()->rssFeedBurnerUrl;
        } else {
            switch ($mode) {
                case 'topic':
                    $rssType = 'mode=topics';
                    break;
                case 'recent':
                    $rssType = 'mode=replies';
                    break;
                case 'post':
                    $rssType = 'layout=posts';
                    break;
            }

            return KunenaRoute::_("index.php?option=com_kunena&view=topics&layout=default&{$rssType}{$params}?format=feed&type=rss", $xhtml);
        }
    }

    /**
     * @param   string  $name    name
     * @param   string  $rel     rel
     * @param   string  $params  params
     *
     * @return  string
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     * @throws  null
     */
    public function getRSSLink($name, $rel = 'follow', $params = '')
    {
        return '<a href="' . $this->getRSSURL($params) . '">' . $name . '</a>';
    }

    /**
     * @param   null  $tpl  tpl
     *
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     * @throws  null
     */
    public function displayMenu($tpl = null)
    {
        if ($this->offline) {
            return;
        }

        $result            = $this->loadTemplateFile($tpl);

        echo $result;
    }

    /**
     * @return  string
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function getMenu()
    {
        $basemenu = KunenaRoute::getMenu();

        if (!$basemenu) {
            return ' ';
        }

        $parameters = new Registry();
        $parameters->set('showAllChildren', $this->ktemplate->params->get('menu_showall', 0));
        $parameters->set('menutype', $basemenu->menutype);
        $parameters->set('startLevel', $basemenu->level + 1);
        $parameters->set('endLevel', $basemenu->level + $this->ktemplate->params->get('menu_levels', 1));

        $list      = KunenaMenuHelper::getList($parameters);

        return \count($list) ? $this->loadTemplateFile('menu') : '';
    }

    /**
     * @param   null  $tpl  tpl
     *
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     * @throws  null
     */
    public function displayLoginBox($tpl = null)
    {
        if ($this->offline) {
            return;
        }

        $my         = $this->app->getIdentity();
        $cache      = Factory::getCache('com_kunena', 'output');
        $cachekey   = "{$this->ktemplate->name}.common.loginbox.u{$my->id}";
        $cachegroup = 'com_kunena.template';

        // FIXME: enable caching after fixing the issues
        $contents = false; // $cache->get($cachekey, $cachegroup);

        if (!$contents) {
            $login = KunenaLogin::getInstance();

            if ($my->get('guest')) {
                $this->setLayout('login');

                if ($login) {
                    $login1          = $login;
                    $registerUrl     = $login->getRegistrationUrl();
                    $lostPasswordUrl = $login->getResetUrl();
                    $lostUsernameUrl = $login->getRemindUrl();
                    $remember        = $login->getRememberMe();
                }
            } else {
                $this->setLayout('logout');

                // Private messages
                $this->getPrivateMessageLink();

                // TODO: Edit profile (need to get link to edit page, even with integration)
                // $this->editProfileLink = '<a href="' . $url.'">'. Text::_('COM_KUNENA_PROFILE_EDIT').'</a>';
            }

            $contents = $this->loadTemplateFile($tpl);

            // FIXME: enable caching after fixing the issues
            // $cache->store($contents, $cachekey, $cachegroup);
        }

        $contents = preg_replace_callback('|\[K=(\w+)(?:\:([\w_-]+))?\]|', [$this, 'fillLoginBoxInfo'], $contents);

        echo $contents;
    }

    /**
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function getPrivateMessageLink()
    {
    }

    /**
     * @param   array  $matches  matches
     *
     * @return  mixed|string|void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function fillLoginBoxInfo($matches)
    {
        switch ($matches[1]) {
            case 'RETURN_URL':
                return base64_encode(Uri::getInstance()->toString(['path', 'query', 'fragment']));
            case 'TOKEN':
                return HTMLHelper::_('form.token');
            case 'MODULE':
                return $this->getModulePosition('kunena_profilebox');
        }
    }
}
