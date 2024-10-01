<?php

/**
 * Kunena Component
 * 
 * @package         Kunena.Site
 * @subpackage      Layout.widget
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\Widget;

\defined('_JEXEC') or die;

use Kunena\Forum\Libraries\Layout\KunenaLayout;

/**
 * KunenaLayoutTopicEditEditor
 *
 * @since  K6.1
 */
class WidgetStatistics extends KunenaLayout
{
    public $output;

    public $user;

    public $headerText;

    public $pagination;

    public $config;

    public $latestMemberLink;

    public $statisticsUrl;

    public $lastUserId;

    public $memberCount;

    public $sectionCount;

    public $categoryCount;

    public $topicCount;

    public $messageCount;

    public $todayTopicCount;

    public $yesterdayTopicCount;

    public $todayReplyCount;

    public $yesterdayReplyCount;

    public $topTopics;

    public $topPosters;

    public $topProfiles;

    public $topPolls;

    public $topThanks;

    public $top;

    public $showGenStats;

    public $showPopUserStats;

    public $showPopSubjectStats;

    public $showPopPollStats;

    public $showPopThankYouStats;

    public $showStats;
}
