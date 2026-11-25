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

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Kunena\Forum\Libraries\Tools\KunenaUpdateCheck as U;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$checks = $this->tool['checks'];
$order  = [U::PROBLEM => 0, U::WARNING => 1, U::INFO => 2, U::OK => 3];
usort($checks, static fn ($a, $b) => $order[$a[1]] <=> $order[$b[1]]);

$problems = \count(array_filter($checks, static fn ($c) => $c[1] === U::PROBLEM));
$warnings = \count(array_filter($checks, static fn ($c) => $c[1] === U::WARNING));
$icons    = [U::OK => 'circle-check', U::WARNING => 'triangle-exclamation', U::PROBLEM => 'circle-xmark', U::INFO => 'circle-info'];

// The text of a check: its key, its status, its value and the first detail.
$text = static function (array $check): string {
    [$key, $status, $value, $details] = $check;
    $suffix = $key === 'update' ? ($value === '' ? 'NONE' : 'FOUND') : strtoupper($status === U::INFO ? U::OK : $status);

    return Text::sprintf('COM_KUNENA_TOOLS_UPDATE_' . strtoupper($key) . '_' . $suffix, htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'), htmlspecialchars((string) ($details[0] ?? ''), ENT_QUOTES, 'UTF-8'));
};
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero<?php echo $problems || $warnings ? ' is-problem' : '' ?>">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-<?php echo $problems ? 'circle-xmark' : ($warnings ? 'triangle-exclamation' : 'rocket') ?>" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title">
                    <?php echo $problems ? Text::plural('COM_KUNENA_TOOLS_UPDATE_N_PROBLEMS', $problems) : ($warnings ? Text::plural('COM_KUNENA_TOOLS_UPDATE_N_WARNINGS', $warnings) : Text::_('COM_KUNENA_TOOLS_UPDATE_READY')) ?>
                </h2>
                <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_TOOLS_UPDATE_INTRO') ?></p>
            </div>
        </div>
        <div class="kd-hero__actions">
            <a class="kd-btn kd-btn--ghost" href="<?php echo Route::_('index.php?option=com_installer&view=update') ?>">
                <span class="fa-solid fa-arrows-rotate" aria-hidden="true"></span>
                <?php echo Text::_('COM_KUNENA_TOOLS_UPDATE_OPEN_UPDATES') ?>
            </a>
        </div>
    </section>

    <ul class="kd-checks">
        <?php foreach ($checks as $check) :
            [$key, $status, $value, $details] = $check; ?>
            <li class="kd-check<?php echo \in_array($status, [U::PROBLEM, U::WARNING], true) ? ' is-problem' : '' ?>">
                <span class="kd-check__icon fa-solid fa-<?php echo $icons[$status] ?>" aria-hidden="true"></span>
                <div class="kd-check__body">
                    <span class="kd-check__title"><?php echo Text::_('COM_KUNENA_TOOLS_UPDATE_' . strtoupper($key)) ?></span>
                    <span class="kd-form__hint"><?php echo $text($check) ?></span>
                    <?php if (\count($details) > 0 && \in_array($key, ['extensions', 'overrides', 'templates', 'plugins'], true)) : ?>
                        <ul class="kd-tags mt-1">
                            <?php foreach ($details as $detail) : ?>
                                <li><code><?php echo $this->escape((string) $detail) ?></code></li>
                            <?php endforeach ?>
                        </ul>
                    <?php endif ?>
                </div>
                <span class="kd-check__count"><?php echo Text::_('COM_KUNENA_TOOLS_UPDATE_STATUS_' . strtoupper($status)) ?></span>
            </li>
        <?php endforeach ?>
    </ul>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
