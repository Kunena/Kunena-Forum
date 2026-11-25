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
use Joomla\Filesystem\File;
use Kunena\Forum\Libraries\Attachment\KunenaAttachment;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\User\KunenaAvatarPath;
use Kunena\Forum\Libraries\User\KunenaBan;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Forum\Libraries\User\KunenaUserSocials;
use Throwable;

/**
 * Everything a user has in the forum, and the clean-up after a spammer in one step (tool "Clean up a user"): the messages
 * trashed or deleted, the attachments, the profile emptied, the user banned or blocked.
 *
 * The caller checks who may do it (KunenaAccountActionPolicy: never an administrator, never oneself).
 *
 * @since   Kunena 8.0
 */
class KunenaUserCleanup
{
    /**
     * The messages stay.
     *
     * @since   Kunena 8.0
     */
    public const MESSAGES_KEEP = 'keep';

    /**
     * The messages go to the trash (they can be restored).
     *
     * @since   Kunena 8.0
     */
    public const MESSAGES_TRASH = 'trash';

    /**
     * The messages are deleted for good.
     *
     * @since   Kunena 8.0
     */
    public const MESSAGES_DELETE = 'delete';

    /**
     * The account stays.
     *
     * @since   Kunena 8.0
     */
    public const ACCOUNT_KEEP = 'keep';

    /**
     * The user is banned from the forum (he can read, not write).
     *
     * @since   Kunena 8.0
     */
    public const ACCOUNT_BAN = 'ban';

    /**
     * The user is blocked from the whole site.
     *
     * @since   Kunena 8.0
     */
    public const ACCOUNT_BLOCK = 'block';

    /**
     * At most this many addresses and other users are listed.
     *
     * @since   Kunena 8.0
     */
    public const LIST_LIMIT = 20;

    /**
     * The options of apply() from a request: only known values, the defaults keep everything.
     *
     * @param   array  $input  The request.
     *
     * @return  array{messages: string, attachments: bool, profile: bool, account: string}
     *
     * @since   Kunena 8.0
     */
    public static function options(array $input): array
    {
        $messages = (string) ($input['messages'] ?? '');
        $account  = (string) ($input['account'] ?? '');

        return [
            'messages'    => \in_array($messages, [self::MESSAGES_TRASH, self::MESSAGES_DELETE], true) ? $messages : self::MESSAGES_KEEP,
            'attachments' => !empty($input['attachments']),
            'profile'     => !empty($input['profile']),
            'account'     => \in_array($account, [self::ACCOUNT_BAN, self::ACCOUNT_BLOCK], true) ? $account : self::ACCOUNT_KEEP,
        ];
    }

