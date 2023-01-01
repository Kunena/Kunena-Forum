<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Icons\KunenaIcons;
use Kunena\Forum\Libraries\Login\KunenaLogin;
use Kunena\Forum\Libraries\Route\KunenaRoute;

?>
    <div class="btn-group ">
        <button class="btn btn-light dropdown-toggle" id="klogin-desktop" type="button" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
            <?php echo KunenaIcons::user(); ?>
            <span class="login-text"><?php echo Text::_('JLOGIN'); ?></span>
        </button>

        <div class="dropdown-menu dropdown-menu-end" id="kdesktop-userdropdown">
            <form id="kdesktop-loginform" action="<?php echo KunenaRoute::current('index.php?option=com_kunena'); ?>" method="post">
                <input type="hidden" name="view" value="user"/>
                <input type="hidden" name="task" value="login"/>
                <?php echo HTMLHelper::_('form.token'); ?>

                <div class="mod-login__username form-group" id="kform-desktop-login-username">
                    <div class="input-group">
                        <input id="kdesktop-username" type="text" name="username" class="form-control" tabindex="1" autocomplete="username" placeholder="<?php echo Text::_('JGLOBAL_USERNAME'); ?>">
                        <label for="kdesktop-username" class="visually-hidden"><?php echo Text::_('JGLOBAL_USERNAME'); ?></label>
                        <span class="input-group-text" data-bs-toggle="tooltip" title="Username">
                        <?php echo KunenaIcons::user(); ?>
                    </span>
                    </div>
                </div>

                <div class="mod-login__username form-group" id="kform-desktop-login-password">
                    <div class="input-group">
                        <input id="klogin-desktop-passwd" type="password" name="password" class="form-control" tabindex="1" autocomplete="current-password" placeholder="<?php echo Text::_('JGLOBAL_PASSWORD'); ?>">
                        <label for="klogin-desktop-passwd" class="visually-hidden"><?php echo Text::_('JGLOBAL_PASSWORD'); ?></label>
                        <span class="input-group-text" data-bs-toggle="tooltip" title="password">
                        <?php echo KunenaIcons::lock(); ?>
                    </span>
                    </div>
                </div>

                <?php $login = KunenaLogin::getInstance(); ?>
                <?php
                if ($login->getTwoFactorMethods() > 1) :
                    ?>
                    <div id="form-login-tfa" class="control-group center">
                        <div class="controls">
                            <div class="input-prepend input-append">
                            <span class="add-on">
                                <?php echo KunenaIcons::star(); ?>
                                <label for="k-lgn-secretkey" class="element-invisible">
                                    <?php echo Text::_('COM_KUNENA_LOGIN_SECRETKEY'); ?>
                                </label>
                          </span>
                                <input id="k-lgn-secretkey" type="text" name="secretkey" class="input-small"
                                       tabindex="3"
                                       size="18" placeholder="<?php echo Text::_('COM_KUNENA_LOGIN_SECRETKEY'); ?>"/>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($this->rememberMe) :
                    ?>
                    <div class="form-group row center" id="kform-login-remember">
                        <div class="controls">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="klogin-desktop-remember"
                                       id="klogin-desktop-remember"
                                       value="1"/>
                                <label class="custom-control-label"
                                       for="klogin-desktop-remember"><?php echo Text::_('JGLOBAL_REMEMBER_ME'); ?></label>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div id="kform-login-desktop-submit" class="control-group center">
                    <p>
                        <button type="submit" tabindex="3" name="submit" class="btn btn-outline-primary">
                            <?php echo Text::_('JLOGIN'); ?>
                        </button>
                    </p>

                    <p>
                        <?php if ($this->resetPasswordUrl) :
                            ?>
                            <a href="<?php echo $this->resetPasswordUrl; ?>">
                                <?php echo Text::_('COM_KUNENA_PROFILEBOX_FORGOT_PASSWORD'); ?>
                            </a>
                            <br/>
                        <?php endif ?>

                        <?php if ($this->remindUsernameUrl) :
                            ?>
                            <a href="<?php echo $this->remindUsernameUrl; ?>">
                                <?php echo Text::_('COM_KUNENA_PROFILEBOX_FORGOT_USERNAME'); ?>
                            </a>
                            <br/>
                        <?php endif ?>

                        <?php if ($this->registrationUrl) :
                            ?>
                            <a href="<?php echo $this->registrationUrl; ?>">
                                <?php echo Text::_('COM_KUNENA_PROFILEBOX_CREATE_ACCOUNT'); ?>
                            </a>
                        <?php endif ?>

                    </p>
                </div>
            </form>
            <?php echo $this->subLayout('Widget/Module')->set('position', 'kunena_login'); ?>
        </div>
    </div>
