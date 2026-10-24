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

// Use function defined;

/**
 * KunenaLayoutWidgetFooter
 *
 * @since   Kunena 6.2
 */
class WidgetButton extends KunenaLayout
{
    public mixed $url = null;

    public mixed $name = null;

    public mixed $scope = null;

    public mixed $type = null;

    public mixed $primary = null;

    public mixed $normal = null;

    public mixed $icon = null;

    public $id;

    public mixed $pullright = null;

    public mixed $modal = null;

    public mixed $success = null;
}
