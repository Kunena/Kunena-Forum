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
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;
use Kunena\Forum\Libraries\Cache\KunenaCacheHelper;
use Kunena\Forum\Libraries\Forum\Message\Thankyou\KunenaMessageThankyouHelper;
use Kunena\Forum\Libraries\Forum\Topic\User\KunenaTopicUserHelper;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Throwable;

/**
 * Moves everything of one user in the forum to another one (tool "Merge users"): his messages, topics, attachments,
 * thank-yous, votes, bookmarks, subscriptions... The first account stays (blocked when asked); its logs and the history of
 * its bans stay with it, as a record of what happened.
 *
 * The caller checks who may do it (KunenaAccountActionPolicy: never an administrator as the account which is merged).
 *
 * @since   Kunena 8.0
 */
class KunenaUserMerge
{
    /**
     * The columns which name a user, in a table where the user can be changed freely: table => columns.
     *
     * @since   Kunena 8.0
     */
    public const COLUMNS = [
        '#__kunena_announcement'     => ['created_by'],
        '#__kunena_attachments'      => ['userid'],
        '#__kunena_drafts'           => ['user_id'],
        '#__kunena_karma'            => ['userid', 'target_userid'],
        '#__kunena_messages'         => ['userid', 'modified_by'],
        '#__kunena_messages_history' => ['edited_by'],
        '#__kunena_private'          => ['author_id'],
        '#__kunena_thankyou'         => ['targetuserid'],
        '#__kunena_topics'           => ['first_post_userid', 'last_post_userid'],
        '#__kunena_warnings'         => ['user_id'],
    ];

    /**
     * The columns which name a user in a key with another column: table => [user column, other column]. A row of the first
     * user is dropped when the second one has a row for the same item already.
     *
     * @since   Kunena 8.0
     */
    public const KEYED = [
        ['#__kunena_bookmarks', 'user_id', 'message_id'],
        ['#__kunena_digest', 'user_id', 'message_id'],
        ['#__kunena_polls_users', 'userid', 'pollid'],
        ['#__kunena_private_user_map', 'user_id', 'private_id'],
        ['#__kunena_rate', 'userid', 'topic_id'],
        ['#__kunena_reactions', 'user_id', 'message_id'],
        ['#__kunena_thankyou', 'userid', 'postid'],
        ['#__kunena_user_categories', 'user_id', 'category_id'],
        ['#__kunena_user_follow', 'user_id', 'followed_id'],
        ['#__kunena_user_follow', 'followed_id', 'user_id'],
        ['#__kunena_user_ignore', 'user_id', 'ignored_id'],
        ['#__kunena_user_ignore', 'ignored_id', 'user_id'],
        ['#__kunena_user_read', 'user_id', 'topic_id'],
        ['#__kunena_user_topics', 'user_id', 'topic_id'],
    ];

    /**
     * The rows which name the same user twice after the merge, and make no sense: table => [column, column].
     *
     * @since   Kunena 8.0
     */
    public const SELF_ROWS = [
        ['#__kunena_karma', 'userid', 'target_userid'],
        ['#__kunena_thankyou', 'userid', 'targetuserid'],
        ['#__kunena_user_follow', 'user_id', 'followed_id'],
        ['#__kunena_user_ignore', 'user_id', 'ignored_id'],
    ];

    /**
     * Can the first user be merged into the second one?
     *
     * @param   int  $from  The user who is merged.
     * @param   int  $into  The user who gets everything.
     *
     * @return  string|null  The language key of the error, null when it can.
     *
     * @since   Kunena 8.0
     */
    public static function check(int $from, int $into): ?string
    {
        if ($from <= 0 || $into <= 0) {
            return 'COM_KUNENA_TOOLS_MERGE_ERROR_CHOOSE';
        }

        if ($from === $into) {
            return 'COM_KUNENA_TOOLS_MERGE_ERROR_SAME';
        }

        return null;
    }

    /**
     * The numbers of rows of the first user, by table (what the merge moves).
     *
     * @param   int  $from  The user.
     *
     * @return  array<string, int>
     *
     * @since   Kunena 8.0
     */
    public static function count(int $from): array
    {
        $db     = Factory::getContainer()->get(DatabaseInterface::class);
        $counts = [];

        foreach (self::allColumns() as [$table, $column]) {
            $query = $db->createQuery()
                ->select('COUNT(*)')
                ->from($db->quoteName($table))
                ->where($db->quoteName($column) . ' = :id')
                ->bind(':id', $from, ParameterType::INTEGER);

            try {
                $total = (int) $db->setQuery($query)->loadResult();
            } catch (Throwable $e) {
                $total = 0;
            }

            if ($total) {
                $counts[$table] = ($counts[$table] ?? 0) + $total;
            }
        }

        return $counts;
    }

