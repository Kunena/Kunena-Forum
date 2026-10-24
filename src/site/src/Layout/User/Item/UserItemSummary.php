<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.User
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\User\Item;

\defined('_JEXEC') or die;

use Kunena\Forum\Libraries\Layout\KunenaLayout;

/**
 * KunenaLayoutUserItem
 *
 * @since   Kunena 6.1
 */
class UserItemSummary extends KunenaLayout
{
    public mixed $profile = null;

    public mixed $config = null;

    public mixed $candisplaymail = null;

    public mixed $me = null;

    public mixed $private = null;

    public mixed $points = null;

    public mixed $medals = null;

    public mixed $socials = null;

    public mixed $avatar = null;

    public mixed $banInfo = null;
}
