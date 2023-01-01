<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.User
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\User;

\defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Input\Input;
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\User\KunenaBan;
use Kunena\Forum\Libraries\User\KunenaUser;
use stdClass;

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
    public $profile;

    /**
     * @var     KunenaUser
     * @since   Kunena 6.0
     */
    public $me;

    /**
     * @var     KunenaConfig
     * @since   Kunena 6.0
     */
    public $config;

    /**
     * @var     KunenaUser
     * @since   Kunena 6.0
     */
    public $user;

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
            $tab->content  = $this->subRequest('Message/Listing/Recent', new Input($params), $params);
            $tab->active   = true;
            $tabs['posts'] = $tab;
        }

        if ($showSubscriptions) {
            $tab          = new stdClass();
            $tab->title   = Text::_('COM_KUNENA_SUBSCRIPTIONS');
            $tab->content = '';

            if ($this->config->categorySubscriptions != 'disabled') {
                $params       = [
                    'embedded' => 1,

                    'userid'           => $this->profile->userid,
                    'limit'            => 10,
                    'filter_order'     => 'time',
                    'limitstart'       => 0,
                    'filter_order_Dir' => 'desc',
                ];
                $tab->content .= $this->subRequest('Category/Subscriptions', new Input($params), $params);
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
                $tab->content .= $this->subRequest('Topic/Listing/User', new Input($params), $params);
            }

            $tab->active = false;

            if ($tab->content) {
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
            $tab->content      = $this->subRequest('Topic/Listing/User', new Input($params), $params);
            $tab->active       = false;
            $tabs['favorites'] = $tab;
        }

        if ($showThankYou) {
            $tab          = new stdClass();
            $tab->title   = Text::_('COM_KUNENA_THANK_YOU');
            $tab->content = '';

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
            $tab->content .= $this->subRequest('Message/Listing/Recent', new Input($params), $params);

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
            $tab->content .= $this->subRequest('Message/Listing/Recent', new Input($params), $params);

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
            $tab->content       = $this->subRequest('Message/Listing/Recent', new Input($params), $params);
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
            $tab->content        = $this->subRequest('User/Attachments', new Input($params), $params);
            $tab->active         = false;
            $tabs['attachments'] = $tab;
        }

        if ($showListofBanGiven) {
            $tab                = new stdClass();
            $tab->title         = Text::_('COM_KUNENA_BAN_LIST_OF_BANNED_USERS');
            $tab->content       = $this->subRequest('User/Ban/Manager', new Input($params), $params);
            $tab->active        = false;
            $tabs['banmanager'] = $tab;
        }

        if ($showBanHistory) {
            $tab                = new stdClass();
            $tab->title         = Text::_('COM_KUNENA_BAN_BANHISTORY');
            $tab->content       = $this->subRequest('User/Ban/History');
            $tab->active        = false;
            $tabs['banhistory'] = $tab;
        }

        if ($showBanUser) {
            $tab             = new stdClass();
            $tab->title      = $banInfo->exists() ? Text::_('COM_KUNENA_BAN_EDIT') : Text::_('COM_KUNENA_BAN_NEW');
            $tab->content    = $this->subRequest('User/Ban/Form');
            $tab->active     = false;
            $tabs['banuser'] = $tab;
        }

        PluginHelper::importPlugin('kunena');

        $plugins = Factory::getApplication()->triggerEvent('on\Kunena\Forum\Libraries\User\KunenaUserTabs', [$tabs]);

        $tabs = $tabs + $plugins;

        return $tabs;
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