    /**
     * Does apply() change anything with these options?
     *
     * @param   array  $options  The options (options()).
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function hasAction(array $options): bool
    {
        return $options['messages'] !== self::MESSAGES_KEEP || $options['attachments'] || $options['profile']
            || $options['account'] !== self::ACCOUNT_KEEP;
    }

    /**
     * What the user has in the forum.
     *
     * @param   int  $userid  The user.
     *
     * @return  array|null  Null when the user does not exist.
     *
     * @since   Kunena 8.0
     */
    public static function summary(int $userid): ?array
    {
        if ($userid <= 0) {
            return null;
        }

        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select($db->quoteName(['id', 'name', 'username', 'email', 'block', 'registerDate', 'lastvisitDate']))
            ->from($db->quoteName('#__users'))
            ->where($db->quoteName('id') . ' = :id')
            ->bind(':id', $userid, ParameterType::INTEGER);
        $account = $db->setQuery($query)->loadAssoc();

        if (!$account) {
            return null;
        }

        $user = KunenaUserHelper::get($userid);

        $query = $db->createQuery()
            ->select([$db->quoteName('hold'), 'COUNT(*) AS ' . $db->quoteName('total')])
            ->from($db->quoteName('#__kunena_messages'))
            ->where($db->quoteName('userid') . ' = :id')
            ->bind(':id', $userid, ParameterType::INTEGER)
            ->group($db->quoteName('hold'));
        $byHold = array_map('intval', (array) $db->setQuery($query)->loadAssocList('hold', 'total'));

        $query = $db->createQuery()
            ->select('COUNT(*)')
            ->from($db->quoteName('#__kunena_topics'))
            ->where($db->quoteName('first_post_userid') . ' = :id')
            ->bind(':id', $userid, ParameterType::INTEGER);
        $topics = (int) $db->setQuery($query)->loadResult();

        $query = $db->createQuery()
            ->select(['COUNT(*) AS ' . $db->quoteName('total'), 'SUM(' . $db->quoteName('size') . ') AS ' . $db->quoteName('bytes')])
            ->from($db->quoteName('#__kunena_attachments'))
            ->where($db->quoteName('userid') . ' = :id')
            ->bind(':id', $userid, ParameterType::INTEGER);
        $attachments = (array) $db->setQuery($query)->loadAssoc();

        $query = $db->createQuery()
            ->select([$db->quoteName('ip'), 'COUNT(*) AS ' . $db->quoteName('total')])
            ->from($db->quoteName('#__kunena_messages'))
            ->where($db->quoteName('userid') . ' = :id')
            ->where($db->quoteName('ip') . ' <> ' . $db->quote(''))
            ->where($db->quoteName('ip') . ' IS NOT NULL')
            ->bind(':id', $userid, ParameterType::INTEGER)
            ->group($db->quoteName('ip'))
            ->order('COUNT(*) DESC');
        $ips = array_map('intval', (array) $db->setQuery($query, 0, self::LIST_LIMIT)->loadAssocList('ip', 'total'));

        $others = [];

        if ($ips) {
            $query = $db->createQuery()
                ->select('DISTINCT ' . $db->quoteName('u.id') . ', ' . $db->quoteName('u.username'))
                ->from($db->quoteName('#__kunena_messages', 'm'))
                ->innerJoin($db->quoteName('#__users', 'u') . ' ON ' . $db->quoteName('u.id') . ' = ' . $db->quoteName('m.userid'))
                ->whereIn($db->quoteName('m.ip'), array_map('strval', array_keys($ips)), ParameterType::STRING)
                ->where($db->quoteName('m.userid') . ' <> :id')
                ->bind(':id', $userid, ParameterType::INTEGER)
                ->order($db->quoteName('u.username'));
            $others = (array) $db->setQuery($query, 0, self::LIST_LIMIT)->loadAssocList('id', 'username');
        }

        $ban = KunenaBan::getInstanceByUserid($userid);

        return [
            'id'         => $userid,
            'username'   => (string) $account['username'],
            'name'       => (string) $account['name'],
            'email'      => (string) $account['email'],
            'blocked'    => (bool) $account['block'],
            'registered' => (string) $account['registerDate'],
            'lastVisit'  => (string) ($account['lastvisitDate'] ?? ''),
            'avatar'     => (string) $user->avatar,
            'signature'  => (string) $user->signature,
            'messages'   => [
                'published'  => $byHold[KunenaForum::PUBLISHED] ?? 0,
                'unapproved' => $byHold[KunenaForum::UNAPPROVED] ?? 0,
                'deleted'    => ($byHold[KunenaForum::DELETED] ?? 0) + ($byHold[KunenaForum::TOPIC_DELETED] ?? 0),
                'total'      => array_sum($byHold),
            ],
            'topics'      => $topics,
            'attachments' => ['total' => (int) ($attachments['total'] ?? 0), 'bytes' => (int) ($attachments['bytes'] ?? 0)],
            'ips'         => $ips,
            'others'      => $others,
            'banned'      => $ban && $ban->id && $ban->isEnabled(),
            'banBlocks'   => $ban && $ban->id && $ban->isEnabled() && $ban->blocked,
        ];
    }

