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
use Kunena\Forum\Libraries\Cache\KunenaCacheHelper;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Throwable;

/**
 * Merges a category into another one (tool "Merge categories"): its topics move (in batches, with their counters), its
 * subcategories go below the other one, its subscribers are subscribed to the other one; then the empty category is
 * unpublished or deleted.
 *
 * @since   Kunena 8.0
 */
class KunenaCategoryMerge
{
    /**
     * At most this many topics move in one run.
     *
     * @since   Kunena 8.0
     */
    public const BATCH = 200;

    /**
     * The empty category stays unpublished.
     *
     * @since   Kunena 8.0
     */
    public const AFTER_UNPUBLISH = 'unpublish';

    /**
     * The empty category is deleted.
     *
     * @since   Kunena 8.0
     */
    public const AFTER_DELETE = 'delete';

    /**
     * Can the first category be merged into the second one?
     *
     * @param   array  $rows  The categories (id, parentid).
     * @param   int    $from  The category which is merged.
     * @param   int    $into  The category which gets its topics.
     *
     * @return  string|null  The language key of the error, null when it can.
     *
     * @since   Kunena 8.0
     */
    public static function check(array $rows, int $from, int $into): ?string
    {
        $parents = [];

        foreach ($rows as $row) {
            $parents[(int) $row['id']] = (int) $row['parentid'];
        }

        if (!isset($parents[$from], $parents[$into])) {
            return 'COM_KUNENA_TOOLS_CATMERGE_ERROR_CHOOSE';
        }

        if ($from === $into) {
            return 'COM_KUNENA_TOOLS_CATMERGE_ERROR_SAME';
        }

        // A section (a category at the top) holds no topic.
        if ($parents[$into] === 0) {
            return 'COM_KUNENA_TOOLS_CATMERGE_ERROR_SECTION';
        }

        // The other one below the first: its subcategories would go below themselves.
        for ($id = $into, $guard = 0; $id !== 0 && $guard < 100; $id = $parents[$id] ?? 0, $guard++) {
            if ($id === $from) {
                return 'COM_KUNENA_TOOLS_CATMERGE_ERROR_BELOW';
            }
        }

        return null;
    }

    /**
     * What the first category holds: its topics (in any state), its subcategories, its subscribers.
     *
     * @param   int  $from  The category.
     *
     * @return  array{topics: int, children: int, subscribers: int}
     *
     * @since   Kunena 8.0
     */
    public static function count(int $from): array
    {
        $db = Factory::getContainer()->get('DatabaseDriver');

        $count = static function (string $table, string $column, string $extra = '') use ($db, $from): int {
            $query = $db->createQuery()
                ->select('COUNT(*)')
                ->from($db->quoteName($table))
                ->where($db->quoteName($column) . ' = :id')
                ->bind(':id', $from, ParameterType::INTEGER);

            if ($extra !== '') {
                $query->where($extra);
            }

            return (int) $db->setQuery($query)->loadResult();
        };

        return [
            'topics'      => $count('#__kunena_topics', 'category_id', $db->quoteName('moved_id') . ' = 0'),
            'children'    => $count('#__kunena_categories', 'parentid'),
            'subscribers' => $count('#__kunena_user_categories', 'category_id', $db->quoteName('subscribed') . ' = 1'),
        ];
    }

