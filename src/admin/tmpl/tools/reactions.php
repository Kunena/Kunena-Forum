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
use Kunena\Forum\Libraries\Tools\KunenaReactionsOverview;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$report   = $this->tool;
$suspects = $report['suspects'];
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero<?php echo $suspects ? ' is-problem' : '' ?>">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-face-smile" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title"><?php echo Text::plural('COM_KUNENA_TOOLS_REACTIONS_N_TOTAL', (int) $report['total'], (int) $report['users']) ?></h2>
                <p class="kd-hero__meta"><?php echo Text::sprintf('COM_KUNENA_TOOLS_REACTIONS_INTRO', KunenaReactionsOverview::MIN_REACTIONS, KunenaReactionsOverview::RATIO) ?></p>
            </div>
        </div>
    </section>

    <?php if ($report['top']) : ?>
        <section class="kd-panel" aria-labelledby="kd-reactions-top">
            <h3 id="kd-reactions-top" class="kd-panel__title"><?php echo Text::_('COM_KUNENA_TOOLS_REACTIONS_TOP') ?></h3>
            <ul class="kd-tags">
                <?php foreach ($report['top'] as $reaction => $count) : ?>
                    <li><span class="fs-4"><?php echo $this->escape((string) $reaction) ?></span> <span class="kd-pill"><?php echo (int) $count ?></span></li>
                <?php endforeach ?>
            </ul>
        </section>
    <?php endif ?>

    <?php if ($suspects) : ?>
        <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post">
            <input type="hidden" name="task" value="tools.removeReactions">
            <?php echo HTMLHelper::_('form.token') ?>

            <section class="kd-panel" aria-labelledby="kd-reactions-suspects">
                <h3 id="kd-reactions-suspects" class="kd-panel__title"><?php echo Text::plural('COM_KUNENA_TOOLS_REACTIONS_N_SUSPECTS', \count($suspects)) ?></h3>
                <div class="kd-scroll">
                    <table class="table">
                        <thead>
                            <tr>
                                <td class="w-1 text-center">
                                    <input type="checkbox" class="form-check-input" data-kd-all="kd-reactor"
                                           aria-label="<?php echo $this->escape(Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_SELECT_ALL')) ?>">
                                </td>
                                <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_PROFILESPAM_USER') ?></th>
                                <th scope="col" class="w-15 text-end"><?php echo Text::_('COM_KUNENA_TOOLS_REACTIONS_REACTIONS') ?></th>
                                <th scope="col" class="w-15 text-end"><?php echo Text::_('COM_KUNENA_TOOLS_BIGTOPICS_POSTS') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($suspects as $user) :
                                $id = (int) $user['user']; ?>
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" class="form-check-input kd-reactor" name="ids[]" value="<?php echo $id ?>" id="kd-reactor-<?php echo $id ?>">
                                    </td>
                                    <th scope="row">
                                        <label for="kd-reactor-<?php echo $id ?>"><?php echo $this->escape($user['username'] !== '' ? $user['username'] : '#' . $id) ?></label>
                                        <br><a class="kd-form__hint" href="<?php echo Route::_('index.php?option=com_kunena&view=user&layout=edit&userid=' . $id) ?>"><?php echo Text::_('COM_KUNENA_TOOLS_BANS_EDIT') ?> ›</a>
                                    </th>
                                    <td class="text-end"><?php echo (int) $user['reactions'] ?></td>
                                    <td class="text-end"><?php echo (int) $user['posts'] ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <?php if ($this->canAdmin) : ?>
                <div class="kd-form__actions">
                    <button type="submit" class="kd-btn kd-btn--danger" data-kd-confirm>
                        <span class="fa-solid fa-trash" aria-hidden="true"></span>
                        <?php echo Text::_('COM_KUNENA_TOOLS_REACTIONS_REMOVE') ?>
                    </button>
                    <span class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_REACTIONS_REMOVE_HINT') ?></span>
                </div>
            <?php endif ?>
        </form>
    <?php endif ?>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
