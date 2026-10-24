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

namespace Kunena\Forum\Site\Layout\User\Ban;

\defined('_JEXEC') or die;

use Kunena\Forum\Libraries\Layout\KunenaLayout;

/**
 * KunenaLayoutUserItem
 *
 * @since   Kunena 6.1
 */
class UserBanManager extends KunenaLayout
{
    public mixed $user = null;

    public mixed $output = null;

    public mixed $headerText = null;

    public mixed $pagination = null;

    public mixed $config = null;

    public mixed $me = null;

    public mixed $profile = null;

    public mixed $userBans = null;

    public mixed $moreUri = null;

    public mixed $embedded = null;
}
