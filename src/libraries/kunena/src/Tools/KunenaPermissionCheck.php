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

use Joomla\CMS\Access\Access;
use Joomla\CMS\Factory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\User\KunenaUser;
use ReflectionClass;
use Throwable;

/**
 * What a user may do in a category, and why (tool "Permission checker"): each action of the forum with the rules which
 * decide it (read access, guests writing, ban, locked, section, polls, uploads, moderation), the one which refuses shown
 * with its message. The answer is the one of KunenaCategory::tryAuthorise(), the same as on the site.
 *
 * @since   Kunena 8.0
 */
class KunenaPermissionCheck
{
    /**
     * The actions which are checked: action => language key of its name.
     *
     * @since   Kunena 8.0
     */
    public const ACTIONS = [
        'read'                              => 'COM_KUNENA_TOOLS_PERMS_ACTION_READ',
        'topic.create'                      => 'COM_KUNENA_TOOLS_PERMS_ACTION_CREATE',
        'topic.reply'                       => 'COM_KUNENA_TOOLS_PERMS_ACTION_REPLY',
        'topic.post.edit'                   => 'COM_KUNENA_TOOLS_PERMS_ACTION_EDIT',
        'topic.post.attachment.createimage' => 'COM_KUNENA_TOOLS_PERMS_ACTION_IMAGE',
        'topic.post.attachment.createfile'  => 'COM_KUNENA_TOOLS_PERMS_ACTION_FILE',
        'topic.poll.create'                 => 'COM_KUNENA_TOOLS_PERMS_ACTION_POLL',
        'topic.poll.vote'                   => 'COM_KUNENA_TOOLS_PERMS_ACTION_VOTE',
        'topic.post.thankyou'               => 'COM_KUNENA_TOOLS_PERMS_ACTION_THANKYOU',
        'subscribe'                         => 'COM_KUNENA_TOOLS_PERMS_ACTION_SUBSCRIBE',
        'moderate'                          => 'COM_KUNENA_TOOLS_PERMS_ACTION_MODERATE',
        'admin'                             => 'COM_KUNENA_TOOLS_PERMS_ACTION_ADMIN',
    ];

    /**
     * The rules of each action of a category (KunenaCategory::$actions).
     *
     * @return  array<string, string[]>
     *
     * @since   Kunena 8.0
     */
    public static function rules(): array
    {
        $property = (new ReflectionClass(KunenaCategory::class))->getProperty('actions');

        return (array) $property->getValue();
    }

    /**
     * What the user may do in the category: by action, allowed or not, and each rule with its refusal (null when it passes).
     *
     * @param   KunenaCategory  $category  The category.
     * @param   KunenaUser      $user      The user (a guest: userid 0).
     *
     * @return  array<string, array{allowed: bool, reason: string, rules: array<string, string|null>}>
     *
     * @since   Kunena 8.0
     */
    public static function check(KunenaCategory $category, KunenaUser $user): array
    {
        $rules  = self::rules();
        $class  = new ReflectionClass($category);
        $result = [];
        $cache  = [];

        foreach (array_keys(self::ACTIONS) as $action) {
            $list = [];

            foreach ($rules[$action] ?? [] as $rule) {
                if (!\array_key_exists($rule, $cache)) {
                    try {
                        $method       = $class->getMethod('authorise' . $rule);
                        $error        = $method->invoke($category, $user);
                        $cache[$rule] = $error ? $error->getMessage() : null;
                    } catch (Throwable $e) {
                        $cache[$rule] = $e->getMessage();
                    }
                }

                $list[$rule] = $cache[$rule];
            }

            try {
                $error = $category->tryAuthorise($action, $user, false);
            } catch (Throwable $e) {
                $error = $e;
            }

            $result[$action] = [
                'allowed' => !$error,
                // All the rules pass but the action is refused: a plugin of Kunena refuses it.
                'reason' => $error ? $error->getMessage() : '',
                'rules'  => $list,
            ];
        }

        return $result;
    }

    /**
     * Who the user is for the forum: his groups, his access levels, his roles, his ban.
     *
     * @param   KunenaUser      $user      The user.
     * @param   KunenaCategory  $category  The category.
     *
     * @return  array{groups: string[], levels: string[], admin: bool, moderator: bool, banned: bool}
     *
     * @since   Kunena 8.0
     */
    public static function profile(KunenaUser $user, KunenaCategory $category): array
    {
        $db     = Factory::getContainer()->get('DatabaseDriver');
        $groups = Access::getGroupsByUser((int) $user->userid, true);
        $levels = Access::getAuthorisedViewLevels((int) $user->userid);
        $names  = static function (string $table, array $ids) use ($db): array {
            if (!$ids) {
                return [];
            }

            $query = $db->createQuery()
                ->select($db->quoteName('title'))
                ->from($db->quoteName($table))
                ->whereIn($db->quoteName('id'), array_map('intval', $ids))
                ->order($db->quoteName('title'));

            return (array) $db->setQuery($query)->loadColumn();
        };

        return [
            'groups'    => $names('#__usergroups', $groups),
            'levels'    => $names('#__viewlevels', $levels),
            'admin'     => (bool) $user->isAdmin($category),
            'moderator' => (bool) $user->isModerator($category),
            'banned'    => (bool) $user->isBanned(),
        ];
    }

    /**
     * The access of the category: the type, the level or group, published, locked.
     *
     * @param   KunenaCategory  $category  The category.
     *
     * @return  array{type: string, access: string, published: bool, locked: bool, section: bool}
     *
     * @since   Kunena 8.0
     */
    public static function categoryAccess(KunenaCategory $category): array
    {
        $db     = Factory::getContainer()->get('DatabaseDriver');
        $type   = (string) $category->accesstype;
        $access = (string) $category->access;

        try {
            if ($type === 'joomla.level') {
                $query  = $db->createQuery()->select($db->quoteName('title'))->from($db->quoteName('#__viewlevels'))->where($db->quoteName('id') . ' = ' . (int) $category->access);
                $access = (string) ($db->setQuery($query)->loadResult() ?: $access);
            } elseif ($type === 'joomla.group') {
                $query  = $db->createQuery()->select($db->quoteName('title'))->from($db->quoteName('#__usergroups'))->where($db->quoteName('id') . ' = ' . (int) $category->pubAccess);
                $access = (string) ($db->setQuery($query)->loadResult() ?: $category->pubAccess);
            }
        } catch (Throwable $e) {
            // The id is shown.
        }

        return [
            'type'      => $type,
            'access'    => $access,
            'published' => (int) $category->published === 1,
            'locked'    => (bool) $category->locked,
            'section'   => $category->isSection(),
        ];
    }
}
