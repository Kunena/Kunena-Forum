<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      User
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\User;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;

/**
 * The ranks which are linked to a Joomla user group: a user in the group gets the rank. The links are kept in the parameters of
 * the component (rank id => group id).
 *
 * @since   Kunena 7.2
 */
abstract class KunenaRankGroups
{
    /**
     * The rank of a user by the groups of the user, by user id.
     *
     * @var     array
     * @since   Kunena 7.2
     */
    protected static array $userRanks = [];

    /**
     * The id of the group which is linked to a rank (0 when there is none).
     *
     * @param   int  $rankId  The rank id.
     *
     * @return  int
     *
     * @since   Kunena 7.2
     */
    public static function getGroup(int $rankId): int
    {
        return self::getLinks()[$rankId] ?? 0;
    }

    /**
     * The links: the id of the group by the id of the rank.
     *
     * @return  array
     *
     * @since   Kunena 7.2
     */
    public static function getLinks(): array
    {
        $links = ComponentHelper::getParams('com_kunena')->get('rankgroups', []);
        $map   = [];

        foreach ((array) $links as $rankId => $groupId) {
            if ((int) $rankId > 0 && (int) $groupId > 0) {
                $map[(int) $rankId] = (int) $groupId;
            }
        }

        return $map;
    }

    /**
     * The id of the rank of a user because of his Joomla user groups: the rank of a group the user is a member of, else the
     * rank of a group the user inherits. Ranks of the same kind: the first rank (the lowest id).
     *
     * @param   int  $userid  The user id.
     *
     * @return  int  The rank id, or 0 when no group of the user has a rank.
     *
     * @since   Kunena 7.2
     */
    public static function getRankIdForUser(int $userid): int
    {
        if ($userid < 1) {
            return 0;
        }

        if (isset(self::$userRanks[$userid])) {
            return self::$userRanks[$userid];
        }

        self::$userRanks[$userid] = 0;
        $links                    = self::getLinks();

        if (!$links) {
            return 0;
        }

        try {
            $direct    = array_map('intval', Access::getGroupsByUser($userid, false));
            $inherited = array_map('intval', Access::getGroupsByUser($userid, true));

            // A group of the user itself goes before the groups it inherits (Registered for an Author).
            foreach ([$direct, array_diff($inherited, $direct)] as $groups) {
                $found = array_keys(array_filter($links, static fn ($group) => \in_array($group, $groups, true)));

                if ($found) {
                    sort($found);
                    self::$userRanks[$userid] = (int) $found[0];

                    return self::$userRanks[$userid];
                }
            }
        } catch (Exception $e) {
            // No group rank: the other ranks are used.
        }

        return 0;
    }

    /**
     * Link a rank to a group, or remove the link (group 0).
     *
     * @param   int  $rankId   The rank id.
     * @param   int  $groupId  The id of the Joomla user group.
     *
     * @return  void
     *
     * @since   Kunena 7.2
     */
    public static function setGroup(int $rankId, int $groupId): void
    {
        if ($rankId < 1) {
            return;
        }

        $links = self::getLinks();

        if ($groupId > 0) {
            $links[$rankId] = $groupId;
        } else {
            unset($links[$rankId]);
        }

        // The keys are the ids of the ranks (they have to stay text in the JSON of the parameters).
        $params = ComponentHelper::getParams('com_kunena');
        $params->set('rankgroups', array_combine(array_map('strval', array_keys($links)), array_values($links)) ?: []);
        $json = $params->toString();

        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->createQuery()
            ->update($db->quoteName('#__extensions'))
            ->set($db->quoteName('params') . ' = :params')
            ->where($db->quoteName('element') . ' = ' . $db->quote('com_kunena'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('component'))
            ->bind(':params', $json);
        $db->setQuery($query)->execute();

        self::$userRanks = [];
    }
}
