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

$files = $this->tool['files'];
$count = array_sum(array_map(static fn ($file) => \count($file['problems']), $files));
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero<?php echo $count ? ' is-problem' : '' ?>">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-pen-ruler" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title">
                    <?php echo $count ? Text::plural('COM_KUNENA_TOOLS_OVERRIDES_N_FOUND', $count) : Text::_('COM_KUNENA_TOOLS_OVERRIDES_NONE') ?>
                </h2>
                <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_TOOLS_OVERRIDES_INTRO') ?></p>
            </div>
        </div>
    </section>

    <?php if ($files) : ?>
        <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post">
            <input type="hidden" name="task" value="tools.removeOverrides">
            <?php echo HTMLHelper::_('form.token') ?>

            <?php foreach ($files as $file) :
                $client = $file['client'] === 'administrator' ? 1 : 0; ?>
                <section class="kd-panel">
                    <h3 class="kd-panel__title">
                        <?php echo $this->escape($file['tag']) ?>
                        · <?php echo Text::_($client ? 'JADMINISTRATOR' : 'JSITE') ?>
                        <a class="kd-form__hint" href="<?php echo Route::_('index.php?option=com_languages&view=overrides&' . http_build_query(['filter' => ['language_client' => $file['tag'] . $client]])) ?>"><?php echo Text::_('COM_KUNENA_TOOLS_OVERRIDES_OPEN') ?> ›</a>
                    </h3>
                    <div class="kd-scroll">
                        <table class="table">
                            <tbody>
                                <?php foreach ($file['problems'] as $problem) :
                                    $value = $file['client'] . '|' . $file['tag'] . '|' . $problem['key'];
                                    $id    = 'kd-override-' . md5($value); ?>
                                    <tr>
                                        <td class="w-1 text-center">
                                            <input type="checkbox" class="form-check-input" name="keys[]" value="<?php echo $this->escape($value) ?>" id="<?php echo $id ?>">
                                        </td>
                                        <th scope="row" class="w-30">
                                            <label for="<?php echo $id ?>"><code class="kd-break"><?php echo $this->escape($problem['key']) ?></code></label>
                                            <br><span class="kd-pill kd-pill--warn"><?php echo Text::_('COM_KUNENA_TOOLS_OVERRIDES_' . strtoupper($problem['kind'])) ?></span>
                                        </th>
                                        <td>
                                            <span class="kd-break"><?php echo $this->escape($problem['text']) ?></span>
                                            <?php if ($problem['english'] !== '') : ?>
                                                <br><span class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_OVERRIDES_KUNENA') ?>: <?php echo $this->escape($problem['english']) ?></span>
                                            <?php endif ?>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            <?php endforeach ?>

            <?php if ($this->canAdmin) : ?>
                <div class="kd-form__actions">
                    <button type="submit" class="kd-btn kd-btn--danger" data-kd-confirm>
                        <span class="fa-solid fa-eraser" aria-hidden="true"></span>
                        <?php echo Text::_('COM_KUNENA_TOOLS_OVERRIDES_REMOVE') ?>
                    </button>
                    <span class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_OVERRIDES_REMOVE_HINT') ?></span>
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