    /**
     * Merges, at most BATCH topics in a run: when the category is empty, its subcategories and its subscribers move and it is
     * unpublished or deleted.
     *
     * @param   int     $from   The category which is merged.
     * @param   int     $into   The category which gets everything.
     * @param   string  $after  AFTER_UNPUBLISH or AFTER_DELETE.
     *
     * @return  array{moved: int, failed: int, left: int, done: bool}
     *
     * @since   Kunena 8.0
     */
    public static function mergeOnSite(int $from, int $into, string $after): array
    {
        $db     = Factory::getContainer()->get('DatabaseDriver');
        $target = KunenaCategoryHelper::get($into);
        $result = ['moved' => 0, 'failed' => 0, 'left' => 0, 'done' => false];

        $query = $db->createQuery()
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__kunena_topics'))
            ->where($db->quoteName('category_id') . ' = :id')
            ->where($db->quoteName('moved_id') . ' = 0')
            ->bind(':id', $from, ParameterType::INTEGER)
            ->order($db->quoteName('id'));

        foreach (array_map('intval', (array) $db->setQuery($query, 0, self::BATCH)->loadColumn()) as $id) {
            try {
                $ok = (bool) KunenaTopicHelper::get($id)->move($target);
            } catch (Throwable $e) {
                $ok = false;
            }

            $result[$ok ? 'moved' : 'failed']++;
        }

        // The shadows of the moved topics which pointed to the category stay where they are: only the real topics count.
        $result['left'] = self::count($from)['topics'];

        if ($result['left'] === 0) {
            self::moveChildren($from, $into);
            self::moveSubscribers($from, $into);

            $category = KunenaCategoryHelper::get($from);

            try {
                if ($after === self::AFTER_DELETE) {
                    $category->delete();
                } else {
                    $category->published = 0;
                    $category->save();
                }
            } catch (Throwable $e) {
                // The topics are moved; the category is left as it is.
            }

            $result['done'] = true;
        }

        try {
            KunenaCategoryHelper::recountBatch([$from, $into]);
        } catch (Throwable $e) {
            // The recount of the tools fixes the counters.
        }

        KunenaCacheHelper::clear();

        return $result;
    }

    /**
     * The subcategories of the first category go below the second one.
     *
     * @param   int  $from  The category.
     * @param   int  $into  The new parent.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    private static function moveChildren(int $from, int $into): void
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__kunena_categories'))
            ->where($db->quoteName('parentid') . ' = :id')
            ->bind(':id', $from, ParameterType::INTEGER);

        foreach (array_map('intval', (array) $db->setQuery($query)->loadColumn()) as $id) {
            try {
                $child           = KunenaCategoryHelper::get($id);
                $child->parentid = $into;
                $child->save();
            } catch (Throwable $e) {
                // The others still move.
            }
        }
    }

    /**
     * The subscribers of the first category are subscribed to the second one.
     *
     * @param   int  $from  The category.
     * @param   int  $into  The other one.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    private static function moveSubscribers(int $from, int $into): void
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select($db->quoteName('user_id'))
            ->from($db->quoteName('#__kunena_user_categories'))
            ->where($db->quoteName('category_id') . ' = :id')
            ->where($db->quoteName('subscribed') . ' = 1')
            ->bind(':id', $from, ParameterType::INTEGER);
        $users = array_map('intval', (array) $db->setQuery($query)->loadColumn());

        if (!$users) {
            return;
        }

        $query = $db->createQuery()
            ->select($db->quoteName('user_id'))
            ->from($db->quoteName('#__kunena_user_categories'))
            ->where($db->quoteName('category_id') . ' = :id')
            ->bind(':id', $into, ParameterType::INTEGER);
        $existing = array_flip(array_map('intval', (array) $db->setQuery($query)->loadColumn()));

        foreach ($users as $user) {
            if (isset($existing[$user])) {
                $query = $db->createQuery()
                    ->update($db->quoteName('#__kunena_user_categories'))
                    ->set($db->quoteName('subscribed') . ' = 1')
                    ->where($db->quoteName('user_id') . ' = :user')
                    ->where($db->quoteName('category_id') . ' = :id')
                    ->bind(':user', $user, ParameterType::INTEGER)
                    ->bind(':id', $into, ParameterType::INTEGER);
            } else {
                $query = $db->createQuery()
                    ->insert($db->quoteName('#__kunena_user_categories'))
                    ->columns($db->quoteName(['user_id', 'category_id', 'role', 'allreadtime', 'subscribed', 'params']))
                    ->values(':user, :id, 0, 0, 1, ' . $db->quote(''))
                    ->bind(':user', $user, ParameterType::INTEGER)
                    ->bind(':id', $into, ParameterType::INTEGER);
            }

            $db->setQuery($query)->execute();
        }
    }
}
