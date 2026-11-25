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
use Kunena\Forum\Libraries\Tools\KunenaCategoryHealth;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$report = $this->tool;
$items  = $report['items'];
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero<?php echo $items ? ' is-problem' : '' ?>">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-folder-tree" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title">
                    <?php echo $items ? Text::plural('COM_KUNENA_TOOLS_CATHEALTH_N_FOUND', \count($items), (int) $report['categories']) : Text::_('COM_KUNENA_TOOLS_CATHEALTH_NONE') ?>
                </h2>
                <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_TOOLS_CATHEALTH_INTRO') ?></p>
            </div>
        </div>
    </section>

    <form action="<?php echo Route::_('index.php') ?>" method="get" class="kd-panel kd-form">
        <input type="hidden" name="option" value="com_kunena">
        <input type="hidden" name="view" value="tools">
        <input type="hidden" name="layout" value="categoryhealth">
        <div class="kd-form__field">
            <label for="kd-cathealth-days"><?php echo Text::_('COM_KUNENA_TOOLS_CATHEALTH_QUIET_FOR') ?></label>
            <select class="form-select" id="kd-cathealth-days" name="days">
                <?php foreach (KunenaCategoryHealth::PERIODS as $days) : ?>
                    <option value="<?php echo $days ?>"<?php echo $days === $report['days'] ? ' selected' : '' ?>><?php echo Text::plural('COM_KUNENA_TOOLS_UNANSWERED_OLDER', $days) ?></option>
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

    <?php if ($items) : ?>
        <div class="kd-scroll">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col"><?php echo Text::_('COM_KUNENA_GEN_CATEGORY') ?></th>
                        <th scope="col" class="w-20"><?php echo Text::_('COM_KUNENA_TOOLS_BANS_PROBLEMS') ?></th>
                        <th scope="col" class="w-10 text-end"><?php echo Text::_('COM_KUNENA_TOOLS_UNANSWERED_TOPICS') ?></th>
                        <th scope="col" class="w-15"><?php echo Text::_('COM_KUNENA_TOOLS_CATHEALTH_LAST') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item) : ?>
                        <tr>
                            <th scope="row">
                                <a href="<?php echo Route::_('index.php?option=com_kunena&view=category&layout=edit&catid=' . (int) $item['id']) ?>"><?php echo $this->escape($item['name']) ?></a>
                                <?php if ($item['path'] !== '') : ?>
                                    <br><span class="kd-form__hint"><?php echo $this->escape($item['path']) ?></span>
                                <?php endif ?>
                            </th>
                            <td><span class="kd-pill kd-pill--warn"><?php echo Text::_('COM_KUNENA_TOOLS_CATHEALTH_' . strtoupper($item['problem'])) ?></span></td>
                            <td class="text-end"><?php echo (int) $item['topics'] ?></td>
                            <td><?php echo $item['last'] ? HTMLHelper::_('date', (int) $item['last'], Text::_('DATE_FORMAT_LC4')) : '—' ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>

        <p class="kd-form__hint">
            <?php echo Text::_('COM_KUNENA_TOOLS_CATHEALTH_NEXT') ?>
            <a href="<?php echo Route::_('index.php?option=com_kunena&view=tools&layout=mergecategories') ?>"><?php echo Text::_('COM_KUNENA_TOOLS_CATMERGE') ?> ›</a>
            · <a href="<?php echo Route::_('index.php?option=com_kunena&view=tools&layout=prune') ?>"><?php echo Text::_('COM_KUNENA_C_PRUNETAB') ?> ›</a>
            · <a href="<?php echo Route::_('index.php?option=com_kunena&view=categories') ?>"><?php echo Text::_('COM_KUNENA_CATEGORY_MANAGER') ?> ›</a>
        </p>
    <?php endif ?>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
