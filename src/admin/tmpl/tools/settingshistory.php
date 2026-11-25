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
use Kunena\Forum\Libraries\Tools\KunenaSettingsHistory;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$entries = $this->tool['entries'];
$setting = $this->tool['setting'];
$link    = static fn (string $name) => Route::_('index.php?option=com_kunena&view=tools&layout=settingshistory&setting=' . rawurlencode($name));
$value   = static fn (string $text) => $text === '' ? '—' : (mb_strlen($text) > 120 ? mb_substr($text, 0, 120) . '…' : $text);
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-clock-rotate-left" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title">
                    <?php if ($setting !== '') : ?>
                        <?php echo Text::sprintf('COM_KUNENA_TOOLS_HISTORY_OF', $this->escape($setting)) ?>
                    <?php else : ?>
                        <?php echo $entries ? Text::plural('COM_KUNENA_TOOLS_HISTORY_N_SAVES', \count($entries)) : Text::_('COM_KUNENA_TOOLS_HISTORY_NONE') ?>
                    <?php endif ?>
                </h2>
                <p class="kd-hero__meta"><?php echo Text::sprintf('COM_KUNENA_TOOLS_HISTORY_INTRO', KunenaSettingsHistory::LIMIT) ?></p>
            </div>
        </div>
        <?php if ($setting !== '') : ?>
            <div class="kd-hero__actions">
                <a class="kd-btn kd-btn--ghost" href="<?php echo Route::_('index.php?option=com_kunena&view=tools&layout=settingshistory') ?>">
                    <span class="fa-solid fa-xmark" aria-hidden="true"></span>
                    <?php echo Text::_('COM_KUNENA_TOOLS_HISTORY_ALL') ?>
                </a>
            </div>
        <?php endif ?>
    </section>

    <?php foreach ($entries as $entry) : ?>
        <section class="kd-panel">
            <h3 class="kd-panel__title">
                <?php echo HTMLHelper::_('date', (int) $entry['time'], Text::_('DATE_FORMAT_LC2')) ?>
                <span class="kd-form__hint">
                    · <?php echo $this->escape($entry['username'] !== '' ? $entry['username'] : '#' . $entry['user']) ?>
                    · <?php echo Text::_('COM_KUNENA_TOOLS_HISTORY_SOURCE_' . strtoupper($entry['source'])) ?>
                </span>
            </h3>
            <div class="kd-scroll">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col" class="w-30"><?php echo Text::_('COM_KUNENA_TOOLS_HISTORY_SETTING') ?></th>
                            <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_HISTORY_OLD') ?></th>
                            <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_HISTORY_NEW') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($entry['changes'] as $name => [$old, $new]) : ?>
                            <tr>
                                <th scope="row"><a href="<?php echo $link((string) $name) ?>"><code><?php echo $this->escape((string) $name) ?></code></a></th>
                                <td><span class="kd-break"><?php echo $this->escape($value($old)) ?></span></td>
                                <td><span class="kd-break"><?php echo $this->escape($value($new)) ?></span></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </section>
    <?php endforeach ?>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
