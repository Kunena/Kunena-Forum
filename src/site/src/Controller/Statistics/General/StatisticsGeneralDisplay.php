<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Statistics
 *
 * @copyright       Copyright (C) 2008 - 2024 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Statistics\General;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\KunenaStatistics;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Controller\KunenaController;

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
    public $name = 'Statistics/General';

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

    /**
     * Prepare general statistics display.
     *
     * @return  void
     *
     * @throws  Exception
     * @throws  null
     * @since   Kunena 6.0
     */
    protected function before()
    {
        parent::before();

        $Itemid = $this->input->getInt('Itemid');

        if (!$Itemid && $this->config->sefRedirect) {
            $itemid     = KunenaRoute::fixMissingItemID();
            $controller = new KunenaController();
            $controller->setRedirect(KunenaRoute::_("index.php?option=com_kunena&view=statistics&Itemid={$itemid}", false));
            $controller->redirect();
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
