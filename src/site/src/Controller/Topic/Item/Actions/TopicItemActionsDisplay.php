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

namespace Kunena\Forum\Site\Controller\Topic\Item\Actions;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Session\Session;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Event\KunenaGetButtonsEvent;
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicLockReason;
use Kunena\Forum\Libraries\Icons\KunenaIcons;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * Class ComponentTopicControllerItemActionsDisplay
 *
 * @since   Kunena 4.0
 */
class TopicItemActionsDisplay extends KunenaControllerDisplay
{
    /**
     * @var     KunenaTopic
     * @since   Kunena 6.0
     */
    public mixed $topic = null;

    /**
     * @var     array
     * @since   Kunena 6.0
     */
    public mixed $topicButtons = null;

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    protected mixed $name = 'Topic/Item/Actions';

    /**
     * Prepare topic actions display.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    protected function before()
    {
        parent::before();

        $id = $this->input->getInt('id');

        $this->topic = KunenaTopic::getInstance($id);

        $catid  = $this->topic->category_id;
        $token  = Session::getFormToken();
        $Itemid = (int) KunenaRoute::getItemID("index.php?option=com_kunena&view=topic&catid={$catid}&id={$id}");

        $task   = "index.php?option=com_kunena&view=topic&task=%s&catid={$catid}&id={$id}&Itemid={$Itemid}&{$token}=1";
        $layout = "index.php?option=com_kunena&view=topic&layout=%s&catid={$catid}&id={$id}&Itemid={$Itemid}";

        $userTopic          = $this->topic->getUserTopic();
        $template           = KunenaFactory::getTemplate();
        $this->topicButtons = new Registry();

        $fullactions   = $template->params->get('fullactions');

        $button = $fullactions ? true : false;

        if ($this->config->readOnly) {
            throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_NO_ACCESS'), '401');
        }

        if ($this->topic->isAuthorised('reply')) {
            $this->topicButtons->set(
                'reply',
                $this->getButton(\sprintf($layout, 'reply'), 'reply', 'topic', 'communication', false, $button, KunenaIcons::undo())
            );

            // Dropdown Item version
            $this->topicButtons->set(
                'reply_dropdown',
                $this->getButton(\sprintf($layout, 'reply'), 'reply', 'topic', 'communication', false, true, KunenaIcons::undo())
            );
        }

        if ($userTopic->subscribed) {
            // User can always remove existing subscription.
            $this->topicButtons->set(
                'subscribe',
                $this->getButton(\sprintf($task, 'unsubscribe'), 'unsubscribe', 'topic', 'user', false, $button, KunenaIcons::emailOpen())
            );
            // Dropdown Item version
            $this->topicButtons->set(
                'subscribe_dropdown',
                $this->getButton(\sprintf($task, 'unsubscribe'), 'unsubscribe', 'topic', 'user', false, true, KunenaIcons::emailOpen())
            );
        } elseif ($this->topic->isAuthorised('subscribe')) {
            // Add subscribe topic button.
            $this->topicButtons->set(
                'subscribe',
                $this->getButton(\sprintf($task, 'subscribe'), 'subscribe', 'topic', 'user', false, $button, KunenaIcons::email())
            );
            // Dropdown Item version
            $this->topicButtons->set(
                'subscribe_dropdown',
                $this->getButton(\sprintf($task, 'subscribe'), 'subscribe', 'topic', 'user', false, true, KunenaIcons::email())
            );
        }

        // Mark as solved: the author of the topic (when it is not locked) and the moderators.
        $moderator = KunenaUserHelper::getMyself()->isModerator($this->topic->getCategory());
        $owner     = KunenaUserHelper::getMyself()->exists() && (int) $this->topic->first_post_userid === (int) KunenaUserHelper::getMyself()->userid && !$this->topic->locked;

        if ($this->config->topicIcons && ($moderator || $owner) && isset($template->getUserTopicIcons()[KunenaTopic::ICON_SOLVED])) {
            $solvedName = (int) $this->topic->icon_id === KunenaTopic::ICON_SOLVED ? 'unsolved' : 'solved';

            $this->topicButtons->set(
                'solved',
                $this->getButton(\sprintf($task, 'solved'), $solvedName, 'topic', 'user', false, $button, KunenaIcons::check())
            );
            // Dropdown Item version
            $this->topicButtons->set(
                'solved_dropdown',
                $this->getButton(\sprintf($task, 'solved'), $solvedName, 'topic', 'user', false, true, KunenaIcons::check())
            );
        }

        // The reasons of a lock: the author of the topic locks it with one of them when the setting allows it.
        $reasons      = KunenaTopicLockReason::parse(isset($this->config->lockReasons) ? (string) $this->config->lockReasons : '');
        $lockByAuthor = isset($this->config->lockByAuthor) && (bool) $this->config->lockByAuthor;

        if (!$moderator && KunenaTopicLockReason::authorMayLock($this->topic, KunenaUserHelper::getMyself(), $lockByAuthor, $reasons)) {
            $this->topicButtons->set('lock_author', $this->getLockReasonButtons($task, $reasons, 'user'));
        }

        if ($userTopic->favorite) {
            // User can always remove existing favorite.
            $this->topicButtons->set(
                'favorite',
                $this->getButton(\sprintf($task, 'unfavorite'), 'unfavorite', 'topic', 'user', false, $button, KunenaIcons::star())
            );
            // Dropdown Item version
            $this->topicButtons->set(
                'favorite_dropdown',
                $this->getButton(\sprintf($task, 'unfavorite'), 'unfavorite', 'topic', 'user', false, true, KunenaIcons::star())
            );
        } elseif ($this->topic->isAuthorised('favorite')) {
            // Add favorite topic button.
            $this->topicButtons->set(
                'favorite',
                $this->getButton(\sprintf($task, 'favorite'), 'favorite', 'topic', 'user', false, $button, KunenaIcons::starOpen())
            );
            // Dropdown Item version
            $this->topicButtons->set(
                'favorite_dropdown',
                $this->getButton(\sprintf($task, 'favorite'), 'favorite', 'topic', 'user', false, true, KunenaIcons::starOpen())
            );
        }

        if ($this->topic->getCategory()->isAuthorised('moderate')) {
            // Add moderator specific buttons.
            $sticky = $this->topic->ordering ? 'unsticky' : 'sticky';
            $lock   = $this->topic->locked ? 'unlock' : 'lock';

            $this->topicButtons->set(
                'sticky',
                $this->getButton(\sprintf($task, $sticky), $sticky, 'topic', 'moderation', false, $button, KunenaIcons::sticky())
            );

            // Dropdown Item version
            $this->topicButtons->set(
                'sticky_dropdown',
                $this->getButton(\sprintf($task, $sticky), $sticky, 'topic', 'moderation', false, true, KunenaIcons::sticky())
            );

            $this->topicButtons->set(
                'lock',
                $this->getButton(\sprintf($task, $lock), $lock, 'topic', 'moderation', false, $button, KunenaIcons::lock())
            );

            // Dropdown Item version
            $this->topicButtons->set(
                'lock_dropdown',
                $this->getButton(\sprintf($task, $lock), $lock, 'topic', 'moderation', false, true, KunenaIcons::lock())
            );

            // One more item per reason of the list, after the lock without a reason.
            if (!$this->topic->locked && $reasons) {
                $this->topicButtons->set('lock_reasons_dropdown', $this->getLockReasonButtons($task, $reasons, 'moderation'));
            }

            $this->topicButtons->set(
                'moderate',
                $this->getButton(\sprintf($layout, 'moderate'), 'moderate', 'topic', 'moderation', false, $button, KunenaIcons::shield())
            );

            // Dropdown Item version
            $this->topicButtons->set(
                'moderate_dropdown',
                $this->getButton(\sprintf($layout, 'moderate'), 'moderate', 'topic', 'moderation', false, true, KunenaIcons::shield())
            );

            if ($this->topic->hold == 1) {
                $this->topicButtons->set(
                    'approve',
                    $this->getButton(\sprintf($task, 'approve'), 'moderate', 'topic', 'moderation', false, $button, KunenaIcons::poll_add())
                );

                // Dropdown Item version
                $this->topicButtons->set(
                    'approve_dropdown',
                    $this->getButton(\sprintf($task, 'approve'), 'moderate', 'topic', 'moderation', false, true, KunenaIcons::poll_add())
                );
            }

            if ($this->topic->hold == 1 || $this->topic->hold == 0) {
                $this->topicButtons->set(
                    'delete',
                    $this->getButton(\sprintf($task, 'delete'), 'delete', 'topic', 'moderation', false, $button, KunenaIcons::delete())->set('confirm', Text::_('COM_KUNENA_TOPIC_DELETE_CONFIRM'))
                );

                // Dropdown Item version
                $this->topicButtons->set(
                    'delete_dropdown',
                    $this->getButton(\sprintf($task, 'delete'), 'delete', 'topic', 'moderation', false, true, KunenaIcons::delete())->set('confirm', Text::_('COM_KUNENA_TOPIC_DELETE_CONFIRM'))
                );
            } elseif ($this->topic->hold == 2 || $this->topic->hold == 3) {
                if ($this->topic->isAuthorised('permdelete')) {
                    $this->topicButtons->set(
                        'permdelete',
                        $this->getButton(\sprintf($task, 'permdelete'), 'permdelete', 'topic', 'moderation', false, $button)->set('confirm', Text::_('COM_KUNENA_TOPIC_PERMDELETE_CONFIRM'))
                    );

                    // Dropdown Item version
                    $this->topicButtons->set(
                        'permdelete_dropdown',
                        $this->getButton(\sprintf($task, 'permdelete'), 'permdelete', 'topic', 'moderation', false, true)->set('confirm', Text::_('COM_KUNENA_TOPIC_PERMDELETE_CONFIRM'))
                    );
                }

                if ($this->topic->isAuthorised('undelete')) {
                    $this->topicButtons->set(
                        'undelete',
                        $this->getButton(\sprintf($task, 'undelete'), 'undelete', 'topic', 'moderation', false, $button)
                    );

                    // Dropdown Item version
                    $this->topicButtons->set(
                        'undelete_dropdown',
                        $this->getButton(\sprintf($task, 'undelete'), 'undelete', 'topic', 'moderation', false, true)
                    );
                }
            }
        }

        // Add buttons for changing between different layout modes.
        // The layout in which the user sees the topic now gets no button, the two others do (items of the dropdown Layout).
        if ($template->params->get('enableThreadedLayouts', '0')) {
            $url     = "index.php?option=com_kunena&view=user&task=change&topicLayout=%s&{$token}=1";
            $current = KunenaUserHelper::getMyself()->getTopicLayout();

            foreach (['flat', 'threaded', 'indented'] as $topicLayout) {
                if ($topicLayout != $current) {
                    $this->topicButtons->set(
                        $topicLayout,
                        $this->getButton(\sprintf($url, $topicLayout), $topicLayout, 'layout', 'user', false, true)
                    );
                }
            }
        }

        $dispatcher = Factory::getApplication()->getDispatcher();
        PluginHelper::importPlugin('kunena');

        $dispatcher->dispatch(
            'onKunenaGetButtons',
            new KunenaGetButtonsEvent(
                'onKunenaGetButtons',
                ['context' => 'topic.action', 'buttons' => $this->topicButtons, 'object' => $this]
            )
        );
    }

    /**
     * Get button.
     *
     * @param   string  $url      Target link (do not route it).
     * @param   string  $name     Name of the button.
     * @param   string  $scope    Scope of the button.
     * @param   string  $type     Type of the button.
     * @param   bool    $primary  True if primary button.
     * @param   bool    $normal   Define if the button will have the class btn or btn-small
     * @param   string  $icon     icon
     *
     * @return  KunenaLayout
     *
     * @throws  Exception
     * @since   Kunena 6.0
     */
    public function getButton($url, $name, $scope, $type, $primary = false, $normal = true, $icon = '')
    {
        return KunenaLayout::factory('Widget/Button')
            ->setProperties(
                [
                    'url'     => $url,
                    'name'    => $name,
                    'scope'   => $scope,
                    'type'    => $type,
                    'primary' => $primary,
                    'normal'  => $normal,
                    'icon'    => $icon,
                ]
            );
    }

    /**
     * The dropdown items which lock the topic with a reason, one per reason of the list.
     *
     * @param   string    $task     Target link of the tasks (do not route it), with %s for the task.
     * @param   string[]  $reasons  The reasons of the list (see KunenaTopicLockReason::parse).
     * @param   string    $type     Type of the buttons.
     *
     * @return  KunenaLayout[]
     *
     * @throws  Exception
     * @since   Kunena 8.0
     */
    protected function getLockReasonButtons(string $task, array $reasons, string $type): array
    {
        $buttons = [];

        foreach ($reasons as $reason) {
            $label = Text::sprintf('COM_KUNENA_BUTTON_TOPIC_LOCK_REASON', Text::_($reason));

            $buttons[] = $this->getButton(\sprintf($task, 'lock') . '&reason=' . urlencode($reason), 'lock', 'topic', $type, false, true, KunenaIcons::lock())
                ->set('label', htmlspecialchars($label, ENT_QUOTES, 'UTF-8'));
        }

        return $buttons;
    }
}
