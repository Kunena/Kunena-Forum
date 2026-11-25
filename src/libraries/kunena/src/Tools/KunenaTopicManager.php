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
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Throwable;

/**
 * All the topics of the forum in one list (view "Topics"): filtered by category, state, age, author and replies,
 * approved, locked, stuck, moved, trashed, restored or deleted in bulk. The actions go through the topic objects: the counters stay right.
 *
 * @since   Kunena 8.0
 */
class KunenaTopicManager
{
    /**
     * The topics of a page.
     *
     * @since   Kunena 8.0
     */
    public const PAGE_SIZE = 50;

    /**
     * The numbers of topics of a page which can be chosen.
     *
     * @since   Kunena 8.0
     */
    public const LIMITS = [20, 50, 100, 200];

    /**
     * The states of the filter.
     *
     * @since   Kunena 8.0
     */
    public const STATES = ['all', 'published', 'unapproved', 'trashed', 'locked', 'sticky'];

    /**
     * The ages of the filter, in days (0: any).
     *
     * @since   Kunena 8.0
     */
    public const AGES = [0, 30, 90, 180, 365, 730];

    /**
     * The actions.
     *
     * @since   Kunena 8.0
     */
    public const ACTIONS = ['approve', 'lock', 'unlock', 'stick', 'unstick', 'move', 'trash', 'restore', 'delete'];

    /**
     * The filters from a request: only known values.
     *
     * @param   array  $input  The request.
     *
     * @return  array{category: int, state: string, age: int, author: string, noreplies: bool, search: string, page: int, limit: int}
     *
     * @since   Kunena 8.0
     */
    public static function filters(array $input): array
    {
        $state = (string) ($input['state'] ?? 'all');
        $age   = (int) ($input['age'] ?? 0);
        $limit = (int) ($input['limit'] ?? self::PAGE_SIZE);

        return [
            'category'  => max(0, (int) ($input['category'] ?? 0)),
            'state'     => \in_array($state, self::STATES, true) ? $state : 'all',
            'age'       => \in_array($age, self::AGES, true) ? $age : 0,
            'author'    => trim((string) ($input['author'] ?? '')),
            'noreplies' => !empty($input['noreplies']),
            'search'    => trim((string) ($input['search'] ?? '')),
            'page'      => max(1, (int) ($input['page'] ?? 1)),
            'limit'     => \in_array($limit, self::LIMITS, true) ? $limit : self::PAGE_SIZE,
        ];
    }

    /**
     * The filters which differ from the defaults, for an address: the list is shown again with them.
     *
     * @param   array  $filters  The filters (filters()).
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    public static function active(array $filters): array
    {
        return array_filter($filters, static fn ($value, $key) => $value !== '' && $value !== 0 && $value !== false && $value !== 'all'
            && !($key === 'page' && $value === 1) && !($key === 'limit' && $value === self::PAGE_SIZE), ARRAY_FILTER_USE_BOTH);
    }

    /**
     * The pages which the pagination shows: the first and the last one, and the ones around the current page; null where
     * pages are left out.
     *
     * @param   int  $page    The current page.
     * @param   int  $pages   The number of pages.
     * @param   int  $around  The pages shown on each side of the current one.
     *
     * @return  array<int, int|null>
     *
     * @since   Kunena 8.0
     */
    public static function pageWindow(int $page, int $pages, int $around = 2): array
    {
        $list = [];
        $last = 0;

        for ($number = 1; $number <= $pages; $number++) {
            if ($number === 1 || $number === $pages || abs($number - $page) <= $around) {
                // One page left out is shown itself: a gap for one page is no shorter.
                if ($number - $last === 2) {
                    $list[] = $last + 1;
                } elseif ($number - $last > 2) {
                    $list[] = null;
                }

                $list[] = $number;
                $last   = $number;
            }
        }

        return $list;
    }

    /**
     * The topics of this site which match the filters: their number and the topics of the page.
     *
     * @param   array  $filters  The filters (filters()).
     * @param   int    $now      The current time.
     *
     * @return  array{total: int, items: array, page: int, pages: int, limit: int}
     *
     * @since   Kunena 8.0
     */
    public static function findOnSite(array $filters, int $now): array
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->from($db->quoteName('#__kunena_topics', 't'))
            ->leftJoin($db->quoteName('#__kunena_categories', 'c') . ' ON ' . $db->quoteName('c.id') . ' = ' . $db->quoteName('t.category_id'))
            ->leftJoin($db->quoteName('#__users', 'u') . ' ON ' . $db->quoteName('u.id') . ' = ' . $db->quoteName('t.first_post_userid'))
            // A shadow of a moved topic is no topic of its own.
            ->where($db->quoteName('t.moved_id') . ' = 0');

        if ($filters['category'] > 0) {
            $query->where($db->quoteName('t.category_id') . ' = ' . (int) $filters['category']);
        }

