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
use Kunena\Forum\Administrator\Helper\ConfigHelper;
use Kunena\Forum\Libraries\Tools\KunenaUpdateCheck as U;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$checks = $this->tool['checks'];
$order  = [U::PROBLEM => 0, U::WARNING => 1, U::INFO => 2, U::OK => 3];
usort($checks, static fn ($a, $b) => $order[$a[1]] <=> $order[$b[1]]);

$problems = \count(array_filter($checks, static fn ($c) => $c[1] === U::PROBLEM));
$warnings = \count(array_filter($checks, static fn ($c) => $c[1] === U::WARNING));
$icons    = [U::OK => 'circle-check', U::WARNING => 'triangle-exclamation', U::PROBLEM => 'circle-xmark', U::INFO => 'circle-info'];

// Where a check is changed.
$places = [
    'kunena'     => [ConfigHelper::link(), 'COM_KUNENA_TOOLS_SECURITY_OPEN_KUNENA'],
    'com_users'  => [Route::_('index.php?option=com_config&view=component&component=com_users'), 'COM_KUNENA_TOOLS_SECURITY_OPEN_USERS'],
    'com_config' => [Route::_('index.php?option=com_config'), 'COM_KUNENA_TOOLS_SECURITY_OPEN_CONFIG'],
];
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero<?php echo $problems || $warnings ? ' is-problem' : '' ?>">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-<?php echo $problems ? 'circle-xmark' : ($warnings ? 'triangle-exclamation' : 'shield-halved') ?>" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title">
                    <?php echo $problems ? Text::plural('COM_KUNENA_TOOLS_SECURITY_N_PROBLEMS', $problems) : ($warnings ? Text::plural('COM_KUNENA_TOOLS_SECURITY_N_WARNINGS', $warnings) : Text::_('COM_KUNENA_TOOLS_SECURITY_FINE')) ?>
                </h2>
                <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_TOOLS_SECURITY_INTRO') ?></p>
            </div>
        </div>
    </section>

    <ul class="kd-checks">
        <?php foreach ($checks as [$key, $status, $value, $details, $where]) : ?>
            <li class="kd-check<?php echo \in_array($status, [U::PROBLEM, U::WARNING], true) ? ' is-problem' : '' ?>">
                <span class="kd-check__icon fa-solid fa-<?php echo $icons[$status] ?>" aria-hidden="true"></span>
                <div class="kd-check__body">
                    <span class="kd-check__title"><?php echo Text::_('COM_KUNENA_TOOLS_SECURITY_' . strtoupper($key)) ?></span>
                    <span class="kd-form__hint"><?php echo Text::sprintf('COM_KUNENA_TOOLS_SECURITY_' . strtoupper($key) . '_' . strtoupper($status), $this->escape($value)) ?></span>
                    <?php if ($key === 'integrity' && $details) : ?>
                        <ul class="kd-tags mt-1">
                            <?php foreach (\array_slice($details, 0, 25) as $detail) :
                                [$kind, $path] = explode(':', (string) $detail, 2); ?>
                                <li><code><?php echo $this->escape($path) ?></code> <?php echo Text::_('COM_KUNENA_TOOLS_SECURITY_INTEGRITY_' . strtoupper($kind)) ?></li>
                            <?php endforeach ?>
                        </ul>
                        <?php if (\count($details) > 25) : ?>
                            <span class="kd-form__hint"><?php echo Text::sprintf('COM_KUNENA_TOOLS_TRANSLATIONS_FIRST_N', 25) ?></span>
                        <?php endif ?>
                    <?php endif ?>
                    <?php if ($status !== U::OK && isset($places[$where])) : ?>
                        <a class="kd-form__hint" href="<?php echo $this->escape($places[$where][0]) ?>"><?php echo Text::_($places[$where][1]) ?> ›</a>
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
