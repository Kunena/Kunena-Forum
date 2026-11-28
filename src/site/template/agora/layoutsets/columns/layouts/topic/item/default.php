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
 * The layout Columns: the topic of Aurelia in two columns. The title, the messages and the actions on the left; on the right a side
 * column with the numbers of the topic (replies, views, start, last post), the search in the topic, the moderators and the forum
 * jump. On a small screen the side column comes after the messages (assets/scss/layoutsets/_columns.scss).
 */

use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessageQuotes;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicAnswer;
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

    <?php
    // The head of the topic as in the mockup: the marks and the tags as chips, the title, one line with the numbers.
    $topicTagsHtml = KunenaTopicTags::render((int) $this->topic->id, 'agora-topic-tags');
    // The chip of the accepted answer as the box of the answer shows it: when the forum has accepted answers, for an answer the
    // user may read. It was shown with the setting off (an answer accepted before, the box and the actions hidden).
    $config        = KunenaConfig::getInstance();
    $answer        = (!isset($config->acceptedAnswers) || $config->acceptedAnswers) ? KunenaTopicAnswer::getAnswer($this->topic) : null;
    $solved        = $answer !== null && $answer->isAuthorised('read');
    ?>
    <header class="agora-topic-head">
        <?php if ($solved || $this->topic->locked || $topicTagsHtml !== '') : ?>
            <div class="agora-topic-chips">
                <?php if ($solved) : ?>
                    <span class="badge agora-chip-solved"><?php echo Text::_('COM_KUNENA_TOPIC_ACCEPTED_ANSWER'); ?></span>
                <?php endif; ?>
                <?php if ($this->topic->locked) : ?>
                    <?php echo $this->subLayout('Widget/Lock')->set('topic', $this->topic); ?>
                <?php endif; ?>
                <?php echo $topicTagsHtml; ?>
            </div>
        <?php endif; ?>

        <h1>
            <?php
            if ($this->ktemplate->params->get('labels') != 0) {
                echo $this->subLayout('Widget/Label')->set('topic', $this->topic)->setLayout('default');
            }
            ?>
            <?php echo $this->topic->displayField('subject'); ?>
        </h1>

        <p class="agora-topic-meta">
            <span><?php echo $this->formatLargeNumber($this->topic->getReplies()) . ' ' . Text::_('COM_KUNENA_GEN_REPLIES'); ?></span>
            <span><?php echo $this->formatLargeNumber($this->topic->hits) . ' ' . Text::_('COM_KUNENA_GEN_HITS'); ?></span>
            <?php // No date with the date format "None" (the text "Topic started" stood alone). ?>
            <?php if (KunenaConfig::getInstance()->postDateFormat != 'none') : ?>
                <span><?php echo Text::_('COM_KUNENA_TOPIC_STARTED_ON') . ' ' . $this->topic->getFirstPostTime()->toKunena('config_postDateFormat'); ?></span>
            <?php endif; ?>
            <?php if (KunenaConfig::getInstance()->ratingEnabled) {
                echo $this->subLayout('Topic/Item/Rating')->set('category', $this->category)
                    ->set('topic', $this->topic);
            } ?>
        </p>
    </header>

    <div class="agora-topic-columns">
    <div class="agora-topic-main">

    <div><?php echo $this->subRequest('Topic/Item/Actions')->set('id', $this->topic->id); ?></div>

    <div>
        <?php echo $this->subLayout('Widget/Pagination/List')
            ->set('pagination', $this->pagination)
            ->set('display', true); ?>
    </div>

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

    <div>
        <?php echo $this->subLayout('Widget/Pagination/List')
            ->set('pagination', $this->pagination)
            ->set('display', true); ?>
    </div>

    <div><?php echo $this->subRequest('Topic/Item/Actions')->set('id', $this->topic->id); ?></div>

    <?php if ($this->ktemplate->params->get('writeaccess')) : ?>
        <div><?php echo $this->subLayout('Widget/Writeaccess')->set('id', $this->topic->id); ?></div>
    <?php endif; ?>
    </div>

    <aside class="agora-topic-aside" aria-label="<?php echo Text::_('COM_KUNENA_TEMPLATE_AGORA_TOPIC_ASIDE'); ?>">
        <section class="agora-aside-card">
            <h2 class="agora-aside-title"><?php echo Text::_('COM_KUNENA_TEMPLATE_AGORA_TOPIC_ASIDE'); ?></h2>
            <dl class="agora-topic-numbers">
                <div><dt><?php echo Text::_('COM_KUNENA_GEN_REPLIES'); ?></dt><dd><?php echo $this->formatLargeNumber($this->topic->getReplies()); ?></dd></div>
                <div><dt><?php echo Text::_('COM_KUNENA_GEN_HITS'); ?></dt><dd><?php echo $this->formatLargeNumber($this->topic->hits); ?></dd></div>
            </dl>
            <p class="agora-topic-dates">
                <?php // Without the dates with the date format "None" ("Topic started on  by X"). ?>
                <?php if (KunenaConfig::getInstance()->postDateFormat != 'none') : ?>
                    <?php echo Text::sprintf(
                        'COM_KUNENA_TOPIC_STARTED_ON_DATE_BY_USER',
                        $this->topic->getFirstPostTime()->toKunena('config_postDateFormat'),
                        $this->topic->getFirstPostAuthor()->getLink(null, null, '', '', null, $this->category->id)
                    ); ?>
                <?php else : ?>
                    <?php echo Text::_('COM_KUNENA_TOPIC_STARTED_ON') . ' ' . Text::_('COM_KUNENA_BY') . ' ' . $this->topic->getFirstPostAuthor()->getLink(null, null, '', '', null, $this->category->id); ?>
                <?php endif; ?>
                <br>
                <?php echo $this->getTopicLink($this->topic, 'last', Text::_('COM_KUNENA_GEN_LAST_POST'), null, null, $this->category, false, true); ?>
                <?php if (KunenaConfig::getInstance()->postDateFormat != 'none') : ?>
                    <?php echo $this->topic->getLastPostTime()->toKunena('config_postDateFormat'); ?>
                <?php endif; ?>
                <?php echo Text::_('COM_KUNENA_BY') . ' ' . $this->topic->getLastPostAuthor()->getLink(null, null, '', '', null, $this->category->id); ?>
            </p>
        </section>

        <section class="agora-aside-card kunena-search-widget">
            <?php echo $this->subLayout('Widget/Search')
                ->set('id', $this->topic->id)
                ->set('title', Text::_('COM_KUNENA_SEARCH_TOPIC'))
                ->setLayout('topic'); ?>
        </section>

        <?php
        // No card for a category without moderators (the layout is then empty, or only has the comments of the debug mode).
        $moderators = $this->config->topicShowModerators
            ? (string) $this->subLayout('Category/Moderators')->set('moderators', $this->category->getModerators(false))
            : '';
        ?>
        <?php if (trim((string) preg_replace('/<!--.*?-->/s', '', $moderators)) !== '') : ?>
            <section class="agora-aside-card">
                <?php echo $moderators; ?>
            </section>
        <?php endif; ?>

        <?php if ($this->config->enableForumJump) : ?>
            <section class="agora-aside-card">
                <?php echo $this->subLayout('Widget/Forumjump')->set('categorylist', $this->categorylist); ?>
            </section>
        <?php endif; ?>
    </aside>
    </div>
</div>