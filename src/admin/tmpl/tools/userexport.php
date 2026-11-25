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

$summary = $this->tool['summary'];
$page    = 'index.php?option=com_kunena&view=tools&layout=userexport';
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-file-zipper" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title"><?php echo Text::_('COM_KUNENA_TOOLS_EXPORT') ?></h2>
                <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_TOOLS_EXPORT_INTRO') ?></p>
            </div>
        </div>
    </section>

    <form action="<?php echo Route::_('index.php') ?>" method="get" class="kd-panel">
        <input type="hidden" name="option" value="com_kunena">
        <input type="hidden" name="view" value="tools">
        <input type="hidden" name="layout" value="userexport">
        <div class="kd-form">
            <div class="kd-form__field">
                <label for="kd-export-search"><?php echo Text::_('COM_KUNENA_TOOLS_USER_SEARCH') ?></label>
                <input type="search" class="form-control" id="kd-export-search" name="search" value="<?php echo $this->escape($this->tool['search']) ?>"
                       data-kd-users="<?php echo $this->escape($this->usersEndpoint) ?>" data-kd-users-go="<?php echo $this->escape(Route::_($page . '&userid=__ID__', false)) ?>"
                       placeholder="<?php echo $this->escape(Text::_('COM_KUNENA_TOOLS_USER_SEARCH_HINT')) ?>">
            </div>
            <div>
                <button type="submit" class="kd-btn kd-btn--ghost">
                    <span class="fa-solid fa-magnifying-glass" aria-hidden="true"></span>
                    <?php echo Text::_('JSEARCH_FILTER_SUBMIT') ?>
                </button>
            </div>
        </div>

        <?php if ($this->tool['search'] !== '') : ?>
            <?php if ($this->tool['matches']) : ?>
                <ul class="kd-tags mt-3">
                    <?php foreach ($this->tool['matches'] as $id => $match) : ?>
                        <li><a href="<?php echo Route::_($page . '&userid=' . (int) $id) ?>"><?php echo $this->escape($match['username']) ?> (#<?php echo (int) $id ?>)</a></li>
                    <?php endforeach ?>
                </ul>
            <?php else : ?>
                <p class="kd-form__hint mt-3"><?php echo Text::_('COM_KUNENA_TOOLS_USER_NO_MATCH') ?></p>
            <?php endif ?>
        <?php endif ?>
    </form>

    <?php if ($this->tool['userid'] && !$summary) : ?>
        <div class="alert alert-warning"><?php echo Text::_('COM_KUNENA_PROFILE_NO_USER') ?></div>
    <?php endif ?>

    <?php if ($summary) : ?>
        <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post" class="kd-panel">
            <input type="hidden" name="task" value="tools.exportUser">
            <input type="hidden" name="userid" value="<?php echo (int) $summary['id'] ?>">
            <?php echo HTMLHelper::_('form.token') ?>
            <h3 class="kd-panel__title"><?php echo $this->escape($summary['username']) ?> · <?php echo $this->escape($summary['name']) ?> (#<?php echo (int) $summary['id'] ?>)</h3>
            <ul class="kd-tags">
                <li><?php echo Text::plural('COM_KUNENA_TOOLS_EXPORT_N_MESSAGES', (int) $summary['messages']['total']) ?></li>
                <li><?php echo Text::plural('COM_KUNENA_TOOLS_EXPORT_N_ATTACHMENTS', (int) $summary['attachments']['total']) ?> (<?php echo HTMLHelper::_('number.bytes', $summary['attachments']['bytes']) ?>)</li>
            </ul>
            <p class="kd-form__hint mt-3"><?php echo Text::_('COM_KUNENA_TOOLS_EXPORT_CONTENT') ?></p>
            <?php if ($this->canAdmin) : ?>
                <div class="kd-form__actions">
                    <button type="submit" class="kd-btn kd-btn--primary">
                        <span class="fa-solid fa-download" aria-hidden="true"></span>
                        <?php echo Text::_('COM_KUNENA_TOOLS_EXPORT_DOWNLOAD') ?>
                    </button>
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
