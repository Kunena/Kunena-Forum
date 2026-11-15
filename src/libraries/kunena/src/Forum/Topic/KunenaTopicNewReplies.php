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

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

/**
 * The new replies of a topic while it is read: the page asks in the background for the published replies of the other users which
 * came after the last message it shows, and offers to show them.
 *
 * @since   Kunena 8.0
 */
abstract class KunenaTopicNewReplies
{
    /**
     * The shortest time between two questions of a page, in seconds.
     *
     * @since   Kunena 8.0
     */
    public const int MIN_INTERVAL = 10;

    /**
     * The time between two questions of a page: the interval of the template, at least MIN_INTERVAL.
     *
     * @param   mixed  $interval  The setting of the template (seconds).
     *
     * @return  integer
     *
     * @since   Kunena 8.0
     */
    public static function interval(mixed $interval): int
    {
        $interval = is_numeric($interval) ? (int) $interval : 30;

        return max(self::MIN_INTERVAL, $interval > 0 ? $interval : 30);
    }

    /**
     * The new replies of a topic after a message: their number and the id of the first one. Only the published replies of the other
     * users count (the user knows his own replies).
     *
     * @param   int  $topicId  The topic.
     * @param   int  $afterId  The last message which the page shows.
     * @param   int  $userid   The user who reads (0: a guest, every reply counts).
     *
     * @return  array{count: int, first: int}
     *
     * @since   Kunena 8.0
     */
    public static function find(int $topicId, int $afterId, int $userid): array
    {
        if ($topicId <= 0 || $afterId < 0) {
            return ['count' => 0, 'first' => 0];
        }

        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->createQuery()
            ->select(['COUNT(*) AS ' . $db->quoteName('number'), 'MIN(' . $db->quoteName('id') . ') AS ' . $db->quoteName('first')])
            ->from($db->quoteName('#__kunena_messages'))
            ->where($db->quoteName('thread') . ' = :topic')
            ->where($db->quoteName('id') . ' > :after')
            ->where($db->quoteName('hold') . ' = 0')
            ->bind(':topic', $topicId, ParameterType::INTEGER)
            ->bind(':after', $afterId, ParameterType::INTEGER);

        if ($userid > 0) {
            $query->where($db->quoteName('userid') . ' != :userid')
                ->bind(':userid', $userid, ParameterType::INTEGER);
        }

        $row = $db->setQuery($query)->loadObject();

        return ['count' => (int) ($row->number ?? 0), 'first' => (int) ($row->first ?? 0)];
    }
}
