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
use Kunena\Forum\Libraries\Tools\KunenaDataRetention;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$report = $this->tool;
$total  = array_sum(array_column($report['stats'], 'bytes'));
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-box-archive" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title"><?php echo Text::sprintf('COM_KUNENA_TOOLS_RETENTION_TOTAL', HTMLHelper::_('number.bytes', $total)) ?></h2>
                <p class="kd-hero__meta"><?php echo Text::sprintf('COM_KUNENA_TOOLS_RETENTION_INTRO', KunenaDataRetention::NEW_DAYS) ?></p>
            </div>
        </div>
    </section>

    <form action="<?php echo Route::_('index.php') ?>" method="get" class="kd-panel kd-form">
        <input type="hidden" name="option" value="com_kunena">
        <input type="hidden" name="view" value="tools">
        <input type="hidden" name="layout" value="dataretention">
        <div class="kd-form__field">
            <label for="kd-retention-days"><?php echo Text::_('COM_KUNENA_TOOLS_RETENTION_KEEP') ?></label>
            <select class="form-select" id="kd-retention-days" name="days">
                <?php foreach (KunenaDataRetention::AGES as $days) : ?>
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

    <div class="kd-scroll">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_RETENTION_TABLE') ?></th>
                    <th scope="col" class="w-10 text-end"><?php echo Text::_('COM_KUNENA_TOOLS_RETENTION_ROWS') ?></th>
                    <th scope="col" class="w-10 text-end"><?php echo Text::_('COM_KUNENA_TOOLS_LIMITS_FILESIZE') ?></th>
                    <th scope="col" class="w-15"><?php echo Text::_('COM_KUNENA_TOOLS_RETENTION_OLDEST') ?></th>
                    <th scope="col" class="w-25 text-end"><?php echo Text::plural('COM_KUNENA_TOOLS_RETENTION_OLDER', $report['days']) ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($report['stats'] as $key => $stats) :
                    $old = (int) $report['old'][$key]; ?>
                    <tr>
                        <th scope="row">
                            <?php echo Text::_('COM_KUNENA_TOOLS_RETENTION_' . strtoupper($key)) ?>
                            <br><span class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_RETENTION_' . strtoupper($key) . '_HINT') ?></span>
                        </th>
                        <td class="text-end"><?php echo number_format((int) $stats['rows']) ?></td>
                        <td class="text-end"><?php echo $stats['bytes'] ? HTMLHelper::_('number.bytes', (int) $stats['bytes']) : '—' ?></td>
                        <td><?php echo $stats['oldest'] ? HTMLHelper::_('date', (int) $stats['oldest'], Text::_('DATE_FORMAT_LC4')) : '—' ?></td>
                        <td class="text-end">
                            <?php echo number_format($old) ?>
                            <?php if ($old && $this->canAdmin) : ?>
                                <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post" class="d-inline">
                                    <input type="hidden" name="task" value="tools.pruneData">
                                    <input type="hidden" name="table" value="<?php echo $this->escape($key) ?>">
                                    <input type="hidden" name="days" value="<?php echo (int) $report['days'] ?>">
                                    <?php echo HTMLHelper::_('form.token') ?>
                                    <button type="submit" class="kd-btn kd-btn--danger" data-kd-confirm>
                                        <span class="fa-solid fa-trash" aria-hidden="true"></span>
                                        <?php echo Text::_('COM_KUNENA_TOOLS_RETENTION_DELETE') ?>
                                    </button>
                                </form>
                            <?php endif ?>
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
