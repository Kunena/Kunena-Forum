<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Statistics
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Statistics\General;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\KunenaStatistics;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use RuntimeException;

/**
 * Class ComponentStatisticsControllerGeneralDisplay
 *
 * @since   Kunena 4.0
 */
class StatisticsGeneralDisplay extends KunenaControllerDisplay
{
    /**
     * @var     string
     * @since   Kunena 6.0
     */
    public mixed $name = 'Statistics/General';

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

    /**
     * Prepare general statistics display.
     *
     * @return  mixed
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function before()
    {
        parent::before();

        $Itemid = $this->input->getInt('Itemid');

        if (!$Itemid && $this->config->sefRedirect) {
            try {
                $itemid = KunenaRoute::fixMissingItemID();

                $params = [
                    'option' => 'com_kunena',
                    'view'   => 'statistics',
                    'Itemid' => $itemid,
                ];

                return $this->app->redirect(KunenaRoute::_('index.php?' . http_build_query(KunenaRoute::keepQuery($params)), false));
            } catch (Exception $e) {
                throw new RuntimeException('Failed to create controller: ' . $e->getMessage());
            }
        }

        if (!$this->config->showStats) {
            throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), '404');
        }

        if (!$this->config->statsLinkAllowed && $this->app->getIdentity()->guest) {
            throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), '401');
        }

        $statistics = KunenaStatistics::getInstance();
        $statistics->loadAll();
        $this->setProperties($statistics);

        $this->latestMemberLink = KunenaFactory::getUser((int) $this->lastUserId)->getLink(null, null, '');
        $this->userlistUrl      = KunenaFactory::getProfile()->getUserListUrl();
    }

    /**
     * Prepare document.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function prepareDocument()
    {
        $menu_item       = $this->app->getMenu()->getActive();
        $componentParams = ComponentHelper::getParams('com_config');
        $robots          = $componentParams->get('robots');

        if ($robots == 'noindex, follow') {
            $this->setMetaData('robots', 'noindex, follow');
        } elseif ($robots == 'index, nofollow') {
            $this->setMetaData('robots', 'index, nofollow');
        } elseif ($robots == 'noindex, nofollow') {
            $this->setMetaData('robots', 'noindex, nofollow');
        } else {
            $this->setMetaData('robots', 'index, follow');
        }

        if ($menu_item) {
            $params             = $menu_item->getParams();
            $params_title       = $params->get('page_title');
            $params_description = $params->get('menu-meta_description');
            $params_robots      = $params->get('robots');

            if (!empty($params_title)) {
                $title = $params->get('page_title');
                $this->setTitle($title);
            } else {
                $this->setTitle(Text::_('COM_KUNENA_STAT_FORUMSTATS'));
            }

            if (!empty($params_description)) {
                $description = $params->get('menu-meta_description');
                $this->setDescription($description);
            } else {
                $description = Text::_('COM_KUNENA_STAT_FORUMSTATS') . ': ' . $this->config->boardTitle;
                $this->setDescription($description);
            }

            if (!empty($params_robots)) {
                $robots = $params->get('robots');
                $this->setMetaData('robots', $robots);
            }
        }
    }
}
