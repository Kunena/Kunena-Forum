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

/*
 * The layout Chat: the topic of Aurelia in three panes, like a messaging app. On the left the latest topics of the category
 * (the current one marked), in the middle the conversation, on the right the people who wrote on this page. The panes come one under
 * the other on a small screen, the conversation first (assets/scss/layoutsets/_chat.scss).
 */

use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageQuotes;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicNewReplies;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicTags;
use Kunena\Forum\Libraries\Route\KunenaRoute;

Text::script('COM_KUNENA_RATE_LOGIN');
Text::script('COM_KUNENA_RATE_NOT_YOURSELF');
Text::script('COM_KUNENA_RATE_ALLREADY');
Text::script('COM_KUNENA_RATE_SUCCESSFULLY_SAVED');
Text::script('COM_KUNENA_RATE_NOT_ALLOWED_WHEN_BANNED');

Text::script('COM_KUNENA_SOCIAL_EMAIL_LABEL');
Text::script('COM_KUNENA_SOCIAL_X_SOCIAL_LABEL');
Text::script('COM_KUNENA_SOCIAL_FACEBOOK_LABEL');
Text::script('COM_KUNENA_SOCIAL_GOOGLEPLUS_LABEL');
Text::script('COM_KUNENA_SOCIAL_LINKEDIN_LABEL');
Text::script('COM_KUNENA_SOCIAL_PINTEREST_LABEL');
Text::script('COM_KUNENA_SOCIAL_WHATSAPP_LABEL');
Text::script('COM_KUNENA_SOCIAL_REDDIT_LABEL');

$this->addScript('assets/js/topic.js');

// Multi-quote: the bar and the buttons of the messages (assets/js/multiquote.js), for a user who may reply to the topic.
$multiQuote = '';

if ($this->topic->isAuthorised('reply')) {
    $this->addScript('assets/js/multiquote.js');

    foreach (['COM_KUNENA_MULTIQUOTE', 'COM_KUNENA_MULTIQUOTE_REPLY', 'COM_KUNENA_MULTIQUOTE_REPLY_1', 'COM_KUNENA_MULTIQUOTE_CLEAR', 'COM_KUNENA_MULTIQUOTE_CLEARED'] as $key) {
        Text::script($key);
    }

    $multiQuote = ' data-kmultiquote-url="' . KunenaRoute::_('index.php?option=com_kunena&view=topic&layout=reply&catid=' . (int) $this->category->id . '&id=' . (int) $this->topic->id) . '"'
        . ' data-kmultiquote-topic="' . (int) $this->topic->id . '"'
        . ' data-kmultiquote-max="' . KunenaMessageQuotes::MAX_QUOTES . '"';
}

$this->ktemplate = KunenaFactory::getTemplate();
$quick           = $this->ktemplate->params->get('quick');

// The new replies of the topic, asked in the background (assets/js/topicnew.js) with the option of the background reload of the lists.
if ($this->ktemplate->params->get('ajaxTopicList', 1)) {
    foreach (['COM_KUNENA_TOPIC_NEW_REPLIES', 'COM_KUNENA_TOPIC_NEW_REPLIES_1', 'COM_KUNENA_TOPIC_NEW_REPLIES_SHOW'] as $key) {
        Text::script($key);
    }

    $this->ktemplate->addScriptOptions('com_kunena.topicnew', [
        'url'      => KunenaRoute::_(
            'index.php?option=com_kunena&view=topic&task=newreplies&format=raw&id=' . (int) $this->topic->id
            . '&after=' . (int) $this->topic->last_post_id . '&' . Session::getFormToken() . '=1',
            false
        ),
        'interval' => KunenaTopicNewReplies::interval($this->ktemplate->params->get('ajaxTopicListInterval', 30)),
    ]);
    $this->addScript('assets/js/topicnew.js');
}
$txt             = '';

// The left pane: the latest topics of the category which the user may read (KunenaTopicHelper checks it).
[, $paneTopics] = KunenaTopicHelper::getLatestTopics([(int) $this->category->id], 0, 8);

// Their unread messages, which the pane shows (the query of the list does not know them: the number was never shown).
KunenaTopicHelper::fetchNewStatus($paneTopics);

// The right pane: the users who wrote the messages of this page, with their number of messages here.
$people = [];

foreach ((array) $this->messages as $paneMessage) {
    if (!$paneMessage->userid) {
        continue;
    }

    $people[$paneMessage->userid] ??= [$paneMessage->getAuthor(), 0];
    $people[$paneMessage->userid][1]++;
}

if ($this->topic->ordering) {
    $txt .= ' topic-sticky';
}

if ($this->topic->locked) {
    $txt .= ' topic-locked';
}

