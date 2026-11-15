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

use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Kunena\Forum\Libraries\Api\KunenaApi;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;

/**
 * A category, when the user may read it (else nothing: the API answers not found).
 *
 * @since   Kunena 8.0
 */
class CategoryModel extends BaseDatabaseModel
{
    /**
     * @param   int|null  $pk  The id (else the state category.id).
     *
     * @return  object|null
     *
     * @since   Kunena 8.0
     */
    public function getItem($pk = null)
    {
        $category = KunenaCategoryHelper::get((int) ($pk ?? $this->getState('category.id')));

        if (!$category->exists() || (int) $category->published !== 1 || !$category->isAuthorised('read')) {
            return null;
        }

        return KunenaApi::category($category);
    }
}
