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
use Kunena\Forum\Libraries\Forum\Topic\Rate\KunenaRateHelper;

/**
 * The ratings of topics which look abused (tool "Rating abuse"): a user who gives (almost) every topic the same score, and
 * a user who rates many topics of one author with the lowest scores (to push him down) or the highest (to push him up).
 * The rating of a topic is counted from its ratings each time, so removing ratings is enough.
 *
 * @since   Kunena 8.0
 */
class KunenaRatingAbuse
{
    /**
     * A user is checked from this number of ratings.
     *
     * @since   Kunena 8.0
     */
    public const MIN_RATINGS = 20;

    /**
     * The share of the ratings of a user with the same score, at least.
     *
     * @since   Kunena 8.0
     */
    public const SAME_SHARE = 0.9;

    /**
     * A user who rates so many topics of one author is checked.
     *
     * @since   Kunena 8.0
     */
    public const MIN_TARGETED = 5;

    /**
     * The users who give (almost) every topic the same score, the most ratings first.
     *
     * @param   array  $rows  userid, rate, count.
     *
     * @return  array<int, array{user: int, ratings: int, score: int, share: float}>
     *
     * @since   Kunena 8.0
     */
    public static function sameScore(array $rows): array
    {
        $users = [];

        foreach ($rows as $row) {
            $users[(int) $row['userid']][(int) $row['rate']] = ((int) ($users[(int) $row['userid']][(int) $row['rate']] ?? 0)) + (int) $row['count'];
        }

        $result = [];

        foreach ($users as $user => $scores) {
            $total = array_sum($scores);

            if ($user <= 0 || $total < self::MIN_RATINGS) {
                continue;
            }

            arsort($scores);
            $score = (int) array_key_first($scores);
            $share = $scores[$score] / $total;

            if ($share >= self::SAME_SHARE) {
                $result[] = ['user' => $user, 'ratings' => $total, 'score' => $score, 'share' => $share];
            }
        }

        usort($result, static fn ($a, $b) => [$b['ratings'], $a['user']] <=> [$a['ratings'], $b['user']]);

        return $result;
    }

    /**
     * The users who rate many topics of one author only low (1 or 2) or only high (5): rater => author.
     *
     * @param   array  $rows  userid (who rates), author, ratings, low (ratings of 1 or 2), high (ratings of 5).
     *
     * @return  array<int, array{user: int, author: int, ratings: int, kind: string}>  kind: "down" or "up".
     *
     * @since   Kunena 8.0
     */
    public static function targeted(array $rows): array
    {
        $result = [];

        foreach ($rows as $row) {
            $user    = (int) $row['userid'];
            $author  = (int) $row['author'];
            $ratings = (int) $row['ratings'];

            if ($user <= 0 || $author <= 0 || $user === $author || $ratings < self::MIN_TARGETED) {
                continue;
            }

            if ((int) $row['low'] === $ratings) {
                $result[] = ['user' => $user, 'author' => $author, 'ratings' => $ratings, 'kind' => 'down'];
            } elseif ((int) $row['high'] === $ratings) {
                $result[] = ['user' => $user, 'author' => $author, 'ratings' => $ratings, 'kind' => 'up'];
            }
        }

        usort($result, static fn ($a, $b) => [$b['ratings'], $a['user']] <=> [$a['ratings'], $b['user']]);

        return $result;
    }

    /**
     * The ratings of this site.
     *
     * @return  array{total: int, sameScore: array, targeted: array, names: array<int, string>}
     *
     * @since   Kunena 8.0
     */
    public static function findOnSite(): array
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select($db->quoteName(['userid', 'rate']))
            ->select('COUNT(*) AS ' . $db->quoteName('count'))
            ->from($db->quoteName('#__kunena_rate'))
            ->group($db->quoteName(['userid', 'rate']));
        $rows  = (array) $db->setQuery($query)->loadAssocList();
        $same  = self::sameScore($rows);
        $total = array_sum(array_map(static fn ($row) => (int) $row['count'], $rows));

        $query = $db->createQuery()
            ->select($db->quoteName('r.userid'))
            ->select($db->quoteName('t.first_post_userid', 'author'))
            ->select('COUNT(*) AS ' . $db->quoteName('ratings'))
            ->select('SUM(' . $db->quoteName('r.rate') . ' <= 2) AS ' . $db->quoteName('low'))
            ->select('SUM(' . $db->quoteName('r.rate') . ' >= 5) AS ' . $db->quoteName('high'))
            ->from($db->quoteName('#__kunena_rate', 'r'))
            ->innerJoin($db->quoteName('#__kunena_topics', 't') . ' ON ' . $db->quoteName('t.id') . ' = ' . $db->quoteName('r.topic_id'))
            ->group($db->quoteName(['r.userid', 't.first_post_userid']))
            ->having('COUNT(*) >= ' . self::MIN_TARGETED);
        $targeted = self::targeted((array) $db->setQuery($query)->loadAssocList());

        $ids   = array_values(array_unique(array_merge(array_column($same, 'user'), array_column($targeted, 'user'), array_column($targeted, 'author'))));
        $names = [];

        if ($ids) {
            $query = $db->createQuery()->select($db->quoteName(['id', 'username']))->from($db->quoteName('#__users'))->whereIn($db->quoteName('id'), $ids);
            $names = array_map('strval', (array) $db->setQuery($query)->loadAssocList('id', 'username'));
        }

        return ['total' => $total, 'sameScore' => $same, 'targeted' => $targeted, 'names' => $names];
    }

    /**
     * Removes all the ratings of the chosen users.
     *
     * @param   int[]  $ids  The users.
     *
     * @return  int  The ratings removed.
     *
     * @since   Kunena 8.0
     */
    public static function removeOnSite(array $ids): int
    {
        $db      = Factory::getContainer()->get('DatabaseDriver');
        $removed = 0;
        $topics  = [];

        foreach (array_values(array_unique(array_filter(array_map('intval', $ids)))) as $id) {
            $topics = array_merge($topics, KunenaRateHelper::topicsRatedBy($id));

            $query = $db->createQuery()->delete($db->quoteName('#__kunena_rate'))->where($db->quoteName('userid') . ' = :id')
                ->bind(':id', $id, ParameterType::INTEGER);
            $db->setQuery($query)->execute();
            $removed += (int) $db->getAffectedRows();
        }

        // The topics of the removed ratings get the rating of the ratings which are left: they kept the one the removed made.
        KunenaRateHelper::recountTopics($topics);

        return $removed;
    }
}
