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

/**
 * The categories which the user may read, in the order of the forum (filter[parent]: the children of a category).
 *
 * @since   Kunena 8.0
 */
class CategoriesModel extends ApiListModel
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
        $categories = KunenaCategoryHelper::getCategories(false, false, 'read');
        $parent     = $this->getState('filter.parent');

        if ($parent !== null && $parent !== '') {
            $categories = array_filter($categories, static fn ($category): bool => (int) $category->parentid === (int) $parent);
        }

        // Only the categories which are published.
        $categories = array_values(array_filter($categories, static fn ($category): bool => (int) $category->published === 1));

        return [array_map([KunenaApi::class, 'category'], \array_slice($categories, $start, $limit)), \count($categories)];
    }
}
