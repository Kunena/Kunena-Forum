<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Forum.Category.User
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Forum\Category\User;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\Database\Exception\ExecutionFailureException;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * Class \Kunena\Forum\Libraries\Forum\Category\CategoryUserHelper
 *
 * @since   Kunena 6.0
 */
abstract class KunenaCategoryUserHelper
{
    /**
     * @var     array
     * @since   Kunena 6.0
     */
    protected static $_instances = [];

    /**
     * The users which have all their category rows loaded: a category without an instance has no row.
     *
     * @var     array
     * @since   Kunena 7.2
     */
    protected static $_allLoaded = [];

    /**
     * Get an instance of \Kunena\Forum\Libraries\Forum\Category\CategoryUser object.
     *
     * @param   null|int  $category  The category id to load.
     * @param   mixed     $user      The user id to load - Can be only an integer.
     * @param   bool      $reload    Reload objects from the database.
     *
     * @return  KunenaCategoryUser    The user category object.
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public static function get($category = null, $user = null, $reload = false): KunenaCategoryUser
    {
        if ($category instanceof KunenaCategory) {
            $category = $category->id;
        }

        $user = KunenaUserHelper::get($user);

        // Test for null before the conversion: intval(null) is 0 and the test could never be true.
        if ($category === null) {
            return new KunenaCategoryUser(0, $user);
        }

        $category = \intval($category);

        if ($reload || empty(self::$_instances [$user->userid][$category])) {
            // loadCategories() skips the ids which are already loaded, so forget the old instance to really reload it.
            unset(self::$_instances [$user->userid][$category], self::$_allLoaded [$user->userid]);

            $user_categories                             = self::getCategories($category, $user);
            self::$_instances [$user->userid][$category] = array_pop($user_categories);
        }

        return self::$_instances [$user->userid][$category];
    }

    /**
     * Get categories for a specific user.
     *
     * @param   bool|array|int  $ids   The category ids to load.
     * @param   mixed           $user  The user id to load.
     *
     * @return  KunenaCategoryUser[]
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public static function getCategories($ids = false, $user = null): array
    {
        $user = KunenaUserHelper::get($user);

        if ($ids === false) {
            // Get categories which are seen by current user
            $ids = KunenaCategoryHelper::getCategories();
        } elseif (!\is_array($ids)) {
            $ids = [$ids];
        }

        // Convert category objects into ids
        foreach ($ids as $i => $id) {
            if ($id instanceof KunenaCategory) {
                $ids[$i] = $id->id;
            }
        }

        $ids = array_unique($ids);
        self::loadCategories($ids, $user);

        $list = [];

        foreach ($ids as $id) {
            if (!empty(self::$_instances [$user->userid][$id])) {
                $list [$id] = self::$_instances [$user->userid][$id];
            }
        }

        return $list;
    }

    /**
     * Load categories for a specific user.
     *
     * @param   array       $ids   The category ids to load.
     * @param   KunenaUser  $user  user
     *
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    protected static function loadCategories(array $ids, KunenaUser $user): void
    {
        foreach ($ids as $i => $id) {
            $iid = \intval($id);

            if ($iid != $id || isset(self::$_instances [$user->userid][$id])) {
                unset($ids[$i]);
            }
        }

        if (empty($ids)) {
            return;
        }

        // All the rows of the user were loaded before: a category without an instance has no row.
        if (!empty(self::$_allLoaded [$user->userid])) {
            foreach ($ids as $id) {
                self::$_instances [$user->userid][$id] = new KunenaCategoryUser($id, $user);
            }

            return;
        }

        // Load all the rows of the user with one query, there is one row at most for each category. The
        // alternative is one query for each list of categories which a page asks for.
        $db     = Factory::getContainer()->get('DatabaseDriver');
        $query  = $db->createQuery();
        $query->select('*')
            ->from($db->quoteName('#__kunena_user_categories'))
            ->where($db->quoteName('user_id') . ' = ' . $db->quote($user->userid));
        $db->setQuery($query);

        $results = [];

        try {
            $results = (array) $db->loadAssocList('category_id');
        } catch (ExecutionFailureException $e) {
            KunenaError::displayDatabaseError($e);
        }

        foreach ($results as $categoryId => $row) {
            if (isset(self::$_instances [$user->userid][$categoryId])) {
                continue;
            }

            $instance = new KunenaCategoryUser();
            $instance->bind($row);
            $instance->exists(true);
            self::$_instances [$user->userid][$categoryId] = $instance;
        }

        foreach ($ids as $id) {
            if (!isset(self::$_instances [$user->userid][$id])) {
                self::$_instances [$user->userid][$id] = new KunenaCategoryUser($id, $user);
            }
        }

        self::$_allLoaded [$user->userid] = true;

        unset($results);
    }

    /**
     * @param   array             $ids   ids
     * @param   int|string|null   $user  users
     *
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public static function markRead(array $ids, $user = null): void
    {
        $user = KunenaUserHelper::get($user);

        $items      = self::getCategories($ids, $user);
        $updateList = [];
        $insertList = [];

        $db   = Factory::getContainer()->get('DatabaseDriver');
        $time = Factory::getDate()->toUnix();

        foreach ($items as $item) {
            if ($item->exists()) {
                $updateList[] = (int) $item->category_id;
            } else {
                $insertList[] = $db->quote($user->userid) . ', ' . $db->quote($item->category_id) . ', ' . $db->quote($time) . ', ' . $db->quote('');
            }
        }

        if ($updateList) {
            $idlist = implode(',', $updateList);
            $query  = $db->createQuery();
            $query
                ->update($db->quoteName('#__kunena_user_categories'))
                ->set('allreadtime = ' . $db->quote($time))
                ->where('user_id = ' . $db->quote($user->userid))
                ->where($db->quoteName('category_id') . ' IN (' . $idlist . ')');
            $db->setQuery($query);

            try {
                $db->execute();
            } catch (ExecutionFailureException $e) {
                KunenaError::displayDatabaseError($e);
            }
        }

        if ($insertList) {
            $query = $db->createQuery();
            $query
                ->insert($db->quoteName('#__kunena_user_categories'))
                ->columns('user_id, category_id, allreadtime, params')
                ->values($insertList);
            $db->setQuery($query);

            try {
                $db->execute();
            } catch (ExecutionFailureException $e) {
                KunenaError::displayDatabaseError($e);
            }
        }
    }
}
