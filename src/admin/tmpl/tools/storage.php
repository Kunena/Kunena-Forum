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
use Kunena\Forum\Libraries\Version\KunenaVersion;

$data  = $this->tool;
$bytes = static fn ($value) => HTMLHelper::_('number.bytes', (int) $value);
$share = static fn ($value) => $data['bytes'] ? round(100 * (int) $value / $data['bytes']) . ' %' : '—';
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-hard-drive" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title"><?php echo Text::plural('COM_KUNENA_TOOLS_STORAGE_TOTAL', $data['files'], $bytes($data['bytes'])) ?></h2>
                <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_TOOLS_STORAGE_INTRO') ?></p>
            </div>
        </div>
        <div class="kd-hero__actions">
            <a class="kd-btn kd-btn--ghost" href="<?php echo Route::_('index.php?option=com_kunena&view=tools&layout=orphanfiles') ?>">
                <span class="fa-solid fa-file-circle-question" aria-hidden="true"></span>
                <?php echo Text::_('COM_KUNENA_TOOLS_ORPHANS') ?>
            </a>
        </div>
    </section>

    <div class="kd-columns">
        <section class="kd-panel" aria-labelledby="kd-storage-kinds">
            <h3 id="kd-storage-kinds" class="kd-panel__title"><?php echo Text::_('COM_KUNENA_TOOLS_STORAGE_BY_KIND') ?></h3>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_STORAGE_KIND') ?></th>
                        <th scope="col" class="text-end"><?php echo Text::_('COM_KUNENA_TOOLS_CACHES_FILES') ?></th>
                        <th scope="col" class="text-end"><?php echo Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_SIZE') ?></th>
                        <th scope="col" class="text-end"><?php echo Text::_('COM_KUNENA_TOOLS_STORAGE_SHARE') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['kinds'] as $kind => $row) : ?>
                        <tr>
                            <th scope="row"><?php echo Text::_('COM_KUNENA_TOOLS_STORAGE_KIND_' . strtoupper($kind)) ?></th>
                            <td class="text-end"><?php echo (int) $row['files'] ?></td>
                            <td class="text-end"><?php echo $bytes($row['bytes']) ?></td>
                            <td class="text-end"><?php echo $share($row['bytes']) ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </section>

        <section class="kd-panel" aria-labelledby="kd-storage-categories">
            <h3 id="kd-storage-categories" class="kd-panel__title"><?php echo Text::_('COM_KUNENA_TOOLS_STORAGE_BY_CATEGORY') ?></h3>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_QUEUE_CATEGORY') ?></th>
                        <th scope="col" class="text-end"><?php echo Text::_('COM_KUNENA_TOOLS_CACHES_FILES') ?></th>
                        <th scope="col" class="text-end"><?php echo Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_SIZE') ?></th>
                        <th scope="col" class="text-end"><?php echo Text::_('COM_KUNENA_TOOLS_STORAGE_SHARE') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['categories'] as $row) : ?>
                        <tr>
                            <th scope="row"><?php echo $this->escape($row['name']) ?></th>
                            <td class="text-end"><?php echo (int) $row['files'] ?></td>
                            <td class="text-end"><?php echo $bytes($row['bytes']) ?></td>
                            <td class="text-end"><?php echo $share($row['bytes']) ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </section>
    </div>

    <div class="kd-columns">
        <section class="kd-panel" aria-labelledby="kd-storage-users">
            <h3 id="kd-storage-users" class="kd-panel__title"><?php echo Text::_('COM_KUNENA_TOOLS_STORAGE_BY_USER') ?></h3>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_USER_NAME') ?></th>
                        <th scope="col" class="text-end"><?php echo Text::_('COM_KUNENA_TOOLS_CACHES_FILES') ?></th>
                        <th scope="col" class="text-end"><?php echo Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_SIZE') ?></th>
                        <th scope="col" class="text-end"><?php echo Text::_('COM_KUNENA_TOOLS_STORAGE_SHARE') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['users'] as $row) : ?>
                        <tr>
                            <th scope="row">
                                <?php if ((int) $row['userid'] > 0) : ?>
                                    <a href="<?php echo Route::_('index.php?option=com_kunena&view=tools&layout=usercleanup&userid=' . (int) $row['userid']) ?>">
                                        <?php echo $this->escape($row['username'] ?: '#' . (int) $row['userid']) ?>
                                    </a>
                                <?php else : ?>
                                    <?php echo Text::_('COM_KUNENA_TOOLS_SPAM_GUEST') ?>
                                <?php endif ?>
                            </th>
                            <td class="text-end"><?php echo (int) $row['files'] ?></td>
                            <td class="text-end"><?php echo $bytes($row['bytes']) ?></td>
                            <td class="text-end"><?php echo $share($row['bytes']) ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </section>

        <section class="kd-panel" aria-labelledby="kd-storage-biggest">
            <h3 id="kd-storage-biggest" class="kd-panel__title"><?php echo Text::_('COM_KUNENA_TOOLS_STORAGE_BIGGEST') ?></h3>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_FILE') ?></th>
                        <th scope="col" class="text-end"><?php echo Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_SIZE') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['biggest'] as $row) : ?>
                        <tr>
                            <th scope="row">
                                <?php if ((int) $row['mesid'] && $row['thread']) : ?>
                                    <a href="<?php echo $this->escape(Route::link('site', 'index.php?option=com_kunena&view=topic&catid=' . (int) $row['catid'] . '&id=' . (int) $row['thread'] . '&mesid=' . (int) $row['mesid']) . '#' . (int) $row['mesid']) ?>"
                                       target="_blank" rel="noopener"><?php echo $this->escape($row['filename_real'] ?: $row['filename']) ?></a>
                                <?php else : ?>
                                    <?php echo $this->escape($row['filename_real'] ?: $row['filename']) ?>
                                <?php endif ?>
                                <br><span class="kd-form__hint"><?php echo $this->escape((string) $row['username']) ?></span>
                            </th>
                            <td class="text-end"><?php echo $bytes($row['size']) ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </section>
    </div>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
