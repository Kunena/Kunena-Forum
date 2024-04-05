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

namespace Kunena\Forum\Site\Layout\User\Edit;

\defined('_JEXEC') or die;

use Kunena\Forum\Libraries\Layout\KunenaLayout;

/**
 * KunenaLayoutUserItem
 *
 * @since   Kunena 6.2
 */
class UserEditUser extends KunenaLayout
{
    public $output;

    public $user;

    public $headerText;

    public $pagination;

    public $config;

    public $profile;

    public $changeUsername;

    public $frontendForm;
}
