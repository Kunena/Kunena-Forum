<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Widget
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;
?>
<ul class="nav pull-right">
	<li class="dropdown mobile-user">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown" id="klogin">
			<i class="icon-large icon-user"></i> <b class="caret"></b>
		</a>

		<div class="dropdown-menu" id="userdropdown">
			<form action="<?php echo JRoute::_('index.php?option=com_kunena'); ?>" method="post" class="form-inline">
				<input type="hidden" name="view" value="user" />
				<input type="hidden" name="task" value="login" />
				<?php echo JHtml::_('form.token'); ?>

				<div id="form-login-username" class="control-group center">
					<div class="controls">
						<div class="input-prepend input-append">
							<span class="add-on">
								<i class="icon-user tip" title="<?php echo JText::_('JGLOBAL_USERNAME'); ?>"></i>
								<label for="login-username" class="element-invisible">
									<?php echo JText::_('JGLOBAL_USERNAME'); ?>
								</label>
							</span>
							<input id="login-username" type="text" name="username" class="input-small" tabindex="1"
							       size="18" placeholder="<?php echo JText::_('JGLOBAL_USERNAME'); ?>" />
						</div>
					</div>
				</div>

				<div id="form-login-password" class="control-group center">
					<div class="controls">
						<div class="input-prepend input-append">
							<span class="add-on">
								<i class="icon-lock tip" title="<?php echo JText::_('JGLOBAL_PASSWORD'); ?>"></i>
								<label for="login-passwd" class="element-invisible">
									<?php echo JText::_('JGLOBAL_PASSWORD'); ?>
								</label>
							</span>
							<input id="login-passwd" type="password" name="password" class="input-small" tabindex="2"
							       size="18" placeholder="<?php echo JText::_('JGLOBAL_PASSWORD'); ?>" />
						</div>
					</div>
				</div>

				<?php $login = KunenaLogin::getInstance(); ?>
				<?php if ($login->getTwoFactorMethods() > 1) : ?>
				<div id="form-login-tfa" class="control-group center">
					<div class="controls">
						<div class="input-prepend input-append">
							<span class="add-on">
								<i class="icon-star tip" title="<?php echo JText::_('COM_KUNENA_LOGIN_SECRETKEY'); ?>"></i>
								<label for="k-lgn-secretkey" class="element-invisible">
									<?php echo JText::_('COM_KUNENA_LOGIN_SECRETKEY'); ?>
								</label>
						  </span>
							<input id="k-lgn-secretkey" type="text" name="secretkey" class="input-small" tabindex="3"
										size="18" placeholder="<?php echo JText::_('COM_KUNENA_LOGIN_SECRETKEY'); ?>" />
						</div>
					</div>
				</div>
				<?php endif; ?>

				<?php if ($this->rememberMe) : ?>
				<div id="form-login-remember" class="control-group center">
					<div class="controls">
						<div class="input-prepend input-append">
							<div class="add-on">
								<input id="login-remember" type="checkbox" name="remember" class="inputbox" value="yes" />
								<label for="login-remember" class="control-label">
									<?php echo JText::_('JGLOBAL_REMEMBER_ME'); ?>
								</label>
							</div>
						</div>
					</div>
				</div>
				<?php endif; ?>

				<div id="form-login-submit" class="control-group center">
					<p>
						<button type="submit" tabindex="3" name="submit" class="btn btn-primary btn">
							<?php echo JText::_('JLOGIN'); ?>
						</button>
					</p>

					<p>
						<?php if ($this->resetPasswordUrl) : ?>
						<a href="<?php echo $this->resetPasswordUrl; ?>" rel="nofollow">
							<?php echo JText::_('COM_KUNENA_PROFILEBOX_FORGOT_PASSWORD'); ?>
						</a>
						<br />
						<?php endif ?>

						<?php if ($this->remindUsernameUrl) : ?>
						<a href="<?php echo $this->remindUsernameUrl; ?>" rel="nofollow">
							<?php echo JText::_('COM_KUNENA_PROFILEBOX_FORGOT_USERNAME'); ?>
						</a>
						<br />
						<?php endif ?>

						<?php if ($this->registrationUrl) : ?>
						<a href="<?php echo $this->registrationUrl; ?>" rel="nofollow">
							<?php echo JText::_('COM_KUNENA_PROFILEBOX_CREATE_ACCOUNT'); ?>
						</a>
						<?php endif ?>

					</p>
				</div>
			</form>
			<?php echo $this->subLayout('Widget/Module')->set('position', 'kunena_login'); ?>
		</div>
	</li>
</ul>
