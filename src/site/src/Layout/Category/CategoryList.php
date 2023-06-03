<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.Category.Index
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
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
    public $state;

    /**
     * @var     integer
     * @since   Kunena 6.1
     */
    public $total;

    public $output;

    public $user;

    public $headerText;

    public $pagination;

    public $config;

    public $model;

    public $sections;

    public $categories;

    public $actions;
}
