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
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;
use Kunena\Forum\Libraries\Config\KunenaConfig;

/**
 * The three names of a user: the username (login), the real name (the name of the Joomla user) and the nickname (a custom
 * field of the Joomla users, chosen in the configuration). The configuration says which one is shown, and the user can choose
 * which one others see when the configuration lets the users choose.
 *
 * @since   Kunena 7.2
 */
abstract class KunenaUserName
{
    /**
     * The real name.
     *
     * @since   Kunena 7.2
     */
    public const REALNAME = 0;

    /**
     * The username.
     *
     * @since   Kunena 7.2
     */
    public const USERNAME = 1;

    /**
     * The nickname.
     *
     * @since   Kunena 7.2
     */
    public const DISPLAYNAME = 2;

    /**
     * The key of the choice of a user in the parameters of the Joomla user.
     *
     * @since   Kunena 7.2
     */
    public const PARAM = 'kunena_name';

    /**
     * The nicknames by user id.
     *
     * @var     array
     * @since   Kunena 7.2
     */
    protected static $displayNames = [];

    /**
     * The id of the Joomla custom field with the nickname (0 when there is none).
     *
     * @return  int
     *
     * @since   Kunena 7.2
     */
    public static function getDisplayNameField(): int
    {
        static $field = null;

        if ($field !== null) {
            return $field;
        }

        $config = KunenaConfig::getInstance();
        $field  = isset($config->displayNameField) ? (int) $config->displayNameField : 0;

        // The field has to be a published field of the Joomla users: a field which was unpublished or deleted is no source.
        if ($field) {
            try {
                $db    = Factory::getContainer()->get(DatabaseInterface::class);
                $query = $db->createQuery()
                    ->select($db->quoteName('id'))
                    ->from($db->quoteName('#__fields'))
                    ->where($db->quoteName('id') . ' = ' . $field)
                    ->where($db->quoteName('context') . ' = ' . $db->quote('com_users.user'))
                    ->where($db->quoteName('state') . ' = 1');
                $field = (int) $db->setQuery($query)->loadResult();
            } catch (Exception $e) {
                $field = 0;
            }
        }

        return $field;
    }

    /**
     * The name which the configuration shows.
     *
     * @return  int  REALNAME, USERNAME or DISPLAYNAME.
     *
     * @since   Kunena 7.2
     */
    public static function getGlobalType(): int
    {
        $config = KunenaConfig::getInstance();
        $type   = isset($config->username) ? (int) $config->username : self::USERNAME;

        return \in_array($type, [self::REALNAME, self::USERNAME, self::DISPLAYNAME], true) ? $type : self::USERNAME;
    }

    /**
     * May the users choose the name which others see.
     *
     * @return  bool
     *
     * @since   Kunena 7.2
     */
    public static function userCanChoose(): bool
    {
        $config = KunenaConfig::getInstance();

        return isset($config->nameUserChoice) && $config->nameUserChoice;
    }

    /**
     * The choice of a user, or null when the user did not choose (or choosing is off).
     *
     * @param   int  $userid  The user id.
     *
     * @return  int|null
     *
     * @since   Kunena 7.2
     */
    public static function getChoice(int $userid): ?int
    {
        if (!$userid || !self::userCanChoose()) {
            return null;
        }

        $choice = KunenaUserHelper::getJoomlaParam($userid, self::PARAM);

        return $choice !== null && $choice !== '' && \in_array((int) $choice, [self::REALNAME, self::USERNAME, self::DISPLAYNAME], true) ? (int) $choice : null;
    }

    /**
     * The name which is shown of a user: the choice of the user, else the one of the configuration; a name which is empty
     * is replaced by the next one (nickname, real name, username).
     *
     * @param   KunenaUser  $user  The user.
     *
     * @return  string
     *
     * @since   Kunena 7.2
     */
    public static function get(KunenaUser $user): string
    {
        $userid = (int) $user->userid;
        $type   = self::getChoice($userid) ?? self::getGlobalType();

        $candidates = [
            self::DISPLAYNAME => [self::DISPLAYNAME, self::REALNAME, self::USERNAME],
            self::REALNAME    => [self::REALNAME, self::USERNAME],
            self::USERNAME    => [self::USERNAME, self::REALNAME],
        ][$type];

        foreach ($candidates as $candidate) {
            $name = trim((string) match ($candidate) {
                self::DISPLAYNAME => self::getDisplayName($userid),
                self::REALNAME    => $user->name,
                default           => $user->username,
            });

            if ($name !== '') {
                return $name;
            }
        }

        return (string) $user->username;
    }

