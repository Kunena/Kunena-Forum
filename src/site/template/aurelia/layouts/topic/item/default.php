<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
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
use Kunena\Forum\Libraries\Template\KunenaTemplate;

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

$this->ktemplate = KunenaFactory::getTemplate();
$quick           = $this->ktemplate->params->get('quick');
$txt             = '';

if ($this->topic->ordering) {
    $txt .= ' topic-sticky';
}

if ($this->topic->locked) {
    $txt .= ' topic-locked';
}

?>
<div class="kunena-topic-item <?php echo $txt; ?>">
    <?php if ($this->category->headerdesc) : ?>
        <div id="topic-item-category-headerdesc<?php echo $this->category->id; ?>" class="alert alert-info shadow-lg rounded alert-dismissible fade show">
            <?php echo $this->category->displayField('headerdesc'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <h1>
        <?php echo KunenaTemplate::getInstance()->getTopicIcon($this->topic); ?>
        <?php
        if ($this->ktemplate->params->get('labels') != 0) {
            echo $this->subLayout('Widget/Label')->set('topic', $this->topic)->setLayout('default');
        }
        ?>
        <?php if ($this->topic->locked && (string) $this->topic->lock_reason !== '') : ?>
            <?php echo $this->subLayout('Widget/Lock')->set('topic', $this->topic); ?>
        <?php endif; ?>
        <?php echo $this->topic->displayField('subject'); ?>

        <?php if (KunenaConfig::getInstance()->ratingEnabled) {
            echo $this->subLayout('Topic/Item/Rating')->set('category', $this->category)
                ->set('topic', $this->topic);
        } ?>
    </h1>

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