<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Api
 * @subpackage      Model
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Api\Model;

\defined('_JEXEC') or die();

use Kunena\Forum\Libraries\Api\KunenaApi;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageFinder;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * The published posts which the user may read: the newest first, or the posts of one topic in their order (filter[topic]).
 *
 * @since   Kunena 8.0
 */
class PostsModel extends ApiListModel
{
    /**
     * @param   int  $start  The first item.
     * @param   int  $limit  The number of items.
     *
     * @return  array{0: object[], 1: int}
     *
     * @since   Kunena 8.0
     */
    protected function load(int $start, int $limit): array
    {
        $me     = KunenaUserHelper::getMyself();
        $finder = new KunenaMessageFinder();
        $finder
            ->filterByUserAccess($me)
            ->filterByHold([0]);

        $topicId = (int) $this->getState('filter.topic', 0);

        if ($topicId > 0) {
            // A topic which the user may not read gives nothing.
            $topic = KunenaTopicHelper::get($topicId);
            $finder->where('a.thread', '=', $topic->exists() && (int) $topic->hold === 0 && $topic->isAuthorised('read') ? $topicId : 0);
            $finder->order('time', 1);
        } else {
            $finder->order('time', -1);
        }

        $total = $finder->count();
        $items = $finder->start($start)->limit($limit)->find();

        return [array_map(static fn ($message): object => KunenaApi::post($message, $me), array_values($items)), $total];
    }
}
