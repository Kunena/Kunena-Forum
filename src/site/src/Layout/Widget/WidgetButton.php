<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.widget
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\Widget;

\defined('_JEXEC') or die;

use Kunena\Forum\Libraries\Layout\KunenaLayout;

// Use function defined;

/**
 * KunenaLayoutWidgetFooter
 *
 * @since   Kunena 6.2
 */
class WidgetButton extends KunenaLayout
{
    public $url;

    public $name;

    public $scope;

    public $type;

    public $primary;

    public $normal;

    public $icon;

    public $id;

    public $pullright;

    public $modal;

    public $success;
}
