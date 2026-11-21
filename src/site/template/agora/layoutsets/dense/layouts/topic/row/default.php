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
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicTags;
use Kunena\Forum\Libraries\Icons\KunenaIcons;
use Kunena\Forum\Libraries\Template\KunenaTemplate;
use Kunena\Forum\Libraries\User\KunenaUserFollow;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/*
 * The layout Dense: a topic in one dense row. A dot for its state (new replies, sticky, locked, open), the title with one line of
 * meta (number, category, author, start), the replies and the views as two numbers, the time of the last post. The same columns as
 * the row of Aurelia, so the head of the table stays right.
 */

$topic           = $this->topic;
$userTopic       = $topic->getUserTopic();
$config          = KunenaConfig::getInstance();
$topicPages      = $topic->getPagination(null, $config->messagesPerPage, 3);
$this->ktemplate = KunenaFactory::getTemplate();
$category        = $topic->getCategory();
$tooltips        = KunenaTemplate::getInstance()->tooltips();
$txt             = '';

if ($topic->ordering) {
    $txt = $category->class_sfx ? $txt . '' : $txt . '-stickymsg';
}

if ($topic->hold == 1) {
    $txt .= ' unapproved';
} elseif ($topic->hold) {
    $txt .= ' deleted';
}

if ($topic->moved_id > 0) {
    $txt .= ' moved';
}

if ($category->published == 0) {
    $txt                 = '-grey';
    $category->class_sfx = '';
}

if ($topic->unread) {
    $state = ['unread', Text::_('COM_KUNENA_TEMPLATE_AGORA_STATE_UNREAD')];
} elseif ($topic->locked) {
    $state = ['locked', Text::_('COM_KUNENA_LOCKED_TOPIC')];
} elseif ($topic->ordering) {
    $state = ['sticky', Text::_('COM_KUNENA_TEMPLATE_AGORA_STATE_STICKY')];
} else {
    $state = ['open', Text::_('COM_KUNENA_TEMPLATE_AGORA_STATE_OPEN')];
}

if (!empty($this->spacing)) : ?>
    <tr class="kcontenttablespacer">
        <td colspan="<?php echo empty($this->checkbox) ? 4 : 5; ?>" class="p-0" style="height: .5rem"></td>
    </tr>
<?php endif; ?>

