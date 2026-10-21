<?php

/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Inbox
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Plugin\Kunena\Inbox\Helper;

\defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Integration\KunenaPrivate;
use Kunena\Forum\Libraries\Route\KunenaRoute;

/**
 * The private messages of the forum: the inbox in the profile of the user.
 *
 * @since   Kunena 7.2
 */
class KunenaPrivateInbox extends KunenaPrivate
{
    /**
     * @param   Registry  $params  The parameters of the plugin.
     *
     * @since   Kunena 7.2
     */
    public function __construct(Registry $params)
    {
        $this->params = $params;
    }

    /**
     * @param   int  $userid  The user.
     *
     * @return  int
     *
     * @since   Kunena 7.2
     */
    public function getUnreadCount(int $userid): int
    {
        return InboxHelper::unreadCount($userid);
    }

    /**
     * @param   string  $text  The text of the link.
     *
     * @return  string
     *
     * @since   Kunena 7.2
     */
    public function getInboxLink(string $text)
    {
        if (!$text) {
            $text = Text::_('COM_KUNENA_PMS_INBOX');
        }

        return '<a href="' . $this->getInboxURL() . '" rel="follow">' . $text . '</a>';
    }

    /**
     * @return  string
     *
     * @since   Kunena 7.2
     */
    public function getInboxURL()
    {
        return KunenaRoute::_('index.php?option=com_kunena&view=inbox');
    }

    /**
     * The link of the message button: the form for a new message in the inbox of the user.
     *
     * @param   int  $userid  The recipient.
     *
     * @return  string
     *
     * @since   Kunena 7.2
     */
    protected function getURL(int $userid): string
    {
        if (!$this->params->get('private', 1)) {
            return '';
        }

        return KunenaRoute::_('index.php?option=com_kunena&view=inbox&inboxto=' . $userid);
    }

    /**
     * @param   KunenaMessage  $message  The message which causes the notification.
     * @param   string         $type     subscription, moderation, follow, mention or thanks.
     * @param   int[]          $userids  The users who get the notification.
     * @param   int            $senderId The user who causes it.
     *
     * @return  int[]  The users who do not want the e-mail.
     *
     * @since   Kunena 7.2
     */
    public function notify(KunenaMessage $message, string $type, array $userids, int $senderId = 0): array
    {
        if (!$this->params->get('notifications', 1)) {
            return [];
        }

        InboxHelper::addNotification($message, $type, $userids, $this->params, $senderId);

        return array_values(array_filter(
            array_map('intval', $userids),
            fn (int $userid) => !InboxHelper::wantsEmail($userid, $this->params)
        ));
    }
}
