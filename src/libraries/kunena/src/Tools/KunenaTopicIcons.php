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
use Joomla\Database\ParameterType;

/**
 * The topic icons and the labels (tool "Topic icons and labels"): how many topics use each icon of the icon set of their
 * category, the icons nobody uses, the topics whose icon is not in the set (they show the default icon), the categories
 * whose icon set is gone, and the use of each label.
 *
 * @since   Kunena 8.0
 */
class KunenaTopicIcons
{
    /**
     * The folder of the icon sets, from the root of Joomla.
     *
     * @since   Kunena 8.0
     */
    public const SETS = 'media/kunena/topic_icons';

    /**
     * The file of the labels, from the root of Joomla.
     *
     * @since   Kunena 8.0
     */
    public const LABELS = 'media/kunena/labels/labels.xml';

    /**
     * The name of an icon set as a category keeps it: "/default", "default" or empty (the default set).
     *
     * @param   string|null  $iconset  The value of the category.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public static function setName(?string $iconset): string
    {
        $name = trim((string) $iconset, " /\\");

        return preg_match('/^[\w.\-]+$/', $name) ? $name : 'default';
    }

    /**
     * The icons a user can choose in a topicicons.xml: id => [name, published].
     *
     * @param   string  $xml  The content of the file.
     *
     * @return  array<int, array{name: string, published: bool}>|null  Null when the file cannot be read.
     *
     * @since   Kunena 8.0
     */
    public static function icons(string $xml): ?array
    {
        $doc = @simplexml_load_string($xml);

        if ($doc === false) {
            return null;
        }

        $icons = [];

        foreach ($doc->xpath('/kunena-topicicons/icons[@type="user"]/icon') ?: [] as $icon) {
            $icons[(int) $icon['id']] = ['name' => (string) $icon['name'], 'published' => (string) $icon['published'] !== '0'];
        }

        return $icons;
    }

    /**
     * The labels of a labels.xml: id => name (a language key).
     *
     * @param   string  $xml  The content of the file.
     *
     * @return  array<int, string>
     *
     * @since   Kunena 8.0
     */
    public static function labels(string $xml): array
    {
        $doc    = @simplexml_load_string($xml);
        $labels = [];

        foreach ($doc === false ? [] : ($doc->xpath('/kunena-topiclabels/labels/label') ?: []) as $label) {
            $labels[(int) $label['id']] = (string) $label['name'];
        }

        return $labels;
    }

    /**
     * The use of the icons of a set.
     *
     * @param   array<int, array{name: string, published: bool}>  $icons   The icons of the set.
     * @param   array<int, int>                                   $counts  icon id => topics.
     *
     * @return  array{icons: array<int, array{name: string, published: bool, topics: int}>, unknown: array<int, int>}  unknown:
     *          the icon ids of topics which are not in the set (or not published) => topics.
     *
     * @since   Kunena 8.0
     */
    public static function usage(array $icons, array $counts): array
    {
        $result  = [];
        $unknown = [];

        foreach ($icons as $id => $icon) {
            $result[$id] = $icon + ['topics' => (int) ($counts[$id] ?? 0)];
        }

        foreach ($counts as $id => $topics) {
            // The default icon (0) is always shown.
            if ($id !== 0 && (!isset($icons[$id]) || !$icons[$id]['published'])) {
                $unknown[$id] = (int) $topics;
            }
        }

        ksort($unknown);

        return ['icons' => $result, 'unknown' => $unknown];
    }

