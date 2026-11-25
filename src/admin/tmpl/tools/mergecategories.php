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
use Kunena\Forum\Libraries\Tools\KunenaCategoryMerge;
use Kunena\Forum\Libraries\Version\KunenaVersion;

// The categories as a tree: each one after its parent, indented by its depth.
$children = [];
$names    = [];

foreach ($this->tool['categories'] as $category) {
    $children[(int) $category['parentid']][] = $category;
    $names[(int) $category['id']]            = (string) $category['name'];
}

$options = [];
$walk    = static function (int $parent, int $depth) use (&$walk, &$options, $children): void {
    foreach ($children[$parent] ?? [] as $category) {
        $options[] = [(int) $category['id'], str_repeat('— ', $depth) . $category['name'], $depth === 0];
        $walk((int) $category['id'], $depth + 1);
    }
};
$walk(0, 0);

$from   = $this->tool['from'];
$into   = $this->tool['into'];
$counts = $this->tool['counts'];
$select = function (string $name, int $value, bool $sections) use ($options): string {
    $html = '<select class="form-select" id="kd-catmerge-' . $name . '" name="' . $name . '" required><option value="">' . Text::_('COM_KUNENA_TOOLS_CATMERGE_CHOOSE') . '</option>';

    foreach ($options as [$id, $label, $isSection]) {
        $disabled = !$sections && $isSection ? ' disabled' : '';
        $html .= '<option value="' . $id . '"' . ($id === $value ? ' selected' : '') . $disabled . '>' . $this->escape($label) . '</option>';
    }

    return $html . '</select>';
};
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-object-group" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title"><?php echo Text::_('COM_KUNENA_TOOLS_CATMERGE') ?></h2>
                <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_TOOLS_CATMERGE_INTRO') ?></p>
            </div>
        </div>
    </section>

    <form action="<?php echo Route::_('index.php') ?>" method="get" class="kd-panel">
        <input type="hidden" name="option" value="com_kunena">
        <input type="hidden" name="view" value="tools">
        <input type="hidden" name="layout" value="mergecategories">
        <div class="kd-form">
            <div class="kd-form__field">
                <label for="kd-catmerge-from"><?php echo Text::_('COM_KUNENA_TOOLS_CATMERGE_FROM') ?></label>
                <?php echo $select('from', $from, true) ?>
            </div>
            <div class="kd-form__field">
                <label for="kd-catmerge-into"><?php echo Text::_('COM_KUNENA_TOOLS_CATMERGE_INTO') ?></label>
                <?php echo $select('into', $into, false) ?>
            </div>
            <div>
                <button type="submit" class="kd-btn kd-btn--ghost">
                    <span class="fa-solid fa-eye" aria-hidden="true"></span>
                    <?php echo Text::_('COM_KUNENA_TOOLS_REPLACE_PREVIEW') ?>
                </button>
            </div>
        </div>
        <p class="kd-form__hint mt-2"><?php echo Text::_('COM_KUNENA_TOOLS_CATMERGE_SECTIONS') ?></p>
    </form>

    <?php if ($this->tool['error']) : ?>
        <div class="alert alert-warning"><?php echo Text::_($this->tool['error']) ?></div>
    <?php elseif ($from && $into && $counts) : ?>
        <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post" class="kd-panel">
            <input type="hidden" name="task" value="tools.mergeCategories">
            <input type="hidden" name="from" value="<?php echo $from ?>">
            <input type="hidden" name="into" value="<?php echo $into ?>">
            <?php echo HTMLHelper::_('form.token') ?>
            <h3 class="kd-panel__title">
                <?php echo Text::sprintf('COM_KUNENA_TOOLS_MERGE_SUMMARY', $this->escape($names[$from] ?? ''), $this->escape($names[$into] ?? '')) ?>
            </h3>
            <ul class="kd-tags mb-3">
                <li><?php echo Text::plural('COM_KUNENA_TOOLS_CATMERGE_N_TOPICS', $counts['topics']) ?></li>
                <li><?php echo Text::plural('COM_KUNENA_TOOLS_CATMERGE_N_CHILDREN', $counts['children']) ?></li>
                <li><?php echo Text::plural('COM_KUNENA_TOOLS_CATMERGE_N_SUBSCRIBERS', $counts['subscribers']) ?></li>
            </ul>
            <?php if ($counts['topics'] > KunenaCategoryMerge::BATCH) : ?>
                <p class="kd-form__hint"><?php echo Text::sprintf('COM_KUNENA_TOOLS_CATMERGE_BATCH', KunenaCategoryMerge::BATCH) ?></p>
            <?php endif ?>

            <fieldset class="kd-choices">
                <legend><?php echo Text::_('COM_KUNENA_TOOLS_CATMERGE_AFTER') ?></legend>
                <?php foreach ([KunenaCategoryMerge::AFTER_UNPUBLISH, KunenaCategoryMerge::AFTER_DELETE] as $value) : ?>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="after" id="kd-catmerge-<?php echo $value ?>" value="<?php echo $value ?>"
                            <?php echo $value === KunenaCategoryMerge::AFTER_UNPUBLISH ? 'checked' : '' ?>>
                        <label class="form-check-label" for="kd-catmerge-<?php echo $value ?>"><?php echo Text::_('COM_KUNENA_TOOLS_CATMERGE_AFTER_' . strtoupper($value)) ?></label>
                    </div>
                <?php endforeach ?>
            </fieldset>

            <?php if ($this->canAdmin) : ?>
                <div class="kd-form__actions">
                    <button type="submit" class="kd-btn kd-btn--danger" data-kd-confirm>
                        <span class="fa-solid fa-object-group" aria-hidden="true"></span>
                        <?php echo Text::_('COM_KUNENA_TOOLS_MERGE_APPLY') ?>
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
