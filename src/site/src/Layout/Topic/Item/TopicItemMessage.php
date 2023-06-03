<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.Topic
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
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
    public $headerText;

    public $pagination;

    public $user;

    public $output;

    public $config;

    public $message;

    public $topic;

    public $category;

    public $profile;

    public $reportMessageLink;

    public $ipLink;

    public $location;

    public $detail;

    public $ktemplate;

    public $candisplaymail;

    public $captchaEnabled;

    public $thankyou;

    public $total_thankyou;

    public $more_thankyou;

    public $thankyou_delete;
}
