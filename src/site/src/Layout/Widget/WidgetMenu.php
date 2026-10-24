<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.widget
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
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
    public mixed $output = null;

    public mixed $user = null;

    public mixed $headerText = null;

    public mixed $pagination = null;

    public mixed $config = null;

    public mixed $basemenu = null;

    public mixed $list = null;

    public mixed $menu = null;

    public mixed $active = null;

    public mixed $path = null;

    public mixed $active_id = null;

    public mixed $showAll = null;

    public mixed $class_sfx = null;
}
