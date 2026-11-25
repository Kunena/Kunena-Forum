<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Topics
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
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Tools\KunenaTopicManager;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$filters = $this->filters;
$found   = $this->found;
$current = (int) $found['page'];
$pages   = (int) $found['pages'];

// The categories as a tree: each one after its parent, indented by its depth.
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

$page = static function (int $number) use ($filters): string {
    return Route::_('index.php?option=com_kunena&view=topics&' . http_build_query(['filter' => ['page' => $number] + KunenaTopicManager::active($filters)]));
};
// The list to go back to after an edit.
$back = http_build_query(['filter' => KunenaTopicManager::active($filters)]);
$date = static fn ($time) => $time ? HTMLHelper::_('date', (int) $time, Text::_('DATE_FORMAT_LC5')) : '—';
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-list-check" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title"><?php echo Text::plural('COM_KUNENA_TOOLS_TOPICS_N_FOUND', $found['total']) ?></h2>
                <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_TOOLS_TOPICS_INTRO') ?></p>
            </div>
        </div>
    </section>

    <form action="<?php echo Route::_('index.php') ?>" method="get" class="kd-panel">
        <input type="hidden" name="option" value="com_kunena">
        <input type="hidden" name="view" value="topics">
        <div class="kd-form">
            <div class="kd-form__field">
                <label for="kd-topics-search"><?php echo Text::_('COM_KUNENA_TOOLS_TOPICS_SUBJECT') ?></label>
                <input type="search" class="form-control" id="kd-topics-search" name="filter[search]" value="<?php echo $this->escape($filters['search']) ?>">
            </div>
            <div class="kd-form__field">
                <label for="kd-topics-category"><?php echo Text::_('COM_KUNENA_TOOLS_QUEUE_CATEGORY') ?></label>
                <select class="form-select" id="kd-topics-category" name="filter[category]">
                    <option value="0"><?php echo Text::_('COM_KUNENA_TOOLS_TRANSFER_ALL') ?></option>
                    <?php foreach ($options as [$id, $label]) : ?>
                        <option value="<?php echo $id ?>"<?php echo $id === $filters['category'] ? ' selected' : '' ?>><?php echo $this->escape($label) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="kd-form__field">
                <label for="kd-topics-state"><?php echo Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_STATUS') ?></label>
                <select class="form-select" id="kd-topics-state" name="filter[state]">
                    <?php foreach (KunenaTopicManager::STATES as $state) : ?>
                        <option value="<?php echo $state ?>"<?php echo $state === $filters['state'] ? ' selected' : '' ?>><?php echo Text::_('COM_KUNENA_TOOLS_TOPICS_STATE_' . strtoupper($state)) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="kd-form__field">
                <label for="kd-topics-age"><?php echo Text::_('COM_KUNENA_TOOLS_TOPICS_AGE') ?></label>
                <select class="form-select" id="kd-topics-age" name="filter[age]">
                    <?php foreach (KunenaTopicManager::AGES as $age) : ?>
                        <option value="<?php echo $age ?>"<?php echo $age === $filters['age'] ? ' selected' : '' ?>>
                            <?php echo $age ? Text::plural('COM_KUNENA_TOOLS_TOPICS_N_DAYS', $age) : Text::_('COM_KUNENA_TOOLS_TOPICS_ANY_AGE') ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="kd-form__field">
                <label for="kd-topics-author"><?php echo Text::_('COM_KUNENA_TOOLS_SPAM_AUTHOR') ?></label>
                <input type="search" class="form-control" id="kd-topics-author" name="filter[author]" value="<?php echo $this->escape($filters['author']) ?>"
                       placeholder="<?php echo $this->escape(Text::_('COM_KUNENA_TOOLS_TOPICS_AUTHOR_HINT')) ?>">
            </div>
            <div class="kd-form__field">
                <label for="kd-topics-limit"><?php echo Text::_('COM_KUNENA_TOPICS_PER_PAGE') ?></label>
                <select class="form-select" id="kd-topics-limit" name="filter[limit]">
                    <?php foreach (KunenaTopicManager::LIMITS as $limit) : ?>
                        <option value="<?php echo $limit ?>"<?php echo $limit === $filters['limit'] ? ' selected' : '' ?>><?php echo $limit ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
        <div class="kd-form__actions">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="filter[noreplies]" id="kd-topics-noreplies" value="1"<?php echo $filters['noreplies'] ? ' checked' : '' ?>>
                <label class="form-check-label" for="kd-topics-noreplies"><?php echo Text::_('COM_KUNENA_TOOLS_TOPICS_NOREPLIES') ?></label>
            </div>
            <button type="submit" class="kd-btn kd-btn--ghost">
                <span class="fa-solid fa-filter" aria-hidden="true"></span>
                <?php echo Text::_('JSEARCH_FILTER_SUBMIT') ?>
            </button>
            <a class="kd-btn kd-btn--ghost" href="<?php echo Route::_('index.php?option=com_kunena&view=topics') ?>"><?php echo Text::_('JSEARCH_FILTER_CLEAR') ?></a>
        </div>
    </form>

    <?php if ($found['items']) : ?>
        <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=topics') ?>" method="post" name="adminForm" id="adminForm" class="kd-contents">
            <input type="hidden" name="task" value="">
            <input type="hidden" name="boxchecked" value="0">
            <?php foreach (KunenaTopicManager::active($filters) as $key => $value) : ?>
                <input type="hidden" name="filter[<?php echo $this->escape($key) ?>]" value="<?php echo $this->escape((string) $value) ?>">
            <?php endforeach ?>
            <?php echo HTMLHelper::_('form.token') ?>

            <div class="kd-scroll">
                <table class="table">
                    <thead>
                        <tr>
                            <?php if ($this->canAdmin) : ?>
                                <td class="w-1 text-center"><?php echo HTMLHelper::_('grid.checkall') ?></td>
                            <?php endif ?>
                            <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_TOPICS_TOPIC') ?></th>
                            <th scope="col" class="w-15 d-none d-md-table-cell"><?php echo Text::_('COM_KUNENA_TOOLS_QUEUE_CATEGORY') ?></th>
                            <th scope="col" class="w-5 text-end"><?php echo Text::_('COM_KUNENA_TOOLS_TOPICS_POSTS') ?></th>
                            <th scope="col" class="w-15 d-none d-lg-table-cell"><?php echo Text::_('COM_KUNENA_TOOLS_TOPICS_LAST') ?></th>
                            <?php if ($this->canAdmin) : ?>
                                <th scope="col" class="w-10 text-end"><?php echo Text::_('COM_KUNENA_TOPICS_MODERATE') ?></th>
                            <?php endif ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($found['items'] as $i => $topic) :
                            $id      = (int) $topic['id'];
                            $hold    = (int) $topic['hold'];
                            $locked  = (bool) $topic['locked'];
                            $sticky  = (int) $topic['ordering'] > 0;
                            $trashed = $hold >= KunenaForum::DELETED;
                            $link    = Route::link('site', 'index.php?option=com_kunena&view=topic&catid=' . (int) $topic['category_id'] . '&id=' . $id, false);
                            $edit    = Route::_('index.php?option=com_kunena&view=topic&layout=edit&id=' . $id . ($back !== '' ? '&' . $back : ''), false);

                            // The quick actions of the row: [task, icon, label, on].
                            $actions = [
                                [$locked ? 'unlock' : 'lock', $locked ? 'lock' : 'lock-open', $locked ? 'UNLOCK' : 'LOCK', $locked],
                                [$sticky ? 'unstick' : 'stick', 'thumbtack', $sticky ? 'UNSTICK' : 'STICK', $sticky],
                            ];

                            if ($hold === KunenaForum::UNAPPROVED) {
                                $actions[] = ['approve', 'check', 'APPROVE', false];
                            }

                            $actions[] = $trashed ? ['restore', 'rotate-left', 'RESTORE', false] : ['trash', 'trash', 'TRASH', false]; ?>
                            <tr>
                                <?php if ($this->canAdmin) : ?>
                                    <td class="text-center"><?php echo HTMLHelper::_('grid.id', $i, $id, false, 'cid', 'cb', (string) $topic['subject']) ?></td>
                                <?php endif ?>
                                <th scope="row">
                                    <a href="<?php echo $this->escape($edit) ?>"><?php echo $this->escape($topic['subject']) ?></a>
                                    <?php if ($sticky) : ?>
                                        <span class="kd-pill kd-pill--ok"><?php echo Text::_('COM_KUNENA_TOOLS_TOPICS_STATE_STICKY') ?></span>
                                    <?php endif ?>
                                    <?php if ($locked) : ?>
                                        <span class="kd-pill kd-pill--warn"><?php echo Text::_('COM_KUNENA_TOOLS_TOPICS_STATE_LOCKED') ?></span>
                                    <?php endif ?>
                                    <?php if ($hold === KunenaForum::UNAPPROVED) : ?>
                                        <span class="kd-pill kd-pill--warn"><?php echo Text::_('COM_KUNENA_TOOLS_TOPICS_STATE_UNAPPROVED') ?></span>
                                    <?php elseif ($trashed) : ?>
                                        <span class="kd-pill kd-pill--warn"><?php echo Text::_('COM_KUNENA_TOOLS_TOPICS_STATE_TRASHED') ?></span>
                                    <?php endif ?>
                                    <br><span class="kd-form__hint"><?php echo $this->escape($topic['username'] ?: (string) $topic['first_post_guest_name']) ?> · <?php echo $date($topic['first_post_time']) ?> ·
                                        <a href="<?php echo $this->escape($link) ?>" target="_blank" rel="noopener"><?php echo Text::_('COM_KUNENA_TOOLS_BBCODE_PREVIEW') ?></a></span>
                                </th>
                                <td class="d-none d-md-table-cell"><?php echo $this->escape((string) $topic['category']) ?></td>
                                <td class="text-end"><?php echo (int) $topic['posts'] ?></td>
                                <td class="d-none d-lg-table-cell"><?php echo $date($topic['last_post_time']) ?></td>
                                <?php if ($this->canAdmin) : ?>
                                    <td class="text-end">
                                        <div class="kd-row-actions">
                                            <?php foreach ($actions as [$task, $icon, $label, $on]) :
                                                $title = Text::_('COM_KUNENA_TOOLS_TOPICS_ACTION_' . $label); ?>
                                                <button type="button" class="js-grid-item-action kd-row-action<?php echo $on ? ' is-on' : '' ?>"
                                                        data-item-id="cb<?php echo $i ?>" data-item-task="topics.<?php echo $task ?>"
                                                        title="<?php echo $this->escape($title) ?>" aria-label="<?php echo $this->escape($title . ': ' . $topic['subject']) ?>">
                                                    <span class="fa-solid fa-<?php echo $icon ?>" aria-hidden="true"></span>
                                                </button>
                                            <?php endforeach ?>
                                            <a class="kd-row-action" href="<?php echo $this->escape($edit) ?>" title="<?php echo $this->escape(Text::_('COM_KUNENA_TOPIC_EDIT')) ?>"
                                               aria-label="<?php echo $this->escape(Text::_('COM_KUNENA_TOPIC_EDIT') . ': ' . $topic['subject']) ?>">
                                                <span class="fa-solid fa-pen" aria-hidden="true"></span>
                                            </a>
                                        </div>
                                    </td>
                                <?php endif ?>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>

            <div class="kd-pager">
                <span class="kd-form__hint">
                    <?php echo Text::sprintf(
                        'COM_KUNENA_TOPICS_SHOWING',
                        ($current - 1) * $found['limit'] + 1,
                        ($current - 1) * $found['limit'] + \count($found['items']),
                        $found['total']
                    ) ?>
                </span>
                <?php if ($pages > 1) : ?>
                    <nav aria-label="<?php echo $this->escape(Text::_('JLIB_HTML_PAGINATION')) ?>">
                        <ul class="pagination mb-0">
                            <?php // First and previous: not links on the first page.
                            foreach ([[1, 'JLIB_HTML_START', 'angles-left'], [$current - 1, 'JPREV', 'angle-left']] as [$number, $label, $icon]) : ?>
                                <li class="page-item<?php echo $current === 1 ? ' disabled' : '' ?>">
                                    <?php if ($current === 1) : ?>
                                        <span class="page-link" aria-hidden="true"><span class="fa-solid fa-<?php echo $icon ?>"></span></span>
                                    <?php else : ?>
                                        <a class="page-link" href="<?php echo $page($number) ?>" aria-label="<?php echo $this->escape(Text::_($label)) ?>">
                                            <span class="fa-solid fa-<?php echo $icon ?>" aria-hidden="true"></span>
                                        </a>
                                    <?php endif ?>
                                </li>
                            <?php endforeach ?>

                            <?php foreach (KunenaTopicManager::pageWindow($current, $pages) as $number) : ?>
                                <?php if ($number === null) : ?>
                                    <li class="page-item disabled"><span class="page-link">…</span></li>
                                <?php elseif ($number === $current) : ?>
                                    <li class="page-item active" aria-current="page"><span class="page-link"><?php echo $number ?></span></li>
                                <?php else : ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?php echo $page($number) ?>" aria-label="<?php echo $this->escape(Text::sprintf('JLIB_HTML_GOTO_PAGE', $number)) ?>"><?php echo $number ?></a>
                                    </li>
                                <?php endif ?>
                            <?php endforeach ?>

                            <?php // Next and last: not links on the last page.
                            foreach ([[$current + 1, 'JNEXT', 'angle-right'], [$pages, 'JLIB_HTML_END', 'angles-right']] as [$number, $label, $icon]) : ?>
                                <li class="page-item<?php echo $current === $pages ? ' disabled' : '' ?>">
                                    <?php if ($current === $pages) : ?>
                                        <span class="page-link" aria-hidden="true"><span class="fa-solid fa-<?php echo $icon ?>"></span></span>
                                    <?php else : ?>
                                        <a class="page-link" href="<?php echo $page($number) ?>" aria-label="<?php echo $this->escape(Text::_($label)) ?>">
                                            <span class="fa-solid fa-<?php echo $icon ?>" aria-hidden="true"></span>
                                        </a>
                                    <?php endif ?>
                                </li>
                            <?php endforeach ?>
                        </ul>
                    </nav>
                <?php endif ?>
            </div>

            <?php if ($this->canAdmin) : ?>
                <div class="kd-panel kd-form">
                    <div class="kd-form__field">
                        <label for="kd-topics-target"><?php echo Text::_('COM_KUNENA_TOPICS_MOVE_TO') ?></label>
                        <select class="form-select" id="kd-topics-target" name="target">
                            <option value="0">—</option>
                            <?php foreach ($options as [$value, $label, $isSection]) : ?>
                                <option value="<?php echo $value ?>"<?php echo $isSection ? ' disabled' : '' ?>><?php echo $this->escape($label) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div>
                        <button type="button" class="kd-btn kd-btn--primary" data-kd-task="topics.move">
                            <span class="fa-solid fa-arrow-right-arrow-left" aria-hidden="true"></span>
                            <?php echo Text::_('COM_KUNENA_TOPICS_MOVE') ?>
                        </button>
                    </div>
                </div>
            <?php else : ?>
                <div class="alert alert-info"><?php echo Text::_('COM_KUNENA_TOOLS_ADMIN_ONLY') ?></div>
            <?php endif ?>
        </form>

    <?php endif ?>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
