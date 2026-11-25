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
use Joomla\CMS\Session\Session;
use Kunena\Forum\Libraries\Forum\KunenaDiagnostics;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Version\KunenaVersion;

Text::script('COM_KUNENA_DIAGNOSTICS_DELETE_CONFIRM');

$base = 'administrator/index.php?option=com_kunena';
$page = KunenaRoute::_($base . '&view=tools&layout=diagnostics');

// The title of a check: its text, or its name in words when it has no text yet.
$title = static function (string $check): string {
    $key  = KunenaDiagnostics::titleKey($check);
    $text = Text::_($key);

    return $text !== $key ? $text : ucfirst(strtolower(preg_replace('/(?<=[a-z0-9])(?=[A-Z])/', ' ', $check)));
};

// The buttons of a check with issues: fix, delete (asks first), show the items.
$actions = static function (string $check, bool $show) use ($base): string {
    $token = Session::getFormToken() . '=1';
    $html  = '';

    if (KunenaDiagnostics::canFix($check)) {
        $html .= '<a class="kd-btn kd-btn--primary kd-btn--sm" href="' . KunenaRoute::_($base . "&task=tools.diagnostics&fix={$check}&{$token}") . '">'
            . '<span class="fa-solid fa-wrench" aria-hidden="true"></span>' . Text::_('COM_KUNENA_DIAGNOSTICS_FIX') . '</a>';
    }

    if (KunenaDiagnostics::canDelete($check)) {
        $html .= '<a class="kd-btn kd-btn--danger kd-btn--sm" data-kd-confirm href="' . KunenaRoute::_($base . "&task=tools.diagnostics&delete={$check}&{$token}") . '">'
            . '<span class="fa-solid fa-trash-can" aria-hidden="true"></span>' . Text::_('COM_KUNENA_DIAGNOSTICS_DELETE') . '</a>';
    }

    if ($show) {
        $html .= '<a class="kd-btn kd-btn--ghost kd-btn--sm" href="' . KunenaRoute::_($base . "&view=tools&layout=diagnostics&check={$check}") . '">'
            . '<span class="fa-solid fa-list" aria-hidden="true"></span>' . Text::_('COM_KUNENA_DIAGNOSTICS_SHOW') . '</a>';
    }

    return $html;
};
?>

<form action="<?php echo KunenaRoute::_($base . '&view=tools') ?>" method="post" id="adminForm" name="adminForm">
    <input type="hidden" name="task" value="diagnostics"/>
    <?php echo HTMLHelper::_('form.token'); ?>
</form>

