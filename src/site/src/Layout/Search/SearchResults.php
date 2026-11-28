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

namespace Kunena\Forum\Site\Layout\Search;

\defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Event\KunenaPrepareEvent;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\Html\KunenaHighlight;
use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * KunenaLayoutSearchResults
 *
 * @since   Kunena 4.0
 */
class SearchResults extends KunenaLayout
{
    /**
     * @var     KunenaMessage
     * @since   Kunena 6.0
     */
    public $message;

    /**
     * @var     KunenaCategory
     * @since   Kunena 6.0
     */
    public mixed $category = null;

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    public mixed $categoryLink = null;

    /**
     * @var     array
     * @since   Kunena 6.0
     */
    public mixed $results = null;

    /**
     * @var     KunenaTopic
     * @since   Kunena 6.0
     */
    public mixed $topic = null;

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    public mixed $useravatar = null;

    /**
     * @var     array
     * @since   Kunena 6.0
     */
    public mixed $searchwords = null;

    /**
     * @var     KunenaUser
     * @since   Kunena 6.0
     */
    public mixed $author = null;

    /**
     * @var     KunenaUser
     * @since   Kunena 6.0
     */
    public mixed $topicAuthor = null;

    /**
     * @var     integer
     * @since   Kunena 6.0
     */
    public mixed $topicTime = null;

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    public mixed $subjectHtml = null;

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    public mixed $messageHtml = null;

    public mixed $output = null;

    public mixed $user = null;

    public mixed $headerText = null;

    public mixed $pagination = null;

    public mixed $config = null;

    public mixed $model = null;

    public mixed $total = null;

    public mixed $state = null;

    public mixed $message_ordering = null;

    public mixed $isModerator = null;

    public mixed $error = null;

    /**
     * Method to display the layout of search results
     *
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function displayRows()
    {
        // Run events
        $params = new Registry();
        $params->set('ksource', 'kunena');
        $params->set('kunena_view', 'search');
        $params->set('kunena_layout', 'default');

        PluginHelper::importPlugin('kunena');
        foreach ($this->results as &$result) {
            $prepareEvent = new KunenaPrepareEvent('onKunenaPrepare', [
                'context' => 'kunena.messages',
                'subject' => $result,
                'params'  => $params,
                'page'    => 0,
            ]);
            $this->app->getDispatcher()->dispatch('onKunenaPrepare', $prepareEvent);
        }
        unset($result);

        $userIds           = [];
        $categoryLinkCache = [];
        $profileCache      = [];

        foreach ($this->results as $result) {
            $userId = (int) $result->userid;

            if ($userId) {
                $userIds[$userId] = $userId;
            }

            $category = $result->getCategory();
            $cacheKey = (int) $category->id;

            if (!\array_key_exists($cacheKey, $categoryLinkCache)) {
                $parent = $category->getParent();

                if ($parent) {
                    $categoryLinkCache[$cacheKey] = $this->getCategoryLink($parent) . ' / ' . $this->getCategoryLink($category);
                } else {
                    $categoryLinkCache[$cacheKey] = $this->getCategoryLink($category);
                }
            }
        }

        if (!empty($userIds)) {
            KunenaUserHelper::loadUsers($userIds);
        }

        foreach ($this->results as $this->message) {
            $this->topic        = $this->message->getTopic();
            $this->category     = $this->message->getCategory();
            $this->categoryLink = $categoryLinkCache[(int) $this->category->id] ?? $this->getCategoryLink($this->category);
            $ressubject         = KunenaParser::parseText($this->message->subject);
            // The message as the parent of the text (its author and category: [confidential], [attachment]) and 500 as its
            // length: 500 was given as the parent, so a result had the whole text and its tags had no message.
            $resmessage         = KunenaParser::parseBBCode($this->message->message, $this->message, 500);

            $userId = (int) $this->message->userid;

            if (!\array_key_exists($userId, $profileCache)) {
                $profileCache[$userId] = KunenaFactory::getUser($userId);
            }

            $profile          = $profileCache[$userId];
            $this->useravatar = $profile->getAvatarImage('kavatar', 'post');

            // The words of the search marked in the subject and the message: in their text only, the HTML stays whole.
            $ressubject = KunenaHighlight::html($ressubject, (array) $this->searchwords);
            $resmessage = KunenaHighlight::html($resmessage, (array) $this->searchwords);

            $this->author      = $this->message->getAuthor();
            $this->topicAuthor = $this->topic->getAuthor();
            $this->topicTime   = $this->topic->first_post_time;
            $this->subjectHtml = $ressubject;
            $this->messageHtml = $resmessage;

            $contents = $this->subLayout('Search/Results/Row')->setProperties($this->getProperties());
            echo $contents;
        }
    }
}
