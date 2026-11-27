<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Statistics
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Widget\Whoisonline;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * Class ComponentStatisticsControllerWhoisonlineDisplay
 *
 * @since   Kunena 4.0
 */
class WidgetWhoisonlineDisplay extends KunenaControllerDisplay
{
    /**
     * @var     string
     * @since   Kunena 6.0
     */
    public mixed $usersUrl = null;

    public mixed $membersOnline = null;

    public mixed $onlineList = null;

    public mixed $hiddenList = null;

    public mixed $linkUserlistAllowedToShow = null;

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    protected mixed $name = 'Widget/WhoIsOnline';

    /**
     * Prepare Who is online display.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function before()
    {
        parent::before();

        $this->config = KunenaConfig::getInstance();

        if (!$this->config->showWhoIsOnline) {
            throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), '404');
        }

        $me        = KunenaUserHelper::getMyself();
        $moderator = \intval($me->isModerator()) + \intval($me->isAdmin());

        $users = KunenaUserHelper::getOnlineUsers();
        KunenaUserHelper::loadUsers(array_keys($users));
        $onlineusers = KunenaUserHelper::getOnlineCount();

        $this->onlineList = [];
        $this->hiddenList = [];

        foreach ($users as $userid => $usertime) {
            $user = KunenaUserHelper::get($userid);

            // A member who does not show that he is online, or who chose the status "Invisible" (as the login widget treats them).
            if (!$user->showOnline || (int) $user->status === 3) {
                if ($moderator) {
                    $this->hiddenList[$user->getName()] = $user;
                }
            } else {
                $this->onlineList[$user->getName()] = $user;
            }
        }

        // The members whom the viewer sees: a member who hides that he is online was counted (only a moderator sees him in
        // the list), so the number told when he came and went.
        $members = $moderator ? (int) $onlineusers['user'] : \count($this->onlineList);

        $who = '<strong>' . $members . ' </strong>';

        if ($members == 1) {
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

        $who .= Text::_('COM_KUNENA_WHO_ONLINE_NOW');
        $this->membersOnline = $who;

        ksort($this->onlineList);
        ksort($this->hiddenList);

        $profile        = KunenaFactory::getProfile();
        $this->usersUrl = $profile->getUserListURL();

        if (!$me->exists() && $this->config->userlistAllowed || $me->exists()) {
            $this->linkUserlistAllowedToShow = 1;
        } else {
            $this->linkUserlistAllowedToShow = 0;
        }
    }

    /**
     * Prepare document.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function prepareDocument()
    {
        $this->setTitle(Text::_('COM_KUNENA_MENU_STATISTICS_WHOSONLINE'));
    }

    /**
     * Who is online: a copy for each member (he is in the list himself, a moderator sees the hidden users) and one for all the
     * guests (KunenaLayoutCache), for the cache time.
     *
     * @return  array|null
     *
     * @since   Kunena 8.0
     */
    protected function getCacheContext(): ?array
    {
        return ['user' => (int) KunenaUserHelper::getMyself()->userid];
    }
}
