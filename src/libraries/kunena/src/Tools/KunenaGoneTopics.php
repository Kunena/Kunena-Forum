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

use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Database\ParameterType;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicRedirect;

/**
 * The addresses of the deleted topics (tool "Deleted topic addresses"): Kunena answers them with 410 Gone, so that the
 * search engines forget them (KunenaTopicRedirect, in the redirects of Joomla). An address is freed, or sent to another
 * page with a redirect (301, by the plugin Redirect of Joomla).
 *
 * @since   Kunena 8.0
 */
class KunenaGoneTopics
{
    /**
     * At most this many addresses, the newest first.
     *
     * @since   Kunena 8.0
     */
    public const LIST_LIMIT = 500;

    /**
     * Is an address a place to send a visitor to: a web address or a path of the site, nothing which runs a script or
     * leaves the site through "//".
     *
     * @param   string  $url  The address.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function validTarget(string $url): bool
    {
        if ($url === '' || \strlen($url) > 2048 || preg_match('/[\x00-\x20]/', $url)) {
            return false;
        }

        if (str_starts_with($url, '/')) {
            return !str_starts_with($url, '//') && !str_starts_with($url, '/\\');
        }

        $parts = parse_url($url);

        return $parts !== false && \in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true) && !empty($parts['host']);
    }

    /**
     * The addresses of the deleted topics of this site, with whether Kunena makes them and the plugin Redirect is on.
     *
     * @return  array{enabled: bool, plugin: bool, items: array}
     *
     * @since   Kunena 8.0
     */
    public static function listOnSite(): array
    {
        $db      = Factory::getContainer()->get('DatabaseDriver');
        $comment = KunenaTopicRedirect::COMMENT;
        $query   = $db->createQuery()
            ->select($db->quoteName(['id', 'old_url', 'new_url', 'header', 'hits', 'published', 'created_date']))
            ->from($db->quoteName('#__redirect_links'))
            ->where($db->quoteName('comment') . ' = :comment')
            ->bind(':comment', $comment)
            ->order($db->quoteName('created_date') . ' DESC');

        return [
            'enabled' => KunenaTopicRedirect::isEnabled(),
            'plugin'  => PluginHelper::isEnabled('system', 'redirect'),
            'items'   => (array) $db->setQuery($query, 0, self::LIST_LIMIT)->loadAssocList(),
        ];
    }

    /**
     * Frees the chosen addresses: they answer as any other address of the site again.
     *
     * @param   int[]  $ids  The redirects.
     *
     * @return  integer  The addresses freed.
     *
     * @since   Kunena 8.0
     */
    public static function removeOnSite(array $ids): int
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));

        if (!$ids) {
            return 0;
        }

        $db      = Factory::getContainer()->get('DatabaseDriver');
        $comment = KunenaTopicRedirect::COMMENT;
        $query   = $db->createQuery()
            ->delete($db->quoteName('#__redirect_links'))
            ->whereIn($db->quoteName('id'), $ids)
            // Only the redirects which Kunena made.
            ->where($db->quoteName('comment') . ' = :comment')
            ->bind(':comment', $comment);
        $db->setQuery($query)->execute();

        return $db->getAffectedRows();
    }

    /**
     * Sends the chosen addresses to another page with a redirect 301.
     *
     * @param   int[]   $ids     The redirects.
     * @param   string  $target  The address to send to (validTarget()).
     *
     * @return  integer  The addresses changed; -1 when the address is not valid.
     *
     * @since   Kunena 8.0
     */
    public static function redirectOnSite(array $ids, string $target): int
    {
        $target = trim($target);
        $ids    = array_values(array_unique(array_filter(array_map('intval', $ids))));

        if (!self::validTarget($target)) {
            return -1;
        }

        if (!$ids) {
            return 0;
        }

        $db      = Factory::getContainer()->get('DatabaseDriver');
        $comment = KunenaTopicRedirect::COMMENT;
        $header  = 301;
        $now     = (new Date())->toSql();
        $query   = $db->createQuery()
            ->update($db->quoteName('#__redirect_links'))
            ->set($db->quoteName('new_url') . ' = :target')
            ->set($db->quoteName('header') . ' = :header')
            ->set($db->quoteName('published') . ' = 1')
            ->set($db->quoteName('modified_date') . ' = :now')
            ->whereIn($db->quoteName('id'), $ids)
            ->where($db->quoteName('comment') . ' = :comment')
            ->bind(':target', $target)
            ->bind(':header', $header, ParameterType::INTEGER)
            ->bind(':now', $now)
            ->bind(':comment', $comment);
        $db->setQuery($query)->execute();

        return $db->getAffectedRows();
    }
}
