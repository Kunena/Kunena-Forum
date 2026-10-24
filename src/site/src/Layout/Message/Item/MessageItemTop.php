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
    public mixed $thankyou_delete = null;

    public mixed $more_thankyou = null;

    public mixed $numLink = null;

    public mixed $total_thankyou = null;

    public mixed $thankyou = null;

    public mixed $captchaEnabled = null;

    public mixed $candisplaymail = null;

    public mixed $detail = null;

    public mixed $location = null;

    public mixed $ipLink = null;

    public mixed $reportMessageLink = null;

    public mixed $profile = null;

    public mixed $pmHasBody = null;
}
