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

/**
 * The name a guest may sign a message with.
 *
 * A guest types the name, and the name is what the readers see (and what the search by user matches). Only the login name was
 * refused, so a guest was able to sign as the name which a member is shown with (the "name" of the account, which is what the
 * forum prints), with a different case or spacing, or with characters which can not be seen (zero width space, direction
 * marks ...): a message "from the administrator" written by anybody.
 *
 * @since   Kunena 7.1
 */
abstract class KunenaGuestName
{
    public const MAX_LENGTH = 35;

    /**
     * The name as it is compared and shown: no control and invisible characters, one space between the words, composed in the
     * normal form (so a lookalike made of combining characters is the same text), not longer than the form allows.
     *
     * @param   string  $name  What the guest typed.
     *
     * @return  string
     *
     * @since   Kunena 7.1
     */
    public static function clean(string $name): string
    {
        if (!preg_match('//u', $name)) {
            return '';
        }

        if (class_exists('Normalizer')) {
            $name = \Normalizer::normalize($name, \Normalizer::FORM_KC) ?: $name;
        }

        // Control characters, format characters (zero width space and joiner, direction marks and overrides, soft hyphen, BOM),
        // private use and unassigned code points are removed; every kind of space is one space.
        $name = preg_replace('/[\p{Zs}\p{Zl}\p{Zp}\t\r\n]+/u', ' ', $name) ?? '';
        $name = preg_replace('/[\p{Cc}\p{Cf}\p{Co}\p{Cn}\p{Cs}]/u', '', $name) ?? '';
        $name = trim(preg_replace('/ {2,}/', ' ', $name) ?? '');

        return mb_substr($name, 0, self::MAX_LENGTH, 'UTF-8');
    }

    /**
     * The name to store for a guest.
     *
     * @param   string    $name       What the guest typed.
     * @param   callable  $isTaken    Is the (cleaned) name the login name or the name of an account? (string): bool
     * @param   string    $anonymous  The name used when the typed one is not allowed.
     *
     * @return  string
     *
     * @since   Kunena 7.1
     */
    public static function resolve(string $name, callable $isTaken, string $anonymous): string
    {
        $name = self::clean($name);

        if ($name === '' || $isTaken($name)) {
            return $anonymous;
        }

        return $name;
    }
}
