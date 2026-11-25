<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Tools
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Tools;

\defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\Database\ParameterType;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * The recent messages which look like spam (tool "Find spam"): each sign adds points, a message with THRESHOLD points or
 * more is listed. Nothing is changed: the list links to the message and to the clean-up of its author.
 *
 * @since   Kunena 8.0
 */
class KunenaSpamFinder
{
    /**
     * The points a message needs to be listed.
     *
     * @since   Kunena 8.0
     */
    public const THRESHOLD = 3;

    /**
     * At most this many recent messages are read.
     *
     * @since   Kunena 8.0
     */
    public const SCAN_LIMIT = 2000;

    /**
     * At most this many suspects are listed.
     *
     * @since   Kunena 8.0
     */
    public const LIST_LIMIT = 100;

    /**
     * The signs: name => points.
     *
     * @since   Kunena 8.0
     */
    public const SIGNS = [
        'links_many'  => 3,
        'links'       => 1,
        'new_minutes' => 3,
        'new_day'     => 1,
        'first_post'  => 1,
        'shared_ip'   => 2,
        'unapproved'  => 1,
        'guest_link'  => 1,
    ];

    /**
     * The number of links of a text: addresses and BBCode [url].
     *
     * @param   string  $text  The text.
     *
     * @return  integer
     *
     * @since   Kunena 8.0
     */
    public static function countLinks(string $text): int
    {
        $bbcode = preg_match_all('#\[url\b#i', $text);

        // The addresses which are not inside an [url=...] tag already counted.
        $plain = preg_replace('#\[url[^\]]*\].*?\[/url\]#is', ' ', $text) ?? $text;
        $plain = preg_match_all('#(?:https?://|www\.)[^\s\[\]<>"]+#i', $plain);

        return (int) $bbcode + (int) $plain;
    }

    /**
     * The points of a message and the signs which gave them.
     *
     * @param   array  $row       The message: subject, message, time, hold, userid, posts (of the author), registered (time of the
     *                            registration of the author, 0 for a guest), ip.
     * @param   int    $ipUsers  The number of users who posted from the address of the message.
     *
     * @return  array{score: int, signs: string[]}
     *
     * @since   Kunena 8.0
     */
    public static function score(array $row, int $ipUsers = 0): array
    {
        $signs = [];
        $links = self::countLinks((string) ($row['subject'] ?? '') . ' ' . (string) ($row['message'] ?? ''));

        if ($links >= 3) {
            $signs[] = 'links_many';
        } elseif ($links > 0) {
            $signs[] = 'links';
        }

        $userid = (int) ($row['userid'] ?? 0);

        if ($userid > 0) {
            $registered = (int) ($row['registered'] ?? 0);
            $age        = (int) ($row['time'] ?? 0) - $registered;

            if ($registered > 0 && $age >= 0 && $age <= 600) {
                $signs[] = 'new_minutes';
            } elseif ($registered > 0 && $age >= 0 && $age <= 86400) {
                $signs[] = 'new_day';
            }

            if ((int) ($row['posts'] ?? 0) <= 1) {
                $signs[] = 'first_post';
            }
        } elseif ($links > 0) {
            $signs[] = 'guest_link';
        }

        // A private or local address is shared by design (a proxy, an office, the site itself): it tells nothing.
        if ($ipUsers >= 2 && self::isPublicIp((string) ($row['ip'] ?? ''))) {
            $signs[] = 'shared_ip';
        }

        if ((int) ($row['hold'] ?? 0) === 1) {
            $signs[] = 'unapproved';
        }

        $score = 0;

        foreach ($signs as $sign) {
            $score += self::SIGNS[$sign];
        }

        return ['score' => $score, 'signs' => $signs];
    }

