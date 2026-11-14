<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Kunena\Forum\Libraries\Icons\KunenaIcons;
use Kunena\Forum\Libraries\Login\KunenaLogin;

$login       = KunenaLogin::getInstance();
$showHeading = isset($this->showHeading) ? $this->showHeading : true;
?>
<div class="kloginform">
    <?php if ($showHeading) :
        ?>
        <h1 class="text-center"><?php echo Text::_('COM_KUNENA_LOGIN_FORUM'); ?></h1>
    <?php endif; ?>
    <div class="row justify-content-center">
        <div class="col-12 col-sm-8 col-md-6 col-lg-4">
            <form action="<?php echo Route::_('index.php?option=com_kunena'); ?>" method="post">
                <input type="hidden" name="view" value="user"/>
                <input type="hidden" name="task" value="login"/>
                <?php echo HTMLHelper::_('form.token'); ?>

                <div id="kform-login-username" class="mb-3">
                    <label for="klogin-username-full" class="visually-hidden"><?php echo Text::_('JGLOBAL_USERNAME'); ?></label>
                    <div class="input-group">
                        <span class="input-group-text"><?php echo KunenaIcons::user(); ?></span>
                        <input id="klogin-username-full" type="text" name="username" class="form-control" tabindex="1"
                               autocomplete="username" placeholder="<?php echo Text::_('JGLOBAL_USERNAME'); ?>"/>
                    </div>
                </div>

                <div id="kform-login-password" class="mb-3">
                    <label for="klogin-passwd-full" class="visually-hidden"><?php echo Text::_('JGLOBAL_PASSWORD'); ?></label>
                    <div class="input-group">
                        <span class="input-group-text"><?php echo KunenaIcons::lock(); ?></span>
                        <input id="klogin-passwd-full" type="password" name="password" class="form-control" tabindex="2"
                               autocomplete="current-password" placeholder="<?php echo Text::_('JGLOBAL_PASSWORD'); ?>"/>
                    </div>
                </div>

                <div id="kform-login-submit" class="text-center">
                    <button type="submit" tabindex="3" name="submit" class="btn btn-outline-primary mb-3">
                        <?php echo Text::_('JLOGIN'); ?>
                    </button>

                    <?php if ($login->getResetUrl()) :
                        ?>
                        <div>
                            <a href="<?php echo $login->getResetUrl(); ?>" rel="nofollow">
                                <?php echo Text::_('COM_KUNENA_PROFILEBOX_FORGOT_PASSWORD'); ?>
                            </a>
                        </div>
                    <?php endif ?>

                    <?php if ($login->getRemindUrl()) :
                        ?>
                        <div>
                            <a href="<?php echo $login->getRemindUrl(); ?>" rel="nofollow">
                                <?php echo Text::_('COM_KUNENA_PROFILEBOX_FORGOT_USERNAME'); ?>
                            </a>
                        </div>
                    <?php endif ?>

                    <?php if ($login->getRegistrationUrl()) :
                        ?>
                        <div>
                            <a href="<?php echo $login->getRegistrationUrl(); ?>" rel="nofollow">
                                <?php echo Text::_('COM_KUNENA_PROFILEBOX_CREATE_ACCOUNT'); ?>
                            </a>
                        </div>
                    <?php endif ?>
                </div>
            </form>
        </div>
    </div>
</div>
