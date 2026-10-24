<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.Topic
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\Message;

\defined('_JEXEC') or die;

use Kunena\Forum\Libraries\Layout\KunenaLayout;

/**
 * KunenaLayoutMessageList
 *
 * @since   Kunena 6.2
 */
class MessageItem extends KunenaLayout
{
    public mixed $profile = null;

    public mixed $reportMessageLink = null;

    public mixed $category = null;

    public mixed $ipLink = null;

    public mixed $location = null;

    public mixed $headerText = null;

    public mixed $pagination = null;

    public mixed $user = null;

    public mixed $output = null;

    public mixed $config = null;

    public $message;

    public mixed $topic = null;

    public mixed $detail = null;

    public mixed $ktemplate = null;

    public mixed $candisplaymail = null;

    public mixed $captchaEnabled = null;

    public mixed $thankyou = null;

    public mixed $total_thankyou = null;

    public mixed $more_thankyou = null;

    public mixed $thankyou_delete = null;

    public mixed $numLink = null;

    public mixed $pmHasBody = null;

    /**
     * Check if the guest, moderator or registred can see attachments
     *
     * @param   array  $attachments  Array containing KunenaAttachment object
     *
     * @return  boolean
     *
     * @since   Kunena 6.4.3
     */
    public function canSeeAttachments($attachments)
    {
        $attachs = $this->message->getNbAttachments();

        if (!$this->me->exists()) {
            if ($attachs->totalPrivate == \count($attachments)) {
                return false;
            }
        } elseif ($this->me->isModerator($this->topic->getCategory())) {
            if ($attachs->totalPrivate == \count($attachments)) {
                return true;
            }
        } else {
            if ($attachs->inline != \count($attachments) && $attachs->totalPrivate != \count($attachments)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the link number of the specific message
     *
     * @param   int  $location  The identifier of the message to build the link
     *
     * @return  integer
     *
     * @since   Kunena 6.4.3
     */
    public function getNumlink($location)
    {
        if ($this->config->orderingSystem == 'mesid') {
            $numlink = $location;
        } else {
            $numlink = $this->message->replynum;
        }

        return $numlink;
    }
}
