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
use Kunena\Forum\Libraries\Version\KunenaVersion;

$tasks    = $this->tool['tasks'];
$problems = array_filter($tasks, static fn ($item) => !\in_array($item['state'], ['ok', 'never'], true));
$action   = KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools');
$date     = static fn ($value) => $value ? HTMLHelper::_('date', $value, Text::_('DATE_FORMAT_LC2')) : Text::_('COM_KUNENA_TOOLS_NEVER');
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero<?php echo $problems || !$this->tool['plugin'] ? ' is-problem' : '' ?>">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-clock-rotate-left" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title">
                    <?php echo $problems ? Text::plural('COM_KUNENA_TOOLS_TASKS_N_PROBLEMS', \count($problems)) : Text::_('COM_KUNENA_TOOLS_TASKS_OK') ?>
                </h2>
                <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_TOOLS_TASKS_INTRO') ?></p>
            </div>
        </div>
        <div class="kd-hero__actions">
            <a class="kd-btn kd-btn--ghost" href="<?php echo Route::_('index.php?option=com_scheduler&view=tasks') ?>">
                <span class="fa-solid fa-list-check" aria-hidden="true"></span>
                <?php echo Text::_('COM_KUNENA_TOOLS_TASKS_SCHEDULER') ?>
            </a>
        </div>
    </section>

    <ul class="kd-checks kd-checks--compact mb-4">
        <li class="kd-check<?php echo $this->tool['plugin'] ? '' : ' is-problem' ?>">
            <span class="kd-check__icon fa-solid fa-<?php echo $this->tool['plugin'] ? 'circle-check' : 'triangle-exclamation' ?>" aria-hidden="true"></span>
            <div class="kd-check__body"><span class="kd-check__title"><?php echo Text::_('COM_KUNENA_TOOLS_TASKS_PLUGIN') ?></span></div>
            <span class="kd-check__count"><?php echo Text::_($this->tool['plugin'] ? 'COM_KUNENA_TOOLS_ENABLED' : 'COM_KUNENA_TOOLS_DISABLED') ?></span>
        </li>
        <li class="kd-check">
            <span class="kd-check__icon fa-solid fa-circle-info" aria-hidden="true"></span>
            <div class="kd-check__body">
                <span class="kd-check__title"><?php echo Text::_('COM_KUNENA_TOOLS_TASKS_LAZY') ?></span>
                <span class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_TASKS_LAZY_DESC') ?></span>
            </div>
            <span class="kd-check__count"><?php echo Text::_($this->tool['lazy'] ? 'COM_KUNENA_TOOLS_ENABLED' : 'COM_KUNENA_TOOLS_DISABLED') ?></span>
        </li>
    </ul>

    <div class="kd-scroll">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_TASKS_ROUTINE') ?></th>
                    <th scope="col" class="w-10"><?php echo Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_STATUS') ?></th>
                    <th scope="col" class="w-15 d-none d-md-table-cell"><?php echo Text::_('COM_KUNENA_TOOLS_TASKS_LAST') ?></th>
                    <th scope="col" class="w-15 d-none d-md-table-cell"><?php echo Text::_('COM_KUNENA_TOOLS_TASKS_NEXT') ?></th>
                    <th scope="col" class="w-20"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $routine => $item) :
                    $task = $item['task']; ?>
                    <tr>
                        <th scope="row">
                            <?php echo Text::_($item['prefix'] . '_TITLE') ?>
                            <br><code class="kd-check__code"><?php echo $this->escape($routine) ?></code>
                            <?php if ($task) : ?>
                                <span class="kd-form__hint">· <?php echo Text::sprintf('COM_KUNENA_TOOLS_TASKS_RUNS', (int) $task['times_executed'], (int) $task['times_failed']) ?></span>
                            <?php endif ?>
                        </th>
                        <td>
                            <span class="kd-pill kd-pill--<?php echo \in_array($item['state'], ['ok', 'never'], true) ? 'ok' : 'warn' ?>">
                                <?php echo Text::_('COM_KUNENA_TOOLS_TASKS_STATE_' . strtoupper($item['state'])) ?>
                            </span>
                            <?php if ($item['state'] === 'failed') : ?>
                                <br><span class="kd-form__hint"><?php echo Text::sprintf('COM_KUNENA_TOOLS_TASKS_EXIT', (int) $task['last_exit_code']) ?></span>
                            <?php endif ?>
                        </td>
                        <td class="d-none d-md-table-cell"><?php echo $task ? $date($task['last_execution']) : '—' ?></td>
                        <td class="d-none d-md-table-cell"><?php echo $task && (int) $task['state'] === 1 ? $date($task['next_execution']) : '—' ?></td>
                        <td>
                            <div class="kd-check__actions">
                                <?php if ($task) : ?>
                                    <?php if ($this->canAdmin) : ?>
                                        <form action="<?php echo $action ?>" method="post">
                                            <input type="hidden" name="task" value="tools.runScheduledTask">
                                            <input type="hidden" name="task_id" value="<?php echo (int) $task['id'] ?>">
                                            <?php echo HTMLHelper::_('form.token') ?>
                                            <button type="submit" class="kd-btn kd-btn--sm kd-btn--primary">
                                                <span class="fa-solid fa-play" aria-hidden="true"></span>
                                                <?php echo Text::_('COM_KUNENA_TOOLS_TASKS_RUN') ?>
                                            </button>
                                        </form>
                                    <?php endif ?>
                                    <a class="kd-btn kd-btn--sm kd-btn--ghost" href="<?php echo Route::_('index.php?option=com_scheduler&task=task.edit&id=' . (int) $task['id']) ?>">
                                        <?php echo Text::_('JACTION_EDIT') ?>
                                    </a>
                                <?php else : ?>
                                    <a class="kd-btn kd-btn--sm kd-btn--ghost" href="<?php echo Route::_('index.php?option=com_scheduler&view=select&layout=default') ?>">
                                        <span class="fa-solid fa-plus" aria-hidden="true"></span>
                                        <?php echo Text::_('COM_KUNENA_TOOLS_TASKS_CREATE') ?>
                                    </a>
                                <?php endif ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
