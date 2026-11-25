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
use Kunena\Forum\Libraries\Tools\KunenaBanReview;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$items = $this->tool['items'];

// Only a ban of a user who still moderates or subscribes can be cleaned.
$cleanable = array_filter($items, static fn ($item) => array_intersect($item['problems'], ['moderator', 'subscribed']));
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero<?php echo $items ? ' is-problem' : '' ?>">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-user-lock" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title">
                    <?php echo $items ? Text::plural('COM_KUNENA_TOOLS_BANS_N_FOUND', \count($items)) : Text::_('COM_KUNENA_TOOLS_BANS_NONE') ?>
                </h2>
                <p class="kd-hero__meta"><?php echo Text::plural('COM_KUNENA_TOOLS_BANS_INTRO', (int) $this->tool['active'], KunenaBanReview::SOON) ?></p>
            </div>
        </div>
    </section>

    <?php if ($items) : ?>
        <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post">
            <input type="hidden" name="task" value="tools.cleanBanned">
            <?php echo HTMLHelper::_('form.token') ?>

            <div class="kd-scroll">
                <table class="table">
                    <thead>
                        <tr>
                            <td class="w-1 text-center">
                                <?php if ($cleanable) : ?>
                                    <input type="checkbox" class="form-check-input" data-kd-all="kd-ban"
                                           aria-label="<?php echo $this->escape(Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_SELECT_ALL')) ?>">
                                <?php endif ?>
                            </td>
                            <th scope="col" class="w-20"><?php echo Text::_('COM_KUNENA_TOOLS_BANS_WHO') ?></th>
                            <th scope="col" class="w-20"><?php echo Text::_('COM_KUNENA_TOOLS_BANS_UNTIL') ?></th>
                            <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_BANS_PROBLEMS') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item) :
                            $ban  = $item['ban'];
                            $user = (int) $ban['userid'];
                            $id   = (int) $ban['id']; ?>
                            <tr>
                                <td class="text-center">
                                    <?php if ($user && array_intersect($item['problems'], ['moderator', 'subscribed'])) : ?>
                                        <input type="checkbox" class="form-check-input kd-ban" name="ids[]" value="<?php echo $user ?>" id="kd-ban-<?php echo $id ?>">
                                    <?php endif ?>
                                </td>
                                <th scope="row">
                                    <?php if ($user) : ?>
                                        <label for="kd-ban-<?php echo $id ?>"><?php echo $this->escape($this->tool['names'][$user] ?? '#' . $user) ?></label>
                                        <br><a class="kd-form__hint" href="<?php echo Route::_('index.php?option=com_kunena&view=user&layout=edit&userid=' . $user) ?>"><?php echo Text::_('COM_KUNENA_TOOLS_BANS_EDIT') ?> ›</a>
                                    <?php else : ?>
                                        <code><?php echo $this->escape((string) $ban['ip']) ?></code>
                                    <?php endif ?>
                                </th>
                                <td>
                                    <?php echo $item['end'] ? HTMLHelper::_('date', (int) $item['end'], Text::_('DATE_FORMAT_LC4')) : Text::_('COM_KUNENA_TOOLS_BANS_FOREVER') ?>
                                </td>
                                <td>
                                    <?php foreach ($item['problems'] as $problem) : ?>
                                        <span class="kd-pill<?php echo $problem === 'soon' ? '' : ' kd-pill--warn' ?>"><?php echo Text::_('COM_KUNENA_TOOLS_BANS_' . strtoupper($problem)) ?></span>
                                    <?php endforeach ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>

            <?php if ($cleanable && $this->canAdmin) : ?>
                <div class="kd-form__actions">
                    <button type="submit" class="kd-btn kd-btn--danger" data-kd-confirm>
                        <span class="fa-solid fa-user-minus" aria-hidden="true"></span>
                        <?php echo Text::_('COM_KUNENA_TOOLS_BANS_CLEAN') ?>
                    </button>
                    <span class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_BANS_CLEAN_HINT') ?></span>
                </div>
            <?php elseif ($cleanable) : ?>
                <div class="alert alert-info"><?php echo Text::_('COM_KUNENA_TOOLS_ADMIN_ONLY') ?></div>
            <?php endif ?>
        </form>
    <?php endif ?>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
