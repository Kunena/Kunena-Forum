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

/**
 * The categories which are no longer used (tool "Category health"): empty ones, ones where nobody posted for a long time,
 * and unpublished ones which still hold topics. Diagnostics finds broken records; these categories are fine, only unused.
 *
 * @since   Kunena 8.0
 */
class KunenaCategoryHealth
{
    /**
     * The periods without a post, in days.
     *
     * @since   Kunena 8.0
     */
    public const PERIODS = [180, 365, 730, 1825];

    /**
     * The problems of the categories; a category without a problem is left out. A section, or a category with categories
     * below it, holds no topics of its own and is never empty.
     *
     * @param   array  $rows    id, parentid, name, published, numTopics, last_post_time.
     * @param   int    $days    A category without a post for so many days is quiet.
     * @param   int    $now     The current time.
     *
     * @return  array<int, array{id: int, name: string, path: string, problem: string, topics: int, last: int}>  The categories in
     *          the order of the rows.
     *
     * @since   Kunena 8.0
     */
    public static function check(array $rows, int $days, int $now): array
    {
        $byId     = array_column($rows, null, 'id');
        $children = [];

        foreach ($rows as $row) {
            $children[(int) $row['parentid']] = true;
        }

        $result = [];

        foreach ($rows as $row) {
            $id     = (int) $row['id'];
            $topics = (int) $row['numTopics'];
            $last   = (int) $row['last_post_time'];

            if ((int) $row['parentid'] === 0 || isset($children[$id])) {
                continue;
            }

            if ((int) $row['published'] !== 1 && $topics > 0) {
                $problem = 'unpublished';
            } elseif ((int) $row['published'] === 1 && $topics === 0) {
                $problem = 'empty';
            } elseif ((int) $row['published'] === 1 && $last > 0 && $last < $now - $days * 86400) {
                $problem = 'quiet';
            } else {
                continue;
            }

            $result[] = ['id' => $id, 'name' => (string) $row['name'], 'path' => self::path($byId, $id), 'problem' => $problem, 'topics' => $topics, 'last' => $last];
        }

        return $result;
    }

    /**
     * The names of the parents of a category: "Section › Category".
     *
     * @param   array  $byId  The rows by id.
     * @param   int    $id    The category.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function path(array $byId, int $id): string
    {
        $names = [];
        $seen  = [];
        $id    = (int) ($byId[$id]['parentid'] ?? 0);

        // A loop in the parents (a broken tree) ends the path.
        while ($id > 0 && isset($byId[$id]) && !isset($seen[$id])) {
            $seen[$id] = true;
            array_unshift($names, (string) $byId[$id]['name']);
            $id = (int) $byId[$id]['parentid'];
        }

        return implode(' › ', $names);
    }

    /**
     * The categories of this site.
     *
     * @param   int  $days  One of PERIODS.
     * @param   int  $now   The current time.
     *
     * @return  array{days: int, categories: int, items: array}
     *
     * @since   Kunena 8.0
     */
    public static function checkOnSite(int $days, int $now): array
    {
        $days  = \in_array($days, self::PERIODS, true) ? $days : self::PERIODS[1];
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select($db->quoteName(['id', 'parentid', 'name', 'published', 'numTopics', 'last_post_time']))
            ->from($db->quoteName('#__kunena_categories'))
            ->order($db->quoteName(['parentid', 'ordering', 'name']));
        $rows = (array) $db->setQuery($query)->loadAssocList();

        return ['days' => $days, 'categories' => \count($rows), 'items' => self::check($rows, $days, $now)];
    }
}
