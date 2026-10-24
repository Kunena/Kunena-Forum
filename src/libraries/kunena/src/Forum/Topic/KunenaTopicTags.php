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
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Language\KunenaMultilanguage;
use Kunena\Forum\Libraries\Route\KunenaRoute;

/**
 * The tags of the topics: the tags of the core Joomla component Tags, kept in the table #__contentitem_tag_map with the type
 * alias com_kunena.topic.
 *
 * @since   Kunena 7.2
 */
abstract class KunenaTopicTags
{
    /**
     * The type alias in the map of Joomla tags.
     *
     * @since   Kunena 7.2
     */
    public const string TYPE_ALIAS = 'com_kunena.topic';

    /**
     * The tags of the topics which were loaded, by topic id.
     *
     * @var     array
     * @since   Kunena 7.2
     */
    protected static array $topicTags = [];

    /**
     * Remove all the tags of a topic which is deleted.
     *
     * @param   int  $topicId  The topic id.
     *
     * @return  void
     *
     * @since   Kunena 7.2
     */
    public static function delete(int $topicId): void
    {
        try {
            $db = Factory::getContainer()->get(DatabaseInterface::class);
            $db->setQuery(
                $db->createQuery()
                    ->delete($db->quoteName('#__contentitem_tag_map'))
                    ->where($db->quoteName('type_alias') . ' = ' . $db->quote(self::TYPE_ALIAS))
                    ->where($db->quoteName('content_item_id') . ' = ' . $topicId)
            )->execute();
            unset(self::$topicTags[$topicId]);
        } catch (Exception $e) {
            // Nothing to tell: the map can have a row of a topic which does not exist.
        }
    }

    /**
     * The tags of a topic which the logged in user may see.
     *
     * @param   int  $topicId  The topic id.
     *
     * @return  array  Objects with id and title, by id.
     *
     * @since   Kunena 7.2
     */
    public static function get(int $topicId): array
    {
        self::preload([$topicId]);

        return self::$topicTags[$topicId] ?? [];
    }

    /**
     * The ids of all the tags of a topic (also the ones the logged in user may not see).
     *
     * @param   int  $topicId  The topic id.
     *
     * @return  int[]
     *
     * @since   Kunena 7.2
     */
    protected static function getAllIds(int $topicId): array
    {
        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->createQuery()
            ->select($db->quoteName('tag_id'))
            ->from($db->quoteName('#__contentitem_tag_map'))
            ->where($db->quoteName('type_alias') . ' = ' . $db->quote(self::TYPE_ALIAS))
            ->where($db->quoteName('content_item_id') . ' = ' . $topicId);
        $db->setQuery($query);

        return array_map('intval', $db->loadColumn());
    }

