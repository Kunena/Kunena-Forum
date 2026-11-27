<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Api
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Api;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Event\KunenaCheckSpamEvent;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\KunenaFloodPolicy;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Log\KunenaLog;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use RuntimeException;

/**
 * A reply posted through the REST API (POST v1/kunena/posts). It follows the rules of the form of the site: the permission to reply,
 * the flood protection, the identical messages, the links, the moderation of the new users and of the category, the spam check of the
 * plugins, and then the notifications. An error is a RuntimeException with the HTTP status as its code (400, 403, 404, 409, 429).
 *
 * The API cannot show a captcha: a user who gets one on the site may not post through the API.
 *
 * @since   Kunena 8.0
 */
abstract class KunenaApiReply
{
    /**
     * An identical message of the same user in the same topic within this time (seconds) is not posted twice.
     *
     * @since   Kunena 8.0
     */
    public const int DUPLICATE_SECONDS = 300;

    /**
     * The fields of a reply in the body of the request: flat ({"topic_id": 1, "message": "..."}) or as JSON:API
     * ({"data": {"attributes": {...}}}).
     *
     * @param   mixed  $body  The decoded JSON body.
     *
     * @return  array  topic_id, parent_id, message and subscribe.
     *
     * @throws  RuntimeException  400 without a topic or a message.
     * @since   Kunena 8.0
     */
    public static function fields(mixed $body): array
    {
        $body = \is_array($body) ? $body : [];

        if (isset($body['data']['attributes']) && \is_array($body['data']['attributes'])) {
            $body = $body['data']['attributes'];
        }

        $fields = [
            'topic_id'  => self::id($body['topic_id'] ?? 0),
            'parent_id' => self::id($body['parent_id'] ?? 0),
            'message'   => \is_string($body['message'] ?? null) ? trim($body['message']) : '',
            'subscribe' => !empty($body['subscribe']),
        ];

        if (!$fields['topic_id'] && !$fields['parent_id']) {
            throw new RuntimeException(Text::_('COM_KUNENA_API_ERROR_NO_TOPIC'), 400);
        }

        if (!self::hasText($fields['message'])) {
            throw new RuntimeException(Text::_('COM_KUNENA_LIB_TABLE_MESSAGES_ERROR_NO_MESSAGE'), 400);
        }

        return $fields;
    }

