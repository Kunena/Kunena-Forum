<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      Cache
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Cache;

\defined('_JEXEC') or die();

use Joomla\CMS\Cache\Cache;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Cache\Controller\OutputController;
use Joomla\CMS\Document\HtmlDocument;
use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\User\KunenaUser;
use Throwable;

/**
 * A copy of the HTML of a part of a page (a widget of a sub-request: the statistics, who is online, the announcement), with the styles
 * and scripts which it adds, for the cache time of the configuration ("Enable Caching" and "Cache Time" of Kunena).
 *
 * The key has the part, what it depends on (given by its controller: the kind of the user, his id...), the Kunena template with its
 * settings, the Joomla template (its overrides) and the language. A part with a form token is not kept (the token is the one of the
 * session). Any save or delete of a Kunena object (a message, a topic, a category, a user, an announcement...) empties the copies,
 * so a part is never older than the last change of the forum, nor than the cache time.
 *
 * @since   Kunena 8.0
 */
abstract class KunenaLayoutCache
{
    /**
     * The group of the copies in the cache of Joomla.
     */
    public const GROUP = 'com_kunena_layouts';

    /**
     * Whether the copies were emptied in this request (once is enough).
     */
    protected static bool $cleared = false;

    /**
     * The kind of a user for the key of a part which shows the same to each one of a kind: guest, member, moderator or admin.
     *
     * @param   KunenaUser  $user  The user.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function userType(KunenaUser $user): string
    {
        if (!$user->exists()) {
            return 'guest';
        }

        if ($user->isAdmin()) {
            return 'admin';
        }

        return $user->isModerator() ? 'moderator' : 'member';
    }

    /**
     * Is the cache of the parts on: the option of Kunena, a page of the site in HTML, a request which reads (GET).
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    public static function isEnabled(): bool
    {
        try {
            $app = Factory::getApplication();

            return (bool) KunenaConfig::getInstance()->cache
                && $app->isClient('site')
                && $app->getDocument() instanceof HtmlDocument
                && strtoupper((string) $app->getInput()->getMethod()) === 'GET';
        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * The key of a part.
     *
     * @param   string  $name     The part (Widget/Statistics).
     * @param   array   $context  What it depends on.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function key(string $name, array $context): string
    {
        $template = KunenaFactory::getTemplate();
        $app      = Factory::getApplication();

        return 'layout.' . md5(json_encode([
            $name,
            $context,
            (string) $template->name,
            md5(json_encode($template->params ? $template->params->toArray() : [])),
            method_exists($app, 'getTemplate') ? (string) $app->getTemplate() : '',
            $app->getLanguage()->getTag(),
        ]));
    }

    /**
     * The HTML of a part: the copy of the cache, else made by $render and kept.
     *
     * @param   string    $name     The part.
     * @param   array     $context  What it depends on.
     * @param   callable  $render   Makes the HTML of the part.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function render(string $name, array $context, callable $render): string
    {
        $document = Factory::getApplication()->getDocument();

        if (!self::isEnabled() || !method_exists($document, 'getHeadData')) {
            return (string) $render();
        }

        $cache = self::controller();
        $id    = self::key($name, $context);
        $data  = $cache->get($id);

        if (\is_array($data) && isset($data['output'])) {
            return (string) Cache::getWorkarounds($data['output'], ['mergehead' => 1]);
        }

        // The head of the page is emptied while the part is made, so that the copy keeps what the part adds (as the cache of the
        // modules of Joomla), then the head of the page and the one of the part are merged.
        $before = $document->getHeadData();
        $document->resetHeadData();
        $document->getWebAssetManager()->reset();

        try {
            $output = (string) $render();
            $stored = Cache::setWorkarounds($output, ['modulemode' => 1, 'headerbefore' => $before, 'nopathway' => 1, 'nohead' => 0, 'nomodules' => 1]);
        } finally {
            $part = $document->getHeadData();
            $document->resetHeadData();
            $document->mergeHeadData($before);
            $document->mergeHeadData($part);
        }

        if (!str_contains($output, Session::getFormToken())) {
            $cache->store(['output' => $stored], $id);
        }

        return $output;
    }

    /**
     * Empty the copies (a change of the forum), once in a request.
     *
     * @param   bool  $force  Also when they were emptied in this request already.
     *
     * @return  void
     *
     * @since   Kunena 8.0
     */
    public static function clear(bool $force = false): void
    {
        if (self::$cleared && !$force) {
            return;
        }

        self::$cleared = true;

        try {
            Factory::getContainer()->get(CacheControllerFactoryInterface::class)
                ->createCacheController('output', ['defaultgroup' => self::GROUP])
                ->clean(self::GROUP);
        } catch (Throwable $e) {
            // A cache which cannot be emptied keeps its copies until their time ends.
        }
    }

    /**
     * The cache of the copies, with the cache time of Kunena, also when the cache of Joomla is off (the option of Kunena decides).
     *
     * @return  OutputController
     *
     * @since   Kunena 8.0
     */
    protected static function controller(): OutputController
    {
        $seconds = max(60, (int) KunenaConfig::getInstance()->cacheTime);

        return Factory::getContainer()->get(CacheControllerFactoryInterface::class)
            ->createCacheController('output', ['defaultgroup' => self::GROUP, 'caching' => true, 'lifetime' => (int) ceil($seconds / 60)]);
    }
}