<tr class="category<?php echo $this->escape($category->class_sfx) . $txt; ?> agora-row-compact" data-topic-id="<?php echo (int) $topic->id; ?>" data-last-post="<?php echo (int) $topic->last_post_id; ?>">
    <th scope="row" class="center d-none d-md-table-cell agora-state-cell<?php echo $topic->unread ? ' topic-item-unread' : ''; ?>">
        <span class="agora-state agora-state-<?php echo $state[0]; ?>" role="img" aria-label="<?php echo $this->escape($state[1]); ?>" title="<?php echo $this->escape($state[1]); ?>"></span>
    </th>

    <td>
        <div class="agora-row-title">
            <?php
            if ($this->ktemplate->params->get('labels') != 0) {
                echo $this->subLayout('Widget/Label')->set('topic', $topic)->setLayout('default');
            }

            if ($topic->unread) {
                echo $this->getTopicLink($topic, 'unread', $this->escape($topic->subject) . '<sup class="knewchar" dir="ltr">(' . (int) $topic->unread . ' '
                    . Text::_('COM_KUNENA_A_GEN_NEWCHAR') . ')</sup>', null, $tooltips . ' topictitle', $category, true, true);
            } else {
                echo $this->getTopicLink($topic, null, null, null, $tooltips . ' topictitle', $category, true, false);
            }

            echo $this->subLayout('Widget/Rating')->set('config', $config)->set('category', $category)->set('topic', $topic)
                ->set('reviewCount', $topic->getReviewCount())->setLayout('default');
            echo ' ' . KunenaTopicTags::render((int) $topic->id, 'd-inline-block');
            ?>
            <span class="agora-row-icons">
                <?php if (KunenaUserFollow::isFollowing(KunenaUserHelper::getMyself()->userid, (int) $topic->first_post_userid)) : ?>
                    <span class="kfollowed" title="<?php echo Text::sprintf('COM_KUNENA_FOLLOW_MARKER', $this->escape($topic->getAuthor()->getName())); ?>"><?php echo KunenaIcons::bookmark(); ?></span>
                <?php endif; ?>
                <?php if ($userTopic->favorite) : ?>
                    <span title="<?php echo Text::_('COM_KUNENA_FAVORITE'); ?>"><?php echo KunenaIcons::star(); ?></span>
                <?php endif; ?>
                <?php if ($userTopic->posts) : ?>
                    <span title="<?php echo Text::_('COM_KUNENA_MYPOSTS'); ?>"><?php echo KunenaIcons::flag(); ?></span>
                <?php endif; ?>
                <?php if ($topic->attachments) : ?>
                    <span title="<?php echo Text::_('COM_KUNENA_ATTACH'); ?>"><?php echo KunenaIcons::attach(); ?></span>
                <?php endif; ?>
                <?php if ($topic->poll_id && $category->allowPolls) : ?>
                    <span title="<?php echo Text::_('COM_KUNENA_ADMIN_POLLS'); ?>"><?php echo KunenaIcons::poll(); ?></span>
                <?php endif; ?>
                <?php if ($topic->locked != 0) : ?>
                    <?php echo $this->subLayout('Widget/Lock')->set('topic', $topic); ?>
                <?php endif; ?>
            </span>
        </div>
        <div class="agora-row-meta">
            <span>#<?php echo (int) $topic->id; ?></span>
            <span class="ktopic-category"><?php echo $this->getCategoryLink($category, null, $category->description, $tooltips); ?></span>
            <span><?php echo $topic->getAuthor()->getLink(null, Text::sprintf('COM_KUNENA_VIEW_USER_LINK_TITLE', $topic->getFirstPostAuthor()->getName()), '', '', $tooltips, $category->id); ?></span>
            <?php if ($config->postDateFormat != 'none') : ?>
                <span><?php echo $topic->getFirstPostTime()->toKunena('config_postDateFormat'); ?></span>
            <?php endif; ?>
            <span class="d-md-none"><?php echo Text::_('COM_KUNENA_GEN_REPLIES'); ?> <?php echo $this->formatLargeNumber($topic->getReplies()); ?></span>
            <span class="d-md-none"><?php echo $this->getTopicLink($topic, 'last', $topic->getLastPostTime()->toKunena('config_postDateFormat'), null, null, $category, false, true); ?></span>
        </div>
        <?php echo $this->subLayout('Widget/Pagination/List')->set('pagination', $topicPages)->setLayout('simple'); ?>
    </td>

    <td class="d-none d-md-table-cell agora-row-numbers">
        <span class="replies"><span class="repliesnum"><?php echo $this->formatLargeNumber($topic->getReplies()); ?></span> <span class="agora-row-label"><?php echo Text::_('COM_KUNENA_GEN_REPLIES'); ?></span></span>
        <span class="views"><span class="viewsnum"><?php echo $this->formatLargeNumber($topic->hits); ?></span> <span class="agora-row-label"><?php echo Text::_('COM_KUNENA_GEN_HITS'); ?></span></span>
    </td>

    <td class="d-none d-md-table-cell agora-row-last">
        <span class="lastpostlink"><?php echo $this->getTopicLink($topic, 'last', $topic->getLastPostTime()->toKunena('config_postDateFormat'), null, $tooltips, $category, false, true); ?></span>
        <span class="agora-row-by"><?php echo Text::_('COM_KUNENA_BY') . ' ' . $topic->getLastPostAuthor()->getLink(null, Text::sprintf('COM_KUNENA_VIEW_USER_LINK_TITLE', $topic->getLastPostAuthor()->getName()), '', '', $tooltips, $category->id); ?></span>
    </td>

    <?php if (!empty($this->checkbox)) : ?>
        <td class="center">
            <label>
                <input class="kcheck" type="checkbox" name="topics[<?php echo $topic->displayField('id'); ?>]" value="1"
                       aria-label="<?php echo $this->escape(Text::sprintf('COM_KUNENA_SELECT_TOPIC_NAMED', $topic->subject)); ?>" />
            </label>
        </td>
    <?php endif; ?>
</tr>
