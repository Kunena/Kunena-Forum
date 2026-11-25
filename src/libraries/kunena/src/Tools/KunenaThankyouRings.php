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
use Kunena\Forum\Libraries\Forum\Message\Thankyou\KunenaMessageThankyouHelper;

/**
 * Users who thank each other (tool "Thank-you rings"): two users who both thank the other often, and give most of their
 * thanks to each other. That is the usual way to pump up the thanks, and the rank or karma which come with them.
 *
 * @since   Kunena 8.0
 */
class KunenaThankyouRings
{
    /**
     * The thanks each of the two gives the other at least.
     *
     * @since   Kunena 8.0
     */
    public const MIN_EACH = 3;

    /**
     * The share of all the thanks the two give which go to each other, at least.
     *
     * @since   Kunena 8.0
     */
    public const MIN_SHARE = 0.5;

    /**
     * The pairs of users who thank each other.
     *
     * @param   array  $rows  userid (who thanks), targetuserid (who is thanked), count.
     *
     * @return  array<int, array{a: int, b: int, ab: int, ba: int, givenA: int, givenB: int, share: float}>  The most thanks first.
     *
     * @since   Kunena 8.0
     */
    public static function pairs(array $rows): array
    {
        $given = [];
        $count = [];

        foreach ($rows as $row) {
            $from = (int) $row['userid'];
            $to   = (int) $row['targetuserid'];
            $n    = (int) $row['count'];

            // A thank to oneself is not possible in Kunena; a guest (0) has no profile to pump up.
            if ($from <= 0 || $to <= 0 || $from === $to) {
                continue;
            }

            $given[$from]        = ($given[$from] ?? 0) + $n;
            $count[$from][$to]   = ($count[$from][$to] ?? 0) + $n;
        }

        $pairs = [];

        foreach ($count as $a => $targets) {
            foreach ($targets as $b => $ab) {
                $ba = $count[$b][$a] ?? 0;

                if ($a >= $b || $ab < self::MIN_EACH || $ba < self::MIN_EACH) {
                    continue;
                }

                $share = ($ab + $ba) / ($given[$a] + $given[$b]);

                if ($share >= self::MIN_SHARE) {
                    $pairs[] = ['a' => $a, 'b' => $b, 'ab' => $ab, 'ba' => $ba, 'givenA' => $given[$a], 'givenB' => $given[$b], 'share' => $share];
                }
            }
        }

        usort($pairs, static fn ($x, $y) => [$y['ab'] + $y['ba'], $y['share']] <=> [$x['ab'] + $x['ba'], $x['share']]);

        return $pairs;
    }

    /**
     * The pairs of this site.
     *
     * @return  array{pairs: array, names: array<int, string>}
     *
     * @since   Kunena 8.0
     */
    public static function findOnSite(): array
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select($db->quoteName(['userid', 'targetuserid']))
            ->select('COUNT(*) AS ' . $db->quoteName('count'))
            ->from($db->quoteName('#__kunena_thankyou'))
            ->group($db->quoteName(['userid', 'targetuserid']));
        $pairs = self::pairs((array) $db->setQuery($query)->loadAssocList());
        $ids   = array_values(array_unique(array_merge(array_column($pairs, 'a'), array_column($pairs, 'b'))));
        $names = [];

        if ($ids) {
            $query = $db->createQuery()->select($db->quoteName(['id', 'username']))->from($db->quoteName('#__users'))->whereIn($db->quoteName('id'), $ids);
            $names = array_map('strval', (array) $db->setQuery($query)->loadAssocList('id', 'username'));
        }

        return ['pairs' => $pairs, 'names' => $names];
    }

    /**
     * The pairs of a form: "a-b".
     *
     * @param   string[]  $values  The values.
     *
     * @return  array<int, array{0: int, 1: int}>
     *
     * @since   Kunena 8.0
     */
    public static function parse(array $values): array
    {
        $pairs = [];

        foreach ($values as $value) {
            if (preg_match('/^(\d+)-(\d+)$/', (string) $value, $match) && (int) $match[1] > 0 && (int) $match[2] > 0 && $match[1] !== $match[2]) {
                $pair                         = [min((int) $match[1], (int) $match[2]), max((int) $match[1], (int) $match[2])];
                $pairs[implode('-', $pair)]   = $pair;
            }
        }

        return array_values($pairs);
    }

    /**
     * Removes the thanks the users of each pair gave each other, then counts the thanks of the users again.
     *
     * @param   array<int, array{0: int, 1: int}>  $pairs  The pairs (parse()).
     *
     * @return  int  The thanks removed.
     *
     * @since   Kunena 8.0
     */
    public static function removeOnSite(array $pairs): int
    {
        $db      = Factory::getContainer()->get('DatabaseDriver');
        $removed = 0;

        foreach ($pairs as [$a, $b]) {
            foreach ([[$a, $b], [$b, $a]] as [$from, $to]) {
                $query = $db->createQuery()
                    ->delete($db->quoteName('#__kunena_thankyou'))
                    ->where($db->quoteName('userid') . ' = :from')
                    ->where($db->quoteName('targetuserid') . ' = :to')
                    ->bind(':from', $from, ParameterType::INTEGER)
                    ->bind(':to', $to, ParameterType::INTEGER);
                $db->setQuery($query)->execute();
                $removed += (int) $db->getAffectedRows();
            }
        }

        if ($removed) {
            KunenaMessageThankyouHelper::recount();
        }

        return $removed;
    }
}
