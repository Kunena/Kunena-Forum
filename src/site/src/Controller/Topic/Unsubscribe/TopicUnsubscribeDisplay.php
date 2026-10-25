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

namespace Kunena\Forum\Site\Controller\Topic\Unsubscribe;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Email\KunenaEmailUnsubscribe;
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * Class TopicUnsubscribeDisplay: asks to confirm the unsubscribe link of a notification email.
 *
 * The page changes nothing, mail programs and virus scanners open links without anybody reading them.
 *
 * @since   Kunena 8.0
 */
class TopicUnsubscribeDisplay extends KunenaControllerDisplay
{
    /**
     * @var     object
     * @since   Kunena 8.0
     */
    public mixed $unsubscribe = null;

    /**
     * @var     string
     * @since   Kunena 8.0
     */
    protected mixed $name = 'Topic/Unsubscribe';

    /**
     * Check the link and find what it unsubscribes from.
     *
     * @return  void
     *
     * @throws  KunenaExceptionAuthorise
     * @since   Kunena 8.0
     */
    protected function before()
    {
        parent::before();

        $type = $this->input->getCmd('type', '');
        $id   = $this->input->getInt('id', 0);
        $uid  = $this->input->getInt('uid', 0);
        $sig  = $this->input->getString('sig', '');

        if (!KunenaEmailUnsubscribe::verify($type, $id, $uid, $sig)) {
            throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_EMAIL_UNSUBSCRIBE_INVALID'), 403);
        }

        if ($type === KunenaEmailUnsubscribe::TOPIC) {
            $item = KunenaTopicHelper::get($id);
        } elseif ($type === KunenaEmailUnsubscribe::FOLLOW) {
            $item = KunenaUserHelper::get($id);
        } else {
            $item = KunenaCategoryHelper::get($id);
        }

        if (!$item->exists()) {
            throw new KunenaExceptionAuthorise(Text::_('COM_KUNENA_EMAIL_UNSUBSCRIBE_INVALID'), 404);
        }

        $this->unsubscribe = (object) [
            'type' => $type,
            'id'   => $id,
            'uid'  => $uid,
            'sig'  => $sig,
            'name' => $type === KunenaEmailUnsubscribe::TOPIC ? $item->subject : ($type === KunenaEmailUnsubscribe::FOLLOW ? $item->getName() : $item->name),
        ];
    }

    /**
     * Prepare document.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 8.0
     */
    protected function prepareDocument()
    {
        $this->setTitle(Text::_('COM_KUNENA_EMAIL_UNSUBSCRIBE'));
        $this->setDescription(Text::_('COM_KUNENA_EMAIL_UNSUBSCRIBE'));

        // Nobody should find this page in a search engine.
        $this->app->getDocument()->setMetaData('robots', 'noindex, nofollow');
    }
}
