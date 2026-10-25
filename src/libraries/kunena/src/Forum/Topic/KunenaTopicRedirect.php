<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Forum.Topic
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Forum\Topic;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;
use Kunena\Forum\Libraries\Config\KunenaConfig;

/**
 * Makes an entry in the redirects of Joomla (com_redirect) with the status 410 Gone for a topic which is deleted, so the
 * search engines know the topic is gone and do not report a 404 error.
 *
 * The pages of the forum answer with the 410 themselves (isGone): Kunena shows its own error pages, so the redirect plugin of
 * Joomla (System - Redirect) never gets the error. The entry is also there for the plugin, which uses it for addresses outside
 * the forum pages when its advanced mode is on.
 *
 * @since   Kunena 8.0
 */
abstract class KunenaTopicRedirect
{
    /**
     * The comment of the entries: it shows where they came from and makes them recognisable when a topic is restored.
     *
     * @since   Kunena 8.0
     */
    public const string COMMENT = 'Kunena: deleted topic';

    /**
     * Make the redirect with the status 410 Gone for the topic.
     *
     * @param   string  $url  The address of the topic (see getOldUrl, get it before the topic is deleted).
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public static function add(string $url): void
    {
        if ($url === '' || !self::isEnabled()) {
            return;
        }

        try {
            $db      = Factory::getContainer()->get(DatabaseInterface::class);
            $now     = (new Date())->toSql();
            $header  = 410;
            $comment = self::COMMENT;
            $empty   = '';

            $query = $db->createQuery()
                ->select($db->quoteName('id'))
                ->from($db->quoteName('#__redirect_links'))
                ->where($db->quoteName('old_url') . ' = :url')
                ->bind(':url', $url);
            $db->setQuery($query);
            $id = (int) $db->loadResult();

            if ($id) {
                $query = $db->createQuery()
                    ->update($db->quoteName('#__redirect_links'))
                    ->set($db->quoteName('new_url') . ' = ' . $db->quote(''))
                    ->set($db->quoteName('published') . ' = 1')
                    ->set($db->quoteName('header') . ' = :header')
                    ->set($db->quoteName('comment') . ' = :comment')
                    ->set($db->quoteName('modified_date') . ' = :modified')
                    ->where($db->quoteName('id') . ' = :id')
                    ->bind(':header', $header, ParameterType::INTEGER)
                    ->bind(':comment', $comment)
                    ->bind(':modified', $now)
                    ->bind(':id', $id, ParameterType::INTEGER);
            } else {
                $query = $db->createQuery()
                    ->insert($db->quoteName('#__redirect_links'))
                    ->columns($db->quoteName(['old_url', 'new_url', 'referer', 'comment', 'hits', 'published', 'created_date', 'modified_date', 'header']))
                    ->values(':url, :newurl, :referer, :comment, 0, 1, :created, :modified, :header')
                    ->bind(':url', $url)
                    ->bind(':newurl', $empty)
                    ->bind(':referer', $empty)
                    ->bind(':comment', $comment)
                    ->bind(':created', $now)
                    ->bind(':modified', $now)
                    ->bind(':header', $header, ParameterType::INTEGER);
            }

            $db->setQuery($query)->execute();
        } catch (Exception $e) {
            // The redirect is a help for search engines: a failure must never stop the deletion of a topic.
        }
    }

    /**
     * The address of the topic as visitors and search engines know it.
     *
     * @param   KunenaTopic  $topic  The topic.
     *
     * @return  string  The address, or an empty string when there is none.
     *
     * @since   Kunena 8.0
     */
    public static function getOldUrl(KunenaTopic $topic): string
    {
        try {
            $url = (string) $topic->getUrl(null, false);
        } catch (Exception $e) {
            return '';
        }

        // The address is saved without the fragment and as a full address, like the redirect plugin finds them.
        $url = preg_replace('/#.*$/', '', $url);

        if ($url !== '' && !preg_match('#^https?://#i', $url)) {
            $uri = Uri::getInstance();
            $url = $uri->toString(['scheme', 'host', 'port']) . '/' . ltrim($url, '/');
        }

        return $url;
    }

    /**
     * Is the option on.
     *
     * @return  bool
     *
     * @since   Kunena 8.0
     */
    public static function isEnabled(): bool
    {
        $config = KunenaConfig::getInstance();

        return isset($config->redirectDeletedTopics) ? (bool) $config->redirectDeletedTopics : false;
    }

    /**
     * Is the address of the page a topic which is gone (an entry with the status 410 in the redirects of Joomla).
     *
     * Kunena shows its own error page, so the redirect plugin of Joomla does not get the error: Kunena looks for
     * the entry itself and answers with 410 Gone.
     *
     * @return  bool
     *
     * @since   Kunena 8.0
     */
    public static function isGone(): bool
    {
        static $gone = null;

        if ($gone !== null) {
            return $gone;
        }

        $gone = false;

        if (!self::isEnabled()) {
            return $gone;
        }

        try {
            $uri = Uri::getInstance();

            // The addresses the redirect plugin of Joomla looks for: with and without the query.
            $urls = array_unique([
                strtolower(rawurldecode($uri->toString(['scheme', 'host', 'port', 'path', 'query']))),
                strtolower(rawurldecode($uri->toString(['scheme', 'host', 'port', 'path']))),
            ]);

            $db     = Factory::getContainer()->get(DatabaseInterface::class);
            $header = 410;
            $query  = $db->createQuery()
                ->select($db->quoteName('id'))
                ->from($db->quoteName('#__redirect_links'))
                ->where('LOWER(' . $db->quoteName('old_url') . ') IN (' . implode(',', $db->quote($urls)) . ')')
                ->where($db->quoteName('header') . ' = :header')
                ->where($db->quoteName('published') . ' = 1')
                ->bind(':header', $header, ParameterType::INTEGER);
            $query->setLimit(1);
            $db->setQuery($query);

            $id = (int) $db->loadResult();

            if ($id) {
                $db->setQuery(
                    $db->createQuery()
                        ->update($db->quoteName('#__redirect_links'))
                        ->set($db->quoteName('hits') . ' = ' . $db->quoteName('hits') . ' + 1')
                        ->where($db->quoteName('id') . ' = ' . $id)
                )->execute();

                $gone = true;
            }
        } catch (Exception $e) {
            $gone = false;
        }

        return $gone;
    }

    /**
     * Remove the redirect of a topic which is restored (only the ones made here).
     *
     * @param   string  $url  The address of the topic.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public static function remove(string $url): void
    {
        if ($url === '') {
            return;
        }

        try {
            $db      = Factory::getContainer()->get(DatabaseInterface::class);
            $header  = 410;
            $comment = self::COMMENT;
            $query   = $db->createQuery()
                ->delete($db->quoteName('#__redirect_links'))
                ->where($db->quoteName('old_url') . ' = :url')
                ->where($db->quoteName('header') . ' = :header')
                ->where($db->quoteName('comment') . ' = :comment')
                ->bind(':url', $url)
                ->bind(':header', $header, ParameterType::INTEGER)
                ->bind(':comment', $comment);
            $db->setQuery($query)->execute();
        } catch (Exception $e) {
            // Nothing to tell: the entry can be removed in com_redirect.
        }
    }
}
