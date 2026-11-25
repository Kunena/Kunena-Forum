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
use Kunena\Forum\Libraries\Tools\KunenaBrokenLinks as B;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$links   = $this->tool['links'];
$checked = array_filter($links, static fn ($link) => $link['verdict'] !== null);
$broken  = array_filter($links, static fn ($link) => $link['verdict'] === B::BROKEN);
$unsure  = array_filter($links, static fn ($link) => $link['verdict'] === B::UNSURE);
$left    = \count($links) - \count($checked);
$post    = static fn (array $p) => Route::link('site', 'index.php?option=com_kunena&view=topic&catid=' . $p['catid'] . '&id=' . $p['thread'] . '&mesid=' . $p['id'], false) . '#' . $p['id'];
$answer  = static fn (int $code) => $code === B::NO_HOST ? Text::_('COM_KUNENA_TOOLS_LINKS_NO_HOST') : ($code === 0 ? Text::_('COM_KUNENA_TOOLS_LINKS_NO_ANSWER') : (string) $code);
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero<?php echo $broken ? ' is-problem' : '' ?>">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-link-slash" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title">
                    <?php echo Text::plural('COM_KUNENA_TOOLS_LINKS_N_BROKEN', \count($broken)) ?>
                </h2>
                <p class="kd-hero__meta">
                    <?php echo Text::sprintf('COM_KUNENA_TOOLS_LINKS_INTRO', \count($links), (int) $this->tool['posts'], \count($checked)) ?>
                </p>
            </div>
        </div>
        <?php if ($this->canAdmin && $links) : ?>
            <div class="kd-hero__actions">
                <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post">
                    <input type="hidden" name="task" value="tools.checkLinks">
                    <?php echo HTMLHelper::_('form.token') ?>
                    <button type="submit" class="kd-btn kd-btn--primary"<?php echo $left ? '' : ' disabled' ?>>
                        <span class="fa-solid fa-magnifying-glass" aria-hidden="true"></span>
                        <?php echo Text::sprintf('COM_KUNENA_TOOLS_LINKS_CHECK_NEXT', min(B::BATCH, $left)) ?>
                    </button>
                </form>
                <?php if ($checked) : ?>
                    <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post">
                        <input type="hidden" name="task" value="tools.resetLinks">
                        <?php echo HTMLHelper::_('form.token') ?>
                        <button type="submit" class="kd-btn kd-btn--ghost" data-kd-confirm>
                            <span class="fa-solid fa-rotate" aria-hidden="true"></span>
                            <?php echo Text::_('COM_KUNENA_TOOLS_LINKS_RESET') ?>
                        </button>
                    </form>
                <?php endif ?>
            </div>
        <?php endif ?>
    </section>

    <?php if ($left) : ?>
        <div class="alert alert-info"><?php echo Text::plural('COM_KUNENA_TOOLS_LINKS_N_LEFT', $left) ?></div>
    <?php endif ?>

    <?php foreach ([[$broken, 'COM_KUNENA_TOOLS_LINKS_BROKEN', 'kd-pill--warn'], [$unsure, 'COM_KUNENA_TOOLS_LINKS_UNSURE', '']] as $i => [$list, $title, $pill]) : ?>
        <?php if ($list) : ?>
            <section class="kd-panel" aria-labelledby="kd-links-<?php echo $i ?>">
                <h3 id="kd-links-<?php echo $i ?>" class="kd-panel__title"><?php echo Text::_($title) ?> (<?php echo \count($list) ?>)</h3>
                <div class="kd-scroll">
                    <table class="table">
                        <tbody>
                            <?php foreach ($list as $url => $link) : ?>
                                <tr>
                                    <th scope="row">
                                        <a href="<?php echo $this->escape($url) ?>" target="_blank" rel="noopener noreferrer nofollow" class="kd-break"><?php echo $this->escape($url) ?></a>
                                        <br><span class="kd-pill <?php echo $pill ?>"><?php echo $this->escape($answer((int) $link['code'])) ?></span>
                                        <span class="kd-form__hint"><?php echo HTMLHelper::_('date', (int) $link['checked'], Text::_('DATE_FORMAT_LC5')) ?></span>
                                    </th>
                                    <td class="w-40">
                                        <?php foreach ($link['posts'] as $p) : ?>
                                            <a href="<?php echo $this->escape($post($p)) ?>" target="_blank" rel="noopener"><?php echo $this->escape($p['subject']) ?></a><br>
                                        <?php endforeach ?>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </section>
        <?php endif ?>
    <?php endforeach ?>

    <?php if ($broken) : ?>
        <p class="kd-form__hint">
            <?php echo Text::_('COM_KUNENA_TOOLS_LINKS_FIX') ?>
            <a href="<?php echo Route::_('index.php?option=com_kunena&view=tools&layout=findreplace') ?>"><?php echo Text::_('COM_KUNENA_TOOLS_REPLACE') ?> ›</a>
        </p>
    <?php endif ?>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
