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
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Html\KunenaLinkPreview;

/**
 * The links of the posts which lead nowhere any more (tool "Broken links in posts"). The links are checked a few at a time,
 * with the protections of the link cards: only http(s) addresses whose name points to public addresses, read at the
 * address which was checked (no request into the network of the server). The answers are kept for a week.
 *
 * @since   Kunena 8.0
 */
abstract class KunenaBrokenLinks extends KunenaLinkPreview
{
    /**
     * The newest posts whose links are read.
     *
     * @since   Kunena 8.0
     */
    public const SCAN_LIMIT = 500;

    /**
     * The links checked in one run (each waits at most a few seconds for its site).
     *
     * @since   Kunena 8.0
     */
    public const BATCH = 10;

    /**
     * An answer is kept this long (seconds).
     *
     * @since   Kunena 8.0
     */
    public const KEEP = 7 * 86400;

    /**
     * The link works (an answer 2xx or a redirect).
     *
     * @since   Kunena 8.0
     */
    public const OK = 'ok';

    /**
     * The link is gone: 404, 410, or no such name.
     *
     * @since   Kunena 8.0
     */
    public const BROKEN = 'broken';

    /**
     * The site refused, had an error or did not answer: maybe for a while only.
     *
     * @since   Kunena 8.0
     */
    public const UNSURE = 'unsure';

    /**
     * Not read: not a web address, or an address of a private network.
     *
     * @since   Kunena 8.0
     */
    public const SKIPPED = 'skipped';

    /**
     * No such name: the site is gone.
     *
     * @since   Kunena 8.0
     */
    public const NO_HOST = -2;

    /**
     * Not read: not a web address, or an address of a private network.
     *
     * @since   Kunena 8.0
     */
    public const NOT_READ = -1;

    /**
     * The http(s) links of a post: BBCode, Markdown, HTML and plain addresses, each once.
     *
     * @param   string  $text  The post.
     *
     * @return  string[]
     *
     * @since   Kunena 8.0
     */
    public static function extract(string $text): array
    {
        preg_match_all('#https?://[^\s\[\]<>"\'()]+#i', $text, $matches);

        $links = [];

        foreach ($matches[0] as $url) {
            // The end of a sentence is no part of the address.
            $url = rtrim($url, '.,;:!?');

            if (\strlen($url) <= 2000) {
                $links[$url] = $url;
            }
        }

        return array_values($links);
    }

    /**
     * What an answer says about a link.
     *
     * @param   int  $code  The status of the answer, NO_HOST, NOT_READ, or 0 for no answer.
     *
     * @return  string  OK, BROKEN (gone: 404, 410, no such name), UNSURE (refused, an error of the server, no answer), SKIPPED.
     *
     * @since   Kunena 8.0
     */
    public static function verdict(int $code): string
    {
        if ($code === self::NOT_READ) {
            return self::SKIPPED;
        }

        if ($code === self::NO_HOST || $code === 404 || $code === 410) {
            return self::BROKEN;
        }

        return $code >= 200 && $code < 400 ? self::OK : self::UNSURE;
    }

    /**
     * The status of the answer to a link, read with the protections of the link cards.
     *
     * @param   string  $url      The link.
     * @param   int     $timeout  Seconds to wait.
     *
     * @return  integer  The status, NO_HOST, NOT_READ, or 0 for no answer.
     *
     * @since   Kunena 8.0
     */
    public static function status(string $url, int $timeout = 5): int
    {
        $parts = parse_url($url);

        if (!static::isWebAddress($parts)) {
            return self::NOT_READ;
        }

        $host = trim($parts['host'], '[]');

        if (filter_var($host, FILTER_VALIDATE_IP)) {
            $ips = [$host];
        } else {
            $ips = gethostbynamel($host) ?: [];

            foreach (@dns_get_record($host, DNS_AAAA) ?: [] as $record) {
                if (!empty($record['ipv6'])) {
                    $ips[] = $record['ipv6'];
                }
            }
        }

        if (!$ips) {
            return self::NO_HOST;
        }

        // Every address of the name must be public: no request into the network of the server.
        foreach ($ips as $ip) {
            if (!static::isPublicAddress($ip)) {
                return self::NOT_READ;
            }
        }

        [$target, $hostHeader] = static::pinned($parts, $ips[0]);

        // HEAD first; a site which does not answer it is asked with GET (only the start of the page is read).
        foreach (['HEAD', 'GET'] as $method) {
            $code = self::request($target, $hostHeader, $host, $method, $timeout);

            if ($code !== 405 && $code !== 501 && $code !== 0) {
                return $code;
            }
        }

        return $code;
    }

