<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Topic
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Topic\Listing\Moderator;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicFinder;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageFinder;
use Kunena\Forum\Libraries\Pagination\KunenaPagination;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Forum\Site\Controller\Topic\Listing\ListDisplay;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;

/**
 * Class ComponentTopicControllerListDisplay
 *
 * @since   Kunena 4.0
 */
class TopicListingModeratorDisplay extends ListDisplay
{
    /**
     * Prepare topic list for moderators.
     *
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     * @throws  null
     */
    protected function before()
    {
        parent::before();

        $this->me       = KunenaUserHelper::getMyself();
        $access         = KunenaAccess::getInstance();
        $moreUri        = null;
        $this->embedded = $this->getOptions()->get('embedded', true);

        $params = ComponentHelper::getParams('com_kunena');
        $start  = $this->input->getInt('limitstart', 0);
        $limit  = $this->input->getInt('limit', 0);
        $Itemid = $this->input->getInt('Itemid');

        if (!$Itemid && $this->config->sefRedirect) {
            if ($this->config->moderatorsId) {
                $itemidfix = $this->config->moderatorsId;
            } else {
                $menu      = $this->app->getMenu();
                $getid     = $menu->getItem(KunenaRoute::getItemID("index.php?option=com_kunena&view=topics&layout=moderator"));
                $itemidfix = $getid->id;
            }

            if (!$itemidfix) {
                $itemidfix = KunenaRoute::fixMissingItemID();
            }

            $controller = BaseController::getInstance("kunena");
            $controller->setRedirect(KunenaRoute::_("index.php?option=com_kunena&view=topics&layout=moderator&Itemid={$itemidfix}", false));
            $controller->redirect();
        }

        if ($limit < 1 || $limit > 100) {
            $limit = $this->config->threadsPerPage;
        }

        // Get configuration from menu item.
        $categoryIds = $params->get('topics_categories', []);
        $reverse     = !$params->get('topics_catselection', 1);

        // Make sure that category list is an array.
        if (!\is_array($categoryIds)) {
            $categoryIds = explode(',', $categoryIds);
        }

        if ((!$reverse && empty($categoryIds)) || \in_array(0, $categoryIds)) {
            $categoryIds = false;
        }

        $categories = KunenaCategoryHelper::getCategories($categoryIds, $reverse);

        $threadsId = KunenaMessageHelper::getMessagesFromUsers(array_keys($access->getModerators() + $access->getAdmins()));
        $threadSearch = [];

        foreach ($threadsId as $thread) {
            $threadSearch[] = $thread->thread;
        }

        $threadSearch = implode(',', $threadSearch);

        $finder = new KunenaTopicFinder();
        $this->topics = $finder
            ->filterByCategories($categories)
            ->filterByMoved(false)
            ->filterTopicNotIn($threadSearch)
            ->where('locked', '=', 0)->order('last_post_time', -1)
            ->find();

        $this->pagination = new KunenaPagination($finder->count(), $start, $limit);

        if ($moreUri) {
            $this->pagination->setUri($moreUri);
        }

        if ($this->topics) {
            $this->prepareTopics();
        }

        $actions        = ['delete', 'approve', 'undelete', 'move', 'permdelete'];
        $this->actions = $this->getTopicActions($this->topics, $actions);

        $this->headerText = Text::_('COM_KUNENA_TOPICS_NEEDS_ATTENTION');
    }
}
