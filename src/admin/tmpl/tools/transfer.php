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
use Kunena\Forum\Libraries\Version\KunenaVersion;

// The categories as a tree: each one after its parent, indented by its depth.
$children = [];

foreach ($this->tool['categories'] as $category) {
    $children[(int) $category['parentid']][] = $category;
}

$options = [];
$walk    = static function (int $parent, int $depth) use (&$walk, &$options, $children): void {
    foreach ($children[$parent] ?? [] as $category) {
        $options[] = [(int) $category['id'], str_repeat('— ', $depth) . $category['name']];
        $walk((int) $category['id'], $depth + 1);
    }
};
$walk(0, 0);

$action = KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools');
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-right-left" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title"><?php echo Text::_('COM_KUNENA_TOOLS_TRANSFER') ?></h2>
                <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_TOOLS_TRANSFER_INTRO') ?></p>
            </div>
        </div>
    </section>

    <?php if (!$this->canAdmin) : ?>
        <div class="alert alert-info"><?php echo Text::_('COM_KUNENA_TOOLS_ADMIN_ONLY') ?></div>
    <?php else : ?>
        <div class="kd-columns">
            <form action="<?php echo $action ?>" method="post" class="kd-panel" aria-labelledby="kd-export-title">
                <input type="hidden" name="task" value="tools.exportCategories">
                <?php echo HTMLHelper::_('form.token') ?>
                <h3 id="kd-export-title" class="kd-panel__title">
                    <span class="fa-solid fa-file-export" aria-hidden="true"></span>
                    <?php echo Text::_('COM_KUNENA_TOOLS_TRANSFER_EXPORT') ?>
                </h3>
                <div class="kd-form__field">
                    <label for="kd-export-root"><?php echo Text::_('COM_KUNENA_TOOLS_TRANSFER_ROOT') ?></label>
                    <select class="form-select" id="kd-export-root" name="root">
                        <option value="0"><?php echo Text::_('COM_KUNENA_TOOLS_TRANSFER_ALL') ?></option>
                        <?php foreach ($options as [$id, $label]) : ?>
                            <option value="<?php echo $id ?>"><?php echo $this->escape($label) ?></option>
                        <?php endforeach ?>
                    </select>
                    <span class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_TRANSFER_ROOT_HINT') ?></span>
                </div>
                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" name="with_config" id="kd-export-config" value="1">
                    <label class="form-check-label" for="kd-export-config"><?php echo Text::_('COM_KUNENA_TOOLS_TRANSFER_WITH_CONFIG') ?></label>
                </div>
                <p class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_TRANSFER_SECRETS') ?></p>
                <div class="kd-form__actions">
                    <button type="submit" class="kd-btn kd-btn--primary">
                        <span class="fa-solid fa-download" aria-hidden="true"></span>
                        <?php echo Text::_('COM_KUNENA_TOOLS_TRANSFER_DOWNLOAD') ?>
                    </button>
                </div>
            </form>

            <form action="<?php echo $action ?>" method="post" enctype="multipart/form-data" class="kd-panel" aria-labelledby="kd-import-title">
                <input type="hidden" name="task" value="tools.importCategories">
                <?php echo HTMLHelper::_('form.token') ?>
                <h3 id="kd-import-title" class="kd-panel__title">
                    <span class="fa-solid fa-file-import" aria-hidden="true"></span>
                    <?php echo Text::_('COM_KUNENA_TOOLS_TRANSFER_IMPORT') ?>
                </h3>
                <div class="kd-form__field">
                    <label for="kd-import-file"><?php echo Text::_('COM_KUNENA_TOOLS_TRANSFER_FILE') ?></label>
                    <input type="file" class="form-control" id="kd-import-file" name="import_file" accept=".json,application/json" required>
                </div>
                <div class="kd-form__field mt-3">
                    <label for="kd-import-parent"><?php echo Text::_('COM_KUNENA_TOOLS_TRANSFER_PARENT') ?></label>
                    <select class="form-select" id="kd-import-parent" name="parent">
                        <option value="0"><?php echo Text::_('COM_KUNENA_TOOLS_TRANSFER_TOP') ?></option>
                        <?php foreach ($options as [$id, $label]) : ?>
                            <option value="<?php echo $id ?>"><?php echo $this->escape($label) ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" name="unpublished" id="kd-import-unpublished" value="1" checked>
                    <label class="form-check-label" for="kd-import-unpublished"><?php echo Text::_('COM_KUNENA_TOOLS_TRANSFER_UNPUBLISHED') ?></label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="with_config" id="kd-import-config" value="1">
                    <label class="form-check-label" for="kd-import-config"><?php echo Text::_('COM_KUNENA_TOOLS_TRANSFER_IMPORT_CONFIG') ?></label>
                </div>
                <div class="kd-form__actions">
                    <button type="submit" class="kd-btn kd-btn--primary" data-kd-confirm>
                        <span class="fa-solid fa-upload" aria-hidden="true"></span>
                        <?php echo Text::_('COM_KUNENA_TOOLS_TRANSFER_IMPORT') ?>
                    </button>
                </div>
            </form>
        </div>
    <?php endif ?>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
