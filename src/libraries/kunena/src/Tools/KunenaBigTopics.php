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
use Kunena\Forum\Libraries\Config\KunenaConfig;

/**
 * The topics with very many posts (tool "Big topics"): their pages are slow, their last page is far away, and their
 * subscribers get a mail for each reply for years. They can be locked here, or split on the forum.
 *
 * @since   Kunena 8.0
 */
class KunenaBigTopics
{
    /**
     * The sizes from which a topic is big, in posts.
     *
     * @since   Kunena 8.0
     */
    public const SIZES = [250, 500, 1000, 2500, 5000];

    /**
     * The topics shown.
     *
     * @since   Kunena 8.0
     */
    public const LIMIT = 100;

    /**
     * The number of pages of a topic.
     *
     * @param   int  $posts    The posts of the topic.
     * @param   int  $perPage  The posts per page.
     *
     * @return  int
     *
     * @since   Kunena 8.0
     */
    public static function pages(int $posts, int $perPage): int
    {
        return max(1, (int) ceil(max(0, $posts) / max(1, $perPage)));
    }

    /**
     * The big topics of this site, the biggest first.
     *
     * @param   int  $size  One of SIZES.
     *
     * @return  array{size: int, perPage: int, total: int, topics: array, categories: array<int, string>}
     *
     * @since   Kunena 8.0
     */
    public static function findOnSite(int $size): array
    {
        $size    = \in_array($size, self::SIZES, true) ? $size : self::SIZES[1];
        $perPage = max(1, (int) KunenaConfig::getInstance()->messagesPerPage);
        $db      = Factory::getContainer()->get('DatabaseDriver');
        $query   = $db->createQuery()
            ->select($db->quoteName(['id', 'category_id', 'subject', 'posts', 'hits', 'locked', 'hold', 'first_post_time', 'last_post_time']))
            ->from($db->quoteName('#__kunena_topics'))
            ->where($db->quoteName('posts') . ' >= :size')
            ->where($db->quoteName('moved_id') . ' = 0')
            ->where($db->quoteName('hold') . ' = 0')
            ->bind(':size', $size, ParameterType::INTEGER)
            ->order($db->quoteName('posts') . ' DESC');
        $topics = (array) $db->setQuery($query, 0, self::LIMIT)->loadAssocList();

        $count = clone $query;
        $count->clear('select')->clear('order')->select('COUNT(*)');
        $total = (int) $db->setQuery($count)->loadResult();

        foreach ($topics as &$topic) {
            $topic['pages'] = self::pages((int) $topic['posts'], $perPage);
        }

        unset($topic);

        $names = [];
        $ids   = array_values(array_unique(array_map('intval', array_column($topics, 'category_id'))));

        if ($ids) {
            $query = $db->createQuery()->select($db->quoteName(['id', 'name']))->from($db->quoteName('#__kunena_categories'))->whereIn($db->quoteName('id'), $ids);
            $names = array_map('strval', (array) $db->setQuery($query)->loadAssocList('id', 'name'));
        }

        return ['size' => $size, 'perPage' => $perPage, 'total' => $total, 'topics' => $topics, 'categories' => $names];
    }
}
