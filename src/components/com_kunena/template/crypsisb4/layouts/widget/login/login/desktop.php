<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsisb4
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

?>
<ul class="nav float-right">
	<li class="dropdown mobile-user">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown" id="klogin-desktop">
			<?php echo KunenaIcons::user(); ?>
			<span class="login-text"><?php echo Text::_('JLOGIN'); ?></span>
			<b class="caret"></b>
		</a>

		<div class="dropdown-menu dropdown-menu-right" id="userdropdown">
			<form action="<?php echo Route::_('index.php?option=com_kunena'); ?>" method="post" class="px-4 py-3">
				<input type="hidden" name="view" value="user"/>
				<input type="hidden" name="task" value="login"/>
				<?php echo HTMLHelper::_('form.token'); ?>

				<div class="form-group">
					<label for="kform-desktop-login-username"><?php echo KunenaIcons::user(); ?> <?php echo Text::_('JGLOBAL_USERNAME'); ?></label>
					<input type="text" name="username" tabindex="1" autocomplete="username" class="form-control" id="kform-desktop-login-username" placeholder="<?php echo Text::_('JGLOBAL_USERNAME'); ?>">
				</div>
				<div class="form-group">
					<label for="kform-desktop-login-password"><?php echo KunenaIcons::lock(); ?> <?php echo Text::_('JGLOBAL_PASSWORD'); ?></label>
					<input type="password" name="password" tabindex="2" autocomplete="current-password" class="form-control" id="kform-desktop-login-password" placeholder="<?php echo Text::_('JGLOBAL_PASSWORD'); ?>">
				</div>

				<?php $login = KunenaLogin::getInstance(); ?>
				<?php
				if ($login->getTwoFactorMethods() > 1)
					:
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

				<?php if ($this->rememberMe) : ?>
				<div class="form-check">
					<input type="checkbox" class="form-check-input" id="dropdownCheck" name="klogin-desktop-remember" value="1">
						<label class="form-check-label" for="dropdownCheck">
						<?php echo Text::_('JGLOBAL_REMEMBER_ME'); ?>
					</label>
				</div>
				<?php endif; ?>

				<div id="kform-login-desktop-submit" class="control-group center">
					<p>
						<button type="submit" tabindex="3" name="submit" class="btn btn-primary mb-2">
							<?php echo Text::_('JLOGIN'); ?>
						</button>
					</p>

					<div class="dropdown-divider"></div>

					<p>
						<?php if ($this->resetPasswordUrl)
							:
							?>
							<a class="dropdown-item" href="<?php echo $this->resetPasswordUrl; ?>">
								<?php echo Text::_('COM_KUNENA_PROFILEBOX_FORGOT_PASSWORD'); ?>
							</a>
							<br/>
						<?php endif ?>

						<?php if ($this->remindUsernameUrl)
							:
							?>
							<a class="dropdown-item" href="<?php echo $this->remindUsernameUrl; ?>">
								<?php echo Text::_('COM_KUNENA_PROFILEBOX_FORGOT_USERNAME'); ?>
							</a>
							<br/>
						<?php endif ?>

						<?php if ($this->registrationUrl)
							:
							?>
							<a class="dropdown-item" href="<?php echo $this->registrationUrl; ?>">
								<?php echo Text::_('COM_KUNENA_PROFILEBOX_CREATE_ACCOUNT'); ?>
							</a>
						<?php endif ?>

					</p>
				</div>
			</form>
			<?php echo $this->subLayout('Widget/Module')->set('position', 'kunena_login'); ?>
		</div>
	</li>
</ul>
