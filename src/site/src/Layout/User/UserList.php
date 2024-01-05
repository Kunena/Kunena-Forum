<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.User
 *
 * @copyright       Copyright (C) 2008 - 2024 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\User;

\defined('_JEXEC') or die;

use Exception;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\User\KunenaUser;

/**
 * KunenaLayoutUserItem
 *
 * @since   Kunena 6.1
 */
class UserList extends KunenaLayout
{
    /**
     * @var     KunenaUser
     * @since   Kunena 6.0
     */
    public $me;

    /**
     * @var     KunenaConfig
     * @since   Kunena 6.0
     */
    public $config;

    /**
     * @var     KunenaUser
     * @since   Kunena 6.0
     */
    public $user;

    public $output;

    public $headerText;

    public $pagination;

    public $state;

    public $total;

    public $users;

    public $ktemplate;
}