    /**
     * The tags a user can choose from: published and with an access level of the user.
     *
     * @return  array  Objects with id and title, by title.
     *
     * @since   Kunena 7.2
     */
    public static function getAvailable(): array
    {
        $levels = array_map('intval', Factory::getApplication()->getIdentity()->getAuthorisedViewLevels());

        if (!$levels) {
            return [];
        }

        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->createQuery()
            ->select($db->quoteName(['id', 'title']))
            ->from($db->quoteName('#__tags'))
            ->where($db->quoteName('published') . ' = 1')
            ->where($db->quoteName('id') . ' > 1')
            ->where($db->quoteName('access') . ' IN (' . implode(',', $levels) . ')')
            ->order($db->quoteName('title'));

        // On a multilingual site the tags of the language of the page and the ones for all languages.
        if (KunenaMultilanguage::isEnabled()) {
            $query->where($db->quoteName('language') . ' IN (' . $db->quote('*') . ',' . $db->quote(KunenaMultilanguage::getCurrent()) . ')');
        }

        try {
            $db->setQuery($query);

            return (array) $db->loadObjectList('id');
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * The address of the list of topics which have the tag.
     *
     * @param   int  $tagId  The tag id.
     *
     * @return  string
     *
     * @since   Kunena 7.2
     */
    public static function getUrl(int $tagId): string
    {
        return KunenaRoute::_('index.php?option=com_kunena&view=topics&layout=default&ftag=' . $tagId, false);
    }

    /**
     * Is tagging of topics on and is the component Tags there.
     *
     * @return  bool
     *
     * @since   Kunena 7.2
     */
    public static function isEnabled(): bool
    {
        $config = KunenaConfig::getInstance();

        return isset($config->enableTags) && $config->enableTags && is_dir(JPATH_ADMINISTRATOR . '/components/com_tags');
    }

    /**
     * Load the tags of the topics with one query.
     *
     * @param   int[]  $topicIds  The topic ids.
     *
     * @return  void
     *
     * @since   Kunena 7.2
     */
    public static function preload(array $topicIds): void
    {
        $topicIds = array_diff(array_map('intval', $topicIds), array_keys(self::$topicTags));

        if (!$topicIds || !self::isEnabled()) {
            return;
        }

        foreach ($topicIds as $id) {
            self::$topicTags[$id] = [];
        }

        $levels = array_map('intval', Factory::getApplication()->getIdentity()->getAuthorisedViewLevels());

        if (!$levels) {
            return;
        }

        try {
            $db    = Factory::getContainer()->get(DatabaseInterface::class);
            $query = $db->createQuery()
                ->select([$db->quoteName('m.content_item_id', 'topic_id'), $db->quoteName('t.id'), $db->quoteName('t.title')])
                ->from($db->quoteName('#__contentitem_tag_map', 'm'))
                ->join('INNER', $db->quoteName('#__tags', 't'), $db->quoteName('t.id') . ' = ' . $db->quoteName('m.tag_id'))
                ->where($db->quoteName('m.type_alias') . ' = ' . $db->quote(self::TYPE_ALIAS))
                ->where($db->quoteName('m.content_item_id') . ' IN (' . implode(',', $topicIds) . ')')
                ->where($db->quoteName('t.published') . ' = 1')
                ->where($db->quoteName('t.access') . ' IN (' . implode(',', $levels) . ')')
                ->order($db->quoteName('t.title'));
            $db->setQuery($query);

            foreach ((array) $db->loadObjectList() as $row) {
                self::$topicTags[(int) $row->topic_id][(int) $row->id] = (object) ['id' => (int) $row->id, 'title' => $row->title];
            }
        } catch (Exception $e) {
            // No tags is better than a broken page.
        }
    }

    /**
     * The tags of a topic as links to the list of topics with the tag.
     *
     * @param   int     $topicId  The topic id.
     * @param   string  $class    The class of the block.
     *
     * @return  string  The html, or an empty string when the topic has no tags (or tags are off).
     *
     * @since   Kunena 7.2
     */
    public static function render(int $topicId, string $class = ''): string
    {
        if (!self::isEnabled()) {
            return '';
        }

        $tags = self::get($topicId);

        if (!$tags) {
            return '';
        }

        $html = '<span class="ktopic-tags ' . htmlspecialchars($class, ENT_QUOTES, 'UTF-8') . '">';

        foreach ($tags as $tag) {
            $html .= '<a class="badge bg-secondary text-decoration-none me-1" href="' . htmlspecialchars(self::getUrl($tag->id), ENT_QUOTES, 'UTF-8') . '">'
                . htmlspecialchars($tag->title, ENT_QUOTES, 'UTF-8') . '</a>';
        }

        return $html . '</span>';
    }

    /**
     * Save the tags of a topic: the chosen tags the user may use, and the tags the user can not see stay as they are.
     *
     * @param   int    $topicId  The topic id.
     * @param   array  $tagIds   The chosen tag ids.
     *
     * @return  void
     *
     * @since   Kunena 7.2
     */
    public static function save(int $topicId, array $tagIds): void
    {
        if (!self::isEnabled() || $topicId < 1) {
            return;
        }

        $available = array_keys(self::getAvailable());
        $chosen    = array_values(array_intersect(array_unique(array_map('intval', $tagIds)), $available));

        try {
            $db       = Factory::getContainer()->get(DatabaseInterface::class);
            $existing = self::getAllIds($topicId);

            // What the user could choose from is replaced by the choice, what was not available stays.
            $remove = array_diff(array_intersect($existing, $available), $chosen);
            $add    = array_diff($chosen, $existing);

            if ($remove) {
                $db->setQuery(
                    $db->createQuery()
                        ->delete($db->quoteName('#__contentitem_tag_map'))
                        ->where($db->quoteName('type_alias') . ' = ' . $db->quote(self::TYPE_ALIAS))
                        ->where($db->quoteName('content_item_id') . ' = ' . $topicId)
                        ->where($db->quoteName('tag_id') . ' IN (' . implode(',', array_map('intval', $remove)) . ')')
                )->execute();
            }

            foreach ($add as $tagId) {
                $db->setQuery(
                    $db->createQuery()
                        ->insert($db->quoteName('#__contentitem_tag_map'))
                        ->columns($db->quoteName(['type_alias', 'core_content_id', 'content_item_id', 'tag_id', 'tag_date', 'type_id']))
                        ->values($db->quote(self::TYPE_ALIAS) . ', 0, ' . $topicId . ', ' . (int) $tagId . ', ' . $db->quote(Factory::getDate()->toSql()) . ', 0')
                )->execute();
            }

            unset(self::$topicTags[$topicId]);
        } catch (Exception $e) {
            // The tags are an extra: a failure must not lose the topic.
        }
    }
}
