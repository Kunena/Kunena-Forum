<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Icons\KunenaIcons;
use Kunena\Forum\Libraries\Login\KunenaLogin;
use Kunena\Forum\Libraries\Route\KunenaRoute;

?>
    <div class="btn-group">
        <button class="btn btn-light dropdown-toggle" id="klogin-mobile" type="button" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
			<?php echo KunenaIcons::user(); ?>
        </button>
        <div class="dropdown-menu dropdown-menu-end" id="kmobile-userdropdown">
            <form id="kmobile-loginform" action="<?php echo KunenaRoute::current('index.php?option=com_kunena'); ?>" method="post">
                <input type="hidden" name="view" value="user"/>
                <input type="hidden" name="task" value="login"/>
				<?php echo HTMLHelper::_('form.token'); ?>

                <div class="form-group" id="kmobile-form-login-username">
                    <div class="input-group">
                        <div class="input-group-prepend">
							<span class="input-group-text">
								<?php echo KunenaIcons::user(); ?>
								<label for="kmobile-username" class="element-invisible">
									<?php echo Text::_('JGLOBAL_USERNAME'); ?>
								</label>
							</span>
                            <input class="form-control" id="kmobile-username" name="username" tabindex="1"
                                   autocomplete="username" placeholder="<?php echo Text::_('JGLOBAL_USERNAME'); ?>"
                                   type="text">
                        </div>
                    </div>
                </div>

                <div class="form-group" id="kmobile-form-login-password">
                    <div class="input-group">
                        <div class="input-group-prepend">
							<span class="input-group-text">
								<?php echo KunenaIcons::lock(); ?>
								<label for="kmobile-passwd" class="element-invisible">
									<?php echo Text::_('JGLOBAL_PASSWORD'); ?>
								</label>
							</span>
                            <input class="form-control" id="kmobile-passwd" name="password" tabindex="2"
                                   autocomplete="current-password"
                                   placeholder="<?php echo Text::_('JGLOBAL_PASSWORD'); ?>" type="password">
                        </div>
                    </div>
                </div>

				<?php $login = KunenaLogin::getInstance(); ?>
				<?php
				if ($login->getTwoFactorMethods() > 1)
					:
					?>
                    <div id="kmobile-form-login-tfa" class="control-group center">
                        <div class="controls">
                            <div class="input-prepend input-append">
							<span class="add-on">
								<?php echo KunenaIcons::star(); ?>
								<label for="kmobile-secretkey" class="element-invisible">
									<?php echo Text::_('COM_KUNENA_LOGIN_SECRETKEY'); ?>
								</label>
						  </span>
                                <input id="kmobile-secretkey" type="text" name="secretkey" class="input-small"
                                       tabindex="3"
                                       size="18" placeholder="<?php echo Text::_('COM_KUNENA_LOGIN_SECRETKEY'); ?>"/>
                            </div>
                        </div>
                    </div>
				<?php endif; ?>

				<?php if ($this->rememberMe) : ?>
                    <div class="form-group row center" id="kform-login-remember">
                        <div class="controls">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="kmobile-remember"
                                       id="kmobile-remember"
                                       value="1"/>
                                <label class="custom-control-label"
                                       for="kmobile-remember"><?php echo Text::_('JGLOBAL_REMEMBER_ME'); ?></label>
                            </div>
                        </div>
                    </div>
				<?php endif; ?>

                <div id="kmobile-form-login-submit" class="control-group center">
                    <p>
                        <button type="submit" tabindex="3" name="submit" class="btn btn-outline-primary">
							<?php echo Text::_('JLOGIN'); ?>
                        </button>
                    </p>

                    <p>
						<?php if ($this->resetPasswordUrl)
							:
							?>
                            <a href="<?php echo $this->resetPasswordUrl; ?>">
								<?php echo Text::_('COM_KUNENA_PROFILEBOX_FORGOT_PASSWORD'); ?>
                            </a>
                            <br/>
						<?php endif ?>

						<?php if ($this->remindUsernameUrl)
							:
							?>
                            <a href="<?php echo $this->remindUsernameUrl; ?>">
								<?php echo Text::_('COM_KUNENA_PROFILEBOX_FORGOT_USERNAME'); ?>
                            </a>
                            <br/>
						<?php endif ?>

						<?php if ($this->registrationUrl)
							:
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
