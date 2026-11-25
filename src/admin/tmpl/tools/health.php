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
use Kunena\Forum\Libraries\Tools\KunenaHealthSummary;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$report = $this->tool;
$pills  = ['error' => ' kd-pill--warn', 'problem' => ' kd-pill--warn', 'warning' => ' kd-pill--warn', 'info' => '', 'ok' => ''];
$link   = static fn (string $tool) => Route::_('index.php?option=com_kunena&view=tools&layout=' . $tool);
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero<?php echo \in_array($report['level'], ['error', 'problem', 'warning'], true) ? ' is-problem' : '' ?>">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-heart-pulse" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title"><?php echo Text::_('COM_KUNENA_TOOLS_HEALTH_LEVEL_' . strtoupper($report['level'])) ?></h2>
                <p class="kd-hero__meta"><?php echo Text::sprintf('COM_KUNENA_TOOLS_HEALTH_INTRO', \count($report['entries']), $report['seconds']) ?></p>
            </div>
        </div>
        <div class="kd-hero__actions">
            <a class="kd-btn kd-btn--ghost" href="<?php echo Route::_('index.php?option=com_kunena&view=tools&layout=health') ?>">
                <span class="fa-solid fa-rotate" aria-hidden="true"></span>
                <?php echo Text::_('COM_KUNENA_TOOLS_HEALTH_AGAIN') ?>
            </a>
        </div>
    </section>

    <div class="kd-cards">
        <?php foreach ($report['entries'] as $entry) :
            [$title, $icon] = KunenaHealthSummary::TOOLS[$entry['tool']] ?? ['COM_KUNENA_ADMIN_TOOLS', 'wrench']; ?>
            <a class="kd-card" href="<?php echo $link($entry['tool']) ?>">
                <span class="kd-card__icon fa-solid fa-<?php echo $icon ?>" aria-hidden="true"></span>
                <span class="kd-card__title"><?php echo Text::_($title) ?></span>
                <span class="kd-card__desc">
                    <span class="kd-pill<?php echo $pills[$entry['level']] ?? '' ?>">
                        <?php if ($entry['level'] === 'error') : ?>
                            <?php echo Text::_('COM_KUNENA_TOOLS_HEALTH_FAILED') ?>
                        <?php elseif ($entry['count'] > 0) : ?>
                            <?php echo Text::plural('COM_KUNENA_TOOLS_HEALTH_N_FOUND', (int) $entry['count']) ?>
                        <?php else : ?>
                            <?php echo Text::_('COM_KUNENA_TOOLS_HEALTH_FINE') ?>
                        <?php endif ?>
                    </span>
                    <?php if ($entry['level'] === 'error') : ?>
                        <br><span class="kd-break"><?php echo $this->escape((string) ($entry['error'] ?? '')) ?></span>
                    <?php endif ?>
                </span>
            </a>
        <?php endforeach ?>
    </div>

    <p class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_HEALTH_NOT_RUN') ?></p>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