        switch ($filters['state']) {
            case 'published':
                $query->where($db->quoteName('t.hold') . ' = ' . KunenaForum::PUBLISHED);
                break;
            case 'unapproved':
                $query->where($db->quoteName('t.hold') . ' = ' . KunenaForum::UNAPPROVED);
                break;
            case 'trashed':
                $query->where($db->quoteName('t.hold') . ' >= ' . KunenaForum::DELETED);
                break;
            case 'locked':
                $query->where($db->quoteName('t.locked') . ' = 1');
                break;
            case 'sticky':
                $query->where($db->quoteName('t.ordering') . ' > 0');
                break;
        }

        if ($filters['age'] > 0) {
            $query->where($db->quoteName('t.last_post_time') . ' < ' . ($now - $filters['age'] * 86400));
        }

        if ($filters['author'] !== '') {
            $query->where('(' . $db->quoteName('u.username') . ' = :author OR ' . $db->quoteName('t.first_post_guest_name') . ' = :author2)')
                ->bind(':author', $filters['author'])
                ->bind(':author2', $filters['author']);
        }

        if ($filters['noreplies']) {
            $query->where($db->quoteName('t.posts') . ' <= 1');
        }

        if ($filters['search'] !== '') {
            $like = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $filters['search']) . '%';
            $query->where($db->quoteName('t.subject') . ' LIKE :search')->bind(':search', $like);
        }

        $count = clone $query;
        $count->select('COUNT(*)');
        $total = (int) $db->setQuery($count)->loadResult();

        $query->select($db->quoteName(['t.id', 't.category_id', 't.subject', 't.hold', 't.locked', 't.ordering', 't.posts', 't.hits',
                't.first_post_time', 't.last_post_time', 't.first_post_userid', 't.first_post_guest_name']))
            ->select($db->quoteName('c.name', 'category'))
            ->select($db->quoteName('u.username'))
            ->order($db->quoteName('t.last_post_time') . ' DESC');

        // A page after the last one (the list became shorter): the last page.
        $limit = (int) ($filters['limit'] ?? self::PAGE_SIZE);
        $pages = max(1, (int) ceil($total / $limit));
        $page  = min(max(1, (int) $filters['page']), $pages);

        return [
            'total' => $total,
            'items' => (array) $db->setQuery($query, ($page - 1) * $limit, $limit)->loadAssocList(),
            'page'  => $page,
            'pages' => $pages,
            'limit' => $limit,
        ];
    }

    /**
     * Applies an action to topics.
     *
     * @param   int[]   $ids     The topics.
     * @param   string  $action  One of ACTIONS.
     * @param   int     $target  The category of a move.
     *
     * @return  array{done: int, failed: int}
     *
     * @since   Kunena 8.0
     */
    public static function apply(array $ids, string $action, int $target = 0): array
    {
        $result = ['done' => 0, 'failed' => 0];

        if (!\in_array($action, self::ACTIONS, true)) {
            return $result;
        }

        $category = null;
        $deleted  = [];

        if ($action === 'move') {
            $category = $target > 0 ? KunenaCategoryHelper::get($target) : null;

            // A section holds no topic.
            if (!$category || !$category->exists() || $category->isSection()) {
                $result['failed'] = \count($ids);

                return $result;
            }
        }

        foreach (array_unique(array_filter(array_map('intval', $ids))) as $id) {
            try {
                $topic = KunenaTopicHelper::get($id);

                if (!$topic->exists()) {
                    $ok = false;
                } elseif ($action === 'lock' || $action === 'unlock') {
                    $topic->locked = $action === 'lock' ? 1 : 0;
                    $ok            = $topic->save();
                } elseif ($action === 'stick' || $action === 'unstick') {
                    $topic->ordering = $action === 'stick' ? 1 : 0;
                    $ok              = $topic->save();
                } elseif ($action === 'move') {
                    $ok = (int) $topic->category_id === (int) $category->id || (bool) $topic->move($category);
                } elseif ($action === 'trash') {
                    $ok = (int) $topic->hold >= KunenaForum::DELETED || $topic->publish(KunenaForum::DELETED);
                } elseif ($action === 'delete') {
                    // Only a topic in the trash: the delete cannot be undone.
                    $ok = (int) $topic->hold >= KunenaForum::DELETED && $topic->delete();

                    if ($ok) {
                        $deleted[(int) $topic->category_id] = (int) $topic->category_id;
                    }
                } else {
                    // Approve: a topic which waits; restore: a topic in the trash.
                    $from = $action === 'approve' ? [KunenaForum::UNAPPROVED] : [KunenaForum::DELETED, KunenaForum::TOPIC_DELETED];
                    $hold = (int) $topic->hold;
                    $ok   = $hold === KunenaForum::PUBLISHED || (\in_array($hold, $from, true) && $topic->publish(KunenaForum::PUBLISHED));
                }
            } catch (Throwable $e) {
                $ok = false;
            }

            $result[$ok ? 'done' : 'failed']++;
        }

        // The counters of the categories without the deleted topics.
        if ($deleted) {
            KunenaCategoryHelper::recountBatch(array_values($deleted));
        }

        return $result;
    }
}
