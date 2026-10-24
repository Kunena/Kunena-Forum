<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Widget
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Widget\Statistics;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\KunenaStatistics;

/**
 * Class ComponentKunenaControllerWidgetStatisticsDisplay
 *
 * @since   Kunena 4.0
 */
class WidgetStatisticsDisplay extends KunenaControllerDisplay
{
    /**
     * @var     object
     * @since   Kunena 6.0
     */
    public mixed $config = null;

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    public mixed $latestMemberLink = null;

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    public mixed $statisticsUrl = null;

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    protected mixed $name = 'Widget/Statistics';

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

    /**
     * Prepare statistics box display.
     *
     * @return  boolean
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    protected function before()
    {
        parent::before();

        $this->config = KunenaConfig::getInstance();

        if (!$this->config->showStats) {
            throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), '404');
        }

        $statistics = KunenaStatistics::getInstance();
        $statistics->loadGeneral();
        $this->setProperties($statistics);

        $this->latestMemberLink = KunenaFactory::getUser(\intval($this->lastUserId))->getLink(null, null, '');
        $this->statisticsUrl    = KunenaFactory::getProfile()->getStatisticsURL();

        return true;
    }
}
