<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.User
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\User;

\defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Event\DispatcherInterface;
use Joomla\Input\Input;
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Database\Finder\KunenaFinder;
use Kunena\Forum\Libraries\Event\KunenaUserTabsEvent;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicListPreload;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\User\KunenaBan;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserFollow;
use Kunena\Forum\Libraries\User\KunenaUserIgnore;
use stdClass;
use Stringable;

/**
 * KunenaLayoutUserItem
 *
 * @since   Kunena 4.0
 */
class UserItem extends KunenaLayout
{
    /**
     * @var     KunenaUser
     * @since   Kunena 6.0
     */
    public mixed $profile = null;

    /**
     * @var     KunenaUser
     * @since   Kunena 6.0
     */
    public mixed $me = null;

    /**
     * @var     KunenaConfig
     * @since   Kunena 6.0
     */
    public mixed $config = null;

    /**
     * @var     KunenaUser
     * @since   Kunena 6.0
     */
    public mixed $user = null;

    public mixed $output = null;

    public mixed $headerText = null;

    public mixed $pagination = null;

    public mixed $tabs = null;

    public mixed $state = null;

    public mixed $candisplaymail = null;

    public mixed $ktemplate = null;

    public mixed $points = null;

    public mixed $medals = null;

    public mixed $private = null;

    public mixed $socials = null;

    public mixed $avatar = null;

    public mixed $banInfo = null;

    /**
     * The controllers of the lists of the tabs, prepared together.
     *
     * @var     array
     * @since   Kunena 8.0
     */
    protected array $tabRequests = [];

