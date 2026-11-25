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
use Throwable;

/**
 * The profiles which advertise (tool "Signature and profile spam"): a spammer registers, posts little or nothing, and leaves
 * links in the signature, the personal text, the website or the place of the profile. The fields of a profile can be
 * emptied; the account itself is blocked or deleted with the tool "User cleanup".
 *
 * @since   Kunena 8.0
 */
class KunenaProfileSpam
{
    /**
     * The fields of a profile which can hold a link.
     *
     * @since   Kunena 8.0
     */
    public const FIELDS = ['signature', 'personalText', 'websiteurl', 'websitename', 'location'];

    /**
     * The numbers of posts of the filter: a user with at most this many posts.
     *
     * @since   Kunena 8.0
     */
    public const MAX_POSTS = [0, 5, 20, 100];

    /**
     * At most this many profiles are read.
     *
     * @since   Kunena 8.0
     */
    public const SCAN_LIMIT = 3000;

    /**
     * At most this many profiles are listed.
     *
     * @since   Kunena 8.0
     */
    public const LIST_LIMIT = 200;

    /**
     * The number of links in a text: addresses, BBCode links and pictures, HTML links.
     *
     * @param   string  $text  The text.
     *
     * @return  integer
     *
     * @since   Kunena 8.0
     */
    public static function links(string $text): int
    {
        // An address in a [url] or an <a> is counted once: as the address.
        $count = preg_match_all('#https?://|(?<![/\w.@])www\.[a-z0-9-]+\.[a-z]#i', $text);
        $count += preg_match_all('#\[url\](?!\s*(?:https?://|www\.))|\[url=(?!\s*["\']?(?:https?://|www\.))#i', $text);

        return (int) $count;
    }

    /**
     * The links of a profile, by field.
     *
     * @param   array  $row  The fields (FIELDS).
     *
     * @return  array<string, int>  field => number of links, only the fields with a link.
     *
     * @since   Kunena 8.0
     */
    public static function linksOf(array $row): array
    {
        $found = [];

        foreach (self::FIELDS as $field) {
            $links = self::links((string) ($row[$field] ?? ''));

            if ($links > 0) {
                $found[$field] = $links;
            }
        }

        return $found;
    }

    /**
     * The profiles of this site with links, of the users with at most so many posts, the most links first; the staff of the
     * forum is left out.
     *
     * @param   int  $maxPosts  The most posts of a user (MAX_POSTS).
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    public static function findOnSite(int $maxPosts): array
    {
        $maxPosts = \in_array($maxPosts, self::MAX_POSTS, true) ? $maxPosts : self::MAX_POSTS[1];
        $db       = Factory::getContainer()->get('DatabaseDriver');
        $query    = $db->createQuery()
            ->select($db->quoteName(['ku.userid', 'ku.posts', 'ku.signature', 'ku.personalText', 'ku.websiteurl', 'ku.websitename', 'ku.location']))
            ->select($db->quoteName(['u.username', 'u.name', 'u.registerDate', 'u.lastvisitDate', 'u.block']))
            ->from($db->quoteName('#__kunena_users', 'ku'))
            ->innerJoin($db->quoteName('#__users', 'u') . ' ON ' . $db->quoteName('u.id') . ' = ' . $db->quoteName('ku.userid'))
            ->where($db->quoteName('ku.posts') . ' <= :posts')
            ->bind(':posts', $maxPosts, ParameterType::INTEGER)
            ->order($db->quoteName('u.registerDate') . ' DESC');

        // Only the profiles where a link can be: the exact count is made below.
        $like = [];

        foreach (self::FIELDS as $field) {
            foreach (['%http%', '%www.%', '%[url%'] as $pattern) {
                $like[] = $db->quoteName('ku.' . $field) . ' LIKE ' . $db->quote($pattern);
            }
        }

        $query->where('(' . implode(' OR ', $like) . ')');

        $items = [];

        foreach ((array) $db->setQuery($query, 0, self::SCAN_LIMIT)->loadAssocList() as $row) {
            $links = self::linksOf($row);

            if ($links && !self::isStaff((int) $row['userid'])) {
                $items[] = $row + ['links' => $links, 'total' => array_sum($links)];
            }
        }

        usort($items, static fn ($a, $b) => [$b['total'], $b['registerDate']] <=> [$a['total'], $a['registerDate']]);

        return \array_slice($items, 0, self::LIST_LIMIT);
    }

    /**
     * Empties the fields with links of the chosen profiles (the staff of the forum is left alone).
     *
     * @param   int[]  $ids  The users.
     *
     * @return  array{cleared: int, skipped: int}
     *
     * @since   Kunena 8.0
     */
    public static function clearOnSite(array $ids): array
    {
        $result = ['cleared' => 0, 'skipped' => 0];
        $db     = Factory::getContainer()->get('DatabaseDriver');

        foreach (array_values(array_unique(array_filter(array_map('intval', $ids)))) as $id) {
            if (self::isStaff($id)) {
                $result['skipped']++;

                continue;
            }

            $query = $db->createQuery()->update($db->quoteName('#__kunena_users'))->where($db->quoteName('userid') . ' = :id')
                ->bind(':id', $id, ParameterType::INTEGER);

            foreach (self::FIELDS as $field) {
                $query->set($db->quoteName($field) . ' = ' . $db->quote(''));
            }

            try {
                $db->setQuery($query)->execute();
                $ok = $db->getAffectedRows() > 0;
            } catch (Throwable $e) {
                $ok = false;
            }

            $result[$ok ? 'cleared' : 'skipped']++;
        }

        // The profiles which were read before are read again.
        KunenaUserHelper::cleanup();

        return $result;
    }

    /**
     * Is the user an administrator or a moderator of the forum?
     *
     * @param   int  $userid  The user.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    private static function isStaff(int $userid): bool
    {
        if ($userid < 1) {
            return false;
        }

        $user = KunenaUserHelper::get($userid);

        return $user->isAdmin() || $user->isModerator();
    }
}
