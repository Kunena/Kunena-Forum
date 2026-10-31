<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      KunenaPrivate
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\KunenaPrivate;

\defined('_JEXEC') or die();

/**
 * Puts the private parts (private messages) on the messages of a page.
 *
 * A private message lists the posts that it belongs to. The view of a topic walked over that list to create the place for each post,
 * and then put the private message in the place of the *last* post of the list only, outside the loop; a post of the list that is
 * not on the page (the list is of the whole topic, the page shows some posts) was written to as if it was there, which is an error.
 *
 * @since   Kunena 7.1
 */
abstract class KunenaPrivatePartsMap
{
    /**
     * @param   array        $messages  The messages of the page, by id (objects); changed.
     * @param   object       $pm        The private message (with an id).
     * @param   mixed        $posts     The ids of the posts of the private message (receivers.posts); anything else is no list.
     *
     * @return  integer  The number of posts of the page that got the private message.
     *
     * @since   Kunena 7.1
     */
    public static function attach(array &$messages, object $pm, $posts): int
    {
        if (!\is_array($posts) && !$posts instanceof \Traversable && !\is_object($posts)) {
            return 0;
        }

        $count = 0;

        foreach ($posts as $post) {
            if (!\is_scalar($post) || !isset($messages[$post]) || !\is_object($messages[$post])) {
                // The post is on another page (or it is gone): nothing to put it on.
                continue;
            }

            if (!isset($messages[$post]->pm) || !\is_array($messages[$post]->pm)) {
                $messages[$post]->pm = [];
            }

            $messages[$post]->pm[$pm->id] = $pm;
            $count++;
        }

        return $count;
    }
}
