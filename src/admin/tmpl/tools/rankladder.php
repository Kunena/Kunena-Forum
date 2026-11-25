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
use Kunena\Forum\Libraries\Tools\KunenaRankLadder;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$report   = $this->tool;
$ranks    = $report['ranks'];
$title    = fn (int $id) => '<a href="' . Route::_('index.php?option=com_kunena&view=rank&layout=edit&id=' . $id) . '">' . $this->escape(Text::_((string) ($ranks[$id]['rankTitle'] ?? '#' . $id))) . '</a>';
$problems = \count($report['same']) + (int) $report['noStart'] + \count($report['unused']) + \count($report['images']) + \count($report['unknown']);
$same     = [];

foreach ($report['same'] as $ids) {
    $same = array_merge($same, $ids);
}
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero<?php echo $problems ? ' is-problem' : '' ?>">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-ranking-star" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title"><?php echo $problems ? Text::plural('COM_KUNENA_TOOLS_RANKS_N_FOUND', $problems) : Text::_('COM_KUNENA_TOOLS_RANKS_NONE') ?></h2>
                <p class="kd-hero__meta"><?php echo Text::sprintf('COM_KUNENA_TOOLS_RANKS_INTRO', KunenaRankLadder::FOLDER) ?></p>
            </div>
        </div>
    </section>

    <?php if ($report['noStart']) : ?>
        <div class="alert alert-warning"><?php echo Text::sprintf('COM_KUNENA_TOOLS_RANKS_NO_START', (int) $report['ladder'][0]['rankMin']) ?></div>
    <?php endif ?>

    <?php if ($report['unknown']) : ?>
        <div class="alert alert-warning">
            <?php echo Text::plural('COM_KUNENA_TOOLS_RANKS_N_UNKNOWN', array_sum($report['unknown'])) ?>
            <?php if ($this->canAdmin) : ?>
                <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post" class="mt-2">
                    <input type="hidden" name="task" value="tools.resetUnknownRanks">
                    <?php echo HTMLHelper::_('form.token') ?>
                    <button type="submit" class="kd-btn kd-btn--ghost" data-kd-confirm>
                        <span class="fa-solid fa-rotate-left" aria-hidden="true"></span>
                        <?php echo Text::_('COM_KUNENA_TOOLS_RANKS_RESET') ?>
                    </button>
                </form>
            <?php endif ?>
        </div>
    <?php endif ?>

    <section class="kd-panel" aria-labelledby="kd-ranks-ladder">
        <h3 id="kd-ranks-ladder" class="kd-panel__title"><?php echo Text::_('COM_KUNENA_TOOLS_RANKS_LADDER') ?></h3>
        <div class="kd-scroll">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_RANKS_RANK') ?></th>
                        <th scope="col" class="w-15 text-end"><?php echo Text::_('COM_KUNENA_TOOLS_RANKS_FROM') ?></th>
                        <th scope="col" class="w-30"><?php echo Text::_('COM_KUNENA_TOOLS_BANS_PROBLEMS') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($report['ladder'] as $rank) :
                        $id = (int) $rank['rankId']; ?>
                        <tr>
                            <th scope="row"><?php echo $title($id) ?></th>
                            <td class="text-end"><?php echo (int) $rank['rankMin'] ?></td>
                            <td>
                                <?php if (\in_array($id, $same, true)) : ?>
                                    <span class="kd-pill kd-pill--warn"><?php echo Text::_('COM_KUNENA_TOOLS_RANKS_SAME') ?></span>
                                <?php endif ?>
                                <?php if (\in_array($id, $report['images'], true)) : ?>
                                    <span class="kd-pill kd-pill--warn"><?php echo Text::_('COM_KUNENA_TOOLS_RANKS_IMAGE') ?></span>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="kd-panel" aria-labelledby="kd-ranks-special">
        <h3 id="kd-ranks-special" class="kd-panel__title"><?php echo Text::_('COM_KUNENA_TOOLS_RANKS_SPECIAL') ?></h3>
        <ul class="kd-tags">
            <?php foreach ($ranks as $id => $rank) :
                if (!(int) $rank['rankSpecial']) {
                    continue;
                } ?>
                <li>
                    <?php echo $title((int) $id) ?>
                    <?php if (KunenaRankLadder::isAutomatic((string) $rank['rankImage'])) : ?>
                        <span class="kd-pill"><?php echo Text::_('COM_KUNENA_TOOLS_RANKS_AUTOMATIC') ?></span>
                    <?php elseif (\in_array((int) $id, $report['unused'], true)) : ?>
                        <span class="kd-pill"><?php echo Text::_('COM_KUNENA_TOOLS_RANKS_NOBODY') ?></span>
                    <?php else : ?>
                        <span class="kd-pill"><?php echo Text::plural('COM_KUNENA_TOOLS_RANKS_N_USERS', (int) ($report['given'][$id] ?? 0)) ?></span>
                    <?php endif ?>
                    <?php if (\in_array((int) $id, $report['images'], true)) : ?>
                        <span class="kd-pill kd-pill--warn"><?php echo Text::_('COM_KUNENA_TOOLS_RANKS_IMAGE') ?></span>
                    <?php endif ?>
                </li>
            <?php endforeach ?>
        </ul>
        <p class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_RANKS_SPECIAL_HINT') ?></p>
    </section>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
