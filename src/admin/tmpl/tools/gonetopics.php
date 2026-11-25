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
use Kunena\Forum\Administrator\Helper\ConfigHelper;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$items = $this->tool['items'];
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-ban" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title"><?php echo Text::plural('COM_KUNENA_TOOLS_GONE_N_FOUND', \count($items)) ?></h2>
                <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_TOOLS_GONE_INTRO') ?></p>
            </div>
        </div>
    </section>

    <?php if (!$this->tool['enabled']) : ?>
        <div class="alert alert-info">
            <?php echo Text::_('COM_KUNENA_TOOLS_GONE_DISABLED') ?>
            <a href="<?php echo $this->escape(ConfigHelper::link()) ?>"><?php echo Text::_('COM_KUNENA_TOOLS_SECURITY_OPEN_KUNENA') ?> ›</a>
        </div>
    <?php endif ?>

    <?php if (!$this->tool['plugin']) : ?>
        <div class="alert alert-warning">
            <?php echo Text::_('COM_KUNENA_TOOLS_GONE_NO_PLUGIN') ?>
            <a href="<?php echo Route::_('index.php?option=com_plugins&view=plugins&filter[folder]=system&filter[search]=redirect') ?>"><?php echo Text::_('COM_KUNENA_TOOLS_GONE_OPEN_PLUGIN') ?> ›</a>
        </div>
    <?php endif ?>

    <?php if ($items) : ?>
        <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post">
            <?php echo HTMLHelper::_('form.token') ?>

            <div class="kd-scroll">
                <table class="table">
                    <thead>
                        <tr>
                            <td class="w-1 text-center">
                                <input type="checkbox" class="form-check-input" data-kd-all="kd-gone"
                                       aria-label="<?php echo $this->escape(Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_SELECT_ALL')) ?>">
                            </td>
                            <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_GONE_ADDRESS') ?></th>
                            <th scope="col" class="w-20"><?php echo Text::_('COM_KUNENA_TOOLS_GONE_ANSWER') ?></th>
                            <th scope="col" class="w-10 text-end"><?php echo Text::_('COM_KUNENA_TOOLS_GONE_HITS') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $row) :
                            $id = (int) $row['id']; ?>
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input kd-gone" name="ids[]" value="<?php echo $id ?>" id="kd-gone-<?php echo $id ?>">
                                </td>
                                <th scope="row">
                                    <label for="kd-gone-<?php echo $id ?>" class="kd-break"><?php echo $this->escape((string) $row['old_url']) ?></label>
                                    <br><span class="kd-form__hint"><?php echo HTMLHelper::_('date', (string) $row['created_date'], Text::_('DATE_FORMAT_LC5')) ?></span>
                                </th>
                                <td>
                                    <?php if ((int) $row['header'] === 410) : ?>
                                        <span class="kd-pill"><?php echo Text::_('COM_KUNENA_TOOLS_GONE_410') ?></span>
                                    <?php else : ?>
                                        <span class="kd-pill kd-pill--ok"><?php echo Text::sprintf('COM_KUNENA_TOOLS_GONE_REDIRECT', (int) $row['header']) ?></span>
                                        <br><span class="kd-form__hint kd-break"><?php echo $this->escape((string) $row['new_url']) ?></span>
                                    <?php endif ?>
                                </td>
                                <td class="text-end"><?php echo (int) $row['hits'] ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>

            <?php if ($this->canAdmin) : ?>
                <div class="kd-panel kd-form">
                    <div class="kd-form__field">
                        <label for="kd-gone-target"><?php echo Text::_('COM_KUNENA_TOOLS_GONE_TARGET') ?></label>
                        <input type="text" class="form-control" id="kd-gone-target" name="target" maxlength="2048"
                               placeholder="<?php echo $this->escape(Text::_('COM_KUNENA_TOOLS_GONE_TARGET_HINT')) ?>">
                    </div>
                    <div>
                        <button type="submit" class="kd-btn kd-btn--primary" name="task" value="tools.redirectGoneTopics">
                            <span class="fa-solid fa-diamond-turn-right" aria-hidden="true"></span>
                            <?php echo Text::_('COM_KUNENA_TOOLS_GONE_DO_REDIRECT') ?>
                        </button>
                    </div>
                </div>
                <div class="kd-form__actions">
                    <button type="submit" class="kd-btn kd-btn--danger" name="task" value="tools.removeGoneTopics" data-kd-confirm>
                        <span class="fa-solid fa-trash-can" aria-hidden="true"></span>
                        <?php echo Text::_('COM_KUNENA_TOOLS_GONE_REMOVE') ?>
                    </button>
                    <span class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_GONE_REMOVE_HINT') ?></span>
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
