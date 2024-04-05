<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Controllers
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\Controller;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * Constructor.
 *
 * @param   array                $config   An optional associative array of configuration settings.
 * Recognized key values include 'name', 'default_task', 'model_path', and
 * 'view_path' (this list is not meant to be comprehensive).
 * @param   MVCFactoryInterface  $factory  The factory.
 * @param   CMSApplication       $app      The Application for the dispatcher
 * @param   Input                $input    Input
 *
 * @since   Kunena 6.3.0-BETA3
 */
class TrashsController extends AdminController
{
    /**
     * @var     null|string
     * @since   Kunena 6.0
     */
    protected $baseurl = null;

    /**
     * Construct
     *
     * @param   array  $config  config
     *
     * @throws  Exception
     * @since   Kunena 2.0
     */
    public function __construct($config = [], MVCFactoryInterface $factory = null, $app = null, $input = null)
    {
        parent::__construct($config, $factory, $app, $input);

        $layout = $this->input->get('layout', 'messages');
        $this->baseurl = 'administrator/index.php?option=com_kunena&view=trashs&layout=' . $layout;
    }

    /**
     * Purge
     *
     * @return  void
     *
     * @throws  null
     * @throws  Exception
     * @since   Kunena 2.0
     */
    public function purge(): void
    {
        if (!Session::checkToken()) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        $cid = $this->input->get('cid', array(), 'post', 'array');
        $cid = ArrayHelper::toInteger($cid);

        $type = $this->input->getCmd('type', 'topics', 'post');

        if (empty($cid)) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_A_NO_MESSAGES_SELECTED'), 'notice');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        if ($type == 'topics') {
            $topics = KunenaTopicHelper::getTopics($cid, 'none');

            foreach ($topics as $topic) {
                try {
                    $topic->delete();
                } catch (Exception $e) {
                    $this->app->enqueueMessage($e->getMessage(), 'error');
                }
            }

            KunenaTopicHelper::recount($cid);
            KunenaCategoryHelper::recount($topic->getCategory()->id);
            $this->app->enqueueMessage(Text::_('COM_KUNENA_TRASH_DELETE_TOPICS_DONE'), 'success');
        } elseif ($type == 'messages') {
            $messages = KunenaMessageHelper::getMessages($cid, 'none');

            foreach ($messages as $message) {
                try {
                    $message->delete();
                } catch (Exception $e) {
                    $this->app->enqueueMessage($e->getMessage(), 'error');
                }

                $target  = KunenaMessageHelper::get($message->id);
                $topic   = KunenaTopicHelper::get($target->getTopic());

                if ($topic->attachments > 0) {
                    $topic->attachments = $topic->attachments - 1;

                    try {
                        $topic->save(false);
                    } catch (Exception $e) {
                        $this->app->enqueueMessage($e->getMessage(), 'error');
                    }
                }
            }

            KunenaTopicHelper::recount($cid);
            KunenaCategoryHelper::recount($topic->getCategory()->id);
            $this->app->enqueueMessage(Text::_('COM_KUNENA_TRASH_DELETE_MESSAGES_DONE'), 'success');
        }

        $this->setRedirect(KunenaRoute::_($this->baseurl, false));
    }

    /**
     * Restore
     *
     * @return  void
     *
     * @throws  null
     * @throws  Exception
     * @since   Kunena 2.0
     */
    public function restore(): void
    {
        if (!Session::checkToken()) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_ERROR_TOKEN'), 'error');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        $cid = $this->input->get('cid', [], 'array');
        $cid = ArrayHelper::toInteger($cid, []);

        $type = $this->input->getCmd('type', 'topics');

        if (empty($cid)) {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_A_NO_MESSAGES_SELECTED'), 'notice');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        $nbItems = 0;

        if ($type == 'messages') {
            $messages = KunenaMessageHelper::getMessages($cid, 'none');

            foreach ($messages as $target) {
                try {
                    $target->publish();
                } catch (Exception $e) {
                    $this->app->enqueueMessage($e->getMessage(), 'error');
                }

                $nbItems++;
            }
        } elseif ($type == 'topics') {
            $topics = KunenaTopicHelper::getTopics($cid, 'none');

            foreach ($topics as $target) {
                if ($target->getState() == KunenaForum::UNAPPROVED) {
                    $status = KunenaForum::UNAPPROVED;
                } else {
                    $status = KunenaForum::PUBLISHED;
                }

                try {
                    $target->publish($status);
                } catch (Exception $e) {
                    $this->app->enqueueMessage($e->getMessage(), 'error');
                }

                $nbItems++;
            }
        } else {
            $this->app->enqueueMessage(Text::_('COM_KUNENA_A_NO_MESSAGES_SELECTED'), 'notice');
            $this->setRedirect(KunenaRoute::_($this->baseurl, false));

            return;
        }

        if ($nbItems > 0) {
            $this->app->enqueueMessage(Text::sprintf('COM_KUNENA_TRASH_ITEMS_RESTORE_DONE', $nbItems), 'success');
        }

        KunenaUserHelper::recount();
        KunenaTopicHelper::recount();
        KunenaCategoryHelper::recount();

        $this->setRedirect(KunenaRoute::_($this->baseurl, false));
    }
}
