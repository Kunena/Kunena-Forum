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
use Kunena\Forum\Libraries\Cache\KunenaCacheHelper;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Exception\KunenaException;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Throwable;

/**
 * The categories and the configuration in a JSON file (tool "Export and import"): a branch of the categories (or all of
 * them) is exported, and imported under a category of this site or of another one; the configuration goes with it when
 * asked. No topic, no counter, and never a secret of the configuration (the keys of the services).
 *
 * @since   Kunena 8.0
 */
class KunenaCategoryTransfer
{
    /**
     * The format of the file.
     *
     * @since   Kunena 8.0
     */
    public const FORMAT = 'kunena-export';

    /**
     * The version of the format.
     *
     * @since   Kunena 8.0
     */
    public const VERSION = 1;

    /**
     * The columns of a category which are exported (the id is the reference of the parent of another category).
     *
     * @since   Kunena 8.0
     */
    public const FIELDS = [
        'id', 'parentid', 'name', 'alias', 'icon', 'icon_id', 'locked', 'accesstype', 'access', 'pubAccess', 'pubRecurse',
        'adminAccess', 'adminRecurse', 'ordering', 'published', 'channels', 'review', 'allowAnonymous', 'postAnonymous',
        'description', 'headerdesc', 'topictemplate', 'class_sfx', 'allowPolls', 'topicOrdering', 'iconset', 'params',
        'allowRatings', 'language',
    ];

    /**
     * The options of the configuration which are never exported nor imported: the keys and passwords of the services.
     *
     * @since   Kunena 8.0
     */
    public const SECRETS = ['XConsumerKey', 'XConsumerSecret', 'blueskyappPasswordOfApp', 'ebayApiKey', 'googleMapApiKey', 'stopForumSpamKey'];

    /**
     * The ids of a category and of all the categories below it.
     *
     * @param   array  $rows  The categories (id, parentid).
     * @param   int    $root  The category (0: all of them).
     *
     * @return  int[]
     *
     * @since   Kunena 8.0
     */
    public static function branch(array $rows, int $root): array
    {
        if ($root === 0) {
            return array_map(static fn ($row) => (int) $row['id'], $rows);
        }

        $children = [];

        foreach ($rows as $row) {
            $children[(int) $row['parentid']][] = (int) $row['id'];
        }

        $ids  = [];
        $todo = [$root];

        while ($todo) {
            $id = array_shift($todo);

            if (isset($ids[$id])) {
                continue;
            }

            $ids[$id] = $id;
            $todo     = array_merge($todo, $children[$id] ?? []);
        }

        return array_values(array_intersect(array_values($ids), array_map(static fn ($row) => (int) $row['id'], $rows)));
    }

    /**
     * The categories in an order where a parent comes before its children; a category whose parent is not in the list
     * becomes a top one of the list (parentid 0).
     *
     * @param   array  $rows  The categories (id, parentid, ordering).
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    public static function sortTree(array $rows): array
    {
        $byId = [];

        foreach ($rows as $row) {
            $byId[(int) $row['id']] = $row;
        }

        $children = [];

        foreach ($byId as $id => $row) {
            $parent = (int) $row['parentid'];

            if ($parent === $id || !isset($byId[$parent])) {
                $parent = 0;
            }

            $byId[$id]['parentid'] = $parent;
            $children[$parent][]   = $id;
        }

        $sorted = [];
        $todo   = [0];

        while ($todo) {
            $parent = array_shift($todo);
            $ids    = $children[$parent] ?? [];

            usort($ids, static fn ($a, $b) => [(int) ($byId[$a]['ordering'] ?? 0), $a] <=> [(int) ($byId[$b]['ordering'] ?? 0), $b]);

            foreach ($ids as $id) {
                if (!isset($sorted[$id])) {
                    $sorted[$id] = $byId[$id];
                    $todo[]      = $id;
                }
            }
        }

        return array_values($sorted);
    }

    /**
     * The file of an export.
     *
     * @param   array       $rows    The categories of the site (all their columns).
     * @param   int         $root    The category to export with its children (0: all of them).
     * @param   array|null  $config  The configuration to export with them (null: not exported).
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    public static function export(array $rows, int $root, ?array $config = null): array
    {
        $ids        = array_flip(self::branch($rows, $root));
        $categories = [];

        foreach ($rows as $row) {
            if (isset($ids[(int) $row['id']])) {
                $categories[] = array_intersect_key($row, array_flip(self::FIELDS));
            }
        }

        $data = [
            'format'     => self::FORMAT,
            'version'    => self::VERSION,
            'categories' => self::sortTree($categories),
        ];

        if ($config !== null) {
            $data['config'] = self::filterConfig($config);
        }

        return $data;
    }

    /**
     * Checks a file of an export.
     *
     * @param   mixed  $data  The decoded JSON.
     *
     * @return  string|null  The language key of the error, null when the file is good.
     *
     * @since   Kunena 8.0
     */
    public static function validate($data): ?string
    {
        if (!\is_array($data) || ($data['format'] ?? '') !== self::FORMAT) {
            return 'COM_KUNENA_TOOLS_TRANSFER_ERROR_FORMAT';
        }

        if ((int) ($data['version'] ?? 0) > self::VERSION) {
            return 'COM_KUNENA_TOOLS_TRANSFER_ERROR_VERSION';
        }

        foreach ((array) ($data['categories'] ?? []) as $category) {
            if (!\is_array($category) || !isset($category['id'], $category['name']) || !is_numeric($category['id'])) {
                return 'COM_KUNENA_TOOLS_TRANSFER_ERROR_CATEGORY';
            }
        }

        if (isset($data['config']) && !\is_array($data['config'])) {
            return 'COM_KUNENA_TOOLS_TRANSFER_ERROR_FORMAT';
        }

        return null;
    }

