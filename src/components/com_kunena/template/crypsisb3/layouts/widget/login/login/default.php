<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Widget
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;
?>
<ul class="nav pull-right">
	<li class="dropdown mobile-user">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" id="klogin">
			<?php echo KunenaIcons::user();?>
			<span class="login-text"><?php echo JText::_('JLOGIN');?> <span class="caret"></span></span>
		</a>
		<ul class="dropdown-menu dropdown-menu-right" id="userdropdownlogin" role="menu">
			<form action="<?php echo JRoute::_('index.php?option=com_kunena'); ?>" method="post" class="form-inline form-signin">
				<input type="hidden" name="view" value="user" />
				<input type="hidden" name="task" value="login" />
				<?php echo JHtml::_('form.token'); ?>

				<div class="center">
					<a href="#" class="thumbnail">
					<?php echo KunenaIcons::members(); ?>
				 	</a>
				</div>

				<div class="form-group center">
					<div class="input-group">
						<span class="input-group-addon"><?php echo KunenaIcons::user();?></span>
						<input id="login-username" type="text" name="username" class="form-control" tabindex="1" size="18" placeholder="<?php echo JText::_('JGLOBAL_USERNAME'); ?>" />
					</div>

					<div class="input-group">
						<span class="input-group-addon"><?php echo KunenaIcons::lock();?></span>
						<input id="login-passwd" type="password" name="password" class="form-control" tabindex="2" size="18" placeholder="<?php echo JText::_('JGLOBAL_PASSWORD'); ?>" required/>
					</div>

					<?php $login = KunenaLogin::getInstance(); ?>
					<?php if ($login->getTwoFactorMethods() > 1) : ?>


					<div class="input-group">
						<span class="input-group-addon"><?php echo KunenaIcons::star();?></span>
						<input id="k-lgn-secretkey" type="text" name="secretkey" class="form-control" tabindex="3" size="18" placeholder="<?php echo JText::_('COM_KUNENA_LOGIN_SECRETKEY'); ?>" />
					</div>
					<?php endif; ?>
				</div>

				<div class="center">
					<p>
						<div id="remember" class="checkbox">
							<label>
								<input id="login-remember" type="checkbox" name="remember" value="yes" />
								<?php echo JText::_('JGLOBAL_REMEMBER_ME'); ?>
							</label>
						</div>
					</p>
					<p>
						<button class="btn btn-primary" type="submit"><?php echo JText::_('JLOGIN'); ?></button>
					</p>
					<ul class="list-unstyled">

						<?php if ($this->registrationUrl) : ?>
						<li>
							<a href="<?php echo $this->registrationUrl; ?>" rel="nofollow"><?php echo JText::_('COM_KUNENA_PROFILEBOX_CREATE_ACCOUNT'); ?></a>
						</li>
						<?php endif ?>

						<?php if ($this->resetPasswordUrl) : ?>
						<li>
							<a href="<?php echo $this->resetPasswordUrl; ?>" rel="nofollow"><?php echo JText::_('COM_KUNENA_PROFILEBOX_FORGOT_PASSWORD'); ?></a>
						</li>
						<?php endif ?>

						<?php if ($this->remindUsernameUrl) : ?>
						<li>
							<a href="<?php echo $this->remindUsernameUrl; ?>" rel="nofollow"><?php echo JText::_('COM_KUNENA_PROFILEBOX_FORGOT_USERNAME'); ?></a>
						</li>
						<?php endif ?>
					</ul>
				</div>

			</form>
			<?php echo $this->subLayout('Widget/Module')->set('position', 'kunena_login'); ?>
		</ul>
	</li>
</ul>
