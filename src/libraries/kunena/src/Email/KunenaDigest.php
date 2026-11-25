<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Email
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Email;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Mail\MailerFactoryInterface;
use Joomla\CMS\Mail\MailHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * The digest of the subscriptions: a user who chose a daily or a weekly digest gets no e-mail for each new reply in the topics and
 * categories he follows; the replies are collected, and the scheduler task sends him one e-mail with all of them, grouped by topic.
 *
 * The choice of the user is the Joomla parameter kunena_digest, the time of his last digest kunena_digest_sent. Only the e-mails of
 * the subscriptions go into a digest: the moderators, the administrators and the followers of a user keep their e-mail of each post.
 *
 * @since   Kunena 8.0
 */
abstract class KunenaDigest
{
    /**
     * An e-mail for each new reply (the default).
     *
     * @since   Kunena 8.0
     */
    public const int EACH = 0;

    /**
     * One e-mail a day.
     *
     * @since   Kunena 8.0
     */
    public const int DAILY = 1;

    /**
     * One e-mail a week.
     *
     * @since   Kunena 8.0
     */
    public const int WEEKLY = 2;

    /**
     * The Joomla parameter of the user with his choice.
     *
     * @since   Kunena 8.0
     */
    public const string PARAM = 'kunena_digest';

    /**
     * The Joomla parameter of the user with the time of his last digest.
     *
     * @since   Kunena 8.0
     */
    public const string SENT_PARAM = 'kunena_digest_sent';

    /**
     * The most replies in one digest: the newest ones (the others are only counted, with a link to the forum).
     *
     * @since   Kunena 8.0
     */
    public const int MAX_MESSAGES = 50;

    /**
     * The routine of the scheduler which sends the digests (plg_task_kunena).
     *
     * @since   Kunena 8.0
     */
    public const string TASK = 'send.digests';

    /**
     * Can the digests be sent on this site (null: not checked yet in this request)?
     *
     * @var     boolean|null
     * @since   Kunena 8.0
     */
    protected static ?bool $available = null;

    /**
     * Can the digests be sent: the plugin of the tasks of Kunena is enabled and a task of the routine is enabled? Without
     * them a digest is never sent, and a user who chose one would get no e-mail of his subscriptions at all.
     *
     * @param   bool   $pluginEnabled  Is the plugin plg_task_kunena enabled?
     * @param   int[]  $taskStates     The states of the tasks of the routine (1: enabled, 0: disabled, -2: trashed).
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function canSend(bool $pluginEnabled, array $taskStates): bool
    {
        return $pluginEnabled && \in_array(1, array_map('intval', $taskStates), true);
    }

    /**
     * Can the digests be sent on this site (canSend())? Checked once in a request.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function isAvailable(): bool
    {
        if (self::$available === null) {
            try {
                $db    = self::getDatabase();
                $query = $db->createQuery()
                    ->select($db->quoteName('state'))
                    ->from($db->quoteName('#__scheduler_tasks'))
                    ->where($db->quoteName('type') . ' = ' . $db->quote(self::TASK));
                $states = (array) $db->setQuery($query)->loadColumn();

                self::$available = self::canSend(PluginHelper::isEnabled('task', 'kunena'), $states);
            } catch (Exception $e) {
                // Without the scheduler, no digest: each post is sent at once.
                self::$available = false;
            }
        }

        return self::$available;
    }

    /**
     * The seconds between two digests of a choice (0: each reply, no wait).
     *
     * @param   int  $frequency  The choice (EACH, DAILY or WEEKLY).
     *
     * @return  integer
     *
     * @since   Kunena 8.0
     */
    public static function interval(int $frequency): int
    {
        return match ($frequency) {
            self::DAILY  => 86400,
            self::WEEKLY => 604800,
            default      => 0,
        };
    }

    /**
     * The choice which a request sends, or the default.
     *
     * @param   mixed  $value  The value.
     *
     * @return  integer
     *
     * @since   Kunena 8.0
     */
    public static function clean(mixed $value): int
    {
        $value = is_numeric($value) ? (int) $value : self::EACH;

        return \in_array($value, [self::EACH, self::DAILY, self::WEEKLY], true) ? $value : self::EACH;
    }

    /**
     * The recipients of the e-mail of a new post: the ones who get it now, and the ones whose post goes into their digest (a plain
     * subscriber who chose a digest; not a moderator, an administrator or a follower of the author, nor somebody without an id).
     *
     * @param   object[]  $recipients   The recipients (id, subscription, moderator, admin, follower).
     * @param   int[]     $frequencies  The choices of the users: [user id => choice].
     *
     * @return  array{0: object[], 1: int[]}  The recipients of the e-mail now, and the ids of the users of a digest.
     *
     * @since   Kunena 8.0
     */
    public static function split(array $recipients, array $frequencies): array
    {
        $now    = [];
        $digest = [];

        foreach ($recipients as $recipient) {
            $id = (int) ($recipient->id ?? 0);

            $plainSubscriber = !empty($recipient->subscription) && empty($recipient->moderator) && empty($recipient->admin)
                && empty($recipient->follower);

            if ($id > 0 && $plainSubscriber && ($frequencies[$id] ?? self::EACH) !== self::EACH) {
                $digest[$id] = $id;

                continue;
            }

            $now[] = $recipient;
        }

        return [$now, array_values($digest)];
    }

