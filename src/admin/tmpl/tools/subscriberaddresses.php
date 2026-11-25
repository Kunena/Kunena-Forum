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
use Kunena\Forum\Libraries\Tools\KunenaSubscriberAddresses;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$items = $this->tool['items'];
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero<?php echo $items ? ' is-problem' : '' ?>">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-envelope-circle-check" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title">
                    <?php echo $items ? Text::plural('COM_KUNENA_TOOLS_ADDRESSES_N_FOUND', \count($items)) : Text::_('COM_KUNENA_TOOLS_ADDRESSES_NONE') ?>
                </h2>
                <p class="kd-hero__meta"><?php echo Text::sprintf('COM_KUNENA_TOOLS_ADDRESSES_INTRO', (int) $this->tool['subscribers'], (int) $this->tool['checked']) ?></p>
            </div>
        </div>
    </section>

    <?php if ($this->tool['checked'] >= KunenaSubscriberAddresses::DOMAIN_LIMIT) : ?>
        <div class="alert alert-info"><?php echo Text::sprintf('COM_KUNENA_TOOLS_ADDRESSES_LIMIT', KunenaSubscriberAddresses::DOMAIN_LIMIT) ?></div>
    <?php endif ?>

    <?php if ($items) : ?>
        <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post">
            <input type="hidden" name="task" value="tools.pauseSubscriptions">
            <?php echo HTMLHelper::_('form.token') ?>

            <div class="kd-scroll">
                <table class="table">
                    <thead>
                        <tr>
                            <td class="w-1 text-center">
                                <input type="checkbox" class="form-check-input" data-kd-all="kd-address"
                                       aria-label="<?php echo $this->escape(Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_SELECT_ALL')) ?>">
                            </td>
                            <th scope="col" class="w-20"><?php echo Text::_('COM_KUNENA_TOOLS_PROFILESPAM_USER') ?></th>
                            <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_ADDRESSES_ADDRESS') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $row) :
                            $id = (int) $row['id']; ?>
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input kd-address" name="ids[]" value="<?php echo $id ?>" id="kd-address-<?php echo $id ?>">
                                </td>
                                <th scope="row">
                                    <label for="kd-address-<?php echo $id ?>"><?php echo $this->escape((string) $row['username']) ?></label>
                                    <?php if ((int) $row['block']) : ?>
                                        <span class="kd-pill"><?php echo Text::_('COM_KUNENA_TOOLS_PROFILESPAM_BLOCKED') ?></span>
                                    <?php endif ?>
                                    <br><a class="kd-form__hint" href="<?php echo Route::_('index.php?option=com_users&task=user.edit&id=' . $id) ?>"><?php echo Text::_('COM_KUNENA_TOOLS_ADDRESSES_EDIT_USER') ?> ›</a>
                                </th>
                                <td>
                                    <code class="kd-break"><?php echo $this->escape((string) $row['email']) ?></code>
                                    <br><span class="kd-pill kd-pill--warn"><?php echo Text::sprintf('COM_KUNENA_TOOLS_ADDRESSES_' . strtoupper($row['problem']), $this->escape((string) $row['hint'])) ?></span>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>

            <?php if ($this->canAdmin) : ?>
                <div class="kd-form__actions">
                    <button type="submit" class="kd-btn kd-btn--danger" data-kd-confirm>
                        <span class="fa-solid fa-bell-slash" aria-hidden="true"></span>
                        <?php echo Text::_('COM_KUNENA_TOOLS_ADDRESSES_PAUSE') ?>
                    </button>
                    <span class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_ADDRESSES_PAUSE_HINT') ?></span>
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
