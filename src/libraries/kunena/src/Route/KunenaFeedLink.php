<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Route
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Route;

\defined('_JEXEC') or die();

use Kunena\Forum\Libraries\Config\KunenaConfig;

/**
 * The links to the feeds of the forum: the format of the option "RSS Specification" and the address of the option "FeedBurner".
 * Both options were in the configuration and nothing read them: every link was an RSS one of the forum itself.
 *
 * @since   Kunena 8.0
 */
abstract class KunenaFeedLink
{
    /**
     * The type of the feed for the address (type=...): atom for the specification Atom 1.0, rss for the RSS ones (Joomla writes
     * RSS 2.0 for all of them).
     *
     * @param   string|null  $specification  The option "RSS Specification"; null reads the configuration.
     *
     * @return  string  rss or atom.
     *
     * @since   Kunena 8.0
     */
    public static function type(?string $specification = null): string
    {
        $specification ??= (string) (KunenaConfig::getInstance()->rssSpecification ?? '');

        return str_starts_with(strtolower(trim($specification)), 'atom') ? 'atom' : 'rss';
    }

    /**
     * The media type of the feed, for the link in the head of the page and the type of the link.
     *
     * @param   string|null  $specification  The option "RSS Specification"; null reads the configuration.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function mime(?string $specification = null): string
    {
        return self::type($specification) === 'atom' ? 'application/atom+xml' : 'application/rss+xml';
    }

    /**
     * The address of the feed of the forum at FeedBurner (the option "FeedBurner"), when it is a web address.
     *
     * @param   string|null  $url  The option; null reads the configuration.
     *
     * @return  string|null  Null when the forum serves its feed itself.
     *
     * @since   Kunena 8.0
     */
    public static function burner(?string $url = null): ?string
    {
        $url = trim($url ?? (string) (KunenaConfig::getInstance()->rssFeedBurnerUrl ?? ''));

        if ($url === '' || !preg_match('#^https?://[^\s<>"\']+$#Di', $url)) {
            return null;
        }

        return $url;
    }
}
