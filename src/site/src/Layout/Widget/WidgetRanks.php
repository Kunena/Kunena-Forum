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
 * KunenaLayoutTopicEditEditor
 *
 * @since  Kunena 6.2
 */
class WidgetRanks extends KunenaLayout
{
    public mixed $type = null;

    public mixed $topicicontype = null;

    public mixed $rank = null;

    public mixed $stylesecond = null;

    public mixed $stylethird = null;

    public mixed $stylefourth = null;

    public mixed $stylelast = null;
}
