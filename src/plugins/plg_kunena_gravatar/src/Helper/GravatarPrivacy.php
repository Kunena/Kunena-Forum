<?php

/**
 * Kunena Plugin
 *
 * @package         Kunena.Plugins
 * @subpackage      Gravatar
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Plugin\Kunena\Gravatar\Helper;

\defined('_JEXEC') or die();

/**
 * A Gravatar address contains the MD5 of the email address of the member, in the html of every page that shows the avatar.
 * That hash is not secret: it is checked against guessed or leaked addresses, and Gravatar publishes the profile that
 * belongs to it. A member who hides the email address must not have it in the page in this form either.
 *
 * @since   Kunena 7.1
 */
abstract class GravatarPrivacy
{
    /**
     * The email address which may be used for the Gravatar address of a member, or an empty string: then the default image
     * is shown, without anything of the address in it.
     *
     * The setting "hide email" of the member is 0 (show), 1 (hide) or 2 (only for registered users). An avatar is shown to
     * everybody, guests included, and the page can be cached for others, so only 0 (or never set) gives the address.
     *
     * @param   mixed  $email      The email address of the member.
     * @param   mixed  $hideEmail  The setting of the member.
     *
     * @return  string
     *
     * @since   Kunena 7.1
     */
    public static function emailForHash($email, $hideEmail): string
    {
        $email = trim((string) $email);

        if ($email === '') {
            return '';
        }

        // Never set (null or ''), or explicitly "show".
        if ($hideEmail === null || $hideEmail === '' || (is_numeric($hideEmail) && (int) $hideEmail === 0)) {
            return $email;
        }

        return '';
    }
}