    /**
     * The nickname of a user: the value of the Joomla custom field.
     *
     * @param   int  $userid  The user id.
     *
     * @return  string
     *
     * @since   Kunena 7.2
     */
    public static function getDisplayName(int $userid): string
    {
        if (!$userid || !self::getDisplayNameField()) {
            return '';
        }

        if (!isset(self::$displayNames[$userid])) {
            self::preload([$userid]);
        }

        return self::$displayNames[$userid] ?? '';
    }

    /**
     * Load the nicknames of users with one query.
     *
     * @param   int[]  $userids  The user ids.
     *
     * @return  void
     *
     * @since   Kunena 7.2
     */
    public static function preload(array $userids): void
    {
        $field = self::getDisplayNameField();

        if (!$field) {
            return;
        }

        $userids = array_diff(array_map('intval', $userids), array_keys(self::$displayNames));

        if (!$userids) {
            return;
        }

        foreach ($userids as $id) {
            self::$displayNames[$id] = '';
        }

        try {
            $db    = Factory::getContainer()->get(DatabaseInterface::class);
            $query = $db->createQuery()
                ->select($db->quoteName(['item_id', 'value']))
                ->from($db->quoteName('#__fields_values'))
                ->where($db->quoteName('field_id') . ' = ' . $field)
                ->where($db->quoteName('item_id') . ' IN (' . implode(',', $userids) . ')');
            $db->setQuery($query);

            foreach ((array) $db->loadObjectList() as $row) {
                self::$displayNames[(int) $row->item_id] = trim((string) $row->value);
            }
        } catch (Exception $e) {
            // The names fall back to the real name.
        }
    }

    /**
     * Save the nickname of a user in the Joomla custom field (an empty nickname removes the value).
     *
     * @param   int     $userid    The user id.
     * @param   string  $nickname  The nickname.
     *
     * @return  bool  False when there is no field for the nickname or the value could not be saved.
     *
     * @since   Kunena 7.2
     */
    public static function setDisplayName(int $userid, string $nickname): bool
    {
        $field = self::getDisplayNameField();

        if (!$userid || !$field) {
            return false;
        }

        $nickname = trim(preg_replace('/\s+/', ' ', strip_tags($nickname)));
        $nickname = function_exists('mb_substr') ? mb_substr($nickname, 0, 100) : substr($nickname, 0, 100);

        try {
            $db     = Factory::getContainer()->get(DatabaseInterface::class);
            $itemId = (string) $userid;
            $delete = $db->createQuery()
                ->delete($db->quoteName('#__fields_values'))
                ->where($db->quoteName('field_id') . ' = ' . $field)
                ->where($db->quoteName('item_id') . ' = :item')
                ->bind(':item', $itemId);
            $db->setQuery($delete)->execute();

            if ($nickname !== '') {
                $insert = $db->createQuery()
                    ->insert($db->quoteName('#__fields_values'))
                    ->columns($db->quoteName(['field_id', 'item_id', 'value']))
                    ->values(':field, :item, :value')
                    ->bind(':field', $field, ParameterType::INTEGER)
                    ->bind(':item', $itemId)
                    ->bind(':value', $nickname);
                $db->setQuery($insert)->execute();
            }

            self::$displayNames[$userid] = $nickname;

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Save the choice of a user (null for no choice).
     *
     * @param   int       $userid  The user id.
     * @param   int|null  $choice  REALNAME, USERNAME, DISPLAYNAME or null.
     *
     * @return  void
     *
     * @since   Kunena 7.2
     */
    public static function setChoice(int $userid, ?int $choice): void
    {
        KunenaUserHelper::setJoomlaParam($userid, self::PARAM, $choice);
    }

    /**
     * The SQL condition to find the users by any of their names.
     *
     * @param   string  $search  The text to look for.
     * @param   string  $alias   The alias of the table of the Joomla users in the query.
     *
     * @return  string  The condition (without AND).
     *
     * @since   Kunena 7.2
     */
    public static function getSearchCondition(string $search, string $alias = 'a'): string
    {
        $db   = Factory::getContainer()->get(DatabaseInterface::class);
        $like = $db->quote('%' . $db->escape($search, true) . '%', false);
        $conditions = [
            $db->quoteName($alias . '.username') . ' LIKE ' . $like,
            $db->quoteName($alias . '.name') . ' LIKE ' . $like,
        ];

        if ($field = self::getDisplayNameField()) {
            $conditions[] = $db->quoteName($alias . '.id') . ' IN (SELECT ' . $db->quoteName('item_id') . ' FROM '
                . $db->quoteName('#__fields_values') . ' WHERE ' . $db->quoteName('field_id') . ' = ' . $field
                . ' AND ' . $db->quoteName('value') . ' LIKE ' . $like . ')';
        }

        return '(' . implode(' OR ', $conditions) . ')';
    }
}
