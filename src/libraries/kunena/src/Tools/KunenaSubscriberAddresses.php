<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Tools
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Tools;

\defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Mail\MailHelper;

/**
 * The subscribers whose address cannot get the e-mails (tool "Subscriber address check"): a wrong address, a typing error
 * in the name of a big provider (gmial.com), a domain without a mail server. Each e-mail to them fails, and many failures
 * make the providers take the mail of the site for spam. Their subscriptions are paused.
 *
 * @since   Kunena 8.0
 */
class KunenaSubscriberAddresses
{
    /**
     * The address is no address.
     *
     * @since   Kunena 8.0
     */
    public const SYNTAX = 'syntax';

    /**
     * A typing error in the name of a provider.
     *
     * @since   Kunena 8.0
     */
    public const TYPO = 'typo';

    /**
     * The domain has no mail server.
     *
     * @since   Kunena 8.0
     */
    public const NO_MAIL = 'nomail';

    /**
     * At most this many domains are looked up in one run (each asks the DNS).
     *
     * @since   Kunena 8.0
     */
    public const DOMAIN_LIMIT = 200;

    /**
     * Typing errors in the names of big providers: wrong => right.
     *
     * @since   Kunena 8.0
     */
    public const TYPOS = [
        'gmial.com'   => 'gmail.com', 'gmal.com' => 'gmail.com', 'gmaill.com' => 'gmail.com', 'gamil.com' => 'gmail.com',
        'gnail.com'   => 'gmail.com', 'gmail.co' => 'gmail.com', 'gmali.com' => 'gmail.com', 'gmai.com' => 'gmail.com',
        'hotmial.com' => 'hotmail.com', 'hotmal.com' => 'hotmail.com', 'homail.com' => 'hotmail.com', 'hotmai.com' => 'hotmail.com',
        'hotmail.co'  => 'hotmail.com', 'hotamil.com' => 'hotmail.com', 'yaho.com' => 'yahoo.com', 'yahooo.com' => 'yahoo.com',
        'yhoo.com'    => 'yahoo.com', 'yahoo.co' => 'yahoo.com', 'outlok.com' => 'outlook.com', 'outllok.com' => 'outlook.com',
        'outlook.co'  => 'outlook.com', 'iclod.com' => 'icloud.com', 'icoud.com' => 'icloud.com', 'icloud.co' => 'icloud.com',
    ];

    /**
     * The ends of a domain which are typing errors of ".com".
     *
     * @since   Kunena 8.0
     */
    public const TLD_TYPOS = ['.con', '.cmo', '.ocm', '.comm', '.vom', '.xom'];

    /**
     * The domain which the address probably meant, when its domain is a known typing error.
     *
     * @param   string  $domain  The domain.
     *
     * @return  string|null
     *
     * @since   Kunena 8.0
     */
    public static function typo(string $domain): ?string
    {
        $domain = strtolower($domain);

        if (isset(self::TYPOS[$domain])) {
            return self::TYPOS[$domain];
        }

        foreach (self::TLD_TYPOS as $end) {
            if (str_ends_with($domain, $end)) {
                return substr($domain, 0, -\strlen($end)) . '.com';
            }
        }

        return null;
    }

    /**
     * What is wrong with an address.
     *
     * @param   string    $email    The address.
     * @param   callable  $hasMail  Can the domain get mail? (the DNS; null: not looked up).
     *
     * @return  array{0: string, 1: string}|null  [problem, hint]; null when nothing is wrong (or the domain is not looked up).
     *
     * @since   Kunena 8.0
     */
    public static function problem(string $email, callable $hasMail): ?array
    {
        $email = trim($email);

        if (!MailHelper::isEmailAddress($email)) {
            return [self::SYNTAX, ''];
        }

        $domain = strtolower(substr($email, strrpos($email, '@') + 1));
        $right  = self::typo($domain);

        if ($right !== null) {
            return [self::TYPO, $right];
        }

        return $hasMail($domain) === false ? [self::NO_MAIL, $domain] : null;
    }

    /**
     * The subscribers of this site whose address has a problem.
     *
     * @return  array{subscribers: int, checked: int, items: array}  checked: the domains looked up.
     *
     * @since   Kunena 8.0
     */
    public static function findOnSite(): array
    {
        $db     = Factory::getContainer()->get('DatabaseDriver');
        $topics = $db->createQuery()->select($db->quoteName('user_id'))->from($db->quoteName('#__kunena_user_topics'))->where($db->quoteName('subscribed') . ' = 1');
        $cats   = $db->createQuery()->select($db->quoteName('user_id'))->from($db->quoteName('#__kunena_user_categories'))->where($db->quoteName('subscribed') . ' = 1');
        $query  = $db->createQuery()
            ->select($db->quoteName(['u.id', 'u.username', 'u.email', 'u.block']))
            ->from($db->quoteName('#__users', 'u'))
            ->where($db->quoteName('u.id') . ' IN (' . $topics . ' UNION ' . $cats . ')')
            ->order($db->quoteName('u.id') . ' DESC');
        $rows    = (array) $db->setQuery($query)->loadAssocList();
        $domains = [];
        $items   = [];

        // A domain is looked up once; after the limit, the others are not looked up in this run.
        $hasMail = static function (string $domain) use (&$domains): ?bool {
            if (!\array_key_exists($domain, $domains)) {
                if (\count($domains) >= self::DOMAIN_LIMIT) {
                    return null;
                }

                $domains[$domain] = checkdnsrr($domain, 'MX') || checkdnsrr($domain, 'A') || checkdnsrr($domain, 'AAAA');
            }

            return $domains[$domain];
        };

        foreach ($rows as $row) {
            $problem = self::problem((string) $row['email'], $hasMail);

            if ($problem !== null) {
                $items[] = $row + ['problem' => $problem[0], 'hint' => $problem[1]];
            }
        }

        return ['subscribers' => \count($rows), 'checked' => \count($domains), 'items' => $items];
    }

    /**
     * Pauses all the subscriptions of the chosen users (categories and topics).
     *
     * @param   int[]  $ids  The users.
     *
     * @return  integer  The subscriptions paused.
     *
     * @since   Kunena 8.0
     */
    public static function pauseOnSite(array $ids): int
    {
        $paused = 0;

        foreach (array_values(array_unique(array_filter(array_map('intval', $ids)))) as $id) {
            $paused += KunenaSubscriptions::unsubscribeOnSite('user', $id);
        }

        return $paused;
    }
}
