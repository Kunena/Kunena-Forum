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
 * @since   Kunena 7.2
 */
class BbcodeLinkcard extends KunenaLayout
{
    public $url;

    public $title;

    public $description;

    public $image;

    public $site;
}
