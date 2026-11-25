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
use Kunena\Forum\Libraries\Tools\KunenaImageOptimizer;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$items = $this->tool['items'];
$bytes = array_sum(array_column($items, 'bytes'));
$size  = static fn ($value) => HTMLHelper::_('number.bytes', (int) $value);
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero<?php echo $items ? ' is-problem' : '' ?>">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-image" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title">
                    <?php echo $items ? Text::plural('COM_KUNENA_TOOLS_IMAGES_N_FOUND', \count($items), $size($bytes)) : Text::_('COM_KUNENA_TOOLS_IMAGES_NONE') ?>
                </h2>
                <p class="kd-hero__meta">
                    <?php echo Text::sprintf('COM_KUNENA_TOOLS_IMAGES_INTRO', $this->tool['maxWidth'], $this->tool['maxHeight'], $size(KunenaImageOptimizer::HEAVY)) ?>
                </p>
            </div>
        </div>
        <div class="kd-hero__actions">
            <a class="kd-btn kd-btn--ghost" href="<?php echo Route::_('index.php?option=com_kunena&view=tools&layout=storage') ?>">
                <span class="fa-solid fa-hard-drive" aria-hidden="true"></span>
                <?php echo Text::_('COM_KUNENA_TOOLS_STORAGE') ?>
            </a>
        </div>
    </section>

    <?php if ($items) : ?>
        <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post">
            <input type="hidden" name="task" value="tools.optimizeImages">
            <?php echo HTMLHelper::_('form.token') ?>

            <div class="kd-scroll">
                <table class="table">
                    <thead>
                        <tr>
                            <td class="w-1 text-center">
                                <input type="checkbox" class="form-check-input" data-kd-all="kd-image" checked
                                       aria-label="<?php echo $this->escape(Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_SELECT_ALL')) ?>">
                            </td>
                            <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_FILE') ?></th>
                            <th scope="col" class="w-15 text-end"><?php echo Text::_('COM_KUNENA_TOOLS_IMAGES_DIMENSIONS') ?></th>
                            <th scope="col" class="w-15 text-end"><?php echo Text::_('COM_KUNENA_TOOLS_IMAGES_NEW') ?></th>
                            <th scope="col" class="w-10 text-end"><?php echo Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_SIZE') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item) :
                            $id = (int) $item['id']; ?>
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input kd-image" name="ids[]" value="<?php echo $id ?>" id="kd-image-<?php echo $id ?>" checked>
                                </td>
                                <th scope="row">
                                    <label for="kd-image-<?php echo $id ?>"><?php echo $this->escape($item['filename_real'] ?: $item['filename']) ?></label>
                                    <?php if ((int) $item['mesid'] && $item['thread']) : ?>
                                        · <a href="<?php echo $this->escape(Route::link('site', 'index.php?option=com_kunena&view=topic&catid=' . (int) $item['catid'] . '&id=' . (int) $item['thread'] . '&mesid=' . (int) $item['mesid']) . '#' . (int) $item['mesid']) ?>"
                                             target="_blank" rel="noopener"><?php echo Text::_('COM_KUNENA_TOOLS_SPAM_MESSAGE') ?></a>
                                    <?php endif ?>
                                    <br><code class="kd-check__code"><?php echo $this->escape((string) $item['filetype']) ?></code>
                                </th>
                                <td class="text-end"><?php echo (int) $item['width'] ?> × <?php echo (int) $item['height'] ?></td>
                                <td class="text-end"><?php echo (int) $item['newWidth'] ?> × <?php echo (int) $item['newHeight'] ?></td>
                                <td class="text-end"><?php echo $size($item['bytes']) ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>

            <?php if ($this->canAdmin) : ?>
                <div class="kd-panel">
                    <?php if ($this->tool['webp']) : ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="webp" id="kd-images-webp" value="1">
                            <label class="form-check-label" for="kd-images-webp"><?php echo Text::_('COM_KUNENA_TOOLS_IMAGES_WEBP') ?></label>
                        </div>
                        <p class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_IMAGES_WEBP_HINT') ?></p>
                    <?php endif ?>
                    <p class="kd-form__hint"><?php echo Text::sprintf('COM_KUNENA_TOOLS_IMAGES_BATCH', KunenaImageOptimizer::BATCH) ?></p>
                    <div class="kd-form__actions">
                        <button type="submit" class="kd-btn kd-btn--primary" data-kd-confirm>
                            <span class="fa-solid fa-wand-magic-sparkles" aria-hidden="true"></span>
                            <?php echo Text::_('COM_KUNENA_TOOLS_IMAGES_OPTIMIZE') ?>
                        </button>
                    </div>
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
