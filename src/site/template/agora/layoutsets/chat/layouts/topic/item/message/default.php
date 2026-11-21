<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Agora
 * @subpackage      Layout.Topic
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\User\KunenaUserIgnore;

/*
 * The layout Chat: a message as a line of a chat. A small avatar beside a speech bubble which has the name, the rank and the
 * mark of the topic starter on top, then the message, its actions and the quick reply. The messages of the logged in user are on
 * the other side, in the accent colour (assets/scss/layoutsets/_chat.scss). The full profile of the author stays one click away (the name).
 */

$ignoredAuthor = $this->me->exists() && $this->message->userid != $this->me->userid
    && KunenaUserIgnore::isIgnoring($this->me->userid, $this->message->userid);

if ($ignoredAuthor) {
    ob_start();
}

$topicStarter = $this->topic->first_post_userid == $this->message->userid;
$own          = $this->me->exists() && $this->message->userid == $this->me->userid;
$quick        = $this->ktemplate->params->get('quick');
$avatar       = $this->profile->getAvatarImage($this->ktemplate->params->get('avatarType'), 'thumb');
$rank         = $this->profile->exists() ? (string) $this->profile->getRank($this->category->id, 'title') : '';
?>
<div class="agora-chat message<?php echo $own ? ' agora-chat-own' : ''; ?>">
    <div class="agora-chat-avatar">
        <?php if ($avatar) : ?>
            <?php echo $this->profile->getLink($avatar, Text::sprintf('COM_KUNENA_VIEW_USER_LINK_TITLE', $this->profile->getName()), '', '', null, $this->category->id); ?>
        <?php endif; ?>
    </div>
    <div class="agora-chat-bubble message-<?php echo $this->message->getState(); ?>">
        <div class="agora-chat-who">
            <?php echo $this->profile->getLink(null, null, '', '', null, $this->category->id); ?>
            <?php if ($topicStarter) : ?>
                <span class="badge agora-chat-starter"><?php echo Text::_('COM_KUNENA_TEMPLATE_AGORA_TOPIC_STARTER'); ?></span>
            <?php endif; ?>
            <?php if ($rank !== '') : ?>
                <span class="agora-chat-rank"><?php echo $this->escape($rank); ?></span>
            <?php endif; ?>
        </div>
        <?php echo $this->subLayout('Message/Item')->setProperties($this->getProperties()); ?>
        <?php echo $this->subRequest('Message/Item/Actions')->set('mesid', $this->message->id); ?>

        <?php if ($quick != 2) : ?>
            <?php echo $this->subLayout('Message/Edit')->set('message', $this->message)
                ->set('captchaEnabled', $this->captchaEnabled)->setLayout('quickReply'); ?>
        <?php endif; ?>
    </div>
</div>

<?php
if ($ignoredAuthor) :
    $messageHtml = ob_get_clean();
    ?>
    <details class="kignored-message mb-3">
        <summary class="p-2 border rounded text-muted">
            <?php echo Text::sprintf('COM_KUNENA_IGNORE_HIDDEN_MESSAGE', $this->escape($this->profile->getName())); ?>
            &mdash; <span class="link-primary"><?php echo Text::_('COM_KUNENA_IGNORE_SHOW'); ?></span>
        </summary>
        <?php echo $messageHtml; ?>
    </details>
<?php endif; ?>

<?php echo $this->subLayout('Widget/Module')->set('position', 'kunena_msg_' . $this->message->replynum);