    /**
     * Method to get tabs for user profile
     *
     * @return  array
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function getTabs()
    {
        $this->tabRequests = [];

        $banInfo   = KunenaBan::getInstanceByUserid($this->user->id, true);
        $myProfile = $this->profile->isMyself();
        $moderator = $this->me->isModerator();

        // Decide which tabs to display.
        $showPosts          = true;
        $showSubscriptions  = $this->config->allowSubscriptions && ($myProfile || $moderator);
        $showFavorites      = $this->config->allowFavorites && $myProfile;
        $showThankYou       = $this->config->showThankYou && $this->me->exists();
        $showUnapproved     = $myProfile && ($this->me->isAdmin() || KunenaAccess::getInstance()->getModeratorStatus());
        $showAttachments    = $this->config->showImgFilesManageProfile && ($moderator || $myProfile);
        $showListofBanGiven = $moderator && $myProfile;
        $showBanUser        = $moderator && !$myProfile;

        try {
            $showBanHistory = $banInfo->canBan();
        } catch (Exception $e) {
            $showBanHistory = false;
        }

        // Define all tabs.
        $tabs = [];

        if ($showPosts) {
            $params = [
                'embedded'            => 1,
                'topics_categories'   => 0,
                'topics_catselection' => 1,

                'userid'           => $this->profile->userid,
                'mode'             => 'latest',
                'sel'              => -1,
                'limit'            => 10,
                'filter_order'     => 'time',
                'limitstart'       => 0,
                'filter_order_Dir' => 'desc',
                'display'          => $this->state->get('display', ''),
            ];

            $tab           = new stdClass();
            $tab->title    = Text::_('COM_KUNENA_USERPOSTS');
            $tab->content  = $this->tabRequest('Message/Listing/Recent', new Input($params), $params);
            $tab->active   = true;
            $tabs['posts'] = $tab;
        }

        if ($showSubscriptions) {
            $tab          = new stdClass();
            $tab->title   = Text::_('COM_KUNENA_SUBSCRIPTIONS');
            $tab->content = [];

            if ($this->config->categorySubscriptions != 'disabled') {
                $params       = [
                    'embedded' => 1,

                    'userid'           => $this->profile->userid,
                    'limit'            => 10,
                    'filter_order'     => 'time',
                    'limitstart'       => 0,
                    'filter_order_Dir' => 'desc',
                ];
                $tab->content[] = $this->tabRequest('Category/Subscriptions', new Input($params), $params);
            }

            if ($this->config->topicSubscriptions != 'disabled') {
                $params       = [
                    'embedded'            => 1,
                    'topics_categories'   => 0,
                    'topics_catselection' => 1,

                    'userid'           => $this->profile->userid,
                    'mode'             => 'subscriptions',
                    'sel'              => -1,
                    'limit'            => 10,
                    'filter_order'     => 'time',
                    'limitstart'       => 0,
                    'filter_order_Dir' => 'desc',
                ];
                $tab->content[] = $this->tabRequest('Topic/Listing/User', new Input($params), $params);
            }

            $tab->active = false;

            if ($tab->content) {
                $tab->content          = self::joinParts($tab->content);
                $tabs['subscriptions'] = $tab;
            }
        }

        if ($showFavorites) {
            $params = [
                'embedded'            => 1,
                'topics_categories'   => 0,
                'topics_catselection' => 1,
                'userid'              => $this->profile->userid,
                'mode'                => 'favorites',
                'sel'                 => -1,
                'limit'               => 10,
                'filter_order'        => 'time',
                'limitstart'          => 0,
                'filter_order_Dir'    => 'desc',
            ];

            $tab               = new stdClass();
            $tab->title        = Text::_('COM_KUNENA_FAVORITES');
            $tab->content      = $this->tabRequest('Topic/Listing/User', new Input($params), $params);
            $tab->active       = false;
            $tabs['favorites'] = $tab;
        }

        if ($myProfile && KunenaUserFollow::isEnabled()) {
            // The users whom the user follows, and the latest topics which they started.
            $following = KunenaUserFollow::getFollowingList($this->profile->userid);
            $feed      = '';

            if ($following) {
                $params = [
                    'embedded'            => 1,
                    'topics_categories'   => 0,
                    'topics_catselection' => 1,
                    'userid'              => $this->profile->userid,
                    'mode'                => 'following',
                    'sel'                 => -1,
                    'limit'               => 10,
                    'filter_order'        => 'time',
                    'limitstart'          => 0,
                    'filter_order_Dir'    => 'desc',
                ];
                $feed = $this->tabRequest('Topic/Listing/User', new Input($params), $params);
            }

            $tab               = new stdClass();
            $tab->title        = Text::_('COM_KUNENA_FOLLOWING_TAB');
            $tab->content      = $this->subLayout('User/Item/Following')->set('following', $following)->set('feed', $feed);
            $tab->active       = false;
            $tabs['following'] = $tab;

            $tab               = new stdClass();
            $tab->title        = Text::_('COM_KUNENA_FOLLOWERS_TAB');
            $tab->content      = $this->subLayout('User/Item/Followers')->set('followers', KunenaUserFollow::getFollowersList($this->profile->userid));
            $tab->active       = false;
            $tabs['followers'] = $tab;
        }

        if ($myProfile && KunenaUserIgnore::isEnabled()) {
            $tab              = new stdClass();
            $tab->title       = Text::_('COM_KUNENA_IGNORE_TAB');
            $tab->content     = $this->subLayout('User/Item/Ignored')->set('ignored', KunenaUserIgnore::getIgnoredList($this->profile->userid));
            $tab->active      = false;
            $tabs['ignored']  = $tab;
        }

        if ($showThankYou) {
            $tab          = new stdClass();
            $tab->title   = Text::_('COM_KUNENA_THANK_YOU');
            $tab->content = [];

            $params       = [
                'embedded'            => 1,
                'topics_categories'   => 0,
                'topics_catselection' => 1,

                'userid'           => $this->profile->userid,
                'mode'             => 'mythanks',
                'sel'              => -1,
                'limit'            => 10,
                'filter_order'     => 'time',
                'limitstart'       => 0,
                'filter_order_Dir' => 'desc',
            ];
            $tab->content[] = $this->tabRequest('Message/Listing/Recent', new Input($params), $params);

            $params       = [
                'embedded'            => 1,
                'topics_categories'   => 0,
                'topics_catselection' => 1,
                'userid'              => $this->profile->userid,
                'mode'                => 'thankyou',
                'sel'                 => -1,
                'limit'               => 10,
                'filter_order'        => 'time',
                'limitstart'          => 0,
                'filter_order_Dir'    => 'desc',
            ];
            $tab->content[] = $this->tabRequest('Message/Listing/Recent', new Input($params), $params);

            $tab->content     = self::joinParts($tab->content);
            $tab->active      = false;
            $tabs['thankyou'] = $tab;
        }

        if ($showUnapproved) {
            $params             = [
                'embedded'            => 1,
                'topics_categories'   => 0,
                'topics_catselection' => 1,
                'userid'              => $this->profile->userid,
                'mode'                => 'unapproved',
                'sel'                 => -1,
                'limit'               => 10,
                'filter_order'        => 'time',
                'limitstart'          => 0,
                'filter_order_Dir'    => 'desc',
            ];
            $tab                = new stdClass();
            $tab->title         = Text::_('COM_KUNENA_MESSAGE_ADMINISTRATION');
            $tab->content       = $this->tabRequest('Message/Listing/Recent', new Input($params), $params);
            $tab->active        = false;
            $tabs['unapproved'] = $tab;
        }

        if ($showAttachments) {
            $params              = [
                'embedded' => 1,
                'userid'   => $this->profile->userid,
            ];
            $tab                 = new stdClass();
            $tab->title          = Text::_('COM_KUNENA_MANAGE_ATTACHMENTS');
            $tab->content        = $this->tabRequest('User/Attachments', new Input($params), $params);
            $tab->active         = false;
            $tabs['attachments'] = $tab;
        }

        if ($showListofBanGiven) {
            $tab                = new stdClass();
            $tab->title         = Text::_('COM_KUNENA_BAN_LIST_OF_BANNED_USERS');
            $tab->content       = $this->tabRequest('User/Ban/Manager', new Input($params), $params);
            $tab->active        = false;
            $tabs['banmanager'] = $tab;
        }

        if ($showBanHistory) {
            $tab                = new stdClass();
            $tab->title         = Text::_('COM_KUNENA_BAN_BANHISTORY');
            $tab->content       = $this->tabRequest('User/Ban/History');
            $tab->active        = false;
            $tabs['banhistory'] = $tab;
        }

        if ($showBanUser) {
            $tab             = new stdClass();
            $tab->title      = $banInfo->exists() ? Text::_('COM_KUNENA_BAN_EDIT') : Text::_('COM_KUNENA_BAN_NEW');
            $tab->content    = $this->tabRequest('User/Ban/Form');
            $tab->active     = false;
            $tabs['banuser'] = $tab;
        }

        PluginHelper::importPlugin('kunena');

        $dispatcher = Factory::getContainer()->get(DispatcherInterface::class);
        $event      = $dispatcher->dispatch(
            'onKunenaUserTabs',
            new KunenaUserTabsEvent(
                'onKunenaUserTabs',
                ['tabs' => $tabs, 'profile' => $this->profile, 'me' => $this->me]
            )
        );

        // A plugin can add tabs (and change the active tab): it sets the argument tabs of the event.
        $pluginTabs = $event->getArgument('tabs', $tabs);
        $tabs       = \is_array($pluginTabs) ? $pluginTabs : $tabs;

        // Every tab is shown, but their lists do not load what they need one after the other: all the lists are prepared
        // first, what they need is loaded together (one query per kind), then the layout makes them.
        $this->prepareTabs();

        return $tabs;
    }

    /**
     * A list of a tab: the request of a controller, kept for the preparation of all the tabs.
     *
     * @param   string      $path     The path of the controller.
     * @param   Input|null  $input    The input.
     * @param   mixed       $options  The options.
     *
     * @return  mixed  The controller (made when it is shown).
     *
     * @since   Kunena 8.0
     */
    protected function tabRequest(string $path, ?Input $input = null, $options = null)
    {
        $request             = $this->subRequest($path, $input, $options);
        $this->tabRequests[] = $request;

        return $request;
    }

