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
 * The IP address that is stored with a message.
 *
 * Joomla takes the address from the header X-Forwarded-For when the site is set to be behind a load balancer, and a header is text
 * from the visitor: if it is not an address, Joomla keeps it as it is. The text was stored in the column "ip" of the messages, and
 * the page of a user in the administrator put the stored values into a query without quotes: whoever wrote a header such as
 * x'OR'1'='1 changed the query for the administrator who opened that page.
 *
 * @since   Kunena 7.1
 */
abstract class KunenaIpAddress
{
    /**
     * An address, or nothing. A value that is not an IP address is replaced by the address of the connection (when that is one),
     * else by an empty text.
     *
     * @param   mixed   $detected    What the framework found (it may be text of the visitor).
     * @param   mixed   $connection  The address of the connection (REMOTE_ADDR).
     *
     * @return  string
     *
     * @since   Kunena 7.1
     */
    public static function clean($detected, $connection = ''): string
    {
        foreach ([$detected, $connection] as $candidate) {
            if (!\is_string($candidate)) {
                continue;
            }

            $candidate = trim($candidate);

            if ($candidate !== '' && filter_var($candidate, FILTER_VALIDATE_IP) !== false) {
                // The same text for the same address (inet_ntop gives the short form of an IPv6 address).
                $packed = @inet_pton($candidate);

                return $packed === false ? $candidate : (string) inet_ntop($packed);
            }
        }

        return '';
    }

    /**
     * The html of the stored address of a message, for a moderator. A message that was written before the address was checked
     * can hold the text of a header: the value is always encoded, and only a real address becomes a link to the lookup site.
     *
     * @param   mixed  $ip  The stored value.
     *
     * @return  string
     *
     * @since   Kunena 7.1
     */
    public static function lookupLink($ip): string
    {
        $ip = \is_string($ip) ? trim($ip) : '';

        if ($ip === '') {
            return '&nbsp;';
        }

        $text = htmlspecialchars($ip, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
            return ' IP: ' . $text;
        }

        return '<a href="https://search.dnslytics.com/ip/' . rawurlencode($ip)
            . '" target="_blank" rel="nofollow noopener noreferrer"> IP: ' . $text . '</a>';
    }
}
