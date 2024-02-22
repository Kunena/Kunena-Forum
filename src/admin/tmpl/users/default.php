<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Users
 *
 * @copyright       Copyright (C) 2008 - 2024 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Version\KunenaVersion;

/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('table.columns')
    ->useScript('multiselect');

$app       = Factory::getApplication();
$user      = $this->getCurrentUser();
$userId    = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$language  = $app->getLanguage();
$language->load('com_users');
?>
<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=users'); ?>" method="post" id="adminForm" name="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <?php
                // Search tools bar
                echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]);
                ?>
                <table class="table itemList" id="userList">
                    <thead>
                        <tr>
                            <td class="w-1 text-center">
                                <?php echo HTMLHelper::_('grid.checkall'); ?>
                            </td>
                            <th scope="col" style="min-width:100px">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_USRL_USERNAME', 'username', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" style="min-width:100px">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_GEN_EMAIL', 'email', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-5 d-none d-md-table-cell">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_GEN_IP', 'ip', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-10 d-none d-md-table-cell">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_A_RANKS', 'rank', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-5 d-none d-md-table-cell">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_GEN_SIGNATURE', 'signature', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-5 d-none d-md-table-cell">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_USRL_ENABLED', 'enabled', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-5 d-none d-md-table-cell">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_USRL_BANNED', 'banned', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-5 d-none d-md-table-cell">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_VIEW_MODERATOR', 'moderator', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-1 text-center d-none d-lg-table-cell">
                                <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i       = 0;
                        $img_no  = '<span class="icon-unpublish" aria-hidden="true"></span>';
                        $img_yes = '<span class="icon-publish" aria-hidden="true"></span>';

                        foreach ($this->users as $user) :
                            $userBlockTask    = $user->isBlocked() ? 'users.unblock' : 'users.block';
                            $userBannedTask   = $user->isBanned() ? 'users.unban' : 'users.ban';
                        ?>
                            <tr>
                                <td class="text-center">
                                    <?php echo HTMLHelper::_('grid.id', $i, intval($user->userid)) ?>
                                </td>
                                <th>
                                    <div class="editlinktip d-flex kwho-<?php echo $user->getType(0, true); ?>" title="<?php echo $this->escape($user->username); ?> ">
                                        <div class="avatar me-2 align-self-center">
                                            <?php echo $user->getAvatarImage(KunenaFactory::getTemplate()->params->get('avatarType'), 'thumb'); ?>
                                        </div>
                                        <div class="user">
                                            <div class="username">
                                                <a href="<?php echo Route::_('index.php?option=com_kunena&view=user&layout=edit&userid=' . (int) $user->userid); ?>" title="<?php echo Text::sprintf('COM_USERS_EDIT_USER', $this->escape($user->name)); ?>">
                                                    <?php echo $this->escape($user->username); ?>
                                                </a>
                                            </div>
                                            <div class="username">
                                                <small>(<?php echo Text::sprintf('COM_KUNENA_LABEL_USER_NAME', $this->escape($user->name)); ?>)</small>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                                <td>
                                    <a href="<?php echo Route::_('index.php?option=com_kunena&view=user&layout=edit&userid=' . (int) $user->userid); ?>" title="<?php echo Text::sprintf('COM_USERS_EDIT_USER', $this->escape($user->name)); ?>">
                                        <?php echo $this->escape($user->email); ?></a>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <?php echo $this->escape($user->ip); ?>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <?php echo $this->escape($user->getRank(0, 'title')); ?>
                                </td>
                                <td class="d-none d-md-table-cell text-center">
                                    <a class="tbody-icon <?php echo $user->signature ? 'active' : ''; ?>" href="<?php echo Route::_('index.php?option=com_kunena&view=user&layout=edit&userid=' . (int) $user->userid); ?>" title="<?php echo Text::sprintf('COM_USERS_EDIT_USER', $this->escape($user->name)); ?>">
                                        <?php echo $user->signature ? $img_yes : $img_no; ?>
                                    </a>
                                </td>
                                <td class="d-none d-md-table-cell text-center">
                                    <a href="#" class="js-grid-item-action tbody-icon <?php echo !$user->isBlocked() ? 'active' : ''; ?>" data-item-id="cb<?php echo $i; ?>" data-item-task="<?php echo $userBlockTask ?>" data-item-form-id="" aria-labelledby="cbblock<?php echo $i; ?>-desc"><?php echo !$user->isBlocked() ? $img_yes : $img_no; ?></a>
                                    <div role="tooltip" id="cbblock<?php echo $i; ?>-desc"><?php echo $user->isBlocked() ? Text::_('COM_KUNENA_TASK_UNBLOCK_USER') : Text::_('COM_KUNENA_TASK_BLOCK_USER'); ?></div>
                                </td>
                                <td class="d-none d-md-table-cell text-center">
                                    <a href="#" class="js-grid-item-action tbody-icon <?php echo $user->isBanned() ? 'active' : ''; ?>" data-item-id="cb<?php echo $i; ?>" data-item-task="<?php echo $userBannedTask ?>" data-item-form-id="" aria-labelledby="cbban<?php echo $i; ?>-desc"><?php echo $user->isBanned() ? $img_yes : $img_no; ?></a>
                                    <div role="tooltip" id="cbban<?php echo $i; ?>-desc"><?php echo $user->isBanned() ? Text::_('COM_KUNENA_TASK_UNBAN_USER') : Text::_('COM_KUNENA_TASK_BAN_USER'); ?></div>
                                </td>
                                <td class="d-none d-md-table-cell text-center">
                                    <div class="tbody-icon <?php echo $user->moderator ? 'active' : ''; ?>">
                                        <?php echo $user->moderator ? $img_yes : $img_no; ?>
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <?php echo (int) $this->escape($user->userid); ?>
                                </td>
                            </tr>
                            <?php $i++;; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php // load the pagination. 
                ?>
                <?php echo $this->pagination->getListFooter(); ?>

                <template id="joomla-dialog-subscribecatsusers"><?php echo $this->loadTemplate('subscribecatsusers'); ?></template>
                <template id="joomla-dialog-moderators"><?php echo $this->loadTemplate('moderators'); ?></template>

                <input type="hidden" name="task" value="">
                <input type="hidden" name="boxchecked" value="0">
                <?php echo HTMLHelper::_('form.token'); ?>
            </div>
        </div>
    </div>
</form>
<div class="mt-3 text-center small">
    <?php echo KunenaVersion::getLongVersionHTML(); ?>
</div>