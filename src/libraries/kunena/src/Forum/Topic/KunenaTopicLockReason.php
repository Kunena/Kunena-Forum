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

use Kunena\Forum\Libraries\User\KunenaUser;

/**
 * The reasons of a locked topic (closed, solved, sold...): the list of the configuration, the check of a reason which is sent,
 * and who may lock a topic with a reason besides the moderators.
 *
 * @since   Kunena 8.0
 */
abstract class KunenaTopicLockReason
{
    /**
     * The longest reason, the length of the column lock_reason.
     *
     * @since   Kunena 8.0
     */
    public const int MAX_LENGTH = 255;

    /**
     * The reasons of the setting, one per line: trimmed, cut to the length of the column, without the empty lines and the
     * duplicates (the first one is kept).
     *
     * @param   string|null  $setting  The setting lockReasons.
     *
     * @return  string[]
     *
     * @since   Kunena 8.0
     */
    public static function parse(?string $setting): array
    {
        $reasons = [];

        foreach (preg_split('/\r\n|\n|\r/', (string) $setting) as $line) {
            $line = mb_substr(trim($line), 0, self::MAX_LENGTH, 'UTF-8');

            if ($line !== '' && !\in_array($line, $reasons, true)) {
                $reasons[] = $line;
            }
        }

        return $reasons;
    }

    /**
     * The reason which is sent, when it is one of the reasons of the list.
     *
     * @param   mixed     $value    The value of the request.
     * @param   string[]  $reasons  The reasons of the list (see parse).
     *
     * @return  string|null  The reason, or null when it is not one of the list.
     *
     * @since   Kunena 8.0
     */
    public static function clean(mixed $value, array $reasons): ?string
    {
        if (!\is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value !== '' && \in_array($value, $reasons, true) ? $value : null;
    }

    /**
     * Whether the author of the topic may lock it with a reason (only the moderators unlock it).
     *
     * @param   KunenaTopic  $topic    The topic.
     * @param   KunenaUser   $user     The user.
     * @param   bool         $enabled  The setting lockByAuthor.
     * @param   string[]     $reasons  The reasons of the list (see parse).
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function authorMayLock(KunenaTopic $topic, KunenaUser $user, bool $enabled, array $reasons): bool
    {
        if (!$enabled || !$reasons || !$user->exists() || $user->isBanned()) {
            return false;
        }

        if (!(int) $user->userid || (int) $topic->first_post_userid !== (int) $user->userid) {
            return false;
        }

        return !$topic->locked && (int) $topic->hold === 0;
    }
}
