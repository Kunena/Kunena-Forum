<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Topic
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Tools\KunenaTopicEditor;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$topic    = $this->data['topic'];
$messages = $this->data['messages'];
$id       = (int) $topic['id'];
$back     = http_build_query(['filter' => $this->back]);
$link     = Route::link('site', 'index.php?option=com_kunena&view=topic&catid=' . $topic['category_id'] . '&id=' . $id, false);
$date     = static fn ($time) => $time ? HTMLHelper::_('date', (int) $time, Text::_('DATE_FORMAT_LC5')) : '—';
$page     = static fn (int $number): string => Route::_('index.php?option=com_kunena&view=topic&layout=edit&id=' . $id . '&page=' . $number . ($back !== '' ? '&' . $back : ''));
$readonly = $this->canAdmin ? '' : ' readonly';

// The categories as a tree: each one after its parent, indented by its depth; a section holds no topic.
$children = [];

foreach ($this->categories as $category) {
    $children[(int) $category['parentid']][] = $category;
}

$options = [];
$walk    = static function (int $parent, int $depth) use (&$walk, &$options, $children): void {
    foreach ($children[$parent] ?? [] as $category) {
        $options[] = [(int) $category['id'], str_repeat('— ', $depth) . $category['name'], $depth === 0];
        $walk((int) $category['id'], $depth + 1);
    }
};
$walk(0, 0);
?>

