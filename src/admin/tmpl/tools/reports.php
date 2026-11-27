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
use Kunena\Forum\Libraries\Tools\KunenaReportsOverview;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$report = $this->tool;
$name   = fn (int $id) => $id > 0 ? $this->escape($report['names'][$id] ?? '#' . $id) : Text::_('COM_KUNENA_TOOLS_REPORTS_GUEST');
$user   = fn (int $id) => $id > 0 ? '<a href="' . Route::_('index.php?option=com_kunena&view=user&layout=edit&userid=' . $id) . '">' . $name($id) . '</a>' : $name($id);
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero<?php echo $report['total'] ? ' is-problem' : '' ?>">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-flag" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title"><?php echo Text::plural('COM_KUNENA_TOOLS_REPORTS_N_REPORTS', $report['total'], $report['days']) ?></h2>
                <p class="kd-hero__meta"><?php echo Text::sprintf('COM_KUNENA_TOOLS_REPORTS_INTRO', KunenaReportsOverview::LIMIT) ?></p>
            </div>
        </div>
    </section>

    <?php if (!$report['logging']) : ?>
        <div class="alert alert-warning">
            <?php echo Text::_('COM_KUNENA_TOOLS_REPORTS_NO_LOG') ?>
            <a href="<?php echo $this->escape(ConfigHelper::link()) ?>"><?php echo Text::_('COM_KUNENA_TOOLS_SECURITY_OPEN_KUNENA') ?> ›</a>
        </div>
    <?php endif ?>

    <form action="<?php echo Route::_('index.php') ?>" method="get" class="kd-panel kd-form">
        <input type="hidden" name="option" value="com_kunena">
        <input type="hidden" name="view" value="tools">
        <input type="hidden" name="layout" value="reports">
        <div class="kd-form__field">
            <label for="kd-reports-days"><?php echo Text::_('COM_KUNENA_TOOLS_MODERATORS_PERIOD') ?></label>
            <select class="form-select" id="kd-reports-days" name="days">
                <?php foreach (KunenaReportsOverview::PERIODS as $days) : ?>
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

    <?php if ($report['total']) : ?>
        <div class="kd-cards">
            <?php foreach (['authors' => 'COM_KUNENA_TOOLS_REPORTS_MOST_REPORTED', 'reporters' => 'COM_KUNENA_TOOLS_REPORTS_MOST_REPORTING'] as $key => $title) : ?>
                <section class="kd-panel" aria-labelledby="kd-reports-<?php echo $key ?>">
                    <h3 id="kd-reports-<?php echo $key ?>" class="kd-panel__title"><?php echo Text::_($title) ?></h3>
                    <ul class="kd-tags">
                        <?php foreach ($report[$key] as $id => $count) : ?>
                            <li><?php echo $user((int) $id) ?> <span class="kd-pill"><?php echo (int) $count ?></span></li>
                        <?php endforeach ?>
                    </ul>
                </section>
            <?php endforeach ?>
        </div>

        <div class="kd-scroll">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col" class="w-15"><?php echo Text::_('COM_KUNENA_TOOLS_REPORTS_WHEN') ?></th>
                        <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_REPORTS_POST') ?></th>
                        <th scope="col" class="w-15"><?php echo Text::_('COM_KUNENA_TOOLS_REPORTS_AUTHOR') ?></th>
                        <th scope="col" class="w-15"><?php echo Text::_('COM_KUNENA_TOOLS_REPORTS_REPORTER') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($report['reports'] as $item) :
                        $topic = $report['topics'][$item['topic']] ?? null; ?>
                        <tr>
                            <td><?php echo HTMLHelper::_('date', $item['time'], Text::_('DATE_FORMAT_LC5')) ?></td>
                            <th scope="row">
                                <?php if ($topic) : ?>
                                    <a href="<?php echo $this->escape(Route::link('site', 'index.php?option=com_kunena&view=topic&catid=' . (int) $topic['category_id'] . '&id=' . (int) $topic['id'] . ($item['message'] ? '&mesid=' . $item['message'] : ''))) ?>" target="_blank" rel="noopener"><?php echo $this->escape((string) $topic['subject']) ?></a>
                                    <?php if ((int) $topic['hold'] !== 0) : ?>
                                        <span class="kd-pill"><?php echo Text::_('COM_KUNENA_TOOLS_REPORTS_HIDDEN') ?></span>
                                    <?php endif ?>
                                <?php else : ?>
                                    <span class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_REPORTS_GONE') ?></span>
                                <?php endif ?>
                                <?php if ($item['reason'] !== '' || $item['text'] !== '') : ?>
                                    <?php // The parts which are there, joined (both are trimmed): trim() with " —" took away bytes of a letter at an end ("мусор"), and the escape of the broken text was empty. ?>
                                    <br><span class="kd-form__hint kd-break"><?php echo $this->escape(implode(' — ', array_filter([$item['reason'], $item['text']], 'strlen'))) ?></span>
                                <?php endif ?>
                            </th>
                            <td><?php echo $user($item['author']) ?></td>
                            <td><?php echo $user($item['reporter']) ?></td>
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
