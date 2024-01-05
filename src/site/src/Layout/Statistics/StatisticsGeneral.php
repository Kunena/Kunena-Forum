<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.Search
 *
 * @copyright       Copyright (C) 2008 - 2024 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\Statistics;

\defined('_JEXEC') or die;

use Kunena\Forum\Libraries\Layout\KunenaLayout;

/**
 * KunenaLayoutSearchForm
 *
 * @since   Kunena 6.2
 */
class StatisticsGeneral extends KunenaLayout
{
    public $name;

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

    public $latestMemberLink;

    public $userlistUrl;
}
