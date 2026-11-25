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

$folders = $this->tool['folders'];
$bytes   = array_sum(array_column($folders, 'bytes'));
$files   = array_sum(array_column($folders, 'files'));
?>

<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post">
    <input type="hidden" name="task" value="tools.emptyCaches">
    <?php echo HTMLHelper::_('form.token') ?>

    <div id="kunena" class="kd">
        <section class="kd-hero kd-diag__hero">
            <div class="kd-hero__brand">
                <span class="kd-diag__heroicon fa-solid fa-database" aria-hidden="true"></span>
                <div>
                    <h2 class="kd-hero__title"><?php echo Text::plural('COM_KUNENA_TOOLS_CACHES_TOTAL', $files, HTMLHelper::_('number.bytes', $bytes)) ?></h2>
                    <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_TOOLS_CACHES_INTRO') ?></p>
                </div>
            </div>
            <?php if ($this->canAdmin) : ?>
                <div class="kd-hero__actions">
                    <button type="submit" class="kd-btn kd-btn--primary">
                        <span class="fa-solid fa-broom" aria-hidden="true"></span>
                        <?php echo Text::_('COM_KUNENA_TOOLS_CACHES_EMPTY') ?>
                    </button>
                </div>
            <?php endif ?>
        </section>

        <?php if (!$this->canAdmin) : ?>
            <div class="alert alert-info"><?php echo Text::_('COM_KUNENA_TOOLS_ADMIN_ONLY') ?></div>
        <?php endif ?>

        <table class="table">
            <thead>
                <tr>
                    <td class="w-1 text-center">
                        <input type="checkbox" class="form-check-input" data-kd-all="kd-cache" checked
                               aria-label="<?php echo $this->escape(Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_SELECT_ALL')) ?>">
                    </td>
                    <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_CACHES_FOLDER') ?></th>
                    <th scope="col" class="w-10 text-end"><?php echo Text::_('COM_KUNENA_TOOLS_CACHES_FILES') ?></th>
                    <th scope="col" class="w-10 text-end"><?php echo Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_SIZE') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($folders as $name => $folder) : ?>
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" class="form-check-input kd-cache" name="folders[]" value="<?php echo $this->escape($name) ?>"
                                   id="kd-cache-<?php echo $this->escape($name) ?>" checked>
                        </td>
                        <th scope="row">
                            <label for="kd-cache-<?php echo $this->escape($name) ?>">
                                <?php echo Text::_('COM_KUNENA_TOOLS_CACHES_' . strtoupper($name)) ?>
                            </label>
                            <br><code class="kd-check__code"><?php echo $this->escape($folder['path']) ?></code>
                        </th>
                        <td class="text-end"><?php echo (int) $folder['files'] ?></td>
                        <td class="text-end"><?php echo HTMLHelper::_('number.bytes', $folder['bytes']) ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>

        <p class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_CACHES_JOOMLA') ?></p>

        <footer class="kd-footer">
            <?php echo KunenaVersion::getLongVersionHTML(); ?>
        </footer>
    </div>
</form>
