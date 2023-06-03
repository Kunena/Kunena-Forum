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

namespace Kunena\Forum\Site\Layout\Topic;

\defined('_JEXEC') or die;

use Kunena\Forum\Libraries\Layout\KunenaLayout;

/**
 * KunenaLayoutTopicList
 *
 * @since   Kunena 6.2
 */
class TopicItem extends KunenaLayout
{
    public $ktemplate;

    public $params;

    public $image;
 
    public $quickReply;

    public $userTopic;

    public $threaded;

    public $messages;

    public $message;

    public $categorylist;

    public $catParams;

    public $cache;

    public $allowed;

    public $topic;

    public $category;

    public $config;

    public $pagination;

    public $headerText;

    public $user;

    public $output;
}
