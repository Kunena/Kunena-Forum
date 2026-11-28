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
use Kunena\Forum\Libraries\Cache\KunenaLayoutCache;

/**
 * The announcements which need a look (tool "Announcements review"): published but past their end date, published but
 * old, published with a start date in the future (they wait), and unpublished ones nobody sees.
 *
 * @since   Kunena 8.0
 */
class KunenaAnnouncementsReview
{
    /**
     * A published announcement older than this many days is old.
     *
     * @since   Kunena 8.0
     */
    public const OLD_DAYS = 365;

    /**
     * The dates which mean "no date" (the default of the column, and the ones of older versions).
     *
     * @since   Kunena 8.0
     */
    public const NO_DATE = ['', '1000-01-01 00:00:00', '0000-00-00 00:00:00'];

    /**
     * A date of the database as a time, 0 when there is no date.
     *
     * @param   string|null  $date  The date (UTC).
     *
     * @return  int
     *
     * @since   Kunena 8.0
     */
    public static function time(?string $date): int
    {
        $date = trim((string) $date);

        return \in_array($date, self::NO_DATE, true) ? 0 : (int) strtotime($date . ' UTC');
    }

    /**
     * The state of each announcement; an announcement which is shown and recent is left out.
     *
     * @param   array  $rows  id, title, published, created, publish_up, publish_down.
     * @param   int    $now   The current time.
     *
     * @return  array<int, array{id: int, title: string, state: string, created: int, up: int, down: int}>  state: "expired"
     *          (published, past its end), "old", "waiting" (starts later) or "unpublished".
     *
     * @since   Kunena 8.0
     */
    public static function review(array $rows, int $now): array
    {
        $result = [];

        foreach ($rows as $row) {
            $created = self::time($row['created'] ?? null);
            $up      = self::time($row['publish_up'] ?? null);
            $down    = self::time($row['publish_down'] ?? null);

            if ((int) $row['published'] !== 1) {
                $state = 'unpublished';
            } elseif ($down && $down < $now) {
                $state = 'expired';
            } elseif ($up && $up > $now) {
                $state = 'waiting';
            } elseif ($created && $created < $now - self::OLD_DAYS * 86400) {
                $state = 'old';
            } else {
                continue;
            }

            $result[] = ['id' => (int) $row['id'], 'title' => (string) $row['title'], 'state' => $state, 'created' => $created, 'up' => $up, 'down' => $down];
        }

        // What is shown and should not be first.
        $order = ['expired' => 0, 'old' => 1, 'waiting' => 2, 'unpublished' => 3];
        usort($result, static fn ($a, $b) => [$order[$a['state']], -$a['created']] <=> [$order[$b['state']], -$b['created']]);

        return $result;
    }

    /**
     * The announcements of this site.
     *
     * @param   int  $now  The current time.
     *
     * @return  array{total: int, items: array}
     *
     * @since   Kunena 8.0
     */
    public static function reviewOnSite(int $now): array
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select($db->quoteName(['id', 'title', 'published', 'created', 'publish_up', 'publish_down']))
            ->from($db->quoteName('#__kunena_announcement'));
        $rows = (array) $db->setQuery($query)->loadAssocList();

        return ['total' => \count($rows), 'items' => self::review($rows, $now)];
    }

    /**
     * Unpublishes the chosen announcements.
     *
     * @param   int[]  $ids  The announcements.
     *
     * @return  int  The announcements unpublished.
     *
     * @since   Kunena 8.0
     */
    public static function unpublishOnSite(array $ids): int
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));

        if (!$ids) {
            return 0;
        }

        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->update($db->quoteName('#__kunena_announcement'))
            ->set($db->quoteName('published') . ' = 0')
            ->whereIn($db->quoteName('id'), $ids)
            ->where($db->quoteName('published') . ' = 1');
        $db->setQuery($query)->execute();

        $changed = (int) $db->getAffectedRows();

        // The banner of the index and the list are cached per type of user: the cache is emptied as a save of an announcement does it
        // (KunenaDatabaseObject), else the unpublished ones stayed shown until the cache time of the forum ran out.
        if ($changed > 0) {
            KunenaLayoutCache::clear(true);
        }

        return $changed;
    }
}