<div id="kunena" class="kd kd-diag">
    <?php if ($this->check !== '') :
        // One check: its broken items.
        $fields = array_keys((array) reset($this->items));
        $notice = $this->items ? KunenaDiagnostics::canNotice($this->check) : '';
        ?>
        <div>
            <a class="kd-btn kd-btn--ghost kd-btn--sm" href="<?php echo $page ?>">
                <span class="fa-solid fa-arrow-left" aria-hidden="true"></span>
                <?php echo Text::_('COM_KUNENA_DIAGNOSTICS_BACK') ?>
            </a>
        </div>

        <section class="kd-hero kd-diag__hero<?php echo $this->items ? ' is-problem' : '' ?>">
            <div class="kd-hero__brand">
                <span class="kd-diag__heroicon fa-solid fa-<?php echo $this->items ? 'triangle-exclamation' : 'circle-check' ?>" aria-hidden="true"></span>
                <div>
                    <h2 class="kd-hero__title"><?php echo $this->escape($title($this->check)) ?></h2>
                    <p class="kd-hero__meta">
                        <code><?php echo $this->escape($this->check) ?></code>
                        <span aria-hidden="true">·</span>
                        <?php echo $this->items
                            ? Text::sprintf('COM_KUNENA_DIAGNOSTICS_LABEL_ITEMS_SHOWN', \count($this->items), $this->itemsTotal)
                            : Text::_('COM_KUNENA_DIAGNOSTICS_PASSED') ?>
                    </p>
                </div>
            </div>
            <div class="kd-hero__actions"><?php echo $this->items ? $actions($this->check, false) : '' ?></div>
        </section>

        <?php if ($notice) : ?>
            <div class="alert alert-warning"><?php echo $this->escape($notice) ?></div>
        <?php endif ?>

        <?php if ($this->items) : ?>
            <table class="table kd-diag__items">
                <thead>
                    <tr>
                        <?php foreach ($fields as $field) : ?>
                            <th scope="col"><?php echo $this->escape($field) ?></th>
                        <?php endforeach ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->items as $row) : ?>
                        <tr>
                            <?php foreach ($row as $field => $value) :
                                $value = (string) $value;

                                if (mb_strlen($value) > 200) {
                                    $value = mb_substr($value, 0, 200) . '…';
                                }
                                ?>
                                <td<?php echo ($this->fieldInfo[$field] ?? '') === 'invalid' ? ' class="kd-diag__invalid"' : '' ?>><?php echo $this->escape($value) ?></td>
                            <?php endforeach ?>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        <?php endif ?>

    <?php else :
        // All the checks, by group; the ones with issues first.
        $groups = KunenaDiagnostics::getGroups();
        $other  = array_values(array_diff(KunenaDiagnostics::getList(), array_merge(...array_values($groups))));

        if ($other) {
            $groups['COM_KUNENA_DIAGNOSTICS_GROUP_OTHER'] = $other;
        }

        $counts = [];

        foreach (KunenaDiagnostics::getList() as $check) {
            $counts[$check] = KunenaDiagnostics::count($check);
        }

        $failed = array_filter($counts);
        ?>
        <section class="kd-hero kd-diag__hero<?php echo $failed ? ' is-problem' : '' ?>">
            <div class="kd-hero__brand">
                <span class="kd-diag__heroicon fa-solid fa-<?php echo $failed ? 'triangle-exclamation' : 'stethoscope' ?>" aria-hidden="true"></span>
                <div>
                    <h2 class="kd-hero__title">
                        <?php echo $failed
                            ? Text::sprintf('COM_KUNENA_DIAGNOSTICS_SUMMARY_PROBLEMS', \count($failed), \count($counts))
                            : Text::sprintf('COM_KUNENA_DIAGNOSTICS_SUMMARY_OK', \count($counts)) ?>
                    </h2>
                    <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_DIAGNOSTICS_INTRO') ?></p>
                </div>
            </div>
            <div class="kd-hero__status">
                <span class="kd-pill kd-pill--<?php echo $failed ? 'warn' : 'ok' ?>">
                    <span class="fa-solid fa-<?php echo $failed ? 'circle-exclamation' : 'circle-check' ?>" aria-hidden="true"></span>
                    <?php echo Text::_($failed ? 'COM_KUNENA_DIAGNOSTICS_STATUS_PROBLEMS' : 'COM_KUNENA_DIAGNOSTICS_STATUS_OK') ?>
                </span>
            </div>
            <div class="kd-hero__actions">
                <a class="kd-btn kd-btn--ghost" href="<?php echo $page ?>">
                    <span class="fa-solid fa-rotate" aria-hidden="true"></span>
                    <?php echo Text::_('COM_KUNENA_DIAGNOSTICS_RUN_AGAIN') ?>
                </a>
            </div>
        </section>

        <?php if ($failed) : ?>
            <section class="kd-section" aria-labelledby="kd-diag-problems">
                <h3 id="kd-diag-problems" class="kd-section__title"><?php echo Text::_('COM_KUNENA_DIAGNOSTICS_SECTION_PROBLEMS') ?></h3>
                <div class="kd-checks">
                    <?php foreach ($failed as $check => $count) : ?>
                        <div class="kd-check is-problem">
                            <span class="kd-check__icon fa-solid fa-triangle-exclamation" aria-hidden="true"></span>
                            <div class="kd-check__body">
                                <span class="kd-check__title"><?php echo $this->escape($title($check)) ?></span>
                                <code class="kd-check__code"><?php echo $this->escape($check) ?></code>
                                <?php if ($notice = KunenaDiagnostics::canNotice($check)) : ?>
                                    <span class="kd-check__notice"><?php echo $this->escape($notice) ?></span>
                                <?php endif ?>
                            </div>
                            <span class="kd-check__count"><?php echo Text::plural('COM_KUNENA_DIAGNOSTICS_N_PROBLEMS', (int) $count) ?></span>
                            <div class="kd-check__actions"><?php echo $actions($check, true) ?></div>
                        </div>
                    <?php endforeach ?>
                </div>
            </section>
        <?php endif ?>

        <div class="kd-diag__groups">
            <?php foreach ($groups as $heading => $checks) : ?>
                <section class="kd-section" aria-labelledby="kd-diag-<?php echo strtolower(str_replace('COM_KUNENA_DIAGNOSTICS_GROUP_', '', $heading)) ?>">
                    <h3 id="kd-diag-<?php echo strtolower(str_replace('COM_KUNENA_DIAGNOSTICS_GROUP_', '', $heading)) ?>" class="kd-section__title"><?php echo Text::_($heading) ?></h3>
                    <ul class="kd-checks kd-checks--compact">
                        <?php foreach ($checks as $check) :
                            $count = $counts[$check] ?? 0; ?>
                            <li class="kd-check<?php echo $count ? ' is-problem' : '' ?>">
                                <span class="kd-check__icon fa-solid fa-<?php echo $count ? 'triangle-exclamation' : 'circle-check' ?>" aria-hidden="true"></span>
                                <div class="kd-check__body">
                                    <span class="kd-check__title"><?php echo $this->escape($title($check)) ?></span>
                                    <code class="kd-check__code"><?php echo $this->escape($check) ?></code>
                                </div>
                                <span class="kd-check__count">
                                    <?php echo $count
                                        ? Text::plural('COM_KUNENA_DIAGNOSTICS_N_PROBLEMS', (int) $count)
                                        : Text::_('COM_KUNENA_DIAGNOSTICS_PASSED') ?>
                                </span>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </section>
            <?php endforeach ?>
        </div>
    <?php endif ?>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>

<script>
    document.querySelectorAll('#kunena [data-kd-confirm]').forEach(function (link) {
        link.addEventListener('click', function (event) {
            if (!window.confirm(Joomla.Text._('COM_KUNENA_DIAGNOSTICS_DELETE_CONFIRM'))) {
                event.preventDefault();
            }
        });
    });
</script>
