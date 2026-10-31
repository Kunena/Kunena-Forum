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
 * The check of a new account at stopforumspam.org.
 *
 * The check asks another site, and a registration waits for the answer. When that site is slow, down or says something that is no
 * answer, the check has to give up after a short time and let the registration through (it is a help against spam, not a gate that
 * closes the site): the plugin had no time limit and no handling of errors, so an unreachable service made every registration fail.
 *
 * @since   Kunena 7.1
 */
abstract class KunenaSpamCheck
{
    /**
     * Seconds to wait for the other site.
     */
    public const TIMEOUT = 5;

    public const ENDPOINT = 'https://api.stopforumspam.org/api?';

    /**
     * What is asked: the user name, the address of the mail and the IP address (not a local one).
     *
     * @param   array        $data  The data of the new user.
     * @param   string|null  $ip    The IP address of the visitor.
     *
     * @return  array
     *
     * @since   Kunena 7.1
     */
    public static function query(array $data, ?string $ip): array
    {
        $query = [
            'username' => (string) ($data['username'] ?? ''),
            'email'    => (string) ($data['email'] ?? ''),
        ];

        if ($ip !== null && $ip !== '' && $ip !== '::1' && $ip !== '127.0.0.1') {
            $query['ip'] = $ip;
        }

        return $query;
    }

    /**
     * Is the new user on the list?
     *
     * @param   callable      $fetch  (string $url, int $timeout): array{0: int, 1: string}  Status and body; it may throw.
     * @param   array         $data   The data of the new user.
     * @param   string|null   $ip     The IP address of the visitor.
     * @param   callable|null $log    (string $message): void, told why the check was skipped.
     *
     * @return  boolean  True only when the service answered and the user is on its list.
     *
     * @since   Kunena 7.1
     */
    public static function isListed(callable $fetch, array $data, ?string $ip, ?callable $log = null): bool
    {
        $query = self::query($data, $ip);

        // Nothing to ask about.
        if ($query['username'] === '' && $query['email'] === '' && !isset($query['ip'])) {
            return false;
        }

        $url = self::ENDPOINT . http_build_query($query) . '&json';

        try {
            [$status, $body] = $fetch($url, self::TIMEOUT);
        } catch (\Throwable $e) {
            self::skip($log, 'the service did not answer: ' . $e->getMessage());

            return false;
        }

        if ((int) $status !== 200) {
            self::skip($log, 'the service answered with the status ' . (int) $status);

            return false;
        }

        $result = json_decode((string) $body);

        if (!\is_object($result) || empty($result->success)) {
            self::skip($log, 'the answer of the service can not be used');

            return false;
        }

        foreach (['username', 'email', 'ip'] as $key) {
            if (!empty($result->$key->appears ?? false)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param   callable|null  $log      The log.
     * @param   string         $message  Why.
     *
     * @return  void
     *
     * @since   Kunena 7.1
     */
    private static function skip(?callable $log, string $message): void
    {
        if ($log !== null) {
            $log('The check of the new user at stopforumspam.org was skipped: ' . $message);
        }
    }
}
