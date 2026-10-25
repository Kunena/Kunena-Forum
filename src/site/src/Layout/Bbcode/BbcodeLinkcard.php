<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.BBCode
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\Bbcode;

\defined('_JEXEC') or die;

use Kunena\Forum\Libraries\Layout\KunenaLayout;

/**
 * BbcodeLinkcard: the card of a link in a message.
 *
 * @since   Kunena 8.0
 */
class BbcodeLinkcard extends KunenaLayout
{
    public mixed $url = null;

    public mixed $title = null;

    public mixed $description = null;

    public mixed $image = null;

    public mixed $site = null;
}
