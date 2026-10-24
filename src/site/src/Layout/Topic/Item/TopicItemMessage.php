<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.Topic
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\Topic\Item;

\defined('_JEXEC') or die;

use Kunena\Forum\Libraries\Layout\KunenaLayout;

/**
 * KunenaLayoutTopicList
 *
 * @since   Kunena 6.2
 */
class TopicItemMessage extends KunenaLayout
{
    public mixed $headerText = null;

    public mixed $pagination = null;

    public mixed $user = null;

    public mixed $output = null;

    public mixed $config = null;

    public $message;

    public mixed $topic = null;

    public mixed $category = null;

    public mixed $profile = null;

    public mixed $reportMessageLink = null;

    public mixed $ipLink = null;

    public mixed $location = null;

    public mixed $detail = null;

    public mixed $ktemplate = null;

    public mixed $candisplaymail = null;

    public mixed $captchaEnabled = null;

    public mixed $thankyou = null;

    public mixed $total_thankyou = null;

    public mixed $more_thankyou = null;

    public mixed $thankyou_delete = null;

    public mixed $pmHasBody = null;
}
