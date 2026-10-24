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

namespace Kunena\Forum\Site\Layout\User;

\defined('_JEXEC') or die;

use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\User\KunenaUser;

/**
 * KunenaLayoutUserItem
 *
 * @since   Kunena 6.1
 */
class UserAttachments extends KunenaLayout
{
    /**
     * @var     KunenaUser
     * @since   Kunena 6.1
     */
    public mixed $profile = null;

    /**
     * @var     KunenaUser
     * @since   Kunena 6.1
     */
    public mixed $me = null;

    /**
     * @var     KunenaConfig
     * @since   Kunena 6.1
     */
    public mixed $config = null;

    /**
     * @var     KunenaUser
     * @since   Kunena 6.1
     */
    public mixed $user = null;

    public mixed $output = null;

    public mixed $headerText = null;

    public mixed $pagination = null;

    public mixed $total = null;

    public mixed $attachments = null;

    public mixed $moreUri = null;

    public mixed $template = null;
}
