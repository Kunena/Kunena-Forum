<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Agora
 * @subpackage      Layout.Search
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Icons\KunenaIcons;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

$message              = $this->message;
$topic                = $message->getTopic();
$category             = $topic->getCategory();
$author               = $message->getAuthor();
$isReply              = $message->id != $topic->first_post_id;
$me                   = isset($this->me) ? $this->me : KunenaUserHelper::getMyself();
$subjectlengthmessage = $this->ktemplate->params->get('SubjectLengthMessage', 20);
?>
<article class="kunena-search-result agora-search-result message-<?php echo $message->getState(); ?><?php echo $author->isModerator() ? ' agora-search-result-moderator' : ''; ?>">
    <div class="agora-search-result-head">
        <span class="agora-search-result-avatar">
            <?php echo $author->getLink($author->getAvatarImage($this->ktemplate->params->get('avatarType'), 'thumb')); ?>
        </span>
        <div class="agora-search-result-title mykmsg-header">
            <?php
            $title   = KunenaMessage::getInstance()->getsubstr($this->escape($message->subject), 0, $subjectlengthmessage);
            $langstr = $isReply ? 'COM_KUNENA_MESSAGE_REPLIED_NEW' : 'COM_KUNENA_MESSAGE_CREATED_NEW';
            echo Text::sprintf($langstr, $author->getLink(), $this->getTopicLink($topic, $message, null, null, $this->ktemplate->tooltips() . ' topictitle', $category, true, false)); ?>
            <div class="agora-search-result-meta">
                <span class="agora-search-result-category"><?php echo $this->escape($category->name); ?></span>
                <span><?php echo KunenaIcons::clock(); ?> <?php echo $message->getTime()->toSpan(); ?></span>
                <?php if ($message->modified_time) : ?>
                    <span><?php echo KunenaIcons::edit(); ?> <?php echo $message->getModifiedTime()->toSpan(); ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="kmessage agora-search-result-text">
        <?php if (!$isReply) :
            echo $message->displayField('message');
        else :
            echo (!$me->userid && $category->hidesRepliesForGuests()) ? Text::_('COM_KUNENA_TEASER_TEXT') : $this->message->displayField('message');
        endif; ?>
    </div>
</article>