    /**
     * Prepare the lists of all the tabs, then load together what they need. Two passes: the first one surveys the lists (their
     * finders note the topics and the messages which they find, and give nothing); the topics and the messages of all the lists
     * are loaded together; the lists which found something are prepared again, for real (their searches are remembered, they do
     * not run again). Then what the lists need (users, tags, places, ...) is loaded together.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 8.0
     */
    protected function prepareTabs(): void
    {
        $requests = array_filter($this->tabRequests, static fn ($request) => $request instanceof KunenaControllerDisplay);

        KunenaTopicListPreload::start();
        KunenaFinder::remember(true);

        try {
            $again = [];

            KunenaTopicListPreload::startSurvey();

            try {
                foreach ($requests as $index => $request) {
                    $request->prepare();

                    if (KunenaTopicListPreload::takeFound()) {
                        $request->resetPreparation();
                        $again[$index] = $request;
                    }
                }
            } finally {
                KunenaTopicListPreload::stopSurvey();
            }

            foreach ($again as $request) {
                $request->prepare();
            }
        } finally {
            KunenaFinder::remember(false);
            KunenaTopicListPreload::flush();
        }
    }

    /**
     * Several lists in one tab, joined into one text only when the tab is shown (joining them before would make them before the
     * other tabs are prepared).
     *
     * @param   array  $parts  The lists.
     *
     * @return  Stringable
     *
     * @since   Kunena 8.0
     */
    public static function joinParts(array $parts): Stringable
    {
        return new class ($parts) implements Stringable {
            public function __construct(public array $parts)
            {
            }

            public function __toString(): string
            {
                return implode('', array_map('strval', $this->parts));
            }
        };
    }

