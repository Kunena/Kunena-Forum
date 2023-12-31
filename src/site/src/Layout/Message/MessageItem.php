<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.Topic
 *
 * @copyright       Copyright (C) 2008 - 2024 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\Message;

\defined('_JEXEC') or die;

use Kunena\Forum\Libraries\Layout\KunenaLayout;

/**
 * KunenaLayoutMessageList
 *
 * @since   Kunena 6.2
 */
class MessageItem extends KunenaLayout
{
    public $profile;

    public $reportMessageLink;

    public $category;

    public $ipLink;

    public $location;

    public $headerText;

    public $pagination;

    public $user;

    public $output;

    public $config;

    public $message;

    public $topic;

    public $detail;

    public $ktemplate;

    public $candisplaymail;

    public $captchaEnabled;

    public $thankyou;

    public $total_thankyou;

    public $more_thankyou;

    public $thankyou_delete;

    public $numLink;
}