    /**
     * Cleans up the user.
     *
     * @param   int    $userid   The user.
     * @param   array  $options  The options (options()).
     *
     * @return  array{messages: int, attachments: int, profile: bool, account: string}  What was done.
     *
     * @since   Kunena 8.0
     */
    public static function apply(int $userid, array $options): array
    {
        $done = ['messages' => 0, 'attachments' => 0, 'profile' => false, 'account' => self::ACCOUNT_KEEP];

        if ($userid <= 0) {
            return $done;
        }

        if ($options['messages'] !== self::MESSAGES_KEEP) {
            foreach (self::messages($userid) as $message) {
                try {
                    $ok = $options['messages'] === self::MESSAGES_DELETE ? $message->delete() : $message->publish(KunenaForum::DELETED);
                } catch (Throwable $e) {
                    $ok = false;
                }

                $done['messages'] += $ok ? 1 : 0;
            }
        }

        if ($options['attachments']) {
            $db    = Factory::getContainer()->get('DatabaseDriver');
            $query = $db->createQuery()
                ->select($db->quoteName('id'))
                ->from($db->quoteName('#__kunena_attachments'))
                ->where($db->quoteName('userid') . ' = :id')
                ->bind(':id', $userid, ParameterType::INTEGER);

            foreach ((array) $db->setQuery($query)->loadColumn() as $id) {
                try {
                    $done['attachments'] += KunenaAttachment::getInstance((int) $id)->delete() ? 1 : 0;
                } catch (Throwable $e) {
                    // The others are still deleted.
                }
            }
        }

        if ($options['profile']) {
            $done['profile'] = self::clearProfile($userid);
        }

        if ($options['account'] !== self::ACCOUNT_KEEP) {
            $ban = KunenaBan::getInstanceByUserid($userid, true);

            if (!$ban->id || !$ban->isEnabled()) {
                $ban->ban($userid, null, $options['account'] === self::ACCOUNT_BLOCK ? 1 : 0);
            } elseif ($options['account'] === self::ACCOUNT_BLOCK) {
                $ban->blocked = 1;
            }

            if ($ban->save()) {
                $done['account'] = $options['account'];
                KunenaUserHelper::recountBanned();

                if ($options['account'] === self::ACCOUNT_BLOCK) {
                    Factory::getApplication()->logout($userid, ['clientid' => 0]);
                }
            }
        }

        return $done;
    }

    /**
     * All the messages of the user, the unapproved and the trashed ones too.
     *
     * @param   int  $userid  The user.
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    private static function messages(int $userid): array
    {
        $messages = [];

        foreach (['recent', 'unapproved', 'deleted'] as $mode) {
            $params = ['starttime' => '-1', 'nolimit' => -1, 'user' => $userid];

            if ($mode !== 'recent') {
                $params['mode'] = $mode;
            }

            [, $list] = KunenaMessageHelper::getLatestMessages(false, 0, 0, $params);

            foreach ($list as $message) {
                $messages[$message->id] = $message;
            }
        }

        return $messages;
    }

    /**
     * Empties the profile: the avatar (and its upload), the signature, the personal text, the website, the place, the socials.
     *
     * @param   int  $userid  The user.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    private static function clearProfile(int $userid): bool
    {
        $user = KunenaUserHelper::get($userid);

        $avatarFile = KunenaAvatarPath::isUploadOf($user->avatar, $userid)
            ? KunenaAvatarPath::getPath(JPATH_ROOT . '/media/kunena/avatars', $user->avatar)
            : null;

        if ($avatarFile !== null && is_file($avatarFile)) {
            File::delete($avatarFile);
        }

        $user->avatar       = '';
        $user->signature    = '';
        $user->personalText = '';
        $user->websitename  = '';
        $user->websiteurl   = '';
        $user->location     = '';

        try {
            $socials = KunenaUserSocials::getInstance($userid);

            foreach ($socials as $key => $social) {
                if (isset($social->value)) {
                    $socials->$key->value = '';
                }
            }

            $socials->save();
        } catch (Throwable $e) {
            // The rest of the profile is still emptied.
        }

        return (bool) $user->save();
    }
}
