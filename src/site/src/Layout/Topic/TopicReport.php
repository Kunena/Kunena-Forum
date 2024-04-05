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

namespace Kunena\Forum\Site\Layout\Topic;

\defined('_JEXEC') or die;

use Kunena\Forum\Libraries\Layout\KunenaLayout;

/**
 * KunenaLayoutTopicList
 *
 * @since   Kunena 6.2
 */
class TopicReport extends KunenaLayout
{
    public $output;

    public $user;

    public $headerText;

    public $pagination;

    public $config;

    public $topic;

    public $message;

    public $uri;

    public $category;
}
