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
use Kunena\Forum\Libraries\Install\KunenaObsoleteFiles;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Version\KunenaVersion;

Text::script('COM_KUNENA_TOOLS_PRUNE_FILES_CONFIRM');

$files     = $this->obsoleteFiles;
$deletable = array_filter($files, static fn ($file) => $file['status'] === KunenaObsoleteFiles::OBSOLETE);
$size      = array_sum(array_column($deletable, 'size'));
$canDelete = $this->canDeleteFiles && $deletable;
$statuses  = [
    KunenaObsoleteFiles::OBSOLETE => ['COM_KUNENA_TOOLS_PRUNE_FILES_STATUS_OBSOLETE', 'ok'],
    KunenaObsoleteFiles::IN_USE   => ['COM_KUNENA_TOOLS_PRUNE_FILES_STATUS_IN_USE', 'warn'],
    KunenaObsoleteFiles::LINKED   => ['COM_KUNENA_TOOLS_PRUNE_FILES_STATUS_LINKED', 'warn'],
];
?>

<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post" id="adminForm" name="adminForm">
    <input type="hidden" name="task" value="">
    <?php echo HTMLHelper::_('form.token'); ?>

    <div id="kunena" class="kd kd-prune">
        <section class="kd-hero kd-diag__hero<?php echo $deletable ? ' is-problem' : '' ?>">
            <div class="kd-hero__brand">
                <span class="kd-diag__heroicon fa-solid fa-<?php echo $deletable ? 'broom' : 'circle-check' ?>" aria-hidden="true"></span>
                <div>
                    <h2 class="kd-hero__title">
                        <?php echo $deletable
                            ? Text::plural('COM_KUNENA_TOOLS_PRUNE_FILES_FOUND', \count($deletable), HTMLHelper::_('number.bytes', $size))
                            : Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_NONE') ?>
                    </h2>
                    <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_INTRO') ?></p>
                </div>
            </div>
            <?php if ($canDelete) : ?>
                <div class="kd-hero__actions">
                    <button type="submit" class="kd-btn kd-btn--danger" name="task" value="tools.pruneFiles" data-kd-confirm>
                        <span class="fa-solid fa-trash-can" aria-hidden="true"></span>
                        <?php echo Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_DELETE') ?>
                    </button>
                </div>
            <?php endif ?>
        </section>

        <?php if ($deletable && !$this->canDeleteFiles) : ?>
            <div class="alert alert-info"><?php echo Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_ADMIN_ONLY') ?></div>
        <?php endif ?>

        <?php if ($files) : ?>
            <table class="table kd-prune__files">
                <thead>
                    <tr>
                        <td class="w-1 text-center">
                            <?php if ($canDelete) : ?>
                                <input type="checkbox" class="form-check-input" id="kd-prune-all" checked
                                       title="<?php echo $this->escape(Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_SELECT_ALL')) ?>"
                                       aria-label="<?php echo $this->escape(Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_SELECT_ALL')) ?>">
                            <?php endif ?>
                        </td>
                        <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_FILE') ?></th>
                        <th scope="col" class="w-10 text-end"><?php echo Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_SIZE') ?></th>
                        <th scope="col" class="w-10 d-none d-md-table-cell"><?php echo Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_DATE') ?></th>
                        <th scope="col" class="w-20"><?php echo Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_STATUS') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($files as $path => $file) :
                        [$label, $tone] = $statuses[$file['status']];
                        $id             = 'kd-prune-' . md5($path); ?>
                        <tr>
                            <td class="text-center">
                                <?php if ($canDelete && $file['status'] === KunenaObsoleteFiles::OBSOLETE) : ?>
                                    <input type="checkbox" class="form-check-input kd-prune__file" name="files[]" id="<?php echo $id ?>"
                                           value="<?php echo $this->escape($path) ?>" checked>
                                <?php endif ?>
                            </td>
                            <th scope="row">
                                <label for="<?php echo $id ?>"><code class="kd-check__code"><?php echo $this->escape($path) ?></code></label>
                            </th>
                            <td class="text-end"><?php echo HTMLHelper::_('number.bytes', $file['size']) ?></td>
                            <td class="d-none d-md-table-cell"><?php echo HTMLHelper::_('date', $file['time'], Text::_('DATE_FORMAT_LC4')) ?></td>
                            <td><span class="kd-pill kd-pill--<?php echo $tone ?>"><?php echo Text::_($label) ?></span></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        <?php endif ?>

        <footer class="kd-footer">
            <?php echo KunenaVersion::getLongVersionHTML(); ?>
        </footer>
    </div>
</form>

<script>
    (function () {
        var all = document.getElementById('kd-prune-all');
        var boxes = document.querySelectorAll('#kunena .kd-prune__file');

        if (all) {
            all.addEventListener('change', function () {
                boxes.forEach(function (box) {
                    box.checked = all.checked;
                });
            });
        }

        document.querySelectorAll('#kunena [data-kd-confirm]').forEach(function (button) {
            button.addEventListener('click', function (event) {
                if (!window.confirm(Joomla.Text._('COM_KUNENA_TOOLS_PRUNE_FILES_CONFIRM'))) {
                    event.preventDefault();
                }
            });
        });
    })();
</script>
