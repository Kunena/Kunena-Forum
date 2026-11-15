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
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicFinder;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * The published topics which the user may read, the last active first (filter[category]: the topics of a category).
 *
 * @since   Kunena 8.0
 */
class TopicsModel extends ApiListModel
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
        $finder = new KunenaTopicFinder();
        $finder
            ->filterByUserAccess(KunenaUserHelper::getMyself())
            ->filterByHold([0])
            ->filterByMoved(false);

        $category = (int) $this->getState('filter.category', 0);

        if ($category > 0) {
            // A category which the user may not read gives nothing.
            $finder->filterByCategories(KunenaCategoryHelper::get($category)->isAuthorised('read') ? [$category] : [0]);
        }

        $total = $finder->count();
        $items = $finder->order('last_post_time', -1)->start($start)->limit($limit)->find();

        return [array_map([KunenaApi::class, 'topic'], array_values($items)), $total];
    }
}
