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

use Normalizer;

\defined('_JEXEC') or die();

/**
 * The name which a guest gives to a post.
 *
 * The name of an account was refused as it was typed, so the same name with a zero width space, a direction mark, a
 * different kind of space or a compatibility form of a letter got through and the post looked like the post of the member.
 *
 * @since   Kunena 8.0
 */
abstract class KunenaGuestName
{
    /**
     * The most characters of the name (as the field of the form).
     *
     * @since   Kunena 8.0
     */
    public const MAX_LENGTH = 35;

    /**
     * The name as it is shown and compared with the names of the accounts.
     *
     * @param   string  $name  The name from the form.
     *
     * @return  string  Empty when nothing usable is left.
     *
     * @since   Kunena 8.0
     */
    public static function clean(string $name): string
    {
        if (!preg_match('//u', $name)) {
            return '';
        }

        if (class_exists('Normalizer')) {
            $name = Normalizer::normalize($name, Normalizer::FORM_KC) ?: $name;
        }

        // Every kind of space is one space. Control and format characters (zero width space and joiner, direction marks and
        // overrides, soft hyphen, BOM), private use and unassigned code points are removed.
        $name = preg_replace('/[\p{Zs}\p{Zl}\p{Zp}\t\r\n]+/u', ' ', $name) ?? '';
        $name = preg_replace('/[\p{Cc}\p{Cf}\p{Co}\p{Cn}\p{Cs}]/u', '', $name) ?? '';
        $name = trim(preg_replace('/ {2,}/', ' ', $name) ?? '');

        return mb_substr($name, 0, self::MAX_LENGTH, 'UTF-8');
    }
}
