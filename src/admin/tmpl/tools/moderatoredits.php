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
use Kunena\Forum\Libraries\Tools\KunenaModeratorEdits;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$report = $this->tool;
$name   = fn (int $id) => $this->escape($report['names'][$id] ?? '#' . $id);
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-user-pen" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title"><?php echo Text::plural('COM_KUNENA_TOOLS_EDITS_N_EDITS', array_sum(array_column($report['editors'], 'edits')), $report['days']) ?></h2>
                <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_TOOLS_EDITS_INTRO') ?></p>
            </div>
        </div>
    </section>

    <form action="<?php echo Route::_('index.php') ?>" method="get" class="kd-panel kd-form">
        <input type="hidden" name="option" value="com_kunena">
        <input type="hidden" name="view" value="tools">
        <input type="hidden" name="layout" value="moderatoredits">
        <div class="kd-form__field">
            <label for="kd-edits-days"><?php echo Text::_('COM_KUNENA_TOOLS_MODERATORS_PERIOD') ?></label>
            <select class="form-select" id="kd-edits-days" name="days">
                <?php foreach (KunenaModeratorEdits::PERIODS as $days) : ?>
                    <option value="<?php echo $days ?>"<?php echo $days === $report['days'] ? ' selected' : '' ?>><?php echo Text::plural('COM_KUNENA_TOOLS_MODERATORS_LAST_DAYS', $days) ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="kd-edits-noreason" name="noreason" value="1"<?php echo $report['noreason'] ? ' checked' : '' ?>>
            <label class="form-check-label" for="kd-edits-noreason"><?php echo Text::_('COM_KUNENA_TOOLS_EDITS_ONLY_NOREASON') ?></label>
        </div>
        <div>
            <button type="submit" class="kd-btn kd-btn--ghost">
                <span class="fa-solid fa-filter" aria-hidden="true"></span>
                <?php echo Text::_('JSEARCH_FILTER_SUBMIT') ?>
            </button>
        </div>
    </form>

    <?php if ($report['editors']) : ?>
        <section class="kd-panel" aria-labelledby="kd-edits-editors">
            <h3 id="kd-edits-editors" class="kd-panel__title"><?php echo Text::_('COM_KUNENA_TOOLS_EDITS_EDITORS') ?></h3>
            <ul class="kd-tags">
                <?php foreach ($report['editors'] as $id => $editor) : ?>
                    <li>
                        <?php echo $name((int) $id) ?> <span class="kd-pill"><?php echo (int) $editor['edits'] ?></span>
                        <?php if ($editor['noreason']) : ?>
                            <span class="kd-pill kd-pill--warn"><?php echo Text::plural('COM_KUNENA_TOOLS_EDITS_N_NOREASON', (int) $editor['noreason']) ?></span>
                        <?php endif ?>
                    </li>
                <?php endforeach ?>
            </ul>
        </section>
    <?php endif ?>

    <?php if ($report['edits']) : ?>
        <div class="kd-scroll">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col" class="w-15"><?php echo Text::_('COM_KUNENA_TOOLS_REPORTS_WHEN') ?></th>
                        <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_REPORTS_POST') ?></th>
                        <th scope="col" class="w-15"><?php echo Text::_('COM_KUNENA_TOOLS_REPORTS_AUTHOR') ?></th>
                        <th scope="col" class="w-15"><?php echo Text::_('COM_KUNENA_TOOLS_EDITS_EDITOR') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($report['edits'] as $edit) :
                        $reason = trim((string) $edit['modified_reason']); ?>
                        <tr>
                            <td><?php echo HTMLHelper::_('date', (int) $edit['modified_time'], Text::_('DATE_FORMAT_LC5')) ?></td>
                            <th scope="row">
                                <a href="<?php echo $this->escape(Route::link('site', 'index.php?option=com_kunena&view=topic&catid=' . (int) $edit['catid'] . '&id=' . (int) $edit['thread'] . '&mesid=' . (int) $edit['id'])) ?>" target="_blank" rel="noopener"><?php echo $this->escape((string) $edit['subject']) ?></a>
                                <br>
                                <?php if ($reason !== '') : ?>
                                    <span class="kd-form__hint kd-break"><?php echo $this->escape($reason) ?></span>
                                <?php else : ?>
                                    <span class="kd-pill kd-pill--warn"><?php echo Text::_('COM_KUNENA_TOOLS_EDITS_NOREASON') ?></span>
                                <?php endif ?>
                            </th>
                            <td><?php echo (int) $edit['userid'] ? $name((int) $edit['userid']) : $this->escape((string) $edit['name']) ?></td>
                            <td><?php echo $name((int) $edit['modified_by']) ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php endif ?>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
