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
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Tools\KunenaSettingsSnapshot;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$changed = $this->tool['changed'];
$preview = $this->tool['preview'];
$action  = KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools');
$show    = static fn ($value) => mb_strimwidth(KunenaSettingsSnapshot::normal($value), 0, 120, '…');
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-sliders" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title"><?php echo Text::plural('COM_KUNENA_TOOLS_SETTINGS_N_CHANGED', \count($changed)) ?></h2>
                <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_TOOLS_SETTINGS_INTRO') ?></p>
            </div>
        </div>
        <?php if ($this->canAdmin) : ?>
            <div class="kd-hero__actions">
                <form action="<?php echo $action ?>" method="post">
                    <input type="hidden" name="task" value="tools.exportSettings">
                    <?php echo HTMLHelper::_('form.token') ?>
                    <button type="submit" class="kd-btn kd-btn--primary">
                        <span class="fa-solid fa-download" aria-hidden="true"></span>
                        <?php echo Text::_('COM_KUNENA_TOOLS_SETTINGS_EXPORT') ?>
                    </button>
                </form>
            </div>
        <?php endif ?>
    </section>

    <?php if ($preview !== null) : ?>
        <section class="kd-panel" aria-labelledby="kd-settings-preview">
            <h3 id="kd-settings-preview" class="kd-panel__title"><?php echo Text::plural('COM_KUNENA_TOOLS_SETTINGS_N_CHANGES', \count($preview['changes'])) ?></h3>
            <?php if ($preview['changes']) : ?>
                <div class="kd-scroll">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_SETTINGS_NAME') ?></th>
                                <th scope="col" class="w-30"><?php echo Text::_('COM_KUNENA_TOOLS_SETTINGS_NOW') ?></th>
                                <th scope="col" class="w-30"><?php echo Text::_('COM_KUNENA_TOOLS_SETTINGS_AFTER') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($preview['changes'] as $name => $change) : ?>
                                <tr>
                                    <th scope="row"><code><?php echo $this->escape($name) ?></code></th>
                                    <td class="kd-break"><?php echo $this->escape($show($change['from'])) ?></td>
                                    <td class="kd-break"><strong><?php echo $this->escape($show($change['to'])) ?></strong></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            <?php endif ?>
            <?php if ($preview['ignored']) : ?>
                <p class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_SETTINGS_IGNORED') ?></p>
                <ul class="kd-tags">
                    <?php foreach ($preview['ignored'] as $name) : ?>
                        <li><code><?php echo $this->escape($name) ?></code></li>
                    <?php endforeach ?>
                </ul>
            <?php endif ?>
            <form action="<?php echo $action ?>" method="post" class="kd-form__actions">
                <?php echo HTMLHelper::_('form.token') ?>
                <?php if ($preview['changes']) : ?>
                    <button type="submit" class="kd-btn kd-btn--danger" name="task" value="tools.importSettings" data-kd-confirm>
                        <span class="fa-solid fa-check" aria-hidden="true"></span>
                        <?php echo Text::_('COM_KUNENA_TOOLS_SETTINGS_APPLY') ?>
                    </button>
                <?php endif ?>
                <button type="submit" class="kd-btn kd-btn--ghost" name="task" value="tools.cancelSettings">
                    <?php echo Text::_('JCANCEL') ?>
                </button>
            </form>
        </section>
    <?php elseif ($this->canAdmin) : ?>
        <form action="<?php echo $action ?>" method="post" enctype="multipart/form-data" class="kd-panel kd-form">
            <input type="hidden" name="task" value="tools.previewSettings">
            <?php echo HTMLHelper::_('form.token') ?>
            <div class="kd-form__field">
                <label for="kd-settings-file"><?php echo Text::_('COM_KUNENA_TOOLS_SETTINGS_FILE') ?></label>
                <input type="file" class="form-control" id="kd-settings-file" name="snapshot" accept=".json,application/json" required>
            </div>
            <div>
                <button type="submit" class="kd-btn kd-btn--ghost">
                    <span class="fa-solid fa-eye" aria-hidden="true"></span>
                    <?php echo Text::_('COM_KUNENA_TOOLS_SETTINGS_PREVIEW') ?>
                </button>
            </div>
        </form>
    <?php endif ?>

    <?php if ($changed) : ?>
        <section class="kd-panel" aria-labelledby="kd-settings-changed">
            <h3 id="kd-settings-changed" class="kd-panel__title"><?php echo Text::_('COM_KUNENA_TOOLS_SETTINGS_CHANGED') ?></h3>
            <div class="kd-scroll">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_SETTINGS_NAME') ?></th>
                            <th scope="col" class="w-30"><?php echo Text::_('COM_KUNENA_TOOLS_SETTINGS_VALUE') ?></th>
                            <th scope="col" class="w-30"><?php echo Text::_('COM_KUNENA_TOOLS_SETTINGS_DEFAULT') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($changed as $name => $setting) : ?>
                            <tr>
                                <th scope="row"><code><?php echo $this->escape($name) ?></code></th>
                                <td class="kd-break"><?php echo $this->escape($show($setting['value'])) ?></td>
                                <td class="kd-break kd-form__hint"><?php echo $this->escape($show($setting['default'])) ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </section>
    <?php endif ?>

    <p class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_SETTINGS_PRIVATE') ?></p>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