    /**
     * Method to display unapproved posts
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function displayUnapprovedPosts()
    {
        $params = [
            'topics_categories'   => 0,
            'topics_catselection' => 1,
            'userid'              => $this->user->id,
            'mode'                => 'unapproved',
            'sel'                 => -1,
            'limit'               => 6,
            'filter_order'        => 'time',
            'limitstart'          => 0,
            'filter_order_Dir'    => 'desc',
        ];

        KunenaForum::display('topics', 'posts', 'embed', $params);
    }

    /**
     * Method to display user posts
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function displayUserPosts()
    {
        $params = [
            'topics_categories'   => 0,
            'topics_catselection' => 1,
            'userid'              => $this->user->id,
            'mode'                => 'latest',
            'sel'                 => 8760,
            'limit'               => 6,
            'filter_order'        => 'time',
            'limitstart'          => 0,
            'filter_order_Dir'    => 'desc',
        ];

        KunenaForum::display('topics', 'posts', 'embed', $params);
    }

    /**
     * Method to display who got thankyou
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function displayGotThankyou()
    {
        $params = [
            'topics_categories'   => 0,
            'topics_catselection' => 1,
            'userid'              => $this->user->id,
            'mode'                => 'mythanks',
            'sel'                 => -1,
            'limit'               => 6,
            'filter_order'        => 'time',
            'limitstart'          => 0,
            'filter_order_Dir'    => 'desc',
        ];

        KunenaForum::display('topics', 'posts', 'embed', $params);
    }

    /**
     * Method to display who said thankyou
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function displaySaidThankyou()
    {
        $params = [
            'topics_categories'   => 0,
            'topics_catselection' => 1,
            'userid'              => $this->user->id,
            'mode'                => 'thankyou',
            'sel'                 => -1,
            'limit'               => 6,
            'filter_order'        => 'time',
            'limitstart'          => 0,
            'filter_order_Dir'    => 'desc',
        ];

        KunenaForum::display('topics', 'posts', 'embed', $params);
    }

    /**
     * Method to display favorites topics
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function displayFavorites()
    {
        $params = [
            'topics_categories'   => 0,
            'topics_catselection' => 1,
            'userid'              => $this->user->id,
            'mode'                => 'favorites',
            'sel'                 => -1,
            'limit'               => 6,
            'filter_order'        => 'time',
            'limitstart'          => 0,
            'filter_order_Dir'    => 'desc',
        ];

        KunenaForum::display('topics', 'user', 'embed', $params);
    }

    /**
     * Method to display subscriptions
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function displaySubscriptions()
    {
        if ($this->config->topicSubscriptions == 'disabled') {
            return;
        }

        $params = [
            'topics_categories'   => 0,
            'topics_catselection' => 1,
            'userid'              => $this->user->id,
            'mode'                => 'subscriptions',
            'sel'                 => -1,
            'limit'               => 6,
            'filter_order'        => 'time',
            'limitstart'          => 0,
            'filter_order_Dir'    => 'desc',
        ];

        KunenaForum::display('topics', 'user', 'embed', $params);
    }

    /**
     * Method to display categories subscriptions
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function displayCategoriesSubscriptions()
    {
        if ($this->config->categorySubscriptions == 'disabled') {
            return;
        }

        $params = [
            'userid'           => $this->user->id,
            'limit'            => 6,
            'filter_order'     => 'time',
            'limitstart'       => 0,
            'filter_order_Dir' => 'desc',
        ];

        KunenaForum::display('category', 'user', 'embed', $params);
    }
}
