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
 * Topics with the same title in a category (tool "Duplicate topic titles"): mostly the same question asked twice. The
 * titles are compared without the case, the punctuation, a "Re:" in front and the order of the words.
 *
 * @since   Kunena 8.0
 */
class KunenaDuplicateTitles
{
    /**
     * The newest topics which are compared.
     *
     * @since   Kunena 8.0
     */
    public const LIMIT = 5000;

    /**
     * A title shorter than this (after its normalization) is too common to compare ("Help", "Question").
     *
     * @since   Kunena 8.0
     */
    public const MIN_LENGTH = 12;

    /**
     * The title as it is compared: lower case, letters and digits only, a "Re:" in front left out, the words sorted.
     *
     * @param   string  $title  The title.
     *
     * @return  string  An empty string when the title is too short to compare.
     *
     * @since   Kunena 8.0
     */
    public static function key(string $title): string
    {
        $title = mb_strtolower(html_entity_decode($title, ENT_QUOTES | ENT_HTML5, 'UTF-8'), 'UTF-8');
        $title = (string) preg_replace('/^(?:\s*(?:re|aw|sv|antw)\s*(?:\[\d+\])?\s*:\s*)+/u', '', $title);
        $words = preg_split('/[^\p{L}\p{N}]+/u', $title, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        sort($words);
        $key = implode(' ', $words);

        return mb_strlen($key, 'UTF-8') >= self::MIN_LENGTH ? $key : '';
    }

    /**
     * The groups of topics with the same title in the same category, the biggest first; the oldest topic of a group first.
     *
     * @param   array  $rows  id, category_id, subject, first_post_time, posts.
     *
     * @return  array<int, array<int, array>>
     *
     * @since   Kunena 8.0
     */
    public static function groups(array $rows): array
    {
        $groups = [];

        foreach ($rows as $row) {
            $key = self::key((string) $row['subject']);

            if ($key !== '') {
                $groups[(int) $row['category_id'] . '|' . $key][] = $row;
            }
        }

        $groups = array_values(array_filter($groups, static fn ($group) => \count($group) > 1));

        foreach ($groups as &$group) {
            usort($group, static fn ($a, $b) => [(int) $a['first_post_time'], (int) $a['id']] <=> [(int) $b['first_post_time'], (int) $b['id']]);
        }

        unset($group);
        usort($groups, static fn ($a, $b) => [\count($b), (int) end($b)['first_post_time']] <=> [\count($a), (int) end($a)['first_post_time']]);

        return $groups;
    }

    /**
     * The groups of this site, from its newest topics.
     *
     * @return  array{compared: int, groups: array, categories: array<int, string>}
     *
     * @since   Kunena 8.0
     */
    public static function findOnSite(): array
    {
        $published = KunenaForum::PUBLISHED;
        $db        = Factory::getContainer()->get('DatabaseDriver');
        $query     = $db->createQuery()
            ->select($db->quoteName(['id', 'category_id', 'subject', 'first_post_time', 'first_post_userid', 'first_post_guest_name', 'posts']))
            ->from($db->quoteName('#__kunena_topics'))
            ->where($db->quoteName('hold') . ' = :hold')
            ->where($db->quoteName('moved_id') . ' = 0')
            ->bind(':hold', $published, ParameterType::INTEGER)
            ->order($db->quoteName('id') . ' DESC');
        $rows   = (array) $db->setQuery($query, 0, self::LIMIT)->loadAssocList();
        $groups = self::groups($rows);
        $names  = [];
        $ids    = array_values(array_unique(array_map(static fn ($group) => (int) $group[0]['category_id'], $groups)));

        if ($ids) {
            $query = $db->createQuery()->select($db->quoteName(['id', 'name']))->from($db->quoteName('#__kunena_categories'))->whereIn($db->quoteName('id'), $ids);
            $names = array_map('strval', (array) $db->setQuery($query)->loadAssocList('id', 'name'));
        }

        return ['compared' => \count($rows), 'groups' => $groups, 'categories' => $names];
    }
}
