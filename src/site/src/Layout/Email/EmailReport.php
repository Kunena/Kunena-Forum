<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.Email.Subscription
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\Email;

\defined('_JEXEC') or die;

use Kunena\Forum\Libraries\Layout\KunenaLayout;

/**
 * KunenaLayoutAnnouncementListRow
 *
 * @since   Kunena 6.2
 */
class EmailReport extends KunenaLayout
{
    public $mail;

    public $title;

    public $messageLink;
}
