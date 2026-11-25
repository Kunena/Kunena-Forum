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

$status = $this->tool;
$ready  = $status['finder'] && $status['plugin'];
$checks = [
    ['COM_KUNENA_TOOLS_SEARCHINDEX_FINDER', $status['finder'], 'index.php?option=com_installer&view=manage&filter[search]=smart search'],
    ['COM_KUNENA_TOOLS_SEARCHINDEX_PLUGIN', $status['plugin'], 'index.php?option=com_plugins&view=plugins&filter[folder]=finder&filter[search]=kunena'],
    ['COM_KUNENA_TOOLS_SEARCHINDEX_CONTENT', $status['content'], 'index.php?option=com_plugins&view=plugins&filter[folder]=content&filter[search]=smart search'],
];
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero<?php echo $ready ? '' : ' is-problem' ?>">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-magnifying-glass" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title"><?php echo Text::_($ready ? 'COM_KUNENA_TOOLS_SEARCHINDEX_READY' : 'COM_KUNENA_TOOLS_SEARCHINDEX_NOT_READY') ?></h2>
                <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_TOOLS_SEARCHINDEX_INTRO') ?></p>
            </div>
        </div>
        <?php if ($ready) : ?>
            <div class="kd-hero__actions">
                <button type="button" class="kd-btn kd-btn--primary" data-kd-frame="<?php echo Route::_('index.php?option=com_finder&view=indexer&tmpl=component') ?>">
                    <span class="fa-solid fa-play" aria-hidden="true"></span>
                    <?php echo Text::_('COM_KUNENA_TOOLS_SEARCHINDEX_RUN') ?>
                </button>
            </div>
        <?php endif ?>
    </section>

    <section class="kd-stats kd-stats--auto">
        <div class="kd-stat<?php echo $ready && $status['indexed'] < $status['messages'] ? ' kd-stat--attention' : '' ?>">
            <span class="kd-stat__label"><?php echo Text::_('COM_KUNENA_TOOLS_SEARCHINDEX_INDEXED') ?></span>
            <span class="kd-stat__value"><?php echo (int) $status['indexed'] ?></span>
            <span class="kd-stat__hint">
                <?php echo $status['lastIndexed'] ? Text::sprintf('COM_KUNENA_TOOLS_SEARCHINDEX_LAST', HTMLHelper::_('date', $status['lastIndexed'], Text::_('DATE_FORMAT_LC2'))) : Text::_('COM_KUNENA_TOOLS_NEVER') ?>
            </span>
        </div>
        <div class="kd-stat">
            <span class="kd-stat__label"><?php echo Text::_('COM_KUNENA_TOOLS_SEARCHINDEX_MESSAGES') ?></span>
            <span class="kd-stat__value"><?php echo (int) $status['messages'] ?></span>
            <span class="kd-stat__hint"><?php echo Text::_('COM_KUNENA_TOOLS_SEARCHINDEX_MESSAGES_HINT') ?></span>
        </div>
    </section>

    <section class="kd-section" aria-labelledby="kd-index-checks">
        <h3 id="kd-index-checks" class="kd-section__title"><?php echo Text::_('COM_KUNENA_TOOLS_SEARCHINDEX_NEEDS') ?></h3>
        <ul class="kd-checks">
            <?php foreach ($checks as [$label, $ok, $link]) : ?>
                <li class="kd-check<?php echo $ok ? '' : ' is-problem' ?>">
                    <span class="kd-check__icon fa-solid fa-<?php echo $ok ? 'circle-check' : 'triangle-exclamation' ?>" aria-hidden="true"></span>
                    <div class="kd-check__body">
                        <span class="kd-check__title"><?php echo Text::_($label) ?></span>
                    </div>
                    <?php if ($ok) : ?>
                        <span class="kd-check__count"><?php echo Text::_('COM_KUNENA_TOOLS_ENABLED') ?></span>
                    <?php else : ?>
                        <div class="kd-check__actions">
                            <a class="kd-btn kd-btn--sm kd-btn--ghost" href="<?php echo Route::_($link) ?>"><?php echo Text::_('COM_KUNENA_TOOLS_SEARCHINDEX_ENABLE') ?></a>
                        </div>
                    <?php endif ?>
                </li>
            <?php endforeach ?>
        </ul>
    </section>

    <?php if ($ready && $status['indexed'] && $this->canAdmin) : ?>
        <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post" class="kd-panel">
            <input type="hidden" name="task" value="tools.purgeSearchIndex">
            <?php echo HTMLHelper::_('form.token') ?>
            <h3 class="kd-panel__title"><?php echo Text::_('COM_KUNENA_TOOLS_SEARCHINDEX_PURGE') ?></h3>
            <p class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_SEARCHINDEX_PURGE_DESC') ?></p>
            <div class="kd-form__actions">
                <button type="submit" class="kd-btn kd-btn--danger" data-kd-confirm>
                    <span class="fa-solid fa-trash-can" aria-hidden="true"></span>
                    <?php echo Text::plural('COM_KUNENA_TOOLS_SEARCHINDEX_PURGE_N', (int) $status['indexed']) ?>
                </button>
            </div>
        </form>
    <?php endif ?>

    <dialog id="kd-tool-frame" class="kd-tool-frame" aria-label="<?php echo $this->escape(Text::_('COM_KUNENA_TOOLS_SEARCHINDEX_RUN')) ?>">
        <div class="kd-tool-frame__bar">
            <button type="button" class="kd-btn kd-btn--sm kd-btn--ghost" data-kd-close data-kd-reload><?php echo Text::_('JTOOLBAR_CLOSE') ?></button>
        </div>
        <iframe src="about:blank" title="<?php echo $this->escape(Text::_('COM_KUNENA_TOOLS_SEARCHINDEX_RUN')) ?>"></iframe>
    </dialog>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
