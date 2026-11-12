<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Topic
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Topic\Poll;

\defined('_JEXEC') or die();

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Forum\Topic\Poll\KunenaPoll;
use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use stdClass;

/**
 * Class ComponentTopicControllerPollDisplay
 *
 * @since   Kunena 4.0
 */
class TopicPollDisplay extends KunenaControllerDisplay
{
    /**
     * @var     KunenaUser
     * @since   Kunena 6.0
     */
    public mixed $me = null;

    /**
     * @var     KunenaCategory
     * @since   Kunena 6.0
     */
    public mixed $category = null;

    /**
     * @var     KunenaTopic
     * @since   Kunena 6.0
     */
    public mixed $topic = null;

    /**
     * @var     KunenaPoll
     * @since   Kunena 6.0
     */
    public mixed $poll = null;

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    public mixed $uri = null;

    public mixed $usercount            = null;
    public mixed $userhasvoted         = null;
    public mixed $show_title           = null;
    public mixed $intervalTimeToLive   = null;
    public mixed $users_voted_list     = null;
    public mixed $users_voted_morelist = null;
    public mixed $canChangeVote        = null;
    public mixed $waitToChangeVote     = null;

    /**
     * Prepare poll display.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function before()
    {
        parent::before();

        $this->topic    = KunenaTopicHelper::get($this->input->getInt('id'));
        $this->category = $this->topic->getCategory();
        $this->config   = KunenaFactory::getConfig();
        $this->me       = KunenaUserHelper::getMyself();

        // Need to check if poll is allowed in this category.
        $this->topic->tryAuthorise('poll.read');

        $this->poll      = $this->topic->getPoll();
        $this->usercount = $this->poll->getUserCount();
        $usersvoted      = $this->poll->getUsers();

        // The votes of the user are a number (the sum of his votes), not an object.
        if ((int) $this->poll->getMyVotes() > 0) {
            $this->userhasvoted           = new stdClass();
            $this->userhasvoted->lastvote = $this->poll->getLastVoteId();
        } else {
            $this->userhasvoted = 0;
        }

        // Check if the poll has ended or if it still active
        if ($this->poll->polltimetolive != '1000-01-01 00:00:00') {
            $origin                   = new DateTimeImmutable('now');
            $target                   = new DateTimeImmutable($this->poll->polltimetolive);
            $this->intervalTimeToLive = $origin->diff($target);
        }

        // A voter may change his vote when not only one vote is allowed: now, or once the time between votes has passed.
        $this->canChangeVote    = false;
        $this->waitToChangeVote = false;

        if ($this->me->exists() && $this->userhasvoted && !$this->config->pollAllowVoteOne) {
            $this->canChangeVote = $this->topic->isAuthorised('poll.vote') && $this->topic->isAuthorised('reply');

            $ended = $this->poll->polltimetolive != '1000-01-01 00:00:00' && $this->poll->getTimeToLive() < Factory::getDate()->toUnix();

            if (!$this->canChangeVote && !$this->topic->locked && !$ended) {
                // The time between two votes, "HH:MM:SS", compared in seconds as in KunenaTopic::authoriseVote().
                $parts = array_reverse(array_map('intval', explode(':', (string) $this->config->pollTimeBtVotes)));
                $wait  = ($parts[0] ?? 0) + 60 * ($parts[1] ?? 0) + 3600 * ($parts[2] ?? 0);

                // The vote time is stored in UTC
                $lastVote = new DateTimeImmutable((string) $this->poll->getMyTime(), new DateTimeZone('UTC'));

                $this->waitToChangeVote = $wait > 0 && time() - $lastVote->getTimestamp() < $wait;
            }
        }

        if (!empty($this->alwaysVote)) {
            // Authorise forced vote.
            $this->topic->tryAuthorise('poll.vote');
            $this->topic->tryAuthorise('reply');
            $this->name = 'Topic/Poll/Vote';
        } elseif (!$this->userhasvoted && $this->topic->isAuthorised('poll.vote') && $this->topic->isAuthorised('reply')) {
            $this->name = 'Topic/Poll/Vote';
        } else {
            $this->name = 'Topic/Poll/Results';

            $this->show_title = true;

            $this->users_voted_list     = [];
            $this->users_voted_morelist = [];

            if ($this->config->pollResultsUserslist && !empty($usersvoted)) {
                $userids_votes = [];

                foreach ($usersvoted as $userid => $vote) {
                    $userids_votes[] = $userid;
                }

                $loaded_users = KunenaUserHelper::loadUsers($userids_votes);

                $i = 0;

                foreach ($loaded_users as $userid => $user) {
                    if ($i <= '4') {
                        $this->users_voted_list[] = $loaded_users[$userid]->getLink();
                    } else {
                        $this->users_voted_morelist[] = $loaded_users[$userid]->getLink();
                    }

                    $i++;
                }
            }
        }

        $this->uri = "index.php?option=com_kunena&view=topic&layout=poll&catid={$this->category->id}&id={$this->topic->id}";
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
        $menu_item = $this->app->getMenu()->getActive();

        if ($menu_item) {
            $params             = $menu_item->getParams();
            $params_title       = $params->get('page_title');
            $params_description = $params->get('menu-meta_description');

            if (!empty($params_title)) {
                $title = $params->get('page_title');
                $this->setTitle($title);
            } else {
                $this->setTitle(Text::_('COM_KUNENA_POLL_NAME') . ' ' . KunenaParser::parseText($this->poll->title));
            }

            if (!empty($params_description)) {
                $description = $params->get('menu-meta_description');
                $this->setDescription($description);
            } else {
                $this->setDescription(Text::_('COM_KUNENA_POLL_NAME') . ' ' . KunenaParser::parseText($this->poll->title));
            }
        }
    }
}
