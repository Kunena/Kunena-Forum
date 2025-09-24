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
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\Attachment\KunenaAttachment;

/**
 * KunenaLayoutMessageList
 *
 * @since   Kunena 6.2
 */
class MessageItem extends KunenaLayout
{
    public $profile;

    public $reportMessageLink;

    public $category;

    public $ipLink;

    public $location;

    public $headerText;

    public $pagination;

    public $user;

    public $output;

    public $config;

    public $message;

    public $topic;

    public $detail;

    public $ktemplate;

    public $candisplaymail;

    public $captchaEnabled;

    public $thankyou;

    public $total_thankyou;

    public $more_thankyou;

    public $thankyou_delete;

    public $numLink;

    /**
     * Check if the guest, moderator or registred can see attachments
     *
     * @param   Array   $attachments  Array containing KunenaAttachment object
     *
     * @since   Kunena 6.4.3
     */
    public function canSeeAttachments($attachments) {
        $attachs = $this->message->getNbAttachments();
        
        if (!$this->me->exists()) {
            if ($attachs->totalPrivate == count($attachments)) {
                return false;
            }
        } elseif ($this->me->isModerator($this->topic->getCategory())) {
            if ($attachs->totalPrivate == count($attachments)) {
                return true;
            }
        } else {
            if ($attachs->inline != count($attachments) && $attachs->totalPrivate != count($attachments)) {
                return true;
            }
        }
        
        return false;
    }    
    
    /**
     * Get the link number of the specific message
     *
     * @param   int            $location The identifier of the message to build the link
     *
     * @since   Kunena 6.4.3
     */
    public function getNumlink($location) {
        if ($this->config->orderingSystem == 'mesid') {
            $numlink = $location;
        } else {
            $numlink = $this->message->replynum;
        }
        
        return $numlink;
    }
}
