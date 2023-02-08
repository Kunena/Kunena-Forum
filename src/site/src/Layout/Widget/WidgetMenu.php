<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controllers.Misc
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\Widget;

\defined('_JEXEC') or die;

use Kunena\Forum\Libraries\Layout\KunenaLayout;

/**
 * KunenaLayoutWidgetMenu
 *
 * @since   Kunena 4.0
 */
class WidgetMenu extends KunenaLayout
{
    public $output;

    public $user;

    public $headerText;

    public $pagination;

    public $config;

    public $basemenu;

    public $list;

    public $menu;

    public $active;

    public $path;

    public $active_id;

    public $showAll;

    public $class_sfx;
}
