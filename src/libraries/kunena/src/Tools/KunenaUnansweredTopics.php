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
use Kunena\Forum\Libraries\Forum\KunenaForum;

/**
 * The topics nobody answered (tool "Unanswered topics"): per category, the published topics which still have only their
 * first post after some days. The topics themselves are handled in the Topics view, filtered on that category.
 *
 * @since   Kunena 8.0
 */
class KunenaUnansweredTopics
{
    /**
     * The ages, in days: the ages of the filter of the Topics view, so that a link opens the same list.
     *
     * @since   Kunena 8.0
     */
    public const AGES = [30, 90, 180, 365, 730];

    /**
     * The categories with their unanswered topics, the most first.
     *
     * @param   array                  $rows   category_id, count, oldest.
     * @param   array<int, string>     $names  The names of the categories.
     *
     * @return  array{total: int, oldest: int, categories: array<int, array{id: int, name: string, count: int, oldest: int}>}
     *
     * @since   Kunena 8.0
     */
    public static function summarize(array $rows, array $names): array
    {
        $categories = [];
        $total      = 0;
        $oldest     = 0;

        foreach ($rows as $row) {
            $id    = (int) $row['category_id'];
            $count = (int) $row['count'];
            $first = (int) $row['oldest'];

            if ($count <= 0) {
                continue;
            }

            $categories[] = ['id' => $id, 'name' => $names[$id] ?? '#' . $id, 'count' => $count, 'oldest' => $first];
            $total += $count;
            $oldest = $oldest && $first ? min($oldest, $first) : max($oldest, $first);
        }

        usort($categories, static fn ($a, $b) => [$b['count'], $a['oldest']] <=> [$a['count'], $b['oldest']]);

        return ['total' => $total, 'oldest' => $oldest, 'categories' => $categories];
    }

    /**
     * The unanswered topics of this site whose post is older than the days.
     *
     * @param   int  $days  One of AGES.
     * @param   int  $now   The current time.
     *
     * @return  array{days: int, total: int, oldest: int, categories: array}
     *
     * @since   Kunena 8.0
     */
    public static function reportOnSite(int $days, int $now): array
    {
        $days      = \in_array($days, self::AGES, true) ? $days : self::AGES[0];
        $before    = $now - $days * 86400;
        $published = KunenaForum::PUBLISHED;
        $db        = Factory::getContainer()->get('DatabaseDriver');

        // As the filter "No replies" of the Topics view: one post at most, its last post older than the age.
        $query = $db->createQuery()
            ->select($db->quoteName('category_id'))
            ->select('COUNT(*) AS ' . $db->quoteName('count'))
            ->select('MIN(' . $db->quoteName('first_post_time') . ') AS ' . $db->quoteName('oldest'))
            ->from($db->quoteName('#__kunena_topics'))
            ->where($db->quoteName('hold') . ' = :hold')
            ->where($db->quoteName('moved_id') . ' = 0')
            ->where($db->quoteName('posts') . ' <= 1')
            ->where($db->quoteName('last_post_time') . ' < :before')
            ->bind(':hold', $published, ParameterType::INTEGER)
            ->bind(':before', $before, ParameterType::INTEGER)
            ->group($db->quoteName('category_id'));
        $rows = (array) $db->setQuery($query)->loadAssocList();

        $names = [];
        $ids   = array_map('intval', array_column($rows, 'category_id'));

        if ($ids) {
            $query = $db->createQuery()->select($db->quoteName(['id', 'name']))->from($db->quoteName('#__kunena_categories'))->whereIn($db->quoteName('id'), $ids);
            $names = array_map('strval', (array) $db->setQuery($query)->loadAssocList('id', 'name'));
        }

        return ['days' => $days] + self::summarize($rows, $names);
    }
}