<div id="kunena" class="kd">
    <form action="<?php echo Route::_('index.php?option=com_kunena&view=topic&layout=edit&id=' . $id) ?>" method="post" name="adminForm" id="adminForm" class="kd-contents">
        <input type="hidden" name="task" value="">
        <input type="hidden" name="id" value="<?php echo $id ?>">
        <input type="hidden" name="page" value="<?php echo (int) $this->data['page'] ?>">
        <?php foreach ($this->back as $key => $value) : ?>
            <input type="hidden" name="filter[<?php echo $this->escape($key) ?>]" value="<?php echo $this->escape((string) $value) ?>">
        <?php endforeach ?>
        <?php echo HTMLHelper::_('form.token') ?>

        <section class="kd-hero kd-diag__hero">
            <div class="kd-hero__brand">
                <span class="kd-diag__heroicon fa-solid fa-pen-to-square" aria-hidden="true"></span>
                <div>
                    <h2 class="kd-hero__title"><?php echo $this->escape($topic['subject']) ?></h2>
                    <p class="kd-hero__meta">
                        <?php echo Text::plural('COM_KUNENA_TOPIC_EDIT_N_MESSAGES', $this->data['total']) ?> ·
                        <a href="<?php echo $this->escape($link) ?>" target="_blank" rel="noopener"><?php echo Text::_('COM_KUNENA_TOOLS_BBCODE_PREVIEW') ?></a>
                    </p>
                </div>
            </div>
        </section>

        <?php if (!$this->canAdmin) : ?>
            <div class="alert alert-info"><?php echo Text::_('COM_KUNENA_TOOLS_ADMIN_ONLY') ?></div>
        <?php endif ?>

        <div class="kd-panel">
            <h3 class="kd-panel__title"><?php echo Text::_('COM_KUNENA_TOPIC_EDIT_TOPIC') ?></h3>
            <div class="kd-form">
                <div class="kd-form__field">
                    <label for="kd-topic-category"><?php echo Text::_('COM_KUNENA_TOOLS_QUEUE_CATEGORY') ?></label>
                    <select class="form-select" id="kd-topic-category" name="topic[category]"<?php echo $this->canAdmin ? '' : ' disabled' ?>>
                        <?php foreach ($options as [$value, $label, $isSection]) : ?>
                            <option value="<?php echo $value ?>"<?php echo $value === $topic['category_id'] ? ' selected' : '' ?><?php echo $isSection && $value !== $topic['category_id'] ? ' disabled' : '' ?>>
                                <?php echo $this->escape($label) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="kd-form__field">
                    <label for="kd-topic-reason"><?php echo Text::_('COM_KUNENA_TOPIC_EDIT_REASON') ?></label>
                    <input type="text" class="form-control" id="kd-topic-reason" name="reason" maxlength="200"<?php echo $readonly ?>
                           placeholder="<?php echo $this->escape(Text::_('COM_KUNENA_TOPIC_EDIT_REASON_HINT')) ?>">
                </div>
            </div>
            <div class="kd-form__actions">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="topic[locked]" id="kd-topic-locked" value="1"<?php echo $topic['locked'] ? ' checked' : '' ?><?php echo $this->canAdmin ? '' : ' disabled' ?>>
                    <label class="form-check-label" for="kd-topic-locked"><?php echo Text::_('COM_KUNENA_TOOLS_TOPICS_STATE_LOCKED') ?></label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="topic[sticky]" id="kd-topic-sticky" value="1"<?php echo $topic['sticky'] ? ' checked' : '' ?><?php echo $this->canAdmin ? '' : ' disabled' ?>>
                    <label class="form-check-label" for="kd-topic-sticky"><?php echo Text::_('COM_KUNENA_TOOLS_TOPICS_STATE_STICKY') ?></label>
                </div>
                <?php if ($topic['hold'] === KunenaForum::UNAPPROVED) : ?>
                    <span class="kd-pill kd-pill--warn"><?php echo Text::_('COM_KUNENA_TOOLS_TOPICS_STATE_UNAPPROVED') ?></span>
                <?php elseif ($topic['hold'] >= KunenaForum::DELETED) : ?>
                    <span class="kd-pill kd-pill--warn"><?php echo Text::_('COM_KUNENA_TOOLS_TOPICS_STATE_TRASHED') ?></span>
                <?php endif ?>
            </div>
        </div>

        <?php if ($this->data['pages'] > 1) : ?>
            <div class="alert alert-info"><?php echo Text::_('COM_KUNENA_TOPIC_EDIT_PAGES_HINT') ?></div>
        <?php endif ?>

        <?php foreach ($messages as $message) :
            $mesid = (int) $message['id'];
            $first = $mesid === $topic['first'];
            $lines = substr_count((string) $message['message'], "\n") + 2; ?>
            <article class="kd-panel kd-post" id="kd-post-<?php echo $mesid ?>">
                <div class="kd-post__head">
                    <strong><?php echo $this->escape((string) ($message['username'] ?: $message['name'])) ?></strong>
                    <span class="kd-form__hint">
                        <?php echo $date($message['time']) ?> · #<?php echo $mesid ?>
                        <?php if ((int) $message['modified_time']) : ?>
                            · <?php echo Text::sprintf('COM_KUNENA_TOPIC_EDIT_EDITED', $date($message['modified_time'])) ?>
                        <?php endif ?>
                    </span>
                    <?php if ($first) : ?>
                        <span class="kd-pill kd-pill--ok"><?php echo Text::_('COM_KUNENA_TOPIC_EDIT_FIRST') ?></span>
                    <?php endif ?>
                    <?php if ((int) $message['hold'] === KunenaForum::UNAPPROVED) : ?>
                        <span class="kd-pill kd-pill--warn"><?php echo Text::_('COM_KUNENA_TOOLS_TOPICS_STATE_UNAPPROVED') ?></span>
                    <?php elseif ((int) $message['hold'] >= KunenaForum::DELETED) : ?>
                        <span class="kd-pill kd-pill--warn"><?php echo Text::_('COM_KUNENA_TOOLS_TOPICS_STATE_TRASHED') ?></span>
                    <?php endif ?>
                </div>
                <div class="kd-form__field">
                    <label for="kd-post-subject-<?php echo $mesid ?>"><?php echo Text::_($first ? 'COM_KUNENA_TOPIC_EDIT_SUBJECT_TOPIC' : 'COM_KUNENA_TOPIC_EDIT_SUBJECT') ?></label>
                    <input type="text" class="form-control" id="kd-post-subject-<?php echo $mesid ?>" name="messages[<?php echo $mesid ?>][subject]"
                           value="<?php echo $this->escape((string) $message['subject']) ?>" maxlength="<?php echo KunenaTopicEditor::SUBJECT_LENGTH ?>" required<?php echo $readonly ?>>
                </div>
                <div class="kd-form__field">
                    <label for="kd-post-text-<?php echo $mesid ?>"><?php echo Text::_('COM_KUNENA_TOPIC_EDIT_TEXT') ?></label>
                    <textarea class="form-control kd-post__text" id="kd-post-text-<?php echo $mesid ?>" name="messages[<?php echo $mesid ?>][message]"
                              rows="<?php echo min(20, max(4, $lines)) ?>" required<?php echo $readonly ?>><?php echo $this->escape((string) $message['message']) ?></textarea>
                </div>
            </article>
        <?php endforeach ?>
    </form>

    <?php if ($this->data['pages'] > 1) : ?>
        <nav class="kd-form__actions" aria-label="<?php echo $this->escape(Text::_('JLIB_HTML_PAGINATION')) ?>">
            <?php if ($this->data['page'] > 1) : ?>
                <a class="kd-btn kd-btn--sm kd-btn--ghost" href="<?php echo $page($this->data['page'] - 1) ?>">‹ <?php echo Text::_('JPREV') ?></a>
            <?php endif ?>
            <span class="kd-form__hint"><?php echo Text::sprintf('COM_KUNENA_TOOLS_TOPICS_PAGE', $this->data['page'], $this->data['pages']) ?></span>
            <?php if ($this->data['page'] < $this->data['pages']) : ?>
                <a class="kd-btn kd-btn--sm kd-btn--ghost" href="<?php echo $page($this->data['page'] + 1) ?>"><?php echo Text::_('JNEXT') ?> ›</a>
            <?php endif ?>
        </nav>
    <?php endif ?>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
