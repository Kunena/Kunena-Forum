<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Tools
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Tools\KunenaBigTopics;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$report = $this->tool;
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-layer-group" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title">
                    <?php echo $report['total'] ? Text::plural('COM_KUNENA_TOOLS_BIGTOPICS_N_FOUND', $report['total'], $report['size']) : Text::plural('COM_KUNENA_TOOLS_BIGTOPICS_NONE', $report['size']) ?>
                </h2>
                <p class="kd-hero__meta"><?php echo Text::sprintf('COM_KUNENA_TOOLS_BIGTOPICS_INTRO', $report['perPage'], KunenaBigTopics::LIMIT) ?></p>
            </div>
        </div>
    </section>

    <form action="<?php echo Route::_('index.php') ?>" method="get" class="kd-panel kd-form">
        <input type="hidden" name="option" value="com_kunena">
        <input type="hidden" name="view" value="tools">
        <input type="hidden" name="layout" value="bigtopics">
        <div class="kd-form__field">
            <label for="kd-bigtopics-size"><?php echo Text::_('COM_KUNENA_TOOLS_BIGTOPICS_SIZE') ?></label>
            <select class="form-select" id="kd-bigtopics-size" name="size">
                <?php foreach (KunenaBigTopics::SIZES as $size) : ?>
                    <option value="<?php echo $size ?>"<?php echo $size === $report['size'] ? ' selected' : '' ?>><?php echo Text::plural('COM_KUNENA_TOOLS_BIGTOPICS_FROM', $size) ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div>
            <button type="submit" class="kd-btn kd-btn--ghost">
                <span class="fa-solid fa-filter" aria-hidden="true"></span>
                <?php echo Text::_('JSEARCH_FILTER_SUBMIT') ?>
            </button>
        </div>
    </form>

    <?php if ($report['topics']) : ?>
        <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post">
            <input type="hidden" name="task" value="tools.lockBigTopics">
            <input type="hidden" name="size" value="<?php echo (int) $report['size'] ?>">
            <?php echo HTMLHelper::_('form.token') ?>

            <div class="kd-scroll">
                <table class="table">
                    <thead>
                        <tr>
                            <td class="w-1 text-center">
                                <input type="checkbox" class="form-check-input" data-kd-all="kd-bigtopic"
                                       aria-label="<?php echo $this->escape(Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_SELECT_ALL')) ?>">
                            </td>
                            <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_REPORTS_POST') ?></th>
                            <th scope="col" class="w-10 text-end"><?php echo Text::_('COM_KUNENA_TOOLS_BIGTOPICS_POSTS') ?></th>
                            <th scope="col" class="w-10 text-end"><?php echo Text::_('COM_KUNENA_TOOLS_BIGTOPICS_PAGES') ?></th>
                            <th scope="col" class="w-15"><?php echo Text::_('COM_KUNENA_TOOLS_CATHEALTH_LAST') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($report['topics'] as $topic) :
                            $id       = (int) $topic['id'];
                            $category = (int) $topic['category_id']; ?>
                            <tr>
                                <td class="text-center">
                                    <?php if (!(int) $topic['locked']) : ?>
                                        <input type="checkbox" class="form-check-input kd-bigtopic" name="ids[]" value="<?php echo $id ?>" id="kd-bigtopic-<?php echo $id ?>">
                                    <?php endif ?>
                                </td>
                                <th scope="row">
                                    <label for="kd-bigtopic-<?php echo $id ?>"><?php echo $this->escape((string) $topic['subject']) ?></label>
                                    <?php if ((int) $topic['locked']) : ?>
                                        <span class="kd-pill"><?php echo Text::_('COM_KUNENA_TOOLS_BIGTOPICS_LOCKED') ?></span>
                                    <?php endif ?>
                                    <br><span class="kd-form__hint"><?php echo $this->escape($report['categories'][$category] ?? '#' . $category) ?></span>
                                    · <a class="kd-form__hint" href="<?php echo Route::_('index.php?option=com_kunena&view=topic&layout=edit&id=' . $id) ?>"><?php echo Text::_('JACTION_EDIT') ?></a>
                                    · <a class="kd-form__hint" href="<?php echo $this->escape(Route::link('site', 'index.php?option=com_kunena&view=topic&catid=' . $category . '&id=' . $id)) ?>" target="_blank" rel="noopener"><?php echo Text::_('COM_KUNENA_TOOLS_TITLES_VIEW') ?></a>
                                </th>
                                <td class="text-end"><?php echo (int) $topic['posts'] ?></td>
                                <td class="text-end"><?php echo (int) $topic['pages'] ?></td>
                                <td><?php echo HTMLHelper::_('date', (int) $topic['last_post_time'], Text::_('DATE_FORMAT_LC4')) ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>

            <?php if ($this->canAdmin) : ?>
                <div class="kd-form__actions">
                    <button type="submit" class="kd-btn kd-btn--danger" data-kd-confirm>
                        <span class="fa-solid fa-lock" aria-hidden="true"></span>
                        <?php echo Text::_('COM_KUNENA_TOOLS_BIGTOPICS_LOCK') ?>
                    </button>
                    <span class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_BIGTOPICS_LOCK_HINT') ?></span>
                </div>
            <?php endif ?>
        </form>
    <?php endif ?>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
