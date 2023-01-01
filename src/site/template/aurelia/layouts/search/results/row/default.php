<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Search
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Icons\KunenaIcons;
use Kunena\Forum\Libraries\Template\KunenaTemplate;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

$message              = $this->message;
$topic                = $message->getTopic();
$category             = $topic->getCategory();
$author               = $message->getAuthor();
$isReply              = $message->id != $topic->first_post_id;
$config               = KunenaFactory::getConfig();
$name                 = $config->username ? $author->username : $author->name;
$me                   = isset($this->me) ? $this->me : KunenaUserHelper::getMyself();
$this->ktemplate      = KunenaFactory::getTemplate();
$subjectlengthmessage = $this->ktemplate->params->get('SubjectLengthMessage', 20);

?>
<div id="kunena_search_results" class="row">
    <div class="col-md-2 text-center">
        <ul class="list-unstyled text-center profilebox">
            <li>
                <strong><?php echo $author->getLink(null, null, 'nofollow', '', null, $topic->getCategory()->id); ?></strong>
            </li>
            <li><?php echo $author->getLink($author->getAvatarImage(KunenaFactory::getTemplate()->params->get('avatarType'), 'post')); ?></li>
        </ul>
    </div>
    <div class="col-md-10">
        <small class="text-muted float-end d-none d-sm-inline-block" style="margin-top:-5px;"> <?php echo KunenaIcons::clock(); ?> <?php echo $message->getTime()->toSpan(); ?><?php if ($message->modified_time) : ?> - <?php echo KunenaIcons::edit() . ' ' . $message->getModifiedTime()->toSpan(); ?>
        <?php endif; ?></small>
        <div class="badger-left badger-info <?php if ($message->getAuthor()->isModerator()) : ?> badger-moderator <?php endif; ?> message-<?php echo $message->getState(); ?> khistory">
            <div class="mykmsg-header alert alert-secondary">
                <?php
                $title   = KunenaMessage::getInstance()->getsubstr($this->escape($message->subject), 0, $subjectlengthmessage);
                $langstr = $isReply ? 'COM_KUNENA_MESSAGE_REPLIED_NEW' : 'COM_KUNENA_MESSAGE_CREATED_NEW';
                echo Text::sprintf($langstr, $message->getAuthor()->getLink(), $this->getTopicLink($topic, $message, null, null, KunenaTemplate::getInstance()->tooltips() . ' topictitle', $category, true, false)); ?>
            </div>
            <div class="kmessage">
                <?php if (!$isReply) :
                    echo $message->displayField('message');
                else :
                    echo (!$me->userid && $config->teaser) ? Text::_('COM_KUNENA_TEASER_TEXT') : $this->message->displayField('message');
                endif; ?>
            </div>
        </div>
    </div>
</div>