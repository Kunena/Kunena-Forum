<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.Message.Item
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\Message\Item;

\defined('_JEXEC') or die;

use Kunena\Forum\Libraries\Layout\KunenaLayout;

/**
 * KunenaLayoutTopicList
 *
 * @since   Kunena 6.2
 */
class MessageItemActions extends KunenaLayout
{
    public $ktemplate;

    public $ipLink;

    public $url;

    public $quickReply;

    public $message_closed;

    public $category;

    public $messageButtons;

    public $message;

    public $topic;

    public $config;

    public $pagination;

    public $headerText;
    
    public $user;
    
    public $output;
}
