<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Config
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Config;

\defined('_JEXEC') or die();

/**
 * Which settings stay out of the system report.
 *
 * The report is made to be pasted into a support forum. The settings that must not be in it were a fixed list of names, so the
 * eBay Cert ID (the client secret of eBay) was in every report. Now the name decides as well: anything that looks like a key, a
 * secret, a password, a token or a certificate id stays out, whatever it is called in a later version.
 *
 * @since   Kunena 7.1
 */
abstract class KunenaConfigReport
{
    /**
     * Settings that are private or personal and are not secrets by their name.
     */
    private const HIDDEN = [
        'id', 'boardTitle', 'email', 'offlineMessage', 'emailVisibleAddress', 'ebayAffiliateId', 'blueskyappHandleOfApp',
    ];

    /**
     * @param   string  $name  The name of the setting.
     *
     * @return  boolean  True when it must not be in the report.
     *
     * @since   Kunena 7.1
     */
    public static function isHidden(string $name): bool
    {
        if (\in_array($name, self::HIDDEN, true)) {
            return true;
        }

        return preg_match('/(key|secret|pass(word|wd)?|token|certid|credential|salt)/i', $name) === 1;
    }
}
