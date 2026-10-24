<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.Message.Item
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
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
    public mixed $ktemplate = null;

    public mixed $ipLink = null;

    public mixed $url = null;

    public mixed $quickReply = null;

    public mixed $message_closed = null;

    public mixed $category = null;

    public mixed $messageButtons = null;

    public $message;

    public mixed $topic = null;

    public mixed $config = null;

    public mixed $pagination = null;

    public mixed $headerText = null;

    public mixed $user = null;

    public mixed $output = null;
}