?>
<div class="kunena-topic-item <?php echo $txt; ?>"<?php echo $multiQuote; ?>>
    <?php if ($this->category->headerdesc) : ?>
        <div id="topic-item-category-headerdesc<?php echo $this->category->id; ?>" class="alert alert-info shadow-lg rounded alert-dismissible fade show">
            <?php echo $this->category->displayField('headerdesc'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="agora-chat-panes">
    <div class="agora-chat-thread">

    <header class="agora-chat-head">
        <h1>
            <?php
            if ($this->ktemplate->params->get('labels') != 0) {
                echo $this->subLayout('Widget/Label')->set('topic', $this->topic)->setLayout('default');
            }
            ?>
            <?php if ($this->topic->locked && (string) $this->topic->lock_reason !== '') : ?>
                <?php echo $this->subLayout('Widget/Lock')->set('topic', $this->topic); ?>
            <?php endif; ?>
            <?php echo $this->topic->displayField('subject'); ?>
        </h1>
        <p class="agora-chat-head-meta">
            <span><?php echo $this->getCategoryLink($this->category, null, null, null); ?></span>
            <span><?php echo Text::plural('COM_KUNENA_TEMPLATE_AGORA_N_PEOPLE', \count($people)); ?></span>
            <span><?php echo $this->formatLargeNumber($this->topic->getReplies()) . ' ' . Text::_('COM_KUNENA_GEN_REPLIES'); ?></span>
            <?php if (KunenaConfig::getInstance()->ratingEnabled) {
                echo $this->subLayout('Topic/Item/Rating')->set('category', $this->category)
                    ->set('topic', $this->topic);
            } ?>
        </p>
    </header>

    <div><?php echo $this->subRequest('Topic/Item/Actions')->set('id', $this->topic->id); ?></div>

    <div class="float-start">
        <?php echo $this->subLayout('Widget/Pagination/List')
            ->set('pagination', $this->pagination)
            ->set('display', true); ?>
    </div>
    <div class="float-end kunena-search-widget">
        <?php echo $this->subLayout('Widget/Search')
            ->set('id', $this->topic->id)
            ->set('title', Text::_('COM_KUNENA_SEARCH_TOPIC'))
            ->setLayout('topic'); ?>
    </div>

    <div class="clearfix"></div>

    <?php $topicTagsHtml = KunenaTopicTags::render((int) $this->topic->id, 'mb-2 d-block'); ?>
    <?php if ($topicTagsHtml !== '') : ?>
        <?php echo $topicTagsHtml; ?>
    <?php endif; ?>

    <?php
    if ($this->ktemplate->params->get('displayModule')) {
        echo $this->subLayout('Widget/Module')->set('position', 'kunena_topictitle');
    }

    echo $this->subRequest('Topic/Poll')->set('id', $this->topic->id);

    if ($this->ktemplate->params->get('displayModule')) {
        echo $this->subLayout('Widget/Module')->set('position', 'kunena_poll');
    }

    // The accepted answer of the topic, on every page.
    echo $this->subLayout('Topic/Item/Answer')
        ->set('topic', $this->topic)
        ->set('category', $this->category);

    $count = 1;

    // The template option "Show the first post on every page": the first message of the topic on top of the pages which
    // do not have it themselves.
    if ($this->stickyFirst) {
        echo '<div class="kunena-sticky-first border border-2 border-primary-subtle rounded p-2 mb-4">';
        echo '<div class="small text-muted mb-2">' . Text::_('COM_KUNENA_STICKY_FIRST_POST') . '</div>';
        echo $this->subRequest('Topic/Item/Message')
            ->set('mesid', $this->stickyFirst->id)
            ->set('location', $this->stickyFirst->id);
        echo '</div>';
    }

    // The layout of the topic (flat, threaded or indented): there is no file of its own, it is chosen here.
    $topicLayout = $this->getLayout();

    if ($this->messages && $topicLayout == 'threaded') {
        // Threaded: the selected message, then the tree of the messages of the page with a link to each of them.
        echo '<div class="topic-item-messages">';
        echo $this->subRequest('Topic/Item/Message')
            ->set('mesid', $this->message->id)
            ->set('location', $this->message->id);
        echo '</div>';

        echo '<div class="table-responsive"><table class="table table-sm kunena-topic-tree">';
        echo '<caption class="visually-hidden">' . Text::sprintf('COM_KUNENA_TOPIC_REPLIES_TITLE', $this->topic->displayField('subject')) . '</caption>';
        echo '<thead><tr><th scope="col">' . Text::_('COM_KUNENA_GEN_SUBJECT') . '</th><th scope="col">' . Text::_('COM_KUNENA_GEN_AUTHOR')
            . '</th><th scope="col">' . Text::_('COM_KUNENA_POSTED_AT') . '</th></tr></thead><tbody>';

        foreach ($this->messages as $message) {
            echo $this->subLayout('Topic/Item/Message')
                ->set('message', $message)
                ->set('selected', $this->message->id)
                ->setLayout('row');
        }

        echo '</tbody></table></div>';
    } elseif ($this->messages) {
        echo '<div class="topic-item-messages">';

        foreach ($this->messages as $id => $message) {
            // Indented: each reply moves in by its depth in the tree of the page (its edges, without the first two), up to 5 steps.
            $depth = $topicLayout == 'indented' ? min(max(\count((array) $message->indent) - 2, 0), 5) : 0;

            if ($depth) {
                echo '<div class="kunena-topic-indent kunena-topic-indent-' . $depth . '">';
            }

            echo $this->subRequest('Topic/Item/Message')
                ->set('mesid', $message->id)
                ->set('location', $id);

            if ($depth) {
                echo '</div>';
            }

            if ($this->ktemplate->params->get('displayModule')) {
                echo $this->subLayout('Widget/Module')
                    ->set('position', 'kunena_msg_row_' . $count++);
            }
        }

        echo '</div>';
    }

    if ($quick == 2 && KunenaConfig::getInstance()->quickReply) {
        echo $this->subLayout('Message/Edit')
            ->set('message', $this->message)
            ->setLayout('full');
    }
    ?>

    <div class="float-start">
        <?php echo $this->subLayout('Widget/Pagination/List')
            ->set('pagination', $this->pagination)
            ->set('display', true); ?>
    </div>
    <div class="float-end">
        <?php echo $this->subLayout('Widget/Search')
            ->set('id', $this->topic->id)
            ->set('title', Text::_('COM_KUNENA_SEARCH_TOPIC'))
            ->setLayout('topic'); ?>
    </div>

    <div><?php echo $this->subRequest('Topic/Item/Actions')->set('id', $this->topic->id); ?></div>

    <?php if ($this->ktemplate->params->get('writeaccess')) : ?>
        <div><?php echo $this->subLayout('Widget/Writeaccess')->set('id', $this->topic->id); ?></div>
    <?php endif; ?>

    <?php
    if ($this->config->enableForumJump) {
        echo $this->subLayout('Widget/Forumjump')->set('categorylist', $this->categorylist);
    } ?>
    <div class="clearfix"></div>
<?php if ($this->config->topicShowModerators) : ?>
    <div class="float-end"><?php echo $this->subLayout('Category/Moderators')->set('moderators', $this->category->getModerators(false)); ?></div>
<?php endif; ?>
    </div>

    <?php if ($paneTopics) : ?>
        <nav class="agora-chat-topics" aria-label="<?php echo $this->escape($this->category->name); ?>">
            <h2 class="agora-chat-pane-title"><?php echo $this->getCategoryLink($this->category, null, null, null); ?></h2>
            <ul>
                <?php foreach ($paneTopics as $paneTopic) :
                    $current    = (int) $paneTopic->id === (int) $this->topic->id;
                    $lastAuthor = $paneTopic->getLastPostAuthor();
                    $paneAvatar = $lastAuthor->getAvatarImage($this->ktemplate->params->get('avatarType'), 'thumb');
                    ?>
                    <li>
                        <a href="<?php echo $paneTopic->getUrl($this->category, true, $paneTopic->unread ? 'unread' : null); ?>"
                           class="agora-chat-topic<?php echo $current ? ' active' : ''; ?>"<?php echo $current ? ' aria-current="page"' : ''; ?>>
                            <span class="agora-chat-topic-avatar" aria-hidden="true"><?php echo $paneAvatar; ?></span>
                            <span class="agora-chat-topic-text">
                                <span class="agora-chat-topic-title"><?php echo $this->escape($paneTopic->subject); ?></span>
                                <span class="agora-chat-topic-meta"><?php echo $this->escape($lastAuthor->getName('', false)) . ' · ' . $paneTopic->getLastPostTime()->toKunena('config_postDateFormat'); ?></span>
                            </span>
                            <?php if ($paneTopic->unread) : ?>
                                <span class="badge agora-chat-unread"><?php echo (int) $paneTopic->unread; ?><span class="visually-hidden"> <?php echo Text::_('COM_KUNENA_A_GEN_NEWCHAR'); ?></span></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    <?php endif; ?>

    <?php if ($people) : ?>
        <aside class="agora-chat-people" aria-label="<?php echo Text::_('COM_KUNENA_TEMPLATE_AGORA_IN_THIS_TOPIC'); ?>">
            <h2 class="agora-chat-pane-title"><?php echo Text::_('COM_KUNENA_TEMPLATE_AGORA_IN_THIS_TOPIC'); ?></h2>
            <ul>
                <?php foreach ($people as [$person, $count]) : ?>
                    <li>
                        <span class="agora-chat-person-avatar" aria-hidden="true"><?php echo $person->getAvatarImage($this->ktemplate->params->get('avatarType'), 'thumb'); ?></span>
                        <?php echo $person->getLink(null, null, '', '', null, $this->category->id); ?>
                        <span class="agora-chat-person-count"><?php echo $this->formatLargeNumber($count); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </aside>
    <?php endif; ?>
    </div>
</div>