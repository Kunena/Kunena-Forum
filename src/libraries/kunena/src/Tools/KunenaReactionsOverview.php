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

/**
 * The reactions to posts (tool "Reactions overview"): the reactions used the most, and the users who react far more than
 * they post, which is how a bot or a bought account looks. Their reactions can be removed.
 *
 * @since   Kunena 8.0
 */
class KunenaReactionsOverview
{
    /**
     * A user is listed from this number of reactions.
     *
     * @since   Kunena 8.0
     */
    public const MIN_REACTIONS = 50;

    /**
     * A user is listed when his reactions are at least this many times his posts (a user without posts counts as one).
     *
     * @since   Kunena 8.0
     */
    public const RATIO = 10;

    /**
     * The users who react far more than they post, the most reactions first.
     *
     * @param   array  $rows  user_id, reactions, posts, username.
     *
     * @return  array<int, array{user: int, username: string, reactions: int, posts: int, ratio: float}>
     *
     * @since   Kunena 8.0
     */
    public static function suspects(array $rows): array
    {
        $result = [];

        foreach ($rows as $row) {
            $reactions = (int) $row['reactions'];
            $posts     = (int) $row['posts'];
            $ratio     = $reactions / max(1, $posts);

            if ((int) $row['user_id'] > 0 && $reactions >= self::MIN_REACTIONS && $ratio >= self::RATIO) {
                $result[] = ['user' => (int) $row['user_id'], 'username' => (string) ($row['username'] ?? ''), 'reactions' => $reactions, 'posts' => $posts, 'ratio' => $ratio];
            }
        }

        usort($result, static fn ($a, $b) => [$b['reactions'], $a['user']] <=> [$a['reactions'], $b['user']]);

        return $result;
    }

    /**
     * The reactions of this site.
     *
     * @return  array{total: int, users: int, top: array<string, int>, suspects: array}
     *
     * @since   Kunena 8.0
     */
    public static function reportOnSite(): array
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select($db->quoteName('reaction'))
            ->select('COUNT(*) AS ' . $db->quoteName('count'))
            ->from($db->quoteName('#__kunena_reactions'))
            ->group($db->quoteName('reaction'))
            ->order($db->quoteName('count') . ' DESC');
        $top = [];

        foreach ((array) $db->setQuery($query, 0, 20)->loadAssocList() as $row) {
            $top[(string) $row['reaction']] = (int) $row['count'];
        }

        $query = $db->createQuery()
            ->select($db->quoteName('r.user_id'))
            ->select('COUNT(*) AS ' . $db->quoteName('reactions'))
            ->select('MAX(' . $db->quoteName('k.posts') . ') AS ' . $db->quoteName('posts'))
            ->select('MAX(' . $db->quoteName('u.username') . ') AS ' . $db->quoteName('username'))
            ->from($db->quoteName('#__kunena_reactions', 'r'))
            ->leftJoin($db->quoteName('#__kunena_users', 'k') . ' ON ' . $db->quoteName('k.userid') . ' = ' . $db->quoteName('r.user_id'))
            ->leftJoin($db->quoteName('#__users', 'u') . ' ON ' . $db->quoteName('u.id') . ' = ' . $db->quoteName('r.user_id'))
            ->group($db->quoteName('r.user_id'));
        $users = (array) $db->setQuery($query)->loadAssocList();

        return [
            'total'    => array_sum(array_map(static fn ($row) => (int) $row['reactions'], $users)),
            'users'    => \count($users),
            'top'      => $top,
            'suspects' => self::suspects($users),
        ];
    }

    /**
     * Removes all the reactions of the chosen users.
     *
     * @param   int[]  $ids  The users.
     *
     * @return  int  The reactions removed.
     *
     * @since   Kunena 8.0
     */
    public static function removeOnSite(array $ids): int
    {
        $db      = Factory::getContainer()->get('DatabaseDriver');
        $removed = 0;

        foreach (array_values(array_unique(array_filter(array_map('intval', $ids)))) as $id) {
            $query = $db->createQuery()->delete($db->quoteName('#__kunena_reactions'))->where($db->quoteName('user_id') . ' = :id')
                ->bind(':id', $id, ParameterType::INTEGER);
            $db->setQuery($query)->execute();
            $removed += (int) $db->getAffectedRows();
        }

        return $removed;
    }
}
