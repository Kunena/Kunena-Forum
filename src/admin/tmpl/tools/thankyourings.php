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
use Kunena\Forum\Libraries\Tools\KunenaThankyouRings;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$pairs = $this->tool['pairs'];
$user  = function (int $id): string {
    $link = Route::_('index.php?option=com_kunena&view=user&layout=edit&userid=' . $id);

    return '<a href="' . $link . '">' . $this->escape($this->tool['names'][$id] ?? '#' . $id) . '</a>';
};
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero<?php echo $pairs ? ' is-problem' : '' ?>">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-handshake" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title">
                    <?php echo $pairs ? Text::plural('COM_KUNENA_TOOLS_RINGS_N_FOUND', \count($pairs)) : Text::_('COM_KUNENA_TOOLS_RINGS_NONE') ?>
                </h2>
                <p class="kd-hero__meta"><?php echo Text::sprintf('COM_KUNENA_TOOLS_RINGS_INTRO', KunenaThankyouRings::MIN_EACH, (int) round(KunenaThankyouRings::MIN_SHARE * 100)) ?></p>
            </div>
        </div>
    </section>

    <?php if ($pairs) : ?>
        <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post">
            <input type="hidden" name="task" value="tools.removeThanks">
            <?php echo HTMLHelper::_('form.token') ?>

            <div class="kd-scroll">
                <table class="table">
                    <thead>
                        <tr>
                            <td class="w-1 text-center">
                                <input type="checkbox" class="form-check-input" data-kd-all="kd-ring"
                                       aria-label="<?php echo $this->escape(Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_SELECT_ALL')) ?>">
                            </td>
                            <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_RINGS_PAIR') ?></th>
                            <th scope="col" class="w-15 text-end"><?php echo Text::_('COM_KUNENA_TOOLS_RINGS_EACH_OTHER') ?></th>
                            <th scope="col" class="w-15 text-end"><?php echo Text::_('COM_KUNENA_TOOLS_RINGS_SHARE') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pairs as $pair) :
                            $value = (int) $pair['a'] . '-' . (int) $pair['b']; ?>
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input kd-ring" name="pairs[]" value="<?php echo $value ?>" id="kd-ring-<?php echo $value ?>"
                                           aria-label="<?php echo $this->escape(Text::_('COM_KUNENA_TOOLS_RINGS_PAIR')) ?>">
                                </td>
                                <th scope="row">
                                    <?php echo $user((int) $pair['a']) ?> ⇄ <?php echo $user((int) $pair['b']) ?>
                                    <br><span class="kd-form__hint"><?php echo Text::sprintf('COM_KUNENA_TOOLS_RINGS_GIVEN', (int) $pair['ab'], (int) $pair['givenA'], (int) $pair['ba'], (int) $pair['givenB']) ?></span>
                                </th>
                                <td class="text-end"><?php echo (int) $pair['ab'] + (int) $pair['ba'] ?></td>
                                <td class="text-end"><?php echo (int) round($pair['share'] * 100) ?>%</td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>

            <?php if ($this->canAdmin) : ?>
                <div class="kd-form__actions">
                    <button type="submit" class="kd-btn kd-btn--danger" data-kd-confirm>
                        <span class="fa-solid fa-trash" aria-hidden="true"></span>
                        <?php echo Text::_('COM_KUNENA_TOOLS_RINGS_REMOVE') ?>
                    </button>
                    <span class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_RINGS_REMOVE_HINT') ?></span>
                </div>
            <?php else : ?>
                <div class="alert alert-info"><?php echo Text::_('COM_KUNENA_TOOLS_ADMIN_ONLY') ?></div>
            <?php endif ?>
        </form>
    <?php endif ?>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
