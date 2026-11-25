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
use Kunena\Forum\Libraries\Version\KunenaVersion;

$items = $this->tool['items'];
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero<?php echo $items ? ' is-problem' : '' ?>">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-circle-user" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title">
                    <?php echo $items ? Text::plural('COM_KUNENA_TOOLS_AVATARS_N_FOUND', \count($items)) : Text::_('COM_KUNENA_TOOLS_AVATARS_NONE') ?>
                </h2>
                <p class="kd-hero__meta">
                    <?php echo Text::sprintf('COM_KUNENA_TOOLS_AVATARS_INTRO', (int) $this->tool['users'], HTMLHelper::_('number.bytes', (int) $this->tool['maxBytes'])) ?>
                    <a href="<?php echo Route::_('index.php?option=com_kunena&view=tools&layout=orphanfiles') ?>"><?php echo Text::_('COM_KUNENA_TOOLS_ORPHANS') ?> ›</a>
                </p>
            </div>
        </div>
    </section>

    <?php if ($items) : ?>
        <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post">
            <input type="hidden" name="task" value="tools.removeAvatars">
            <?php echo HTMLHelper::_('form.token') ?>

            <div class="kd-scroll">
                <table class="table">
                    <thead>
                        <tr>
                            <td class="w-1 text-center">
                                <input type="checkbox" class="form-check-input" data-kd-all="kd-avatar"
                                       aria-label="<?php echo $this->escape(Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_SELECT_ALL')) ?>">
                            </td>
                            <th scope="col" class="w-20"><?php echo Text::_('COM_KUNENA_TOOLS_PROFILESPAM_USER') ?></th>
                            <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_AVATARS_VALUE') ?></th>
                            <th scope="col" class="w-20"><?php echo Text::_('COM_KUNENA_TOOLS_BANS_PROBLEMS') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item) :
                            $id = (int) $item['userid']; ?>
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input kd-avatar" name="ids[]" value="<?php echo $id ?>" id="kd-avatar-<?php echo $id ?>">
                                </td>
                                <th scope="row">
                                    <label for="kd-avatar-<?php echo $id ?>"><?php echo $this->escape($item['username']) ?></label>
                                    <br><a class="kd-form__hint" href="<?php echo Route::_('index.php?option=com_kunena&view=user&layout=edit&userid=' . $id) ?>"><?php echo Text::_('COM_KUNENA_TOOLS_BANS_EDIT') ?> ›</a>
                                </th>
                                <td><code class="kd-break"><?php echo $this->escape($item['avatar']) ?></code></td>
                                <td>
                                    <span class="kd-pill kd-pill--warn"><?php echo Text::_('COM_KUNENA_TOOLS_AVATARS_' . strtoupper($item['problem'])) ?></span>
                                    <?php if ($item['problem'] === 'large') : ?>
                                        <br><span class="kd-form__hint"><?php echo HTMLHelper::_('number.bytes', (int) $item['size']) ?></span>
                                    <?php endif ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>

            <?php if ($this->canAdmin) : ?>
                <div class="kd-form__actions">
                    <button type="submit" class="kd-btn kd-btn--danger" data-kd-confirm>
                        <span class="fa-solid fa-user-slash" aria-hidden="true"></span>
                        <?php echo Text::_('COM_KUNENA_TOOLS_AVATARS_REMOVE') ?>
                    </button>
                    <span class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_AVATARS_REMOVE_HINT') ?></span>
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
