<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.Search
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
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
    public mixed $name = null;

    public mixed $lastUserId = null;

    public mixed $memberCount = null;

    public mixed $sectionCount = null;

    public mixed $categoryCount = null;

    public mixed $topicCount = null;

    public mixed $messageCount = null;

    public mixed $todayTopicCount = null;

    public mixed $yesterdayTopicCount = null;

    public mixed $todayReplyCount = null;

    public mixed $yesterdayReplyCount = null;

    public mixed $topTopics = null;

    public mixed $topPosters = null;

    public mixed $topProfiles = null;

    public mixed $topPolls = null;

    public mixed $topThanks = null;

    public mixed $top = null;

    public mixed $showGenStats = null;

    public mixed $showPopUserStats = null;

    public mixed $showPopSubjectStats = null;

    public mixed $showPopPollStats = null;

    public mixed $showPopThankYouStats = null;

    public mixed $showStats = null;

    public mixed $latestMemberLink = null;

    public mixed $userlistUrl = null;
}