    /**
     * Moves everything of the first user to the second one, in one transaction.
     *
     * @param   int  $from  The user who is merged.
     * @param   int  $into  The user who gets everything.
     *
     * @return  array<string, int>  By table, the rows moved.
     *
     * @since   Kunena 8.0
     *
     * @throws  Throwable  The merge failed: nothing was changed.
     */
    public static function merge(int $from, int $into): array
    {
        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        $moved = [];

        // The topics of the first user, for the recount of their users.
        $query = $db->createQuery()
            ->select('DISTINCT ' . $db->quoteName('topic_id'))
            ->from($db->quoteName('#__kunena_user_topics'))
            ->where($db->quoteName('user_id') . ' = :id')
            ->bind(':id', $from, ParameterType::INTEGER);
        $topics = array_map('intval', (array) $db->setQuery($query)->loadColumn());

        $db->transactionStart();

        try {
            foreach (self::KEYED as [$table, $column, $other]) {
                $moved[$table] = ($moved[$table] ?? 0) + self::moveKeyed($db, $table, $column, $other, $from, $into);
            }

            foreach (self::COLUMNS as $table => $columns) {
                foreach ($columns as $column) {
                    $moved[$table] = ($moved[$table] ?? 0) + self::update($db, $table, $column, $from, $into);
                }
            }

            foreach (self::SELF_ROWS as [$table, $first, $second]) {
                $query = $db->createQuery()
                    ->delete($db->quoteName($table))
                    ->where($db->quoteName($first) . ' = ' . $db->quoteName($second));
                $db->setQuery($query)->execute();
            }

            // The name of the author kept on his messages.
            $name  = (string) KunenaUserHelper::get($into)->getName('', false);
            $query = $db->createQuery()
                ->update($db->quoteName('#__kunena_messages'))
                ->set($db->quoteName('name') . ' = :name')
                ->where($db->quoteName('userid') . ' = :id')
                ->bind(':name', $name)
                ->bind(':id', $into, ParameterType::INTEGER);
            $db->setQuery($query)->execute();

            $query = $db->createQuery()
                ->delete($db->quoteName('#__kunena_sessions'))
                ->where($db->quoteName('userid') . ' = :id')
                ->bind(':id', $from, ParameterType::INTEGER);
            $db->setQuery($query)->execute();

            $db->transactionCommit();
        } catch (Throwable $e) {
            $db->transactionRollback();

            throw $e;
        }

        // The counters: the posts of the users, the thank-yous, the users of the topics.
        try {
            if ($topics) {
                KunenaTopicUserHelper::recount($topics);
            }

            KunenaUserHelper::recount();
            KunenaMessageThankyouHelper::recount();
            KunenaCacheHelper::clear();
        } catch (Throwable $e) {
            // The rows are moved; the recount of the tools fixes the counters.
        }

        return array_filter($moved);
    }

    /**
     * All the columns which name a user: [table, column].
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    public static function allColumns(): array
    {
        $columns = [];

        foreach (self::COLUMNS as $table => $names) {
            foreach ($names as $name) {
                $columns[] = [$table, $name];
            }
        }

        foreach (self::KEYED as [$table, $column]) {
            $columns[] = [$table, $column];
        }

        return $columns;
    }

    /**
     * Updates a column from one user to another.
     *
     * @param   DatabaseInterface  $db      The database.
     * @param   string             $table   The table.
     * @param   string             $column  The column.
     * @param   int                $from    The first user.
     * @param   int                $into    The second user.
     *
     * @return  int  The rows changed.
     *
     * @since   Kunena 8.0
     */
    private static function update(DatabaseInterface $db, string $table, string $column, int $from, int $into): int
    {
        $query = $db->createQuery()
            ->update($db->quoteName($table))
            ->set($db->quoteName($column) . ' = :into')
            ->where($db->quoteName($column) . ' = :from')
            ->bind(':into', $into, ParameterType::INTEGER)
            ->bind(':from', $from, ParameterType::INTEGER);
        $db->setQuery($query)->execute();

        return (int) $db->getAffectedRows();
    }

    /**
     * Moves the rows of a keyed table: the rows of the first user whose item the second one has already are dropped, the
     * others are moved.
     *
     * @param   DatabaseInterface  $db      The database.
     * @param   string             $table   The table.
     * @param   string             $column  The column of the user.
     * @param   string             $other   The other column of the key.
     * @param   int                $from    The first user.
     * @param   int                $into    The second user.
     *
     * @return  int  The rows moved.
     *
     * @since   Kunena 8.0
     */
    private static function moveKeyed(DatabaseInterface $db, string $table, string $column, string $other, int $from, int $into): int
    {
        $query = $db->createQuery()
            ->select($db->quoteName($other))
            ->from($db->quoteName($table))
            ->where($db->quoteName($column) . ' = :into')
            ->bind(':into', $into, ParameterType::INTEGER);
        $taken = array_map('intval', (array) $db->setQuery($query)->loadColumn());

        foreach (array_chunk($taken, 500) as $chunk) {
            $query = $db->createQuery()
                ->delete($db->quoteName($table))
                ->where($db->quoteName($column) . ' = :from')
                ->whereIn($db->quoteName($other), $chunk)
                ->bind(':from', $from, ParameterType::INTEGER);
            $db->setQuery($query)->execute();
        }

        return self::update($db, $table, $column, $from, $into);
    }
}
