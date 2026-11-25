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
use Kunena\Forum\Libraries\Tools\KunenaInactiveUsers;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$criteria = $this->tool['criteria'];
$found    = $this->tool['found'];
$date     = static fn ($value) => $value && !str_starts_with((string) $value, '0000') && !str_starts_with((string) $value, '1000')
    ? HTMLHelper::_('date', $value, Text::_('DATE_FORMAT_LC4')) : Text::_('COM_KUNENA_TOOLS_NEVER');
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero<?php echo $found['total'] ? ' is-problem' : '' ?>">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-user-clock" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title"><?php echo Text::plural('COM_KUNENA_TOOLS_INACTIVE_N_FOUND', $found['total']) ?></h2>
                <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_TOOLS_INACTIVE_INTRO') ?></p>
            </div>
        </div>
    </section>

    <form action="<?php echo Route::_('index.php') ?>" method="get" class="kd-panel">
        <input type="hidden" name="option" value="com_kunena">
        <input type="hidden" name="view" value="tools">
        <input type="hidden" name="layout" value="inactiveusers">
        <div class="kd-form">
            <fieldset class="kd-choices">
                <legend><?php echo Text::_('COM_KUNENA_TOOLS_INACTIVE_KIND') ?></legend>
                <?php foreach ([KunenaInactiveUsers::NEVER_POSTED, KunenaInactiveUsers::NOT_SEEN] as $kind) : ?>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="kind" id="kd-inactive-<?php echo $kind ?>" value="<?php echo $kind ?>"
                            <?php echo $criteria['kind'] === $kind ? 'checked' : '' ?>>
                        <label class="form-check-label" for="kd-inactive-<?php echo $kind ?>"><?php echo Text::_('COM_KUNENA_TOOLS_INACTIVE_' . strtoupper($kind)) ?></label>
                    </div>
                <?php endforeach ?>
            </fieldset>
            <div class="kd-form__field">
                <label for="kd-inactive-months"><?php echo Text::_('COM_KUNENA_TOOLS_INACTIVE_OLDER') ?></label>
                <select class="form-select" id="kd-inactive-months" name="months">
                    <?php foreach ([3, 6, 12, 24, 36, 60] as $months) : ?>
                        <option value="<?php echo $months ?>"<?php echo $months === $criteria['months'] ? ' selected' : '' ?>>
                            <?php echo Text::plural('COM_KUNENA_TOOLS_INACTIVE_N_MONTHS', $months) ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>
            <div>
                <button type="submit" class="kd-btn kd-btn--ghost">
                    <span class="fa-solid fa-magnifying-glass" aria-hidden="true"></span>
                    <?php echo Text::_('JSEARCH_FILTER_SUBMIT') ?>
                </button>
            </div>
        </div>
    </form>

    <?php if ($found['items']) : ?>
        <div class="kd-scroll">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_USER_NAME') ?></th>
                        <th scope="col" class="w-15"><?php echo Text::_('COM_KUNENA_TOOLS_USER_REGISTERED') ?></th>
                        <th scope="col" class="w-15"><?php echo Text::_('COM_KUNENA_TOOLS_USER_LASTVISIT') ?></th>
                        <th scope="col" class="w-10 text-end"><?php echo Text::_('COM_KUNENA_TOOLS_INACTIVE_POSTS') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($found['items'] as $id => $user) : ?>
                        <tr>
                            <th scope="row">
                                <a href="<?php echo Route::_('index.php?option=com_kunena&view=tools&layout=usercleanup&userid=' . (int) $id) ?>"><?php echo $this->escape($user['username']) ?></a>
                                <?php if ((int) $user['block']) : ?>
                                    <span class="kd-pill kd-pill--warn"><?php echo Text::_('COM_KUNENA_TOOLS_USER_BLOCKED') ?></span>
                                <?php endif ?>
                                <br><span class="kd-form__hint"><?php echo $this->escape($user['email']) ?></span>
                            </th>
                            <td><?php echo $date($user['registerDate']) ?></td>
                            <td><?php echo $date($user['lastvisitDate']) ?></td>
                            <td class="text-end"><?php echo (int) $user['posts'] ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
        <?php if ($found['total'] > \count($found['items'])) : ?>
            <p class="kd-form__hint"><?php echo Text::sprintf('COM_KUNENA_TOOLS_N_MORE', $found['total'] - \count($found['items'])) ?></p>
        <?php endif ?>

        <?php if ($this->canAdmin) : ?>
            <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post" class="kd-panel">
                <input type="hidden" name="task" value="tools.inactiveUsers">
                <input type="hidden" name="kind" value="<?php echo $criteria['kind'] ?>">
                <input type="hidden" name="months" value="<?php echo (int) $criteria['months'] ?>">
                <?php echo HTMLHelper::_('form.token') ?>
                <fieldset class="kd-choices">
                    <legend><?php echo Text::plural('COM_KUNENA_TOOLS_INACTIVE_ACTION_N', \count($found['items'])) ?></legend>
                    <?php foreach (KunenaInactiveUsers::ACTIONS as $action) : ?>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="user_action" id="kd-inactive-action-<?php echo $action ?>" value="<?php echo $action ?>" required>
                            <label class="form-check-label" for="kd-inactive-action-<?php echo $action ?>"><?php echo Text::_('COM_KUNENA_TOOLS_INACTIVE_ACTION_' . strtoupper($action)) ?></label>
                        </div>
                    <?php endforeach ?>
                </fieldset>
                <p class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_INACTIVE_STAFF') ?></p>
                <div class="kd-form__actions">
                    <button type="submit" class="kd-btn kd-btn--danger" data-kd-confirm>
                        <span class="fa-solid fa-user-clock" aria-hidden="true"></span>
                        <?php echo Text::_('COM_KUNENA_TOOLS_USERCLEANUP_APPLY') ?>
                    </button>
                </div>
            </form>
        <?php else : ?>
            <div class="alert alert-info"><?php echo Text::_('COM_KUNENA_TOOLS_ADMIN_ONLY') ?></div>
        <?php endif ?>
    <?php endif ?>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
