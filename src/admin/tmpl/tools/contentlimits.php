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
use Kunena\Forum\Libraries\Version\KunenaVersion;

$report = $this->tool;
$limits = $report['limits'];
$count  = $report['attachmentsTotal'] + $report['signaturesTotal'];
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero<?php echo $count ? ' is-problem' : '' ?>">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-ruler" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title"><?php echo $count ? Text::plural('COM_KUNENA_TOOLS_LIMITS_N_FOUND', $count) : Text::_('COM_KUNENA_TOOLS_LIMITS_NONE') ?></h2>
                <p class="kd-hero__meta">
                    <?php echo Text::sprintf(
                        'COM_KUNENA_TOOLS_LIMITS_INTRO',
                        $this->escape(implode(', ', $limits['imageTypes'])),
                        HTMLHelper::_('number.bytes', $limits['imageBytes']),
                        $this->escape(implode(', ', $limits['fileTypes'])),
                        HTMLHelper::_('number.bytes', $limits['fileBytes']),
                        (int) $limits['maxSig']
                    ) ?>
                </p>
            </div>
        </div>
    </section>

    <?php if ($count) : ?>
        <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post">
            <input type="hidden" name="task" value="tools.fixContentLimits">
            <?php echo HTMLHelper::_('form.token') ?>

            <?php if ($report['attachments']) : ?>
                <section class="kd-panel" aria-labelledby="kd-limits-attachments">
                    <h3 id="kd-limits-attachments" class="kd-panel__title"><?php echo Text::plural('COM_KUNENA_TOOLS_LIMITS_N_ATTACHMENTS', $report['attachmentsTotal']) ?></h3>
                    <div class="kd-scroll">
                        <table class="table">
                            <thead>
                                <tr>
                                    <td class="w-1 text-center">
                                        <input type="checkbox" class="form-check-input" data-kd-all="kd-limit-attachment"
                                               aria-label="<?php echo $this->escape(Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_SELECT_ALL')) ?>">
                                    </td>
                                    <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_LIMITS_FILE') ?></th>
                                    <th scope="col" class="w-15 text-end"><?php echo Text::_('COM_KUNENA_TOOLS_LIMITS_FILESIZE') ?></th>
                                    <th scope="col" class="w-20"><?php echo Text::_('COM_KUNENA_TOOLS_BANS_PROBLEMS') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($report['attachments'] as $item) : ?>
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" class="form-check-input kd-limit-attachment" name="attachments[]" value="<?php echo (int) $item['id'] ?>" id="kd-limit-a<?php echo (int) $item['id'] ?>">
                                        </td>
                                        <th scope="row">
                                            <label for="kd-limit-a<?php echo (int) $item['id'] ?>"><code class="kd-break"><?php echo $this->escape($item['filename']) ?></code></label>
                                            <br><a class="kd-form__hint" href="<?php echo Route::_('index.php?option=com_kunena&view=attachments&filter[search]=' . rawurlencode($item['filename'])) ?>"><?php echo Text::_('COM_KUNENA_TOOLS_LIMITS_OPEN') ?> ›</a>
                                        </th>
                                        <td class="text-end"><?php echo HTMLHelper::_('number.bytes', $item['size']) ?></td>
                                        <td><span class="kd-pill kd-pill--warn"><?php echo Text::_('COM_KUNENA_TOOLS_LIMITS_' . strtoupper($item['problem'])) ?></span></td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            <?php endif ?>

            <?php if ($report['signatures']) : ?>
                <section class="kd-panel" aria-labelledby="kd-limits-signatures">
                    <h3 id="kd-limits-signatures" class="kd-panel__title"><?php echo Text::plural('COM_KUNENA_TOOLS_LIMITS_N_SIGNATURES', $report['signaturesTotal']) ?></h3>
                    <div class="kd-scroll">
                        <table class="table">
                            <thead>
                                <tr>
                                    <td class="w-1 text-center">
                                        <input type="checkbox" class="form-check-input" data-kd-all="kd-limit-signature"
                                               aria-label="<?php echo $this->escape(Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_SELECT_ALL')) ?>">
                                    </td>
                                    <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_PROFILESPAM_USER') ?></th>
                                    <th scope="col" class="w-20 text-end"><?php echo Text::_('COM_KUNENA_TOOLS_LIMITS_LENGTH') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($report['signatures'] as $item) : ?>
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" class="form-check-input kd-limit-signature" name="signatures[]" value="<?php echo (int) $item['userid'] ?>" id="kd-limit-s<?php echo (int) $item['userid'] ?>">
                                        </td>
                                        <th scope="row">
                                            <label for="kd-limit-s<?php echo (int) $item['userid'] ?>"><?php echo $this->escape($item['username']) ?></label>
                                            <br><a class="kd-form__hint" href="<?php echo Route::_('index.php?option=com_kunena&view=user&layout=edit&userid=' . (int) $item['userid']) ?>"><?php echo Text::_('COM_KUNENA_TOOLS_BANS_EDIT') ?> ›</a>
                                        </th>
                                        <td class="text-end"><?php echo Text::sprintf('COM_KUNENA_TOOLS_LIMITS_CHARS', (int) $item['length'], (int) $limits['maxSig']) ?></td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            <?php endif ?>

            <?php if ($this->canAdmin) : ?>
                <div class="kd-form__actions">
                    <button type="submit" class="kd-btn kd-btn--danger" data-kd-confirm>
                        <span class="fa-solid fa-scissors" aria-hidden="true"></span>
                        <?php echo Text::_('COM_KUNENA_TOOLS_LIMITS_FIX') ?>
                    </button>
                    <span class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_LIMITS_FIX_HINT') ?></span>
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
