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
use Kunena\Forum\Libraries\Version\KunenaVersion;

$items   = $this->tool['items'];
$link    =static fn (array $m) => Route::link('site', 'index.php?option=com_kunena&view=topic&catid=' . (int) $m['catid'] . '&id=' . (int) $m['thread'] . '&mesid=' . (int) $m['id']) . '#' . (int) $m['id'];
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero<?php echo $items ? ' is-problem' : '' ?>">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-code" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title">
                    <?php echo $items ? Text::plural('COM_KUNENA_TOOLS_BBCODE_N_FOUND', \count($items), $this->tool['checked']) : Text::sprintf('COM_KUNENA_TOOLS_BBCODE_NONE', $this->tool['checked']) ?>
                </h2>
                <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_TOOLS_BBCODE_INTRO') ?></p>
            </div>
        </div>
        <div class="kd-hero__actions">
            <a class="kd-btn kd-btn--ghost" href="<?php echo Route::_('index.php?option=com_kunena&view=tools&layout=findreplace') ?>">
                <span class="fa-solid fa-arrow-right-arrow-left" aria-hidden="true"></span>
                <?php echo Text::_('COM_KUNENA_TOOLS_REPLACE') ?>
            </a>
        </div>
    </section>

    <?php if ($items) : ?>
        <div class="kd-scroll">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_SPAM_MESSAGE') ?></th>
                        <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_BBCODE_PROBLEMS') ?></th>
                        <th scope="col" class="w-10"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item) : ?>
                        <tr>
                            <th scope="row">
                                <a href="<?php echo $this->escape($link($item)) ?>" target="_blank" rel="noopener"><?php echo $this->escape($item['subject']) ?></a>
                                <br><span class="kd-form__hint"><?php echo $this->escape((string) $item['name']) ?> · <?php echo HTMLHelper::_('date', (int) $item['time'], Text::_('DATE_FORMAT_LC5')) ?></span>
                            </th>
                            <td>
                                <ul class="kd-tags">
                                    <?php foreach ($item['problems'] as [$kind, $tag]) : ?>
                                        <li><?php echo Text::sprintf('COM_KUNENA_TOOLS_BBCODE_' . strtoupper($kind), '<code>' . $this->escape($tag) . '</code>') ?></li>
                                    <?php endforeach ?>
                                </ul>
                            </td>
                            <td class="text-end">
                                <a class="kd-btn kd-btn--sm kd-btn--ghost" href="<?php echo $this->escape($link($item)) ?>" target="_blank" rel="noopener">
                                    <?php echo Text::_('COM_KUNENA_TOOLS_BBCODE_PREVIEW') ?>
                                </a>
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
