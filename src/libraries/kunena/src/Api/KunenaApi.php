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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Forum\Libraries\User\KunenaUser;

/**
 * The data of the REST API of Kunena (the web services of Joomla, read only): what a category, a topic and a post give, and what of
 * their text a user may get. The API gives only what the user (a guest without a token) may read on the site.
 *
 * @since   Kunena 8.0
 */
abstract class KunenaApi
{
    /**
     * The fields of a category.
     *
     * @since   Kunena 8.0
     */
    public const array CATEGORY_FIELDS = ['id', 'parent_id', 'name', 'alias', 'description', 'topics', 'posts', 'last_post_id', 'last_post_time', 'url'];

    /**
     * The fields of a topic.
     *
     * @since   Kunena 8.0
     */
    public const array TOPIC_FIELDS = [
        'id', 'category_id', 'subject', 'author_id', 'author', 'first_post_id', 'first_post_time', 'replies', 'hits', 'locked', 'sticky',
        'answer_id', 'last_post_id', 'last_post_time', 'url',
    ];

    /**
     * The fields of a post.
     *
     * @since   Kunena 8.0
     */
    public const array POST_FIELDS = ['id', 'topic_id', 'category_id', 'parent_id', 'subject', 'author_id', 'author', 'time', 'modified_time', 'message', 'text', 'url'];

    /**
     * The text of a message which the API gives: without the confidential, the private and the hidden parts (which the site only
     * shows to some users), and nothing when the text is hidden from the user (the replies of a category which hides them from the
     * guests).
     *
     * @param   string  $message  The text of the message (BBCode).
     * @param   bool    $hidden   The text is hidden from the user.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function safeMessage(string $message, bool $hidden): string
    {
        return $hidden ? '' : KunenaParser::removeBlocks($message, ['confidential', 'private', 'hide']);
    }

    /**
     * Whether the text of a message is hidden from the user: a reply (not the first message) in a category which hides the replies
     * from the guests, read by a guest.
     *
     * @param   bool  $isGuest           The user is a guest.
     * @param   bool  $isFirstMessage    The message is the first one of its topic.
     * @param   bool  $hidesForGuests    The category hides the replies from the guests.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function hidesText(bool $isGuest, bool $isFirstMessage, bool $hidesForGuests): bool
    {
        return $isGuest && !$isFirstMessage && $hidesForGuests;
    }

    /**
     * A date of the forum (a time of Unix) as the API gives it (ISO 8601, UTC), or null.
     *
     * @param   mixed  $time  The time.
     *
     * @return  string|null
     *
     * @since   Kunena 8.0
     */
    public static function date(mixed $time): ?string
    {
        $time = (int) $time;

        return $time > 0 ? gmdate('Y-m-d\TH:i:s\Z', $time) : null;
    }

    /**
     * A category.
     *
     * @param   KunenaCategory  $category  The category.
     *
     * @return  object
     *
     * @since   Kunena 8.0
     */
    public static function category(KunenaCategory $category): object
    {
        return (object) [
            'id'             => (int) $category->id,
            'parent_id'      => (int) $category->parentid,
            'name'           => (string) $category->name,
            'alias'          => (string) $category->alias,
            'description'    => self::safeMessage((string) $category->description, false),
            'topics'         => (int) $category->numTopics,
            'posts'          => (int) $category->numPosts,
            'last_post_id'   => (int) $category->last_post_id,
            'last_post_time' => self::date($category->last_post_time),
            'url'            => self::siteUrl('index.php?option=com_kunena&view=category&catid=' . (int) $category->id),
        ];
    }

    /**
     * A topic.
     *
     * @param   KunenaTopic  $topic  The topic.
     *
     * @return  object
     *
     * @since   Kunena 8.0
     */
    public static function topic(KunenaTopic $topic): object
    {
        return (object) [
            'id'              => (int) $topic->id,
            'category_id'     => (int) $topic->category_id,
            'subject'         => (string) $topic->subject,
            'author_id'       => (int) $topic->first_post_userid,
            'author'          => (string) $topic->first_post_guest_name ?: (string) $topic->getAuthor()->getName(),
            'first_post_id'   => (int) $topic->first_post_id,
            'first_post_time' => self::date($topic->first_post_time),
            'replies'         => max(0, (int) $topic->posts - 1),
            'hits'            => (int) $topic->hits,
            'locked'          => (bool) $topic->locked,
            'sticky'          => (bool) $topic->ordering,
            'answer_id'       => (int) $topic->answer_id,
            'last_post_id'    => (int) $topic->last_post_id,
            'last_post_time'  => self::date($topic->last_post_time),
            'url'             => self::siteUrl('index.php?option=com_kunena&view=topic&catid=' . (int) $topic->category_id . '&id=' . (int) $topic->id),
        ];
    }

    /**
     * A post, with its text as the user may read it.
     *
     * @param   KunenaMessage  $message  The message.
     * @param   KunenaUser     $user     The user who reads.
     *
     * @return  object
     *
     * @since   Kunena 8.0
     */
    public static function post(KunenaMessage $message, KunenaUser $user): object
    {
        $topic  = $message->getTopic();
        $hidden = self::hidesText(!$user->exists(), (int) $message->id === (int) $topic->first_post_id, $message->getCategory()->hidesRepliesForGuests());
        $text   = self::safeMessage((string) $message->message, $hidden);

        return (object) [
            'id'            => (int) $message->id,
            'topic_id'      => (int) $message->thread,
            'category_id'   => (int) $message->catid,
            'parent_id'     => (int) $message->parent,
            'subject'       => (string) $message->subject,
            'author_id'     => (int) $message->userid,
            'author'        => (string) $message->name ?: (string) $message->getAuthor()->getName(),
            'time'          => self::date($message->time),
            'modified_time' => self::date($message->modified_time),
            'message'       => $text,
            'text'          => $text !== '' ? trim(preg_replace('/\s+/', ' ', (string) KunenaParser::stripBBCode($text, 0, false))) : '',
            'url'           => self::siteUrl(
                'index.php?option=com_kunena&view=topic&catid=' . (int) $message->catid . '&id=' . (int) $message->thread . '&mesid=' . (int) $message->id
            ),
        ];
    }

    /**
     * The address of a page of the forum on the site (the API is another application).
     *
     * @param   string  $query  The address of Joomla (index.php?option=com_kunena&...).
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function siteUrl(string $query): string
    {
        try {
            return Route::link('site', $query, false, Route::TLS_IGNORE, true);
        } catch (Exception $e) {
            return rtrim(Uri::root(), '/') . '/' . $query;
        }
    }
}
