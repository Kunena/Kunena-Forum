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
use Kunena\Forum\Libraries\Tools\KunenaModeratorActivity;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$report = $this->tool;
$name   = static fn (int $id) => $report['names'][$id] ?? '#' . $id;
$role   = static function (int $id) use ($report): string {
    $moderator = $report['moderators'][$id] ?? null;

    if ($moderator === null) {
        return Text::_('COM_KUNENA_TOOLS_MODERATORS_NOT_MODERATOR');
    }

    return (int) $moderator['global'] ? Text::_('COM_KUNENA_TOOLS_MODERATORS_GLOBAL') : Text::plural('COM_KUNENA_TOOLS_MODERATORS_N_CATEGORIES', (int) $moderator['categories']);
};
$waited = $report['queue']['oldest'] ? (int) floor((time() - $report['queue']['oldest']) / 86400) : 0;
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-user-shield" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title"><?php echo Text::plural('COM_KUNENA_TOOLS_MODERATORS_N_ACTIONS', array_sum(array_column($report['users'], 'total')), $report['days']) ?></h2>
                <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_TOOLS_MODERATORS_INTRO') ?></p>
            </div>
        </div>
    </section>

    <?php if (!$report['logging']) : ?>
        <div class="alert alert-warning">
            <?php echo Text::_('COM_KUNENA_TOOLS_MODERATORS_NO_LOG') ?>
            <a href="<?php echo $this->escape(ConfigHelper::link()) ?>"><?php echo Text::_('COM_KUNENA_TOOLS_SECURITY_OPEN_KUNENA') ?> ›</a>
        </div>
    <?php endif ?>

    <div class="alert alert-<?php echo $report['queue']['count'] ? 'warning' : 'info' ?>">
        <?php if ($report['queue']['count']) : ?>
            <?php echo Text::plural('COM_KUNENA_TOOLS_MODERATORS_N_WAITING', $report['queue']['count'], $waited) ?>
            <a href="<?php echo Route::_('index.php?option=com_kunena&view=tools&layout=moderation') ?>"><?php echo Text::_('COM_KUNENA_TOOLS_QUEUE') ?> ›</a>
        <?php else : ?>
            <?php echo Text::_('COM_KUNENA_TOOLS_MODERATORS_NONE_WAITING') ?>
        <?php endif ?>
    </div>

    <form action="<?php echo Route::_('index.php') ?>" method="get" class="kd-panel kd-form">
        <input type="hidden" name="option" value="com_kunena">
        <input type="hidden" name="view" value="tools">
        <input type="hidden" name="layout" value="moderators">
        <div class="kd-form__field">
            <label for="kd-moderators-days"><?php echo Text::_('COM_KUNENA_TOOLS_MODERATORS_PERIOD') ?></label>
            <select class="form-select" id="kd-moderators-days" name="days">
                <?php foreach (KunenaModeratorActivity::PERIODS as $days) : ?>
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

    <?php if ($report['users']) : ?>
        <div class="kd-scroll">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col" class="w-20"><?php echo Text::_('COM_KUNENA_TOOLS_PROFILESPAM_USER') ?></th>
                        <th scope="col" class="w-10 text-end"><?php echo Text::_('COM_KUNENA_TOOLS_MODERATORS_ACTIONS') ?></th>
                        <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_MODERATORS_MOST') ?></th>
                        <th scope="col" class="w-15"><?php echo Text::_('COM_KUNENA_TOOLS_MODERATORS_LAST') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($report['users'] as $id => $user) : ?>
                        <tr>
                            <th scope="row">
                                <?php echo $this->escape($name((int) $id)) ?>
                                <br><span class="kd-form__hint"><?php echo $this->escape($role((int) $id)) ?></span>
                            </th>
                            <td class="text-end"><?php echo (int) $user['total'] ?></td>
                            <td>
                                <?php foreach (\array_slice($user['operations'], 0, 3, true) as $operation => $count) : ?>
                                    <span class="kd-pill"><?php echo Text::_('COM_KUNENA_' . $operation) ?> · <?php echo (int) $count ?></span>
                                <?php endforeach ?>
                            </td>
                            <td><?php echo HTMLHelper::_('date', (int) $user['last'], Text::_('DATE_FORMAT_LC5')) ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php endif ?>

    <?php if ($report['idle']) : ?>
        <section class="kd-panel" aria-labelledby="kd-moderators-idle">
            <h3 id="kd-moderators-idle" class="kd-panel__title"><?php echo Text::plural('COM_KUNENA_TOOLS_MODERATORS_N_IDLE', \count($report['idle']), $report['days']) ?></h3>
            <ul class="kd-tags">
                <?php foreach (array_keys($report['idle']) as $id) : ?>
                    <li><?php echo $this->escape($name((int) $id)) ?> <span class="kd-form__hint"><?php echo $this->escape($role((int) $id)) ?></span></li>
                <?php endforeach ?>
            </ul>
        </section>
    <?php endif ?>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
