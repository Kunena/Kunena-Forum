<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Forum.Message
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Forum\Message;

\defined('_JEXEC') or die();

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Uri\Uri;
use Joomla\Database\ParameterType;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Throwable;

/**
 * A reply which is published at a date and time chosen by its author.
 *
 * The reply is saved as an unapproved message (hold 1) with its time in scheduled_time: until then only its author and the
 * moderators see it. The scheduler task publishes it when its time has come, unless it was held for review when it was posted
 * (scheduled_review 1): that one stays for a moderator.
 *
 * @since   Kunena 8.0
 */
abstract class KunenaMessageSchedule
{
    /**
     * The value of the field is no date and time.
     */
    public const INVALID = -1;

    /**
     * The date and time of the field is not in the future.
     */
    public const PAST = -2;

    /**
     * The time of the value of a datetime-local field (Y-m-d\TH:i, or with seconds).
     *
     * @param   string   $value     The value of the field.
     * @param   string   $timezone  The time zone in which the value is read (UTC when it is unknown).
     * @param   integer  $now       The current time.
     *
     * @return  integer  0 for an empty value (no schedule), the Unix time of a date in the future, or self::INVALID or self::PAST.
     *
     * @since   Kunena 8.0
     */
    public static function parse(string $value, string $timezone, int $now): int
    {
        $value = trim($value);

        if ($value === '') {
            return 0;
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}(:\d{2})?$/', $value, $matches)) {
            return self::INVALID;
        }

        try {
            $zone = new DateTimeZone($timezone);
        } catch (Exception $e) {
            $zone = new DateTimeZone('UTC');
        }

        $format = isset($matches[1]) ? 'Y-m-d\TH:i:s' : 'Y-m-d\TH:i';
        $date   = DateTimeImmutable::createFromFormat('!' . $format, $value, $zone);

        // A date which does not exist (the 30th of February, the hour 25, an hour skipped by the summer time) is moved by PHP.
        if (!$date || $date->format($format) !== $value) {
            return self::INVALID;
        }

        $time = $date->getTimestamp();

        return $time > $now ? $time : self::PAST;
    }

    /**
     * Publish the scheduled replies whose time has come, the oldest first.
     *
     * A reply held for review, or which may not be published any more (its topic is deleted, unapproved or locked, its author is
     * removed, banned or blocked, or may not reply to the topic), is not published: its schedule is cleared, it stays an unapproved
     * message for the moderators.
     *
     * @param   integer  $now    The current time.
     * @param   integer  $limit  The most replies in one run.
     *
     * @return  array  ['published' => number, 'skipped' => number]
     *
     * @throws  Exception
     * @since   Kunena 8.0
     */
    public static function publishDue(int $now, int $limit = 50): array
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__kunena_messages'))
            ->where($db->quoteName('hold') . ' = 1')
            ->where($db->quoteName('scheduled_time') . ' > 0')
            ->where($db->quoteName('scheduled_time') . ' <= :now')
            ->bind(':now', $now, ParameterType::INTEGER)
            ->order([$db->quoteName('scheduled_time') . ' ASC', $db->quoteName('id') . ' ASC'])
            ->setLimit($limit);
        $db->setQuery($query);

        $result = ['published' => 0, 'skipped' => 0];

        foreach ($db->loadColumn() as $id) {
            try {
                $published = self::publishOne(KunenaMessageHelper::get((int) $id, true));
            } catch (Throwable $e) {
                Log::add('Kunena: the scheduled reply ' . (int) $id . ' could not be published: ' . $e->getMessage(), Log::ERROR, 'kunena');
                $published = false;
            }

            $result[$published ? 'published' : 'skipped']++;
        }

        return $result;
    }

    /**
     * Publish one scheduled reply, or clear its schedule when it may not be published.
     *
     * @param   KunenaMessage  $message  The reply.
     *
     * @return  boolean  The reply was published.
     *
     * @throws  Exception
     * @since   Kunena 8.0
     */
    protected static function publishOne(KunenaMessage $message): bool
    {
        $topic  = $message->getTopic();
        $author = KunenaUserHelper::get((int) $message->userid);

        if (
            $message->scheduled_review
            || !$topic->exists() || $topic->hold != 0 || $topic->locked
            || !$author->exists() || $author->isBanned() || $author->isBlocked()
            || !$topic->isAuthorised('reply', $author)
        ) {
            self::clear((int) $message->id);

            return false;
        }

        $message->time = max((int) $message->scheduled_time, (int) $topic->last_post_time + 1);

        // The reply is published as if its author posted it now: the user who runs the task (a visitor, an administrator or
        // nobody on the command line) still gets the e-mail of a subscriber, and the topic is not marked read for him.
        $me = KunenaUserHelper::setMyself($author);

        try {
            if (!$message->publish(KunenaForum::PUBLISHED)) {
                return false;
            }

            // The reply is published: an e-mail which cannot be queued does not make it unpublished.
            try {
                // Outside of the site (the administrator, the command line) the link of the e-mail is made for the site.
                $url = Factory::getApplication()->isClient('site') ? null : Uri::root() . $message->getPermaUri()->toString();

                $message->processNotification($url);
                $message->sendMentionNotifications();
            } catch (Throwable $e) {
                Log::add('Kunena: the notifications of the scheduled reply ' . (int) $message->id . ' failed: ' . $e->getMessage(), Log::ERROR, 'kunena');
            }
        } finally {
            KunenaUserHelper::setMyself($me);
        }

        return true;
    }

    /**
     * Clear the schedule of a reply: it stays an unapproved message, which the task does not try again.
     *
     * @param   integer  $id  The id of the reply.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    protected static function clear(int $id): void
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->update($db->quoteName('#__kunena_messages'))
            ->set($db->quoteName('scheduled_time') . ' = 0')
            ->set($db->quoteName('scheduled_review') . ' = 0')
            ->where($db->quoteName('id') . ' = :id')
            ->bind(':id', $id, ParameterType::INTEGER);
        $db->setQuery($query)->execute();

        $message                   = KunenaMessageHelper::get($id);
        $message->scheduled_time   = 0;
        $message->scheduled_review = 0;
    }
}
