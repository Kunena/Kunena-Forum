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
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Component\Finder\Administrator\Indexer\Indexer;
use Joomla\Component\Finder\Administrator\Indexer\Taxonomy;
use Throwable;

/**
 * The messages of the forum in Smart Search (tool "Search index"): what is indexed, what is needed to index it, and the
 * removal of the Kunena items (the indexer of Smart Search then adds them again).
 *
 * @since   Kunena 8.0
 */
class KunenaSearchIndex
{
    /**
     * The type of the items of the plugin plg_finder_kunena.
     *
     * @since   Kunena 8.0
     */
    public const TYPE_TITLE = 'Forum Post';

    /**
     * The status: is Smart Search installed, are the plugins enabled, how many items are indexed for how many messages.
     *
     * @return  array{finder: bool, plugin: bool, content: bool, indexed: int, messages: int, lastIndexed: string}
     *
     * @since   Kunena 8.0
     */
    public static function status(): array
    {
        $db     = Factory::getContainer()->get('DatabaseDriver');
        $status = [
            'finder'      => self::componentEnabled('com_finder'),
            'plugin'      => PluginHelper::isEnabled('finder', 'kunena'),
            'content'     => PluginHelper::isEnabled('content', 'finder'),
            'indexed'     => 0,
            'messages'    => 0,
            'lastIndexed' => '',
        ];

        $query = $db->createQuery()
            ->select('COUNT(*)')
            ->from($db->quoteName('#__kunena_messages'))
            ->where($db->quoteName('hold') . ' = 0');
        $status['messages'] = (int) $db->setQuery($query)->loadResult();

        if (!$status['finder']) {
            return $status;
        }

        try {
            $query = $db->createQuery()
                ->select(['COUNT(*) AS ' . $db->quoteName('total'), 'MAX(' . $db->quoteName('l.indexdate') . ') AS ' . $db->quoteName('last')])
                ->from($db->quoteName('#__finder_links', 'l'))
                ->innerJoin($db->quoteName('#__finder_types', 't') . ' ON ' . $db->quoteName('t.id') . ' = ' . $db->quoteName('l.type_id'))
                ->where($db->quoteName('t.title') . ' = ' . $db->quote(self::TYPE_TITLE));
            $row = (array) $db->setQuery($query)->loadAssoc();

            $status['indexed']     = (int) ($row['total'] ?? 0);
            $status['lastIndexed'] = (string) ($row['last'] ?? '');
        } catch (Throwable $e) {
            // Smart Search without its tables.
        }

        return $status;
    }

    /**
     * Removes the Kunena items from the index of Smart Search.
     *
     * @return  int  The number removed.
     *
     * @since   Kunena 8.0
     */
    public static function purge(): int
    {
        if (!self::componentEnabled('com_finder') || !class_exists(Indexer::class)) {
            return 0;
        }

        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select($db->quoteName('l.link_id'))
            ->from($db->quoteName('#__finder_links', 'l'))
            ->innerJoin($db->quoteName('#__finder_types', 't') . ' ON ' . $db->quoteName('t.id') . ' = ' . $db->quoteName('l.type_id'))
            ->where($db->quoteName('t.title') . ' = ' . $db->quote(self::TYPE_TITLE));
        $ids = (array) $db->setQuery($query)->loadColumn();

        if (!$ids) {
            return 0;
        }

        $indexer = new Indexer($db);
        $removed = 0;

        foreach ($ids as $id) {
            try {
                $indexer->remove((int) $id, false);
                $removed++;
            } catch (Throwable $e) {
                // The others are still removed.
            }
        }

        // The taxonomies which no item uses any more, once at the end.
        try {
            Taxonomy::removeOrphanMaps();
            Taxonomy::removeOrphanNodes();
        } catch (Throwable $e) {
            // The items are removed.
        }

        return $removed;
    }

    /**
     * Is a component installed and enabled?
     *
     * @param   string  $element  The component.
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    private static function componentEnabled(string $element): bool
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select($db->quoteName('enabled'))
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('component'))
            ->where($db->quoteName('element') . ' = ' . $db->quote($element));

        return (bool) $db->setQuery($query)->loadResult();
    }
}