    /**
     * The icons and the labels of this site.
     *
     * @return  array{sets: array, missing: array, labels: array, unknownLabels: array<int, int>}
     *
     * @since   Kunena 8.0
     */
    public static function checkOnSite(): array
    {
        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->createQuery()->select($db->quoteName(['id', 'name', 'iconset']))->from($db->quoteName('#__kunena_categories'));
        $sets  = [];
        $miss  = [];

        foreach ((array) $db->setQuery($query)->loadAssocList() as $category) {
            $name = self::setName($category['iconset']);

            if (!is_file(JPATH_ROOT . '/' . self::SETS . '/' . $name . '/topicicons.xml')) {
                // Kunena shows the default set instead.
                $miss[] = ['id' => (int) $category['id'], 'name' => (string) $category['name'], 'set' => $name];
                $name   = 'default';
            }

            $sets[$name][] = (int) $category['id'];
        }

        $result = [];

        foreach ($sets as $name => $categories) {
            $query = $db->createQuery()
                ->select($db->quoteName('icon_id'))
                ->select('COUNT(*) AS ' . $db->quoteName('topics'))
                ->from($db->quoteName('#__kunena_topics'))
                ->whereIn($db->quoteName('category_id'), $categories)
                ->where($db->quoteName('moved_id') . ' = 0')
                ->group($db->quoteName('icon_id'));
            $counts = array_map('intval', (array) $db->setQuery($query)->loadAssocList('icon_id', 'topics'));
            $icons  = self::icons((string) @file_get_contents(JPATH_ROOT . '/' . self::SETS . '/' . $name . '/topicicons.xml')) ?? [];

            $result[$name] = ['categories' => \count($categories)] + self::usage($icons, $counts);
        }

        ksort($result);

        $labels = self::labels((string) @file_get_contents(JPATH_ROOT . '/' . self::LABELS));
        $query  = $db->createQuery()
            ->select($db->quoteName('label_id'))
            ->select('COUNT(*) AS ' . $db->quoteName('topics'))
            ->from($db->quoteName('#__kunena_topics'))
            ->where($db->quoteName('moved_id') . ' = 0')
            ->group($db->quoteName('label_id'));
        $counts  = array_map('intval', (array) $db->setQuery($query)->loadAssocList('label_id', 'topics'));
        $useOf   = [];
        $unknown = [];

        foreach ($labels as $id => $name) {
            $useOf[$id] = ['name' => $name, 'topics' => $counts[$id] ?? 0];
        }

        foreach ($counts as $id => $topics) {
            if (!isset($labels[$id])) {
                $unknown[$id] = $topics;
            }
        }

        return ['sets' => $result, 'missing' => $miss, 'labels' => $useOf, 'unknownLabels' => $unknown];
    }

    /**
     * Gives the topics whose icon is not in the icon set of their category the default icon (0).
     *
     * @return  int  The topics changed.
     *
     * @since   Kunena 8.0
     */
    public static function resetUnknownOnSite(): int
    {
        $db      = Factory::getContainer()->get('DatabaseDriver');
        $changed = 0;
        $report  = self::checkOnSite();

        foreach ($report['sets'] as $name => $set) {
            $ids = array_keys($set['unknown']);

            if (!$ids) {
                continue;
            }

            $categories = [];
            $query      = $db->createQuery()->select($db->quoteName(['id', 'iconset']))->from($db->quoteName('#__kunena_categories'));

            foreach ((array) $db->setQuery($query)->loadAssocList() as $category) {
                $own = self::setName($category['iconset']);
                $own = is_file(JPATH_ROOT . '/' . self::SETS . '/' . $own . '/topicicons.xml') ? $own : 'default';

                if ($own === $name) {
                    $categories[] = (int) $category['id'];
                }
            }

            $zero  = 0;
            $query = $db->createQuery()
                ->update($db->quoteName('#__kunena_topics'))
                ->set($db->quoteName('icon_id') . ' = :zero')
                ->bind(':zero', $zero, ParameterType::INTEGER)
                ->whereIn($db->quoteName('category_id'), $categories)
                ->whereIn($db->quoteName('icon_id'), array_map('intval', $ids));
            $db->setQuery($query)->execute();
            $changed += (int) $db->getAffectedRows();
        }

        return $changed;
    }
}
