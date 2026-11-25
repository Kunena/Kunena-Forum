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
use Kunena\Forum\Administrator\Helper\ConfigHelper;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Tools\KunenaWarningsOverview;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$report  = $this->tool;
$reached = array_filter($report['users'], static fn ($user) => $user['reaches']);
$user    = fn (int $id) => '<a href="' . Route::_('index.php?option=com_kunena&view=user&layout=edit&userid=' . $id) . '">'
    . $this->escape($report['names'][$id] ?? '#' . $id) . '</a>';
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero<?php echo $reached ? ' is-problem' : '' ?>">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-triangle-exclamation" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title"><?php echo Text::plural('COM_KUNENA_TOOLS_WARNINGS_N_USERS', \count($report['users'])) ?></h2>
                <p class="kd-hero__meta">
                    <?php echo $report['threshold'] > 0 ? Text::sprintf('COM_KUNENA_TOOLS_WARNINGS_INTRO', $report['threshold']) : Text::_('COM_KUNENA_TOOLS_WARNINGS_INTRO_NO_BAN') ?>
                </p>
            </div>
        </div>
    </section>

    <?php if (!$report['enabled']) : ?>
        <div class="alert alert-info">
            <?php echo Text::_('COM_KUNENA_TOOLS_WARNINGS_OFF') ?>
            <a href="<?php echo $this->escape(ConfigHelper::link()) ?>"><?php echo Text::_('COM_KUNENA_TOOLS_SECURITY_OPEN_KUNENA') ?> ›</a>
        </div>
    <?php endif ?>

    <?php if ($report['users']) : ?>
        <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post">
            <input type="hidden" name="task" value="tools.banWarned">
            <?php echo HTMLHelper::_('form.token') ?>

            <div class="kd-scroll">
                <table class="table">
                    <thead>
                        <tr>
                            <td class="w-1"></td>
                            <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_PROFILESPAM_USER') ?></th>
                            <th scope="col" class="w-15 text-end"><?php echo Text::_('COM_KUNENA_TOOLS_WARNINGS_POINTS') ?></th>
                            <th scope="col" class="w-15 text-end"><?php echo Text::_('COM_KUNENA_TOOLS_WARNINGS_COUNT') ?></th>
                            <th scope="col" class="w-15"><?php echo Text::_('COM_KUNENA_TOOLS_MODERATORS_LAST') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($report['users'] as $id => $row) : ?>
                            <tr>
                                <td class="text-center">
                                    <?php if ($row['reaches']) : ?>
                                        <input type="checkbox" class="form-check-input" name="ids[]" value="<?php echo (int) $id ?>"
                                               aria-label="<?php echo $this->escape($report['names'][$id] ?? '#' . $id) ?>">
                                    <?php endif ?>
                                </td>
                                <th scope="row">
                                    <?php echo $user((int) $id) ?>
                                    <?php if ($row['reaches']) : ?>
                                        <span class="kd-pill kd-pill--warn"><?php echo Text::_('COM_KUNENA_TOOLS_WARNINGS_REACHES') ?></span>
                                    <?php endif ?>
                                </th>
                                <td class="text-end"><?php echo (int) $row['points'] ?></td>
                                <td class="text-end"><?php echo (int) $row['warnings'] ?></td>
                                <td><?php echo HTMLHelper::_('date', (int) $row['last'], Text::_('DATE_FORMAT_LC4')) ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>

            <?php if ($reached && $this->canAdmin) : ?>
                <div class="kd-form__actions">
                    <button type="submit" class="kd-btn kd-btn--danger" data-kd-confirm>
                        <span class="fa-solid fa-user-lock" aria-hidden="true"></span>
                        <?php echo Text::_('COM_KUNENA_TOOLS_WARNINGS_BAN') ?>
                    </button>
                    <span class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_WARNINGS_BAN_HINT') ?></span>
                </div>
            <?php endif ?>
        </form>
    <?php endif ?>

    <form action="<?php echo Route::_('index.php') ?>" method="get" class="kd-panel kd-form">
        <input type="hidden" name="option" value="com_kunena">
        <input type="hidden" name="view" value="tools">
        <input type="hidden" name="layout" value="warnings">
        <div class="kd-form__field">
            <label for="kd-warnings-days"><?php echo Text::_('COM_KUNENA_TOOLS_WARNINGS_RECENT') ?></label>
            <select class="form-select" id="kd-warnings-days" name="days">
                <?php foreach (KunenaWarningsOverview::PERIODS as $days) : ?>
                    <option value="<?php echo $days ?>"<?php echo $days === $report['days'] ? ' selected' : '' ?>><?php echo Text::plural('COM_KUNENA_TOOLS_MODERATORS_LAST_DAYS', $days) ?></option>
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

    <?php if ($report['moderators']) : ?>
        <section class="kd-panel" aria-labelledby="kd-warnings-moderators">
            <h3 id="kd-warnings-moderators" class="kd-panel__title"><?php echo Text::_('COM_KUNENA_TOOLS_WARNINGS_BY_MODERATOR') ?></h3>
            <ul class="kd-tags">
                <?php foreach ($report['moderators'] as $id => $count) : ?>
                    <li><?php echo $user((int) $id) ?> <span class="kd-pill"><?php echo (int) $count ?></span></li>
                <?php endforeach ?>
            </ul>
        </section>
    <?php endif ?>

    <?php if ($report['recent']) : ?>
        <div class="kd-scroll">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col" class="w-15"><?php echo Text::_('COM_KUNENA_TOOLS_REPORTS_WHEN') ?></th>
                        <th scope="col" class="w-15"><?php echo Text::_('COM_KUNENA_TOOLS_PROFILESPAM_USER') ?></th>
                        <th scope="col" class="w-10 text-end"><?php echo Text::_('COM_KUNENA_TOOLS_WARNINGS_POINTS') ?></th>
                        <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_WARNINGS_REASON') ?></th>
                        <th scope="col" class="w-15"><?php echo Text::_('COM_KUNENA_TOOLS_WARNINGS_MODERATOR') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($report['recent'] as $warning) : ?>
                        <tr>
                            <td><?php echo HTMLHelper::_('date', (int) $warning['created_time'], Text::_('DATE_FORMAT_LC5')) ?></td>
                            <th scope="row"><?php echo $user((int) $warning['user_id']) ?></th>
                            <td class="text-end"><?php echo (int) $warning['points'] ?></td>
                            <td><span class="kd-break"><?php echo $this->escape((string) $warning['reason']) ?></span></td>
                            <td><?php echo $user((int) $warning['moderator_id']) ?></td>
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