    /**
     * Is the address a public one (not private, not reserved, not local)?
     *
     * @param   string  $ip  The address.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function isPublicIp(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
    }

    /**
     * The suspects among the rows: the ones with THRESHOLD points or more, the most points first, then the newest.
     *
     * @param   array               $rows     The messages (score()), each with an id.
     * @param   array<string, int>  $ipUsers  By address, the number of users who posted from it.
     * @param   int                 $limit    At most this many.
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    public static function suspects(array $rows, array $ipUsers, int $limit = self::LIST_LIMIT): array
    {
        $suspects = [];

        foreach ($rows as $row) {
            $result = self::score($row, (int) ($ipUsers[(string) ($row['ip'] ?? '')] ?? 0));

            if ($result['score'] >= self::THRESHOLD) {
                $suspects[] = $row + $result;
            }
        }

        usort($suspects, static fn ($a, $b) => [$b['score'], (int) $b['time']] <=> [$a['score'], (int) $a['time']]);

        return \array_slice($suspects, 0, $limit);
    }

    /**
     * The suspects among the messages of the last days on this site (published and unapproved).
     *
     * @param   int  $days  The number of days.
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    public static function findOnSite(int $days): array
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $since = time() - max(1, $days) * 86400;
        $query = $db->createQuery()
            ->select($db->quoteName(['m.id', 'm.thread', 'm.catid', 'm.subject', 'm.time', 'm.hold', 'm.userid', 'm.name', 'm.ip', 't.message']))
            ->select($db->quoteName('ku.posts'))
            ->select($db->quoteName('u.username'))
            ->select($db->quoteName('u.registerDate'))
            ->from($db->quoteName('#__kunena_messages', 'm'))
            ->innerJoin($db->quoteName('#__kunena_messages_text', 't') . ' ON ' . $db->quoteName('t.mesid') . ' = ' . $db->quoteName('m.id'))
            ->leftJoin($db->quoteName('#__kunena_users', 'ku') . ' ON ' . $db->quoteName('ku.userid') . ' = ' . $db->quoteName('m.userid'))
            ->leftJoin($db->quoteName('#__users', 'u') . ' ON ' . $db->quoteName('u.id') . ' = ' . $db->quoteName('m.userid'))
            ->where($db->quoteName('m.time') . ' >= :since')
            ->whereIn($db->quoteName('m.hold'), [0, 1])
            ->bind(':since', $since, ParameterType::INTEGER)
            ->order($db->quoteName('m.time') . ' DESC');
        $rows = (array) $db->setQuery($query, 0, self::SCAN_LIMIT)->loadAssocList();

        // The messages of the administrators and the moderators of the forum are not suspects.
        $staff = [];

        foreach (array_unique(array_map('intval', array_column($rows, 'userid'))) as $userid) {
            $user = $userid > 0 ? KunenaUserHelper::get($userid) : null;

            if ($user && ($user->isAdmin() || $user->isModerator())) {
                $staff[$userid] = true;
            }
        }

        $rows = array_values(array_filter($rows, static fn ($row) => !isset($staff[(int) $row['userid']])));

        foreach ($rows as &$row) {
            $row['registered'] = !empty($row['registerDate']) ? (int) strtotime($row['registerDate'] . ' UTC') : 0;
        }

        unset($row);

        $ips     = array_values(array_unique(array_filter(array_column($rows, 'ip'))));
        $ipUsers = [];

        foreach (array_chunk($ips, 500) as $chunk) {
            $query = $db->createQuery()
                ->select([$db->quoteName('ip'), 'COUNT(DISTINCT ' . $db->quoteName('userid') . ') AS ' . $db->quoteName('users')])
                ->from($db->quoteName('#__kunena_messages'))
                ->whereIn($db->quoteName('ip'), $chunk, ParameterType::STRING)
                ->where($db->quoteName('userid') . ' > 0')
                ->group($db->quoteName('ip'));
            $ipUsers += array_map('intval', (array) $db->setQuery($query)->loadAssocList('ip', 'users'));
        }

        return self::suspects($rows, $ipUsers);
    }
}