    /**
     * Whether the digest of a user is due: his choice is over since his last digest (or since his oldest collected reply when he got
     * none yet). A user who went back to an e-mail for each reply gets what was collected at once.
     *
     * @param   int  $frequency     His choice.
     * @param   int  $lastSent      The time of his last digest (0: none).
     * @param   int  $firstPending  The time of his oldest collected reply.
     * @param   int  $now           Now.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function isDue(int $frequency, int $lastSent, int $firstPending, int $now): bool
    {
        $interval = self::interval($frequency);

        if ($interval === 0) {
            return true;
        }

        return $now - ($lastSent > 0 ? $lastSent : $firstPending) >= $interval;
    }

    /**
     * The choices of users, read with one query (their Joomla parameters, kept for the other reads of the request).
     *
     * @param   int[]  $ids  The ids of the users.
     *
     * @return  int[]  [user id => choice]
     *
     * @since   Kunena 8.0
     */
    public static function frequencies(array $ids): array
    {
        return array_map(static fn (array $params): int => self::clean($params[self::PARAM] ?? self::EACH), self::userParams($ids));
    }

    /**
     * The Joomla parameters of users, read with one query.
     *
     * @param   int[]  $ids  The ids of the users.
     *
     * @return  array  [user id => parameters (an empty array when they are not valid)]
     *
     * @since   Kunena 8.0
     */
    protected static function userParams(array $ids): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids), static fn (int $id): bool => $id > 0)));

        if (!$ids) {
            return [];
        }

        $db    = self::getDatabase();
        $query = $db->createQuery()
            ->select($db->quoteName(['id', 'params']))
            ->from($db->quoteName('#__users'))
            ->whereIn($db->quoteName('id'), $ids);

        $users = [];

        foreach ((array) $db->setQuery($query)->loadObjectList() as $row) {
            $params                 = json_decode((string) $row->params, true);
            $users[(int) $row->id]  = \is_array($params) ? $params : [];
        }

        return $users;
    }

    /**
     * Collect a new post for the digests of users (a post which is collected twice stays once).
     *
     * @param   int    $messageId  The id of the message.
     * @param   int[]  $userIds    The users.
     * @param   int    $time       The time of the post (0: now).
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public static function collect(int $messageId, array $userIds, int $time = 0): void
    {
        $userIds = array_values(array_unique(array_filter(array_map('intval', $userIds), static fn (int $id): bool => $id > 0)));

        if ($messageId <= 0 || !$userIds) {
            return;
        }

        $db   = self::getDatabase();
        $time = $time > 0 ? $time : time();
        $rows = array_map(static fn (int $userid): string => $userid . ', ' . $messageId . ', ' . $time, $userIds);

        $db->setQuery(
            'INSERT IGNORE INTO ' . $db->quoteName('#__kunena_digest')
            . ' (' . implode(', ', $db->quoteName(['user_id', 'message_id', 'created_time'])) . ') VALUES (' . implode('), (', $rows) . ')'
        )->execute();
    }

    /**
     * The collected posts, by user: [user id => ['first' => time of the oldest, 'ids' => message ids, the newest first]].
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    public static function pending(): array
    {
        $db    = self::getDatabase();
        $query = $db->createQuery()
            ->select($db->quoteName(['user_id', 'message_id', 'created_time']))
            ->from($db->quoteName('#__kunena_digest'))
            ->order($db->quoteName('user_id') . ', ' . $db->quoteName('created_time') . ' DESC, ' . $db->quoteName('message_id') . ' DESC');

        $pending = [];

        foreach ((array) $db->setQuery($query)->loadObjectList() as $row) {
            $userid = (int) $row->user_id;
            $time   = (int) $row->created_time;

            $pending[$userid]['ids'][] = (int) $row->message_id;
            $pending[$userid]['first'] = min($pending[$userid]['first'] ?? $time, $time);
        }

        return $pending;
    }

    /**
     * Send the digests which are due (the scheduler task): each user gets one e-mail with his collected posts which still exist and
     * which he may still read; his collected posts are removed, and the time of his digest is kept.
     *
     * @param   int  $now  Now.
     *
     * @return  array{users: int, sent: int}  The users whose digest was due, and the e-mails which were sent.
     *
     * @throws  Exception
     * @since   Kunena 8.0
     */
    public static function sendDue(int $now): array
    {
        $result  = ['users' => 0, 'sent' => 0];
        $pending = self::pending();

        if (!$pending) {
            return $result;
        }

        KunenaFactory::loadLanguage('com_kunena', 'site');
        $users = self::userParams(array_keys($pending));

        foreach ($pending as $userid => $collected) {
            $frequency = self::clean($users[$userid][self::PARAM] ?? self::EACH);
            $lastSent  = (int) ($users[$userid][self::SENT_PARAM] ?? 0);

            if (!self::isDue($frequency, $lastSent, (int) $collected['first'], $now)) {
                continue;
            }

            $result['users']++;

            if (self::send(KunenaUserHelper::get($userid), $collected['ids'])) {
                $result['sent']++;
            }

            // The collected posts go, sent or not readable any more: a failed e-mail is not sent again and again.
            $db    = self::getDatabase();
            $query = $db->createQuery()
                ->delete($db->quoteName('#__kunena_digest'))
                ->where($db->quoteName('user_id') . ' = :userid')
                ->whereIn($db->quoteName('message_id'), $collected['ids'])
                ->bind(':userid', $userid, ParameterType::INTEGER);
            $db->setQuery($query)->execute();

            KunenaUserHelper::setJoomlaParam($userid, self::SENT_PARAM, $now);
        }

        return $result;
    }

    /**
     * Send the digest of a user: his posts which still exist and which he may read, grouped by topic.
     *
     * @param   KunenaUser  $user  The user.
     * @param   int[]       $ids   The ids of the messages, the newest first.
     *
     * @return  boolean  Whether an e-mail was sent (false also when nothing is left to send).
     *
     * @since   Kunena 8.0
     */
    public static function send(KunenaUser $user, array $ids): bool
    {
        if (!$user->exists() || $user->isBlocked() || !MailHelper::isEmailAddress((string) $user->email)) {
            return false;
        }

        $more     = max(0, \count($ids) - self::MAX_MESSAGES);
        $messages = KunenaMessageHelper::getMessages(\array_slice($ids, 0, self::MAX_MESSAGES), 'none');
        $topics   = [];

        foreach ($ids as $id) {
            $message = $messages[$id] ?? null;

            if (!$message instanceof KunenaMessage || !$message->exists() || (int) $message->hold !== 0 || !$message->isAuthorised('read', $user)) {
                continue;
            }

            $topics[(int) $message->thread][] = $message;
        }

        if (!$topics) {
            return false;
        }

        $config = KunenaFactory::getConfig();
        $base   = rtrim(Uri::getInstance()->toString(['scheme', 'host', 'port']), '/');
        $escape = static fn (string $text): string => htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        $count  = array_sum(array_map('count', $topics));
        $body   = '<p>' . Text::plural('COM_KUNENA_DIGEST_INTRO', $count, $escape((string) $config->boardTitle)) . '</p>';

        foreach ($topics as $list) {
            $topic = $list[0]->getTopic();
            $body .= '<h2 style="font-size:1.1em;margin:1.2em 0 .4em">'
                . '<a href="' . $escape($base . $list[0]->getPermaUrl()) . '">' . $escape((string) $topic->subject) . '</a></h2><ul>';

            foreach ($list as $message) {
                $body .= '<li><strong>' . $escape((string) $message->getAuthor()->getName()) . '</strong> &middot; '
                    . $escape($message->getTime()->toKunena('datetime')) . '<br>'
                    . $escape((string) mb_substr(trim(preg_replace('/\s+/', ' ', strip_tags(KunenaParser::stripBBCode((string) $message->message, 0, false)))), 0, 200, 'UTF-8'))
                    . ' <a href="' . $escape($base . $message->getPermaUrl()) . '">' . Text::_('COM_KUNENA_DIGEST_READ') . '</a></li>';
            }

            $body .= '</ul>';
        }

        if ($more) {
            $body .= '<p>' . Text::plural('COM_KUNENA_DIGEST_MORE', $more) . '</p>';
        }

        $body .= '<p style="font-size:.9em;color:#666">' . Text::sprintf(
            'COM_KUNENA_DIGEST_FOOTER',
            '<a href="' . $escape($base . KunenaRoute::_('index.php?option=com_kunena&view=user&layout=edit', false)) . '">' . Text::_('COM_KUNENA_DIGEST_SETTINGS') . '</a>'
        ) . '</p>';

        try {
            $mail = Factory::getContainer()->get(MailerFactoryInterface::class)->createMailer();
            $mail->setSubject(MailHelper::cleanSubject(Text::plural('COM_KUNENA_DIGEST_SUBJECT', $count, (string) $config->boardTitle)));
            $mail->setSender([$config->email, (string) $config->boardTitle]);
            $mail->isHtml(true);
            $mail->setBody($body);

            return (bool) KunenaEmail::send($mail, [$user->email]);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * The database.
     *
     * @return  DatabaseInterface
     *
     * @since   Kunena 8.0
     */
    protected static function getDatabase(): DatabaseInterface
    {
        return Factory::getContainer()->get(DatabaseInterface::class);
    }
}
