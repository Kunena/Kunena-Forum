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
class TopicEdit extends KunenaLayout
{
    public $k;

    public $wa;

    public $doc;

    public $UserCanPostImage;

    public $editorType;

    public $canSubscribe;

    public $subscriptionsChecked;

    public $postAnonymous;

    public $privateMessage;

    public $poll;

    public $allowedExtensions;

    public $action;

    public $selectcatlist;

    public $selected;

    public $captchaEnabled;

    public $topicIcons;

    public $message;

    public $topic;

    public $category;

    public $ktemplate;

    public $subscribed;

    public $captchaHtml;

    public $config;

    public $pagination;

    public $headerText;

    public $user;

    public $output;

    public $modified_reason;

    public $attachments;

    public $catid;
}
