<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.Category.Index
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\Category;

\defined('_JEXEC') or die;

use Kunena\Forum\Libraries\Layout\KunenaLayout;

/**
 * KunenaLayoutCategoryIndex
 *
 * @since   Kunena 6.1
 */
class CategoryList extends KunenaLayout
{
    /**
     * @var     object
     * @since   Kunena 6.1
     */
    public mixed $state = null;

    /**
     * @var     integer
     * @since   Kunena 6.1
     */
    public mixed $total = null;

    public mixed $output = null;

    public mixed $user = null;

    public mixed $headerText = null;

    public mixed $pagination = null;

    public mixed $config = null;

    public mixed $model = null;

    public mixed $sections = null;

    public mixed $categories = null;

    public mixed $actions = null;
}
