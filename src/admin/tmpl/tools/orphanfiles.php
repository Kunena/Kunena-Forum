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

$limit  = 200;
$total   = \count($this->tool['attachments']) + \count($this->tool['avatars']) + \count($this->tool['missing']);
$action  = KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools');
$folders = [
    'attachments' => ['COM_KUNENA_TOOLS_ORPHANS_ATTACHMENTS', 'COM_KUNENA_TOOLS_ORPHANS_ATTACHMENTS_DESC', 'paperclip'],
    'avatars'     => ['COM_KUNENA_TOOLS_ORPHANS_AVATARS', 'COM_KUNENA_TOOLS_ORPHANS_AVATARS_DESC', 'circle-user'],
];
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero<?php echo $total ? ' is-problem' : '' ?>">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-<?php echo $total ? 'file-circle-question' : 'circle-check' ?>" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title"><?php echo Text::_($total ? 'COM_KUNENA_TOOLS_ORPHANS_FOUND' : 'COM_KUNENA_TOOLS_ORPHANS_NONE') ?></h2>
                <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_TOOLS_ORPHANS_INTRO') ?></p>
            </div>
        </div>
    </section>

    <?php if ($total && !$this->canAdmin) : ?>
        <div class="alert alert-info"><?php echo Text::_('COM_KUNENA_TOOLS_ADMIN_ONLY') ?></div>
    <?php endif ?>

    <?php foreach ($folders as $scope => [$title, $desc, $icon]) :
        $files = $this->tool[$scope];
        $size  = array_sum(array_column($files, 'size')); ?>
        <section class="kd-section" aria-labelledby="kd-orphans-<?php echo $scope ?>">
            <div class="kd-section__head">
                <h3 id="kd-orphans-<?php echo $scope ?>" class="kd-section__title">
                    <span class="fa-solid fa-<?php echo $icon ?>" aria-hidden="true"></span>
                    <?php echo Text::_($title) ?>
                </h3>
                <span class="kd-pill kd-pill--<?php echo $files ? 'warn' : 'ok' ?>">
                    <?php echo $files ? Text::plural('COM_KUNENA_TOOLS_ORPHANS_N_FILES', \count($files), HTMLHelper::_('number.bytes', $size)) : Text::_('COM_KUNENA_TOOLS_NONE') ?>
                </span>
            </div>
            <p class="kd-section__desc"><?php echo Text::_($desc) ?></p>

            <?php if ($files) : ?>
                <div class="kd-scroll">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_FILE') ?></th>
                                <th scope="col" class="w-10 text-end"><?php echo Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_SIZE') ?></th>
                                <th scope="col" class="w-15 d-none d-md-table-cell"><?php echo Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_DATE') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (\array_slice($files, 0, $limit, true) as $path => $file) : ?>
                                <tr>
                                    <th scope="row"><code class="kd-check__code"><?php echo $this->escape($path) ?></code></th>
                                    <td class="text-end"><?php echo HTMLHelper::_('number.bytes', $file['size']) ?></td>
                                    <td class="d-none d-md-table-cell"><?php echo HTMLHelper::_('date', $file['time'], Text::_('DATE_FORMAT_LC4')) ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
                <?php if (\count($files) > $limit) : ?>
                    <p class="kd-form__hint"><?php echo Text::sprintf('COM_KUNENA_TOOLS_N_MORE', \count($files) - $limit) ?></p>
                <?php endif ?>

                <?php if ($this->canAdmin) : ?>
                    <form action="<?php echo $action ?>" method="post" class="kd-form__actions">
                        <input type="hidden" name="task" value="tools.deleteOrphans">
                        <input type="hidden" name="scope" value="<?php echo $scope ?>">
                        <?php echo HTMLHelper::_('form.token') ?>
                        <button type="submit" class="kd-btn kd-btn--danger" data-kd-confirm>
                            <span class="fa-solid fa-trash-can" aria-hidden="true"></span>
                            <?php echo Text::plural('COM_KUNENA_TOOLS_ORPHANS_DELETE_N', \count($files)) ?>
                        </button>
                    </form>
                <?php endif ?>
            <?php endif ?>
        </section>
    <?php endforeach ?>

    <section class="kd-section" aria-labelledby="kd-orphans-missing">
        <div class="kd-section__head">
            <h3 id="kd-orphans-missing" class="kd-section__title">
                <span class="fa-solid fa-link-slash" aria-hidden="true"></span>
                <?php echo Text::_('COM_KUNENA_TOOLS_ORPHANS_MISSING') ?>
            </h3>
            <span class="kd-pill kd-pill--<?php echo $this->tool['missing'] ? 'warn' : 'ok' ?>">
                <?php echo $this->tool['missing'] ? Text::plural('COM_KUNENA_TOOLS_ORPHANS_N_ATTACHMENTS', \count($this->tool['missing'])) : Text::_('COM_KUNENA_TOOLS_NONE') ?>
            </span>
        </div>
        <p class="kd-section__desc"><?php echo Text::_('COM_KUNENA_TOOLS_ORPHANS_MISSING_DESC') ?></p>

        <?php if ($this->tool['missing']) : ?>
            <form action="<?php echo $action ?>" method="post">
                <input type="hidden" name="task" value="tools.deleteMissingAttachments">
                <?php echo HTMLHelper::_('form.token') ?>
                <div class="kd-scroll">
                    <table class="table">
                        <thead>
                            <tr>
                                <td class="w-1 text-center">
                                    <?php if ($this->canAdmin) : ?>
                                        <input type="checkbox" class="form-check-input" data-kd-all="kd-missing" checked
                                               aria-label="<?php echo $this->escape(Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_SELECT_ALL')) ?>">
                                    <?php endif ?>
                                </td>
                                <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_ORPHANS_ATTACHMENT') ?></th>
                                <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_FILE') ?></th>
                                <th scope="col" class="w-10"><?php echo Text::_('COM_KUNENA_TOOLS_ORPHANS_MESSAGE') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (\array_slice($this->tool['missing'], 0, $limit, true) as $id => $row) : ?>
                                <tr>
                                    <td class="text-center">
                                        <?php if ($this->canAdmin) : ?>
                                            <input type="checkbox" class="form-check-input kd-missing" name="ids[]" value="<?php echo (int) $id ?>"
                                                   id="kd-missing-<?php echo (int) $id ?>" checked>
                                        <?php endif ?>
                                    </td>
                                    <th scope="row">
                                        <label for="kd-missing-<?php echo (int) $id ?>"><?php echo $this->escape($row['filename_real'] ?: $row['filename']) ?></label>
                                    </th>
                                    <td><code class="kd-check__code"><?php echo $this->escape(trim($row['folder'] . '/' . $row['filename'], '/')) ?></code></td>
                                    <td><?php echo (int) $row['mesid'] ? '#' . (int) $row['mesid'] : Text::_('COM_KUNENA_TOOLS_ORPHANS_PENDING') ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($this->canAdmin) : ?>
                    <div class="kd-form__actions">
                        <button type="submit" class="kd-btn kd-btn--danger" data-kd-confirm>
                            <span class="fa-solid fa-trash-can" aria-hidden="true"></span>
                            <?php echo Text::_('COM_KUNENA_TOOLS_ORPHANS_MISSING_DELETE') ?>
                        </button>
                    </div>
                <?php endif ?>
            </form>
        <?php endif ?>
    </section>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