    /**
     * Whether a message has text which is shown: something else than BBCode tags and spaces.
     *
     * @param   string  $message  The BBCode of the message.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function hasText(string $message): bool
    {
        if (preg_match('/\[(img|video|attachment|ebay|map|tweet|instagram|soundcloud)\b/i', $message)) {
            return true;
        }

        return trim(preg_replace('/\[[^\[\]]*\]/', '', $message)) !== '';
    }

    /**
     * Whether the user posted too recently (the option flood protection, in seconds). The moderators of the category may always post.
     *
     * @param   int   $lastPostTime  The time of the last message of the user (0: none).
     * @param   int   $now           The time now.
     * @param   int   $seconds       The option flood protection (0: off).
     * @param   bool  $moderator     The user moderates the category.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function isFlood(int $lastPostTime, int $now, int $seconds, bool $moderator): bool
    {
        // The same rule as the form of the site.
        return !$moderator && KunenaFloodPolicy::secondsToWait($now, $seconds, [$lastPostTime]) > 0;
    }

    /**
     * Whether the reply is the last message of the topic again: same user, same text, within DUPLICATE_SECONDS (a client which sends
     * a request twice).
     *
     * @param   int     $lastUserid   The author of the last message of the topic.
     * @param   string  $lastMessage  Its text.
     * @param   int     $lastTime     Its time.
     * @param   int     $userid       The user who replies.
     * @param   string  $message      The text of the reply.
     * @param   int     $now          The time now.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function isDuplicate(int $lastUserid, string $lastMessage, int $lastTime, int $userid, string $message, int $now): bool
    {
        return $userid > 0 && $lastUserid === $userid && trim($lastMessage) === trim($message) && $lastTime >= $now - self::DUPLICATE_SECONDS;
    }

    /**
     * Whether the reply waits for a moderator because the user has few messages (the option hold the posts of the new users).
     *
     * @param   bool  $moderator  The user moderates the category.
     * @param   int   $posts      The number of messages of the user.
     * @param   int   $holdPosts  The option (0: off).
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function holdsNewUser(bool $moderator, int $posts, int $holdPosts): bool
    {
        return !$moderator && $posts < $holdPosts;
    }

    /**
     * Whether a message has links or images (which the new users may not post, with the option of the site). The form removes them;
     * the API refuses the reply, so that the client knows that its text would not be posted as it is.
     *
     * @param   string  $message  The BBCode of the message.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function hasLinksOrImages(string $message): bool
    {
        // Also the addresses which the site makes links of (an FTP address, www2., an e-mail address): the form removes them.
        return (bool) preg_match('#\[(url|img|email)\b|(?:https?|ftp)://|www\d{0,3}\.[\w-]+\.|[\w.+-]+@[\w-]+\.[\w.-]+#i', $message);
    }

    /**
     * The links of a message to other sites (the option max links counts them; the links to the site itself do not count).
     *
     * @param   string  $message  The BBCode of the message.
     * @param   string  $host     The host of the site.
     *
     * @return  integer
     *
     * @since   Kunena 8.0
     */
    public static function externalLinks(string $message, string $host): int
    {
        // The links as the site shows them (the form counts the links of the shown text): not in a block of code, a [url] tag once
        // (its text is no second link), and an address without a scheme too ([url=example.com], www.example.com, //example.com).
        // Only the addresses with a scheme were counted, so a reply of the API passed the limit with links of the other forms.
        $links = [];
        $text  = (string) preg_replace('#\[code\b[^\]]*\].*?\[/code\]#is', ' ', $message);
        $text  = (string) preg_replace_callback(
            '#\[url=([^\]]+)\].*?\[/url\]|\[url\](.*?)\[/url\]#is',
            static function (array $match) use (&$links): string {
                $links[] = trim($match[1] !== '' ? $match[1] : ($match[2] ?? ''), " \"'");

                return ' ';
            },
            $text
        );

        preg_match_all('#(?<![\w/@.\-])(?:(?:https?|ftp)://|www\d{0,3}\.)[^\s\[\]"\'<>]+#i', $text, $matches);
        $links = array_merge($links, $matches[0]);

        $host  = strtolower((string) preg_replace('/^www\d{0,3}\./i', '', $host));
        $count = 0;

        foreach ($links as $link) {
            // An address of the site itself (a path) is internal.
            if ($link === '' || ($link[0] === '/' && !str_starts_with($link, '//'))) {
                continue;
            }

            $linkHost = (string) preg_replace('#^(?:[a-z][a-z0-9+.\-]*:)?//#i', '', $link);
            $linkHost = strtolower((string) preg_replace('#[/?\#:].*$#s', '', $linkHost));
            $linkHost = (string) preg_replace('/^www\d{0,3}\./i', '', $linkHost);

            if ($linkHost !== '' && $linkHost !== $host) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Posts the reply of the user.
     *
     * @param   KunenaUser  $me      The user of the API token.
     * @param   array       $fields  The fields (see fields()).
     *
     * @return  KunenaMessage  The saved message; its hold is 1 when it waits for a moderator.
     *
     * @throws  RuntimeException  With the HTTP status as the code.
     * @throws  Exception
     * @since   Kunena 8.0
     */
    public static function reply(KunenaUser $me, array $fields): KunenaMessage
    {
        $config = KunenaFactory::getConfig();

        if (!$me->exists()) {
            throw new RuntimeException(Text::_('COM_KUNENA_API_ERROR_GUEST'), 403);
        }

        // Read-only mode: the site hides the reply buttons (isAuthorised()), but tryAuthorise() below does not know the mode, and a
        // reply through the API was saved.
        if ($config->readOnly) {
            throw new RuntimeException(Text::_('COM_KUNENA_API_ERROR_READONLY'), 403);
        }

        // The message to reply to: the one asked for, or the first message of the topic.
        $topic  = KunenaTopicHelper::get($fields['topic_id'] ?: null);
        $parent = KunenaMessageHelper::get($fields['parent_id'] ?: ($topic->exists() ? (int) $topic->first_post_id : null));

        if (
            !$parent->exists() || $parent->hold || !$parent->isAuthorised('read', $me)
            || ($fields['topic_id'] && (int) $parent->thread !== (int) $fields['topic_id'])
        ) {
            throw new RuntimeException(Text::_('COM_KUNENA_API_ERROR_NO_TOPIC'), 404);
        }

        try {
            $parent->tryAuthorise('reply', $me);
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage(), 403);
        }

        if ($me->canDoCaptcha() && self::captchaEnabled()) {
            throw new RuntimeException(Text::_('COM_KUNENA_API_ERROR_CAPTCHA'), 403);
        }

        [$topic, $message] = $parent->newReply(['message' => $fields['message'], 'quote' => false]);
        $category          = $topic->getCategory();
        $moderator         = $me->isModerator($category);
        $now               = Factory::getDate()->toUnix();

        if (self::isFlood(self::lastPostTime((int) $me->userid), $now, (int) $config->floodProtection, $moderator)) {
            throw new RuntimeException(Text::sprintf('COM_KUNENA_POST_TOPIC_FLOOD', (int) $config->floodProtection), 429);
        }

        if (self::isDuplicate((int) $topic->last_post_userid, (string) $topic->last_post_message, (int) $topic->last_post_time, (int) $me->userid, $fields['message'], $now)) {
            throw new RuntimeException(Text::_('COM_KUNENA_POST_DUPLICATE_IGNORED'), 409);
        }

        if (!$me->checkUserAllowedLinksImages() && self::hasLinksOrImages($fields['message'])) {
            throw new RuntimeException(Text::_('COM_KUNENA_API_ERROR_LINKS_IMAGES'), 403);
        }

        if (!$moderator && !$me->isAdmin() && !$topic->isAuthorised('approve', $me)
            && self::externalLinks($fields['message'], (string) Uri::getInstance(Uri::root())->getHost()) > (int) $config->maxLinks
        ) {
            throw new RuntimeException(Text::_('COM_KUNENA_TOPIC_SPAM_LINK_PROTECTION'), 400);
        }

        if (!$config->ipTracking) {
            $message->ip = '';
        }

        if (self::holdsNewUser($moderator, (int) $me->posts, (int) $config->holdNewUsersPosts)) {
            $message->hold = 1;
        }

        // The spam check of the plugins: the reply is rejected or held for a moderator.
        $spamCheck = $message->dispatchSpamCheck($topic, $category, $me, false, (string) KunenaUserHelper::getUserIp());

        if ($spamCheck->getVerdict() === KunenaCheckSpamEvent::REJECT) {
            $reason = $spamCheck->getReason();

            throw new RuntimeException($reason !== '' ? $reason : Text::_('COM_KUNENA_POST_ERROR_SPAM_REJECTED'), 403);
        }

        if ($spamCheck->getVerdict() === KunenaCheckSpamEvent::HOLD) {
            $message->hold = 1;
        }

        $activity = KunenaFactory::getActivityIntegration();

        if ($message->hold == 0) {
            $activity->onBeforeReply($message);
        } else {
            $activity->onBeforeHold($message);
        }

        @ignore_user_abort(true);

        try {
            $message->save();
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage(), 400);
        }

        if ($config->ipTracking && $message->ip) {
            $me->ip = $message->ip;
            $me->save();
        }

        if ($moderator && $config->logModeration) {
            KunenaLog::log(KunenaLog::TYPE_ACTION, KunenaLog::LOG_POST_CREATE, ['mesid' => $message->id, 'parentid' => $parent->id], $category, $topic);
        }

        // The links of the e-mails go to the site, not to the API.
        $url = KunenaApi::siteUrl('index.php?option=com_kunena&view=topic&catid=' . (int) $message->catid . '&id=' . (int) $message->thread . '&mesid=' . (int) $message->id);
        $message->processNotification($url);
        $message->sendMentionNotifications('', true, $url);

        if ($fields['subscribe'] && !$topic->getUserTopic($me)->subscribed) {
            try {
                $topic->subscribe(1, $me);
                $activity->onAfterSubscribe($topic, 1);
            } catch (Exception $e) {
                // The reply is posted: a subscription which fails does not make it an error.
            }
        }

        return $message;
    }

    /**
     * Whether the site has a captcha which the form would show.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    protected static function captchaEnabled(): bool
    {
        $plugin = (string) Factory::getApplication()->get('captcha', '0');

        return $plugin !== '' && $plugin !== '0' && PluginHelper::isEnabled('captcha', $plugin);
    }

    /**
     * The time of the last message of a user.
     *
     * @param   int  $userid  The user.
     *
     * @return  integer  0 when the user has no message.
     *
     * @since   Kunena 8.0
     */
    protected static function lastPostTime(int $userid): int
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select('MAX(' . $db->quoteName('time') . ')')
            ->from($db->quoteName('#__kunena_messages'))
            ->where($db->quoteName('userid') . ' = :userid')
            ->bind(':userid', $userid);

        return (int) $db->setQuery($query)->loadResult();
    }

    /**
     * A positive id, or 0.
     *
     * @param   mixed  $value  The value of the body.
     *
     * @return  integer
     *
     * @since   Kunena 8.0
     */
    private static function id(mixed $value): int
    {
        return (\is_int($value) || (\is_string($value) && ctype_digit($value))) && (int) $value > 0 ? (int) $value : 0;
    }
}
