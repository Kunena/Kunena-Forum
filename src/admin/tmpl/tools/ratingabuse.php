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
use Kunena\Forum\Libraries\Tools\KunenaRatingAbuse;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$report = $this->tool;
$count  = \count($report['sameScore']) + \count($report['targeted']);
$user   = fn (int $id) => '<a href="' . Route::_('index.php?option=com_kunena&view=user&layout=edit&userid=' . $id) . '">'
    . $this->escape($report['names'][$id] ?? '#' . $id) . '</a>';
$box    = fn (int $id) => '<input type="checkbox" class="form-check-input kd-rater" name="ids[]" value="' . $id . '" aria-label="'
    . $this->escape($report['names'][$id] ?? '#' . $id) . '">';
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero<?php echo $count ? ' is-problem' : '' ?>">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-star-half-stroke" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title"><?php echo $count ? Text::plural('COM_KUNENA_TOOLS_RATINGS_N_FOUND', $count) : Text::_('COM_KUNENA_TOOLS_RATINGS_NONE') ?></h2>
                <p class="kd-hero__meta">
                    <?php echo Text::sprintf('COM_KUNENA_TOOLS_RATINGS_INTRO', (int) $report['total'], KunenaRatingAbuse::MIN_RATINGS, (int) round(KunenaRatingAbuse::SAME_SHARE * 100), KunenaRatingAbuse::MIN_TARGETED) ?>
                </p>
            </div>
        </div>
    </section>

    <?php if ($count) : ?>
        <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post">
            <input type="hidden" name="task" value="tools.removeRatings">
            <?php echo HTMLHelper::_('form.token') ?>

            <?php if ($report['targeted']) : ?>
                <section class="kd-panel" aria-labelledby="kd-ratings-targeted">
                    <h3 id="kd-ratings-targeted" class="kd-panel__title"><?php echo Text::_('COM_KUNENA_TOOLS_RATINGS_TARGETED') ?></h3>
                    <div class="kd-scroll">
                        <table class="table">
                            <tbody>
                                <?php foreach ($report['targeted'] as $row) : ?>
                                    <tr>
                                        <td class="w-1 text-center"><?php echo $box((int) $row['user']) ?></td>
                                        <th scope="row"><?php echo $user((int) $row['user']) ?> → <?php echo $user((int) $row['author']) ?></th>
                                        <td class="w-30">
                                            <span class="kd-pill kd-pill--warn"><?php echo Text::plural('COM_KUNENA_TOOLS_RATINGS_' . strtoupper($row['kind']), (int) $row['ratings']) ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            <?php endif ?>

            <?php if ($report['sameScore']) : ?>
                <section class="kd-panel" aria-labelledby="kd-ratings-same">
                    <h3 id="kd-ratings-same" class="kd-panel__title"><?php echo Text::_('COM_KUNENA_TOOLS_RATINGS_SAME') ?></h3>
                    <div class="kd-scroll">
                        <table class="table">
                            <tbody>
                                <?php foreach ($report['sameScore'] as $row) : ?>
                                    <tr>
                                        <td class="w-1 text-center"><?php echo $box((int) $row['user']) ?></td>
                                        <th scope="row"><?php echo $user((int) $row['user']) ?></th>
                                        <td class="w-30">
                                            <?php echo Text::sprintf('COM_KUNENA_TOOLS_RATINGS_SAME_SCORE', (int) round($row['share'] * 100), (int) $row['ratings'], (int) $row['score']) ?>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            <?php endif ?>

            <?php if ($this->canAdmin) : ?>
                <div class="kd-form__actions">
                    <button type="submit" class="kd-btn kd-btn--danger" data-kd-confirm>
                        <span class="fa-solid fa-trash" aria-hidden="true"></span>
                        <?php echo Text::_('COM_KUNENA_TOOLS_RATINGS_REMOVE') ?>
                    </button>
                    <span class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_RATINGS_REMOVE_HINT') ?></span>
                </div>
            <?php endif ?>
        </form>
    <?php endif ?>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
