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
use Kunena\Forum\Libraries\Tools\KunenaCategoryPermissions;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$tool    = $this->tool;
$rows    = $tool['rows'];
$preview = $tool['preview'];
$names   = $tool['names'];

// The categories as a tree: each one after its parent, indented by its depth.
$children = [];

foreach ($rows as $category) {
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
$user = static fn (int $id) => (string) ($names['users'][$id] ?? '#' . $id);
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-copy" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title"><?php echo Text::_('COM_KUNENA_TOOLS_COPYPERMS') ?></h2>
                <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_TOOLS_COPYPERMS_INTRO') ?></p>
            </div>
        </div>
        <div class="kd-hero__actions">
            <a class="kd-btn kd-btn--ghost" href="<?php echo Route::_('index.php?option=com_kunena&view=tools&layout=accessoverview') ?>">
                <span class="fa-solid fa-table-cells" aria-hidden="true"></span>
                <?php echo Text::_('COM_KUNENA_TOOLS_ACCESS') ?>
            </a>
        </div>
    </section>

    <form action="<?php echo Route::_('index.php') ?>" method="get" class="kd-panel">
        <input type="hidden" name="option" value="com_kunena">
        <input type="hidden" name="view" value="tools">
        <input type="hidden" name="layout" value="copypermissions">
        <div class="kd-form">
            <div class="kd-form__field">
                <label for="kd-copy-source"><?php echo Text::_('COM_KUNENA_TOOLS_COPYPERMS_SOURCE') ?></label>
                <select class="form-select" id="kd-copy-source" name="source" required>
                    <option value=""><?php echo Text::_('COM_KUNENA_TOOLS_COPYPERMS_CHOOSE') ?></option>
                    <?php foreach ($options as [$id, $label]) : ?>
                        <option value="<?php echo $id ?>"<?php echo $id === $tool['source'] ? ' selected' : '' ?>><?php echo $this->escape($label) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>

        <fieldset class="mt-3">
            <legend class="kd-form__label"><?php echo Text::_('COM_KUNENA_TOOLS_COPYPERMS_TARGETS') ?></legend>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" data-kd-all="kd-copy-target" id="kd-copy-all">
                <label class="form-check-label" for="kd-copy-all"><?php echo Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_SELECT_ALL') ?></label>
            </div>
            <div class="kd-copy-targets">
                <?php foreach ($options as [$id, $label]) : ?>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input kd-copy-target" name="targets[]" value="<?php echo $id ?>" id="kd-copy-<?php echo $id ?>"
                            <?php echo \in_array($id, $tool['targets'], true) ? ' checked' : '' ?>>
                        <label class="form-check-label" for="kd-copy-<?php echo $id ?>"><?php echo $this->escape($label) ?></label>
                    </div>
                <?php endforeach ?>
            </div>
        </fieldset>

        <div class="kd-form__actions">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" name="moderators" value="1" id="kd-copy-moderators"<?php echo $tool['moderators'] ? ' checked' : '' ?>>
                <label class="form-check-label" for="kd-copy-moderators"><?php echo Text::_('COM_KUNENA_TOOLS_COPYPERMS_MODERATORS') ?></label>
            </div>
            <button type="submit" class="kd-btn kd-btn--ghost">
                <span class="fa-solid fa-eye" aria-hidden="true"></span>
                <?php echo Text::_('COM_KUNENA_TOOLS_SETTINGS_PREVIEW') ?>
            </button>
        </div>
    </form>

    <?php if ($tool['source'] && $tool['targets']) : ?>
        <section class="kd-panel" aria-labelledby="kd-copy-preview">
            <h3 id="kd-copy-preview" class="kd-panel__title"><?php echo Text::plural('COM_KUNENA_TOOLS_COPYPERMS_N_CHANGE', \count($preview)) ?></h3>

            <?php if ($preview) : ?>
                <div class="kd-scroll">
                    <table class="table">
                        <tbody>
                            <?php foreach ($preview as $id => $change) : ?>
                                <tr>
                                    <th scope="row" class="w-30"><?php echo $this->escape((string) $rows[$id]['name']) ?></th>
                                    <td>
                                        <?php foreach ($change['fields'] as $field => $values) : ?>
                                            <div>
                                                <span class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_COPYPERMS_FIELD_' . strtoupper($field)) ?>:</span>
                                                <?php echo $this->escape(KunenaCategoryPermissions::label($field, $values['from'], $names)) ?> →
                                                <strong><?php echo $this->escape(KunenaCategoryPermissions::label($field, $values['to'], $names)) ?></strong>
                                            </div>
                                        <?php endforeach ?>
                                        <?php if ($change['add']) : ?>
                                            <div><span class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_COPYPERMS_ADD') ?>:</span>
                                                <?php echo $this->escape(implode(', ', array_map($user, $change['add']))) ?></div>
                                        <?php endif ?>
                                        <?php if ($change['remove']) : ?>
                                            <div><span class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_COPYPERMS_REMOVE') ?>:</span>
                                                <?php echo $this->escape(implode(', ', array_map($user, $change['remove']))) ?></div>
                                        <?php endif ?>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($this->canAdmin) : ?>
                    <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post" class="kd-form__actions">
                        <input type="hidden" name="task" value="tools.copyPermissions">
                        <input type="hidden" name="source" value="<?php echo (int) $tool['source'] ?>">
                        <input type="hidden" name="moderators" value="<?php echo $tool['moderators'] ? 1 : 0 ?>">
                        <?php foreach (array_keys($preview) as $id) : ?>
                            <input type="hidden" name="targets[]" value="<?php echo (int) $id ?>">
                        <?php endforeach ?>
                        <?php echo HTMLHelper::_('form.token') ?>
                        <button type="submit" class="kd-btn kd-btn--danger" data-kd-confirm>
                            <span class="fa-solid fa-copy" aria-hidden="true"></span>
                            <?php echo Text::_('COM_KUNENA_TOOLS_COPYPERMS_APPLY') ?>
                        </button>
                    </form>
                <?php else : ?>
                    <div class="alert alert-info"><?php echo Text::_('COM_KUNENA_TOOLS_ADMIN_ONLY') ?></div>
                <?php endif ?>
            <?php endif ?>
        </section>
    <?php endif ?>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
