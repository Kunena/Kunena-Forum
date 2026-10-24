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

use Kunena\Forum\Libraries\Layout\KunenaLayout;

/**
 * KunenaLayoutUserItem
 *
 * @since   Kunena 6.2
 */
class UserProfile extends KunenaLayout
{
    public mixed $user = null;

    public mixed $candisplaymail = null;

    public mixed $config = null;

    public mixed $ktemplate = null;

    public mixed $topic_starter = null;

    public mixed $category_id = null;
}
