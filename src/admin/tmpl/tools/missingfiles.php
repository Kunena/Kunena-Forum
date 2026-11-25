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
use Kunena\Forum\Libraries\Tools\KunenaMissingAttachments;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$items = $this->tool['items'];
$link  = static fn (array $row) => Route::link('site', 'index.php?option=com_kunena&view=topic&catid=' . (int) $row['catid'] . '&id=' . (int) $row['thread'] . '&mesid=' . (int) $row['mesid'], false) . '#' . (int) $row['mesid'];
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero<?php echo $items ? ' is-problem' : '' ?>">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-file-circle-question" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title">
                    <?php echo $items ? Text::plural('COM_KUNENA_TOOLS_MISSINGFILES_N_FOUND', \count($items)) : Text::_('COM_KUNENA_TOOLS_MISSINGFILES_NONE') ?>
                </h2>
                <p class="kd-hero__meta"><?php echo Text::sprintf('COM_KUNENA_TOOLS_MISSINGFILES_INTRO', (int) $this->tool['checked']) ?></p>
            </div>
        </div>
    </section>

    <?php if ($items) : ?>
        <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post">
            <input type="hidden" name="task" value="tools.deleteMissingFiles">
            <?php echo HTMLHelper::_('form.token') ?>

            <?php if (\count($items) >= KunenaMissingAttachments::LIST_LIMIT) : ?>
                <p class="kd-form__hint"><?php echo Text::sprintf('COM_KUNENA_TOOLS_TRANSLATIONS_FIRST_N', KunenaMissingAttachments::LIST_LIMIT) ?></p>
            <?php endif ?>

            <div class="kd-scroll">
                <table class="table">
                    <thead>
                        <tr>
                            <td class="w-1 text-center">
                                <input type="checkbox" class="form-check-input" data-kd-all="kd-missing"
                                       aria-label="<?php echo $this->escape(Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_SELECT_ALL')) ?>">
                            </td>
                            <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_MISSINGFILES_FILE') ?></th>
                            <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_MISSINGFILES_POST') ?></th>
                            <th scope="col" class="w-10 text-end"><?php echo Text::_('COM_KUNENA_TOOLS_MISSINGFILES_SIZE') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $row) :
                            $id = (int) $row['id']; ?>
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input kd-missing" name="ids[]" value="<?php echo $id ?>" id="kd-missing-<?php echo $id ?>">
                                </td>
                                <th scope="row">
                                    <label for="kd-missing-<?php echo $id ?>"><?php echo $this->escape((string) ($row['filename_real'] ?: $row['filename'])) ?></label>
                                    <br><code class="kd-form__hint"><?php echo $this->escape(KunenaMissingAttachments::path($row) ?: (string) $row['folder'] . '/' . $row['filename']) ?></code>
                                </th>
                                <td>
                                    <?php if ((int) $row['thread']) : ?>
                                        <a href="<?php echo $this->escape($link($row)) ?>" target="_blank" rel="noopener"><?php echo $this->escape((string) $row['subject']) ?></a>
                                        <br><span class="kd-form__hint"><?php echo $this->escape((string) $row['name']) ?> ·
                                            <?php echo HTMLHelper::_('date', (int) $row['time'], Text::_('DATE_FORMAT_LC5')) ?></span>
                                    <?php else : ?>
                                        <span class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_MISSINGFILES_NO_POST') ?></span>
                                    <?php endif ?>
                                </td>
                                <td class="text-end"><?php echo HTMLHelper::_('number.bytes', (int) $row['size']) ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>

            <?php if ($this->canAdmin) : ?>
                <div class="kd-form__actions">
                    <button type="submit" class="kd-btn kd-btn--danger" data-kd-confirm>
                        <span class="fa-solid fa-trash-can" aria-hidden="true"></span>
                        <?php echo Text::_('COM_KUNENA_TOOLS_MISSINGFILES_DELETE') ?>
                    </button>
                    <span class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_MISSINGFILES_DELETE_HINT') ?></span>
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
