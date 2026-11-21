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

/*
 * The layout Cards: a topic as a card. The table of the list stays (one cell over all the columns), the style sheet lays the cards out
 * in a grid (assets/scss/layoutsets/_cards.scss): the author and the time, the marks, the title, the numbers and the last post.
 */

$topic           = $this->topic;
$userTopic       = $topic->getUserTopic();
$config          = KunenaConfig::getInstance();
$topicPages      = $topic->getPagination(null, $config->messagesPerPage, 3);
$this->ktemplate = KunenaFactory::getTemplate();
$category        = $topic->getCategory();
$tooltips        = KunenaTemplate::getInstance()->tooltips();
$starter         = $topic->getFirstPostAuthor();
$avatar          = $config->avatarOnCategory ? $starter->getAvatarImage($this->ktemplate->params->get('avatarType'), 'thumb') : '';
$columns         = empty($this->checkbox) ? 4 : 5;
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

if (!empty($this->spacing)) : ?>
    <tr class="kcontenttablespacer">
        <td colspan="<?php echo $columns; ?>" class="p-0"></td>
    </tr>
<?php endif; ?>

<tr class="category<?php echo $this->escape($category->class_sfx) . $txt; ?> agora-row-card" data-topic-id="<?php echo (int) $topic->id; ?>" data-last-post="<?php echo (int) $topic->last_post_id; ?>">
    <td colspan="<?php echo $columns; ?>">
        <article class="agora-card<?php echo $topic->unread ? ' topic-item-unread' : ''; ?>">
            <header class="agora-card-head">
                <?php if ($avatar) : ?>
                    <span class="agora-card-avatar"><?php echo $starter->getLink($avatar, Text::sprintf('COM_KUNENA_VIEW_USER_LINK_TITLE', $starter->getName()), '', '', null, $category->id); ?></span>
                <?php endif; ?>
                <span class="agora-card-who">
                    <?php echo $topic->getAuthor()->getLink(null, Text::sprintf('COM_KUNENA_VIEW_USER_LINK_TITLE', $starter->getName()), '', '', $tooltips, $category->id); ?>
                    <?php if ($config->postDateFormat != 'none') : ?>
                        <span class="agora-card-time"><?php echo $topic->getFirstPostTime()->toKunena('config_postDateFormat'); ?></span>
                    <?php endif; ?>
                </span>
                <span class="agora-card-marks">
                    <?php if ($topic->ordering) : ?>
                        <span class="badge agora-badge-sticky"><?php echo Text::_('COM_KUNENA_TEMPLATE_AGORA_STATE_STICKY'); ?></span>
                    <?php endif; ?>
                    <?php if ($topic->locked != 0) : ?>
                        <?php echo $this->subLayout('Widget/Lock')->set('topic', $topic); ?>
                    <?php endif; ?>
                    <?php if ($userTopic->favorite) : ?>
                        <span title="<?php echo Text::_('COM_KUNENA_FAVORITE'); ?>"><?php echo KunenaIcons::star(); ?></span>
                    <?php endif; ?>
                    <?php if ($topic->attachments) : ?>
                        <span title="<?php echo Text::_('COM_KUNENA_ATTACH'); ?>"><?php echo KunenaIcons::attach(); ?></span>
                    <?php endif; ?>
                    <?php if ($topic->poll_id && $category->allowPolls) : ?>
                        <span title="<?php echo Text::_('COM_KUNENA_ADMIN_POLLS'); ?>"><?php echo KunenaIcons::poll(); ?></span>
                    <?php endif; ?>
                </span>
            </header>

            <h3 class="agora-card-title">
                <?php
                if ($this->ktemplate->params->get('labels') != 0) {
                    echo $this->subLayout('Widget/Label')->set('topic', $topic)->setLayout('default');
                }

                if ($topic->unread) {
                    echo $this->getTopicLink($topic, 'unread', $this->escape($topic->subject) . ' <span class="badge agora-badge-new">' . (int) $topic->unread . ' '
                        . Text::_('COM_KUNENA_A_GEN_NEWCHAR') . '</span>', null, $tooltips . ' topictitle', $category, true, true);
                } else {
                    echo $this->getTopicLink($topic, null, null, null, $tooltips . ' topictitle', $category, true, false);
                }
                ?>
            </h3>

            <?php echo $this->subLayout('Widget/Rating')->set('config', $config)->set('category', $category)->set('topic', $topic)
                ->set('reviewCount', $topic->getReviewCount())->setLayout('default'); ?>
            <?php echo KunenaTopicTags::render((int) $topic->id, 'd-block'); ?>
            <span class="ktopic-category"><?php echo $this->getCategoryLink($category, null, $category->description, $tooltips); ?></span>

            <footer class="agora-card-foot">
                <span class="replies"><span class="repliesnum"><?php echo $this->formatLargeNumber($topic->getReplies()); ?></span> <?php echo Text::_('COM_KUNENA_GEN_REPLIES'); ?></span>
                <span class="views"><span class="viewsnum"><?php echo $this->formatLargeNumber($topic->hits); ?></span> <?php echo Text::_('COM_KUNENA_GEN_HITS'); ?></span>
                <span class="lastpostlink">
                    <?php echo $this->getTopicLink($topic, 'last', Text::_('COM_KUNENA_GEN_LAST_POST'), null, $tooltips, $category, false, true); ?>
                    <?php echo $topic->getLastPostTime()->toKunena('config_postDateFormat'); ?>
                </span>
            </footer>

            <?php echo $this->subLayout('Widget/Pagination/List')->set('pagination', $topicPages)->setLayout('simple'); ?>

            <?php if (!empty($this->checkbox)) : ?>
                <label class="agora-card-check">
                    <input class="kcheck" type="checkbox" name="topics[<?php echo $topic->displayField('id'); ?>]" value="1"
                           aria-label="<?php echo $this->escape(Text::sprintf('COM_KUNENA_SELECT_TOPIC_NAMED', $topic->subject)); ?>" />
                </label>
            <?php endif; ?>
        </article>
    </td>
</tr>
