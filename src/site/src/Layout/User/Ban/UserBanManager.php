<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.User
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
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
    public $user;

    public $output;

    public $headerText;

    public $pagination;

    public $config;

    public $me;

    public $profile;

    public $userBans;

    public $moreUri;

    public $embedded;
}
