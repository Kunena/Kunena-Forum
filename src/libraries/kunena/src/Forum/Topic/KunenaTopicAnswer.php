<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Forum.Topic
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Forum\Topic;

\defined('_JEXEC') or die();

use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\User\KunenaUser;

/**
 * The accepted answer of a topic: one reply which the author of the topic or a moderator chose as the answer of the question. The
 * topic keeps its id (answer_id); accepting it also marks the topic as solved (the topic icon solved), removing it marks it as not
 * solved again.
 *
 * @since   Kunena 8.0
 */
abstract class KunenaTopicAnswer
{
    /**
     * Whether the message is the accepted answer of the topic.
     *
     * @param   KunenaTopic    $topic    The topic.
     * @param   KunenaMessage  $message  The message.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function isAnswer(KunenaTopic $topic, KunenaMessage $message): bool
    {
        return (int) $message->id > 0 && (int) $topic->answer_id === (int) $message->id;
    }

    /**
     * Whether the message can be the answer of the topic: a published reply of the topic (not its first post), in a published topic
     * which was not moved.
     *
     * @param   KunenaTopic    $topic    The topic.
     * @param   KunenaMessage  $message  The message.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function canBeAnswer(KunenaTopic $topic, KunenaMessage $message): bool
    {
        if ((int) $message->id <= 0 || (int) $message->thread !== (int) $topic->id) {
            return false;
        }

        if ((int) $message->id === (int) $topic->first_post_id) {
            return false;
        }

        return (int) $message->hold === 0 && (int) $topic->hold === 0 && !(int) $topic->moved_id;
    }

    /**
     * Whether the user may accept the message as the answer of the topic, or remove it: the moderators of the category, and the
     * author of the topic when it is not locked and he is not banned (as Mark as solved).
     *
     * @param   KunenaTopic    $topic      The topic.
     * @param   KunenaMessage  $message    The message.
     * @param   KunenaUser     $user       The user.
     * @param   bool           $moderator  The user is a moderator of the category of the topic.
     * @param   bool           $enabled    The setting acceptedAnswers.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function mayChoose(KunenaTopic $topic, KunenaMessage $message, KunenaUser $user, bool $moderator, bool $enabled): bool
    {
        if (!$enabled || !self::canBeAnswer($topic, $message)) {
            return false;
        }

        if ($moderator) {
            return true;
        }

        if (!$user->exists() || !(int) $user->userid || $user->isBanned()) {
            return false;
        }

        return (int) $topic->first_post_userid === (int) $user->userid && !$topic->locked;
    }

    /**
     * Accept the message as the answer of the topic, or remove it when it is the answer (the topic is not saved here).
     *
     * @param   KunenaTopic    $topic       The topic.
     * @param   KunenaMessage  $message     The message.
     * @param   bool           $solvedIcon  The topic icon solved can be used (the setting topicIcons and the icon of the template).
     *
     * @return  boolean  True when the message is now the answer, false when the answer was removed.
     *
     * @since   Kunena 8.0
     */
    public static function toggle(KunenaTopic $topic, KunenaMessage $message, bool $solvedIcon): bool
    {
        if (self::isAnswer($topic, $message)) {
            $topic->answer_id = 0;

            if ($solvedIcon && (int) $topic->icon_id === KunenaTopic::ICON_SOLVED) {
                $topic->icon_id = 0;
            }

            return false;
        }

        $topic->answer_id = (int) $message->id;

        if ($solvedIcon) {
            $topic->icon_id = KunenaTopic::ICON_SOLVED;
        }

        return true;
    }

    /**
     * The accepted answer of the topic, while it can be one: a reply which was moved to another topic, deleted or held for review is
     * no longer the answer (its id stays in the topic, it is only not shown).
     *
     * @param   KunenaTopic  $topic  The topic.
     *
     * @return  KunenaMessage|null
     *
     * @since   Kunena 8.0
     */
    public static function getAnswer(KunenaTopic $topic): ?KunenaMessage
    {
        if ((int) $topic->answer_id <= 0) {
            return null;
        }

        $message = KunenaMessageHelper::get((int) $topic->answer_id);

        return $message->exists() && self::canBeAnswer($topic, $message) ? $message : null;
    }
}
