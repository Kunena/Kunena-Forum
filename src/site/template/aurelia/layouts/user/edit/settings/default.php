<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.User
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Forum\Libraries\User\KunenaUserIgnore;

Text::script('COM_KUNENA_CLEARED');
?>
<h3>
    <?php echo $this->headerText; ?>
</h3>

<table class="table table-bordered table-striped table-hover">
    <tbody>
        <?php foreach ($this->settings as $field) :
        ?>
            <tr>
                <td class="col-md-7">
                    <?php echo $field->label; ?>
                </td>
                <td class="col-md-5">
                    <?php echo $field->field; ?>
                </td>
            </tr>
        <?php endforeach ?>
        <tr>
            <td class="col-md-3"><?php echo Text::_('COM_KUNENA_USER_SETTINGS_CLEAR'); ?></td>
            <td>
                <button id="clear-kunena-storage-button" type="button" class="btn btn-outline-primary border btn-small"
                    data-loading-text="Loading..."><?php echo Text::_('COM_KUNENA_USER_SETTINGS_CLEAR'); ?></button>
            </td>
        </tr>
        <?php
        // The users whom the user ignores, with a link to stop ignoring them.
        if ($this->profile->isMyself() && KunenaUserIgnore::isEnabled()) :
            $ignoredUsers = KunenaUserHelper::loadUsers(
                KunenaUserIgnore::getIgnored($this->profile->userid)
            );
            ?>
            <tr>
                <td class="col-md-7">
                    <?php echo Text::_('COM_KUNENA_IGNORE_LIST'); ?><br/>
                    <small class="text-muted"><?php echo Text::_('COM_KUNENA_IGNORE_LIST_DESC'); ?></small>
                </td>
                <td class="col-md-5">
                    <?php if (!$ignoredUsers) : ?>
                        <?php echo Text::_('COM_KUNENA_IGNORE_LIST_EMPTY'); ?>
                    <?php else : ?>
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($ignoredUsers as $ignoredUser) : ?>
                                <li class="mb-1">
                                    <?php echo $ignoredUser->getLink(); ?>
                                    <a class="btn btn-outline-secondary btn-sm ms-2" rel="nofollow"
                                       href="<?php echo Route::_('index.php?option=com_kunena&view=user&task=unignore&userid=' . (int) $ignoredUser->userid . '&' . Session::getFormToken() . '=1'); ?>">
                                        <?php echo Text::_('COM_KUNENA_UNIGNORE'); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endif; ?>
        <?php
        // Everybody can ask to delete his own account: the privacy request of Joomla (request type "Remove my information").
        if ($this->profile->isMyself() && ComponentHelper::isEnabled('com_privacy')) :
            ?>
            <tr>
                <td class="col-md-7"><?php echo Text::_('COM_KUNENA_USER_REQUEST_DELETION_DESC'); ?></td>
                <td class="col-md-5">
                    <a class="btn btn-outline-danger border btn-small" rel="nofollow"
                       href="<?php echo Route::_('index.php?option=com_privacy&view=request'); ?>">
                        <?php echo Text::_('COM_KUNENA_USER_REQUEST_DELETION'); ?>
                    </a>
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>