    /**
     * The links of the newest posts of this site with the answers kept so far.
     *
     * @return  array{posts: int, links: array<string, array{posts: array, code: int|null, checked: int, verdict: string|null}>}
     *
     * @since   Kunena 8.0
     */
    public static function findOnSite(): array
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select($db->quoteName(['m.id', 'm.thread', 'm.catid', 'm.subject', 't.message']))
            ->from($db->quoteName('#__kunena_messages', 'm'))
            ->innerJoin($db->quoteName('#__kunena_messages_text', 't') . ' ON ' . $db->quoteName('t.mesid') . ' = ' . $db->quoteName('m.id'))
            ->where($db->quoteName('m.hold') . ' = ' . KunenaForum::PUBLISHED)
            ->order($db->quoteName('m.id') . ' DESC');
        $rows  = (array) $db->setQuery($query, 0, self::SCAN_LIMIT)->loadAssocList();
        $own   = strtolower((string) parse_url(Uri::root(), PHP_URL_HOST));
        $kept  = self::readResults();
        $links = [];

        foreach ($rows as $row) {
            foreach (self::extract((string) $row['message']) as $url) {
                // The links of the site itself are not checked: they are the forum.
                if (strtolower((string) parse_url($url, PHP_URL_HOST)) === $own) {
                    continue;
                }

                if (!isset($links[$url])) {
                    $result      = $kept[$url] ?? null;
                    $links[$url] = [
                        'posts'   => [],
                        'code'    => $result ? (int) $result['code'] : null,
                        'checked' => $result ? (int) $result['checked'] : 0,
                        'verdict' => $result ? self::verdict((int) $result['code']) : null,
                    ];
                }

                if (\count($links[$url]['posts']) < 3) {
                    $links[$url]['posts'][] = ['id' => (int) $row['id'], 'thread' => (int) $row['thread'], 'catid' => (int) $row['catid'], 'subject' => (string) $row['subject']];
                }
            }
        }

        return ['posts' => \count($rows), 'links' => $links];
    }

    /**
     * Checks the next links which have no answer (or an answer older than a week).
     *
     * @param   int  $now  The current time.
     *
     * @return  array{checked: int, broken: int, left: int}
     *
     * @since   Kunena 8.0
     */
    public static function checkNextOnSite(int $now): array
    {
        $found = self::findOnSite();
        $kept  = self::readResults();
        $due   = array_keys(array_filter($found['links'], static fn ($link) => $link['checked'] < $now - self::KEEP));
        $batch = \array_slice($due, 0, self::BATCH);

        // A long check must not stop at the time limit of PHP.
        @set_time_limit(max(60, \count($batch) * 12));

        $broken = 0;

        foreach ($batch as $url) {
            $code       = self::status($url);
            $kept[$url] = ['code' => $code, 'checked' => $now];
            $broken += self::verdict($code) === self::BROKEN ? 1 : 0;
        }

        // The answers of links which are in no post any more are forgotten.
        self::writeResults(array_intersect_key($kept, $found['links']));

        return ['checked' => \count($batch), 'broken' => $broken, 'left' => \count($due) - \count($batch)];
    }

    /**
     * Forgets all the answers: every link is checked again.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public static function resetOnSite(): void
    {
        @unlink(self::file());
    }

    /**
     * Sends a request and reads the status of the answer.
     *
     * @param   string  $target      The address with the checked IP.
     * @param   string  $hostHeader  The name of the site.
     * @param   string  $host        The name for the certificate.
     * @param   string  $method      HEAD or GET.
     * @param   int     $timeout     Seconds.
     *
     * @return  integer  The status, 0 without an answer.
     *
     * @since   Kunena 8.0
     */
    private static function request(string $target, string $hostHeader, string $host, string $method, int $timeout): int
    {
        $agent   = 'Mozilla/5.0 (compatible; KunenaLinkCheck/8.0; +' . Uri::root() . ')';
        $context = stream_context_create([
            'http' => ['method' => $method, 'timeout' => $timeout, 'follow_location' => 0, 'ignore_errors' => true,
                'header'        => "Host: $hostHeader\r\nUser-Agent: $agent\r\nAccept: */*\r\n"],
            'ssl' => ['peer_name' => $host, 'SNI_enabled' => true, 'verify_peer' => true, 'verify_peer_name' => true],
        ]);
        // The address of the DNS was checked as public before, and only it is read (as KunenaLinkPreview::fetchPage()).
        @file_get_contents($target, false, $context, 0, 1);

        foreach (http_get_last_response_headers() ?? [] as $header) {
            if (preg_match('#^HTTP/\S+\s+(\d{3})#', $header, $m)) {
                return (int) $m[1];
            }
        }

        return 0;
    }

    /**
     * The file of the answers, in the temporary folder of Joomla.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    private static function file(): string
    {
        return rtrim((string) Factory::getApplication()->get('tmp_path', JPATH_ROOT . '/tmp'), '/\\') . '/kunena-link-check.json';
    }

    /**
     * The answers kept: url => [code, checked].
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    private static function readResults(): array
    {
        $data = is_file(self::file()) ? json_decode((string) file_get_contents(self::file()), true) : null;

        return \is_array($data) ? $data : [];
    }

    /**
     * Keeps the answers.
     *
     * @param   array  $results  url => [code, checked].
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    private static function writeResults(array $results): void
    {
        @file_put_contents(self::file(), json_encode($results, JSON_UNESCAPED_SLASHES), LOCK_EX);
    }
}
