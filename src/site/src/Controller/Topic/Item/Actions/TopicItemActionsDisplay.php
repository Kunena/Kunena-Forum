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

namespace Kunena\Forum\Site\Controller\Topic\Item\Actions;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Session\Session;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\Icons\KunenaIcons;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\Route\KunenaRoute;

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
    public $topic;

    /**
     * @var     array
     * @since   Kunena 6.0
     */
    public $topicButtons;

    /**
     * @var     string
     * @since   Kunena 6.0
     */
    protected $name = 'Topic/Item/Actions';

    /**
     * Prepare topic actions display.
     *
     * @return  void
     *
     * @throws  null
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
        $Itemid = KunenaRoute::fixMissingItemID();

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
                $this->getButton(sprintf($layout, 'reply'), 'reply', 'topic', 'communication', false, $button, KunenaIcons::undo())
            );

            // Dropdown Item version
            $this->topicButtons->set(
                'reply_dropdown',
                $this->getButton(sprintf($layout, 'reply'), 'reply', 'topic', 'communication', false, true, KunenaIcons::undo())
            );
        }

        if ($userTopic->subscribed) {
            // User can always remove existing subscription.
            $this->topicButtons->set(
                'subscribe',
                $this->getButton(sprintf($task, 'unsubscribe'), 'unsubscribe', 'topic', 'user', false, $button, KunenaIcons::emailOpen())
            );
            // Dropdown Item version
            $this->topicButtons->set(
                'subscribe_dropdown',
                $this->getButton(sprintf($task, 'unsubscribe'), 'unsubscribe', 'topic', 'user', false, true, KunenaIcons::emailOpen())
            );
        } elseif ($this->topic->isAuthorised('subscribe')) {
            // Add subscribe topic button.
            $this->topicButtons->set(
                'subscribe',
                $this->getButton(sprintf($task, 'subscribe'), 'subscribe', 'topic', 'user', false, $button, KunenaIcons::email())
            );
            // Dropdown Item version
            $this->topicButtons->set(
                'subscribe_dropdown',
                $this->getButton(sprintf($task, 'subscribe'), 'subscribe', 'topic', 'user', false, true, KunenaIcons::email())
            );
        }

        if ($userTopic->favorite) {
            // User can always remove existing favorite.
            $this->topicButtons->set(
                'favorite',
                $this->getButton(sprintf($task, 'unfavorite'), 'unfavorite', 'topic', 'user', false, $button, KunenaIcons::star())
            );
            // Dropdown Item version
            $this->topicButtons->set(
                'favorite_dropdown',
                $this->getButton(sprintf($task, 'unfavorite'), 'unfavorite', 'topic', 'user', false, true, KunenaIcons::star())
            );
        } elseif ($this->topic->isAuthorised('favorite')) {
            // Add favorite topic button.
            $this->topicButtons->set(
                'favorite',
                $this->getButton(sprintf($task, 'favorite'), 'favorite', 'topic', 'user', false, $button, KunenaIcons::starOpen())
            );
            // Dropdown Item version
            $this->topicButtons->set(
                'favorite_dropdown',
                $this->getButton(sprintf($task, 'favorite'), 'favorite', 'topic', 'user', false, true, KunenaIcons::starOpen())
            );
        }

        if ($this->topic->getCategory()->isAuthorised('moderate')) {
            // Add moderator specific buttons.
            $sticky = $this->topic->ordering ? 'unsticky' : 'sticky';
            $lock   = $this->topic->locked ? 'unlock' : 'lock';

            $this->topicButtons->set(
                'sticky',
                $this->getButton(sprintf($task, $sticky), $sticky, 'topic', 'moderation', false, $button, KunenaIcons::sticky())
            );

            // Dropdown Item version
            $this->topicButtons->set(
                'sticky_dropdown',
                $this->getButton(sprintf($task, $sticky), $sticky, 'topic', 'moderation', false, true, KunenaIcons::sticky())
            );

            $this->topicButtons->set(
                'lock',
                $this->getButton(sprintf($task, $lock), $lock, 'topic', 'moderation', false, $button, KunenaIcons::lock())
            );

            // Dropdown Item version
            $this->topicButtons->set(
                'lock_dropdown',
                $this->getButton(sprintf($task, $lock), $lock, 'topic', 'moderation', false, true, KunenaIcons::lock())
            );

            $this->topicButtons->set(
                'moderate',
                $this->getButton(sprintf($layout, 'moderate'), 'moderate', 'topic', 'moderation', false, $button, KunenaIcons::shield())
            );

            // Dropdown Item version
            $this->topicButtons->set(
                'moderate_dropdown',
                $this->getButton(sprintf($layout, 'moderate'), 'moderate', 'topic', 'moderation', false, true, KunenaIcons::shield())
            );

            if ($this->topic->hold == 1) {
                $this->topicButtons->set(
                    'approve',
                    $this->getButton(sprintf($task, 'approve'), 'moderate', 'topic', 'moderation', false, $button, KunenaIcons::poll_add())
                );

                // Dropdown Item version
                $this->topicButtons->set(
                    'approve_dropdown',
                    $this->getButton(sprintf($task, 'approve'), 'moderate', 'topic', 'moderation', false, true, KunenaIcons::poll_add())
                );
            }

            if ($this->topic->hold == 1 || $this->topic->hold == 0) {
                $this->topicButtons->set(
                    'delete',
                    $this->getButton(sprintf($task, 'delete'), 'delete', 'topic', 'moderation', false, $button, KunenaIcons::delete())
                );

                // Dropdown Item version
                $this->topicButtons->set(
                    'delete_dropdown',
                    $this->getButton(sprintf($task, 'delete'), 'delete', 'topic', 'moderation', false, true, KunenaIcons::delete())
                );
            } elseif ($this->topic->hold == 2 || $this->topic->hold == 3) {
                if ($this->topic->isAuthorised('permdelete')) {
                    $this->topicButtons->set(
                        'permdelete',
                        $this->getButton(sprintf($task, 'permdelete'), 'permdelete', 'topic', 'moderation', false, $button)
                    );

                    // Dropdown Item version
                    $this->topicButtons->set(
                        'permdelete_dropdown',
                        $this->getButton(sprintf($task, 'permdelete'), 'permdelete', 'topic', 'moderation', false, true)
                    );
                }

                if ($this->topic->isAuthorised('undelete')) {
                    $this->topicButtons->set(
                        'undelete',
                        $this->getButton(sprintf($task, 'undelete'), 'undelete', 'topic', 'moderation', false, $button)
                    );

                    // Dropdown Item version
                    $this->topicButtons->set(
                        'undelete_dropdown',
                        $this->getButton(sprintf($task, 'undelete'), 'undelete', 'topic', 'moderation', false, true)
                    );
                }
            }
        }

        // Add buttons for changing between different layout modes.
        if ($this->config->enableThreadedLayouts) {
            $url = "index.php?option=com_kunena&view=user&task=change&topicLayout=%s&{$token}=1";

            if ($this->layout != 'default') {
                $this->topicButtons->set(
                    'flat',
                    $this->getButton(sprintf($url, 'flat'), 'flat', 'layout', 'user', false, $button)
                );
            }

            if ($this->layout != 'threaded') {
                $this->topicButtons->set(
                    'threaded',
                    $this->getButton(sprintf($url, 'threaded'), 'threaded', 'layout', 'user', false, $button)
                );
            }

            if ($this->layout != 'indented') {
                $this->topicButtons->set(
                    'indented',
                    $this->getButton(sprintf($url, 'indented'), 'indented', 'layout', 'user', false, $button)
                );
            }
        }

        PluginHelper::importPlugin('kunena');

        $this->app->triggerEvent('onKunenaGetButtons', ['topic.action', $this->topicButtons, $this]);
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
     * @throws  null
     * @since   Kunena 6.0
     */
    public function getButton($url, $name, $scope, $type, $primary = false, $normal = true, $icon = '')
    {
        return KunenaLayout::factory('Widget/Button')
            ->setProperties(
                ['url'   => $url, 'name' => $name,
                 'scope' => $scope, 'type' => $type, 'primary' => $primary, 'normal' => $normal, 'icon' => $icon, ]
            );
    }
}
