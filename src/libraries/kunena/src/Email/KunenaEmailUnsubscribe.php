<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Email
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Email;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\User\KunenaUserFollow;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * Unsubscribe links for notification emails.
 *
 * A link is valid for one user and one subscription (a topic or a category) and carries a signature made with
 * the secret of the site, so nobody can unsubscribe somebody else and the recipient does not need to log in.
 *
 * @since   Kunena 8.0
 */
abstract class KunenaEmailUnsubscribe
{
    public const string TOPIC = 'topic';

    public const string CATEGORY = 'category';

    public const string FOLLOW = 'follow';

    /**
     * Absolute URL which unsubscribes at once when it is posted to (one-click unsubscribe of mail programs).
     *
     * @param   string  $type    Topic or category.
     * @param   int     $id      Id of the topic or category.
     * @param   int     $userid  Subscriber.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function getPostUrl(string $type, int $id, int $userid): string
    {
        return Uri::root() . 'index.php?option=com_kunena&view=topic&task=unsubscribemail&' . self::query($type, $id, $userid);
    }

    /**
     * Absolute URL of the page which asks to confirm the unsubscribe (opening it does not change anything).
     *
     * @param   string  $type    Topic or category.
     * @param   int     $id      Id of the topic or category.
     * @param   int     $userid  Subscriber.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function getUrl(string $type, int $id, int $userid): string
    {
        return Uri::root() . 'index.php?option=com_kunena&view=topic&layout=unsubscribe&' . self::query($type, $id, $userid);
    }

    /**
     * @param   string  $type    Topic or category.
     * @param   int     $id      Id of the topic or category.
     * @param   int     $userid  Subscriber.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    protected static function query(string $type, int $id, int $userid): string
    {
        return http_build_query([
            'type' => $type,
            'id'   => $id,
            'uid'  => $userid,
            'sig'  => self::sign($type, $id, $userid),
        ]);
    }

    /**
     * Signature of an unsubscribe link.
     *
     * @param   string  $type    Topic or category.
     * @param   int     $id      Id of the topic or category.
     * @param   int     $userid  Subscriber.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function sign(string $type, int $id, int $userid): string
    {
        $secret = (string) Factory::getApplication()->get('secret');

        return hash_hmac('sha256', "kunena-unsubscribe|{$type}|{$id}|{$userid}", $secret);
    }

    /**
     * Remove the subscription of the user.
     *
     * @param   string  $type    Topic or category.
     * @param   int     $id      Id of the topic or category.
     * @param   int     $userid  Subscriber.
     *
     * @return  boolean  True if the subscription was removed.
     *
     * @throws  Exception
     * @since   Kunena 8.0
     */
    public static function unsubscribe(string $type, int $id, int $userid): bool
    {
        $user = KunenaUserHelper::get($userid);

        if (!$user->exists()) {
            return false;
        }

        if ($type === self::TOPIC) {
            $topic = KunenaTopicHelper::get($id);

            return $topic->exists() && $topic->subscribe(0, $user);
        }

        if ($type === self::FOLLOW) {
            // The id is the user whom the subscriber follows.
            return KunenaUserFollow::unfollow($userid, $id);
        }

        $category = KunenaCategoryHelper::get($id);

        return $category->exists() && $category->subscribe(false, $user);
    }

    /**
     * Check the signature of an unsubscribe link.
     *
     * @param   string  $type       Topic or category.
     * @param   int     $id         Id of the topic or category.
     * @param   int     $userid     Subscriber.
     * @param   string  $signature  Signature from the link.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function verify(string $type, int $id, int $userid, string $signature): bool
    {
        if (!\in_array($type, [self::TOPIC, self::CATEGORY, self::FOLLOW], true) || $id < 1 || $userid < 1 || $signature === '') {
            return false;
        }

        return hash_equals(self::sign($type, $id, $userid), $signature);
    }
}
