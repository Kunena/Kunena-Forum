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
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Version\KunenaVersion;

// The names of the labels are texts of the templates of the site.
KunenaFactory::loadLanguage('com_kunena.templates', 'site');

$report   = $this->tool;
$unknown  = array_sum(array_map(static fn ($set) => array_sum($set['unknown']), $report['sets']));
$problems = $unknown + \count($report['missing']) + array_sum($report['unknownLabels']);
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero<?php echo $problems ? ' is-problem' : '' ?>">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-icons" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title"><?php echo $unknown ? Text::plural('COM_KUNENA_TOOLS_ICONS_N_UNKNOWN', $unknown) : Text::_('COM_KUNENA_TOOLS_ICONS_ALL_KNOWN') ?></h2>
                <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_TOOLS_ICONS_INTRO') ?></p>
            </div>
        </div>
        <?php if ($unknown && $this->canAdmin) : ?>
            <div class="kd-hero__actions">
                <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post">
                    <input type="hidden" name="task" value="tools.resetTopicIcons">
                    <?php echo HTMLHelper::_('form.token') ?>
                    <button type="submit" class="kd-btn kd-btn--primary" data-kd-confirm>
                        <span class="fa-solid fa-rotate-left" aria-hidden="true"></span>
                        <?php echo Text::_('COM_KUNENA_TOOLS_ICONS_RESET') ?>
                    </button>
                </form>
            </div>
        <?php endif ?>
    </section>

    <?php if ($report['missing']) : ?>
        <div class="alert alert-warning">
            <?php echo Text::_('COM_KUNENA_TOOLS_ICONS_SET_MISSING') ?>
            <?php foreach ($report['missing'] as $category) : ?>
                <br><a href="<?php echo Route::_('index.php?option=com_kunena&view=category&layout=edit&catid=' . (int) $category['id']) ?>"><?php echo $this->escape($category['name']) ?></a>
                <code><?php echo $this->escape($category['set']) ?></code>
            <?php endforeach ?>
        </div>
    <?php endif ?>

    <?php foreach ($report['sets'] as $name => $set) : ?>
        <section class="kd-panel" aria-labelledby="kd-icons-<?php echo $this->escape($name) ?>">
            <h3 id="kd-icons-<?php echo $this->escape($name) ?>" class="kd-panel__title">
                <?php echo Text::sprintf('COM_KUNENA_TOOLS_ICONS_SET', $this->escape($name)) ?>
                <span class="kd-form__hint">· <?php echo Text::plural('COM_KUNENA_TOOLS_ICONS_N_CATEGORIES', (int) $set['categories']) ?></span>
            </h3>
            <ul class="kd-tags">
                <?php foreach ($set['icons'] as $id => $icon) : ?>
                    <li>
                        <?php echo $this->escape($icon['name']) ?> <span class="kd-form__hint">#<?php echo (int) $id ?></span>
                        <span class="kd-pill<?php echo $icon['topics'] ? '' : ' kd-pill--warn' ?>"><?php echo $icon['topics'] ? (int) $icon['topics'] : Text::_('COM_KUNENA_TOOLS_ICONS_UNUSED') ?></span>
                        <?php if (!$icon['published']) : ?>
                            <span class="kd-pill"><?php echo Text::_('JUNPUBLISHED') ?></span>
                        <?php endif ?>
                    </li>
                <?php endforeach ?>
            </ul>
            <?php if ($set['unknown']) : ?>
                <p class="kd-form__hint">
                    <?php echo Text::_('COM_KUNENA_TOOLS_ICONS_UNKNOWN') ?>
                    <?php foreach ($set['unknown'] as $id => $topics) : ?>
                        <span class="kd-pill kd-pill--warn">#<?php echo (int) $id ?> · <?php echo (int) $topics ?></span>
                    <?php endforeach ?>
                </p>
            <?php endif ?>
        </section>
    <?php endforeach ?>

    <section class="kd-panel" aria-labelledby="kd-icons-labels">
        <h3 id="kd-icons-labels" class="kd-panel__title"><?php echo Text::_('COM_KUNENA_TOOLS_ICONS_LABELS') ?></h3>
        <ul class="kd-tags">
            <?php foreach ($report['labels'] as $id => $label) : ?>
                <li>
                    <?php echo $this->escape(Text::_($label['name'])) ?> <span class="kd-form__hint">#<?php echo (int) $id ?></span>
                    <span class="kd-pill<?php echo $label['topics'] ? '' : ' kd-pill--warn' ?>"><?php echo $label['topics'] ? (int) $label['topics'] : Text::_('COM_KUNENA_TOOLS_ICONS_UNUSED') ?></span>
                </li>
            <?php endforeach ?>
        </ul>
        <?php if ($report['unknownLabels']) : ?>
            <p class="kd-form__hint">
                <?php echo Text::_('COM_KUNENA_TOOLS_ICONS_UNKNOWN_LABELS') ?>
                <?php foreach ($report['unknownLabels'] as $id => $topics) : ?>
                    <span class="kd-pill kd-pill--warn">#<?php echo (int) $id ?> · <?php echo (int) $topics ?></span>
                <?php endforeach ?>
            </p>
        <?php endif ?>
    </section>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
