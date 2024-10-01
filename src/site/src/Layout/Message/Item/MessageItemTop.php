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
 * @since   Kunena 6.3
 */
class MessageItemTop extends KunenaLayout
{
    public $thankyou_delete;

    public $more_thankyou;

    public $numLink;

    public $total_thankyou;

    public $thankyou;

    public $captchaEnabled;

    public $candisplaymail;

    public $detail;

    public $location;

    public $ipLink;

    public $reportMessageLink;

    public $profile;
}
