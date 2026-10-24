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
class TopicItem extends KunenaLayout
{
    public mixed $ktemplate = null;

    public mixed $params = null;

    public mixed $image = null;

    public mixed $quickReply = null;

    public mixed $userTopic = null;

    public mixed $threaded = null;

    public mixed $messages = null;

    public $message;

    public mixed $categorylist = null;

    public mixed $catParams = null;

    public mixed $cache = null;

    public mixed $allowed = null;

    public mixed $topic = null;

    public mixed $category = null;

    public mixed $config = null;

    public mixed $pagination = null;

    public mixed $headerText = null;

    public mixed $user = null;

    public mixed $output = null;

    public mixed $pmHasBody = null;
}
