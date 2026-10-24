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
class TopicEdit extends KunenaLayout
{
    public mixed $k = null;

    public mixed $wa = null;

    public mixed $doc = null;

    public mixed $UserCanPostImage = null;

    public mixed $editorType = null;

    public mixed $canSubscribe = null;

    public mixed $subscriptionsChecked = null;

    public mixed $postAnonymous = null;

    public mixed $privateMessage = null;

    public mixed $poll = null;

    public mixed $allowedExtensions = null;

    public mixed $action = null;

    public mixed $selectcatlist = null;

    public mixed $selected = null;

    public mixed $captchaEnabled = null;

    public mixed $topicIcons = null;

    public $message;

    public mixed $topic = null;

    public mixed $category = null;

    public mixed $ktemplate = null;

    public mixed $subscribed = null;

    public mixed $captchaHtml = null;

    public mixed $config = null;

    public mixed $pagination = null;

    public mixed $headerText = null;

    public mixed $user = null;

    public mixed $output = null;

    public mixed $modified_reason = null;

    public mixed $attachments = null;

    public mixed $catid = null;
}
