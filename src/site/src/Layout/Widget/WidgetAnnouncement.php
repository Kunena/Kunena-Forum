<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.widget
 *
 * @copyright       Copyright (C) 2008 - 2024 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\Widget;

\defined('_JEXEC') or die;

use Kunena\Forum\Libraries\Layout\KunenaLayout;

/**
 * WidgetAnnouncement
 *
 * @since   Kunena 6.2
 */
class WidgetAnnouncement extends KunenaLayout
{
    public $announcement;

    public $config;

    public $pagination;

    public $headerText;

    public $user;

    public $output;
}
