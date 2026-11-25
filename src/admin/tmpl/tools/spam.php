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
use Kunena\Forum\Libraries\Tools\KunenaSpamFinder;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$suspects = $this->tool['suspects'];
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero<?php echo $suspects ? ' is-problem' : '' ?>">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-<?php echo $suspects ? 'shield-virus' : 'shield-halved' ?>" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title">
                    <?php echo $suspects
                        ? Text::plural('COM_KUNENA_TOOLS_SPAM_N_FOUND', \count($suspects), $this->tool['days'])
                        : Text::sprintf('COM_KUNENA_TOOLS_SPAM_NONE', $this->tool['days']) ?>
                </h2>
                <p class="kd-hero__meta"><?php echo Text::sprintf('COM_KUNENA_TOOLS_SPAM_INTRO', KunenaSpamFinder::THRESHOLD) ?></p>
            </div>
        </div>
    </section>

    <form action="<?php echo Route::_('index.php') ?>" method="get" class="kd-panel">
        <input type="hidden" name="option" value="com_kunena">
        <input type="hidden" name="view" value="tools">
        <input type="hidden" name="layout" value="spam">
        <div class="kd-form">
            <div class="kd-form__field">
                <label for="kd-spam-days"><?php echo Text::_('COM_KUNENA_TOOLS_SPAM_DAYS') ?></label>
                <select class="form-select" id="kd-spam-days" name="days">
                    <?php foreach ([1, 7, 30, 90, 365] as $days) : ?>
                        <option value="<?php echo $days ?>"<?php echo $days === $this->tool['days'] ? ' selected' : '' ?>>
                            <?php echo Text::plural('COM_KUNENA_TOOLS_SPAM_N_DAYS', $days) ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>
            <div>
                <button type="submit" class="kd-btn kd-btn--ghost">
                    <span class="fa-solid fa-rotate" aria-hidden="true"></span>
                    <?php echo Text::_('COM_KUNENA_DIAGNOSTICS_RUN_AGAIN') ?>
                </button>
            </div>
        </div>
    </form>

    <?php if ($suspects) : ?>
        <div class="kd-scroll">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col" class="w-5"><?php echo Text::_('COM_KUNENA_TOOLS_SPAM_SCORE') ?></th>
                        <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_SPAM_MESSAGE') ?></th>
                        <th scope="col" class="w-15"><?php echo Text::_('COM_KUNENA_TOOLS_SPAM_AUTHOR') ?></th>
                        <th scope="col" class="w-25"><?php echo Text::_('COM_KUNENA_TOOLS_SPAM_SIGNS') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($suspects as $row) :
                        $link = Route::link('site', 'index.php?option=com_kunena&view=topic&catid=' . (int) $row['catid'] . '&id=' . (int) $row['thread'] . '&mesid=' . (int) $row['id'])
                            . '#' . (int) $row['id']; ?>
                        <tr>
                            <td><span class="kd-pill kd-pill--warn"><?php echo (int) $row['score'] ?></span></td>
                            <th scope="row">
                                <a href="<?php echo $this->escape($link) ?>" target="_blank" rel="noopener"><?php echo $this->escape($row['subject']) ?></a>
                                <br><span class="kd-form__hint">
                                    <?php echo HTMLHelper::_('date', (int) $row['time'], Text::_('DATE_FORMAT_LC2')) ?>
                                    <?php if ($row['ip']) : ?> · <code><?php echo $this->escape($row['ip']) ?></code><?php endif ?>
                                </span>
                            </th>
                            <td>
                                <?php if ((int) $row['userid'] > 0) : ?>
                                    <a href="<?php echo Route::_('index.php?option=com_kunena&view=tools&layout=usercleanup&userid=' . (int) $row['userid']) ?>">
                                        <?php echo $this->escape($row['username'] ?: $row['name']) ?>
                                    </a>
                                <?php else : ?>
                                    <?php echo $this->escape($row['name']) ?> <span class="kd-form__hint">(<?php echo Text::_('COM_KUNENA_TOOLS_SPAM_GUEST') ?>)</span>
                                <?php endif ?>
                            </td>
                            <td>
                                <ul class="kd-tags">
                                    <?php foreach ($row['signs'] as $sign) : ?>
                                        <li><?php echo Text::_('COM_KUNENA_TOOLS_SPAM_SIGN_' . strtoupper($sign)) ?></li>
                                    <?php endforeach ?>
                                </ul>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php endif ?>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