    /**
     * The options of a configuration which can leave the site: no secret, only plain values.
     *
     * @param   array  $config  The options.
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    public static function filterConfig(array $config): array
    {
        $filtered = [];

        foreach ($config as $key => $value) {
            if (\is_string($key) && !\in_array($key, self::SECRETS, true) && (\is_scalar($value) || $value === null)) {
                $filtered[$key] = $value;
            }
        }

        ksort($filtered);

        return $filtered;
    }

    /**
     * The options of an imported configuration which change this one: only the options which this site knows, never a secret,
     * only plain values.
     *
     * @param   array  $current   The configuration of the site.
     * @param   array  $incoming  The imported one.
     *
     * @return  array  key => new value.
     *
     * @since   Kunena 8.0
     */
    public static function configChanges(array $current, array $incoming): array
    {
        $changes = [];

        foreach (self::filterConfig($incoming) as $key => $value) {
            if (\array_key_exists($key, $current) && (string) $current[$key] !== (string) $value) {
                $changes[$key] = $value;
            }
        }

        return $changes;
    }

    /**
     * The channels of an imported category with the new ids (a list of ids, or a word: THIS, CHILDREN, none).
     *
     * @param   string  $channels  The channels of the file.
     * @param   array   $map       Old id => new id.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function mapChannels(string $channels, array $map): string
    {
        $parts = array_filter(array_map('trim', explode(',', $channels)), 'strlen');
        $out   = [];

        foreach ($parts as $part) {
            if (!ctype_digit($part)) {
                $out[] = $part;
            } elseif (isset($map[(int) $part])) {
                $out[] = (string) $map[(int) $part];
            }
        }

        return $out ? implode(',', $out) : 'THIS';
    }

    /**
     * The categories of this site, all their columns.
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    public static function rowsOnSite(): array
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()
            ->select('*')
            ->from($db->quoteName('#__kunena_categories'))
            ->order($db->quoteName('ordering'));

        return (array) $db->setQuery($query)->loadAssocList();
    }

    /**
     * Imports a file on this site: the categories below a parent (unpublished when asked), the configuration when asked.
     *
     * @param   array  $data         The file (validate() passed).
     * @param   int    $parent       The parent of the top categories of the file (0: a section at the top).
     * @param   bool   $unpublished  The categories are imported unpublished.
     * @param   bool   $withConfig   The configuration of the file is imported too.
     *
     * @return  array{categories: int, failed: int, config: int}
     *
     * @since   Kunena 8.0
     */
    public static function importOnSite(array $data, int $parent, bool $unpublished, bool $withConfig): array
    {
        $result = ['categories' => 0, 'failed' => 0, 'config' => 0];
        $map    = [0 => max(0, $parent)];
        $saved  = [];

        foreach (self::sortTree((array) ($data['categories'] ?? [])) as $row) {
            $old = (int) $row['id'];

            if (!isset($map[(int) $row['parentid']])) {
                $result['failed']++;

                continue;
            }

            $values             = array_intersect_key($row, array_flip(self::FIELDS));
            $values['parentid'] = $map[(int) $row['parentid']];
            unset($values['id']);

            if ($unpublished) {
                $values['published'] = 0;
            }

            if (\is_array($values['params'] ?? null)) {
                $values['params'] = json_encode($values['params']);
            }

            $category = self::saveCategory($values);

            if ($category === null) {
                $result['failed']++;

                continue;
            }

            $map[$old]                  = (int) $category->id;
            $saved[(int) $category->id] = [$category, (string) ($row['channels'] ?? '')];
            $result['categories']++;
        }

        // The channels name other categories of the file: their new ids are known now.
        foreach ($saved as [$category, $channels]) {
            $mapped = self::mapChannels($channels, $map);

            if ($mapped !== (string) $category->channels) {
                $category->channels = $mapped;

                try {
                    $category->save();
                } catch (Throwable $e) {
                    // The category is imported, with its channels of the file.
                }
            }
        }

        if ($withConfig && isset($data['config']) && \is_array($data['config'])) {
            $config  = KunenaConfig::getInstance();
            $changes = self::configChanges($config->toArray(), $data['config']);

            foreach ($changes as $key => $value) {
                $config->$key = $value;
            }

            if ($changes && $config->save()) {
                $result['config'] = \count($changes);
            }
        }

        KunenaCacheHelper::clear();

        return $result;
    }

    /**
     * Saves a new category: with the alias of the file when it is free, else with one made from the name.
     *
     * @param   array  $values  The columns.
     *
     * @return  KunenaCategory|null
     *
     * @since   Kunena 8.0
     */
    private static function saveCategory(array $values): ?KunenaCategory
    {
        foreach ([$values['alias'] ?? '', ''] as $alias) {
            $category = new KunenaCategory();
            $category->bind(['alias' => $alias] + $values);

            try {
                if ($category->save()) {
                    return $category;
                }
            } catch (KunenaException $e) {
                // The alias is taken: the next try makes one from the name.
            } catch (Throwable $e) {
                return null;
            }
        }

        return null;
    }
}
