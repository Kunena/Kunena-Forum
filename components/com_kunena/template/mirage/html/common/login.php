<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<?php if ($this->login) : ?>
<div class="login dropdown-menu">
	<ul class="list-unstyled login-guest">
		<li>
			<div class="login-form">
				<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user') ?>" method="post" class="form login">
					<input type="hidden" name="task" value="login" />
					<?php echo $this->displayFormToken() ?>

					<fieldset>
						<legend class="legend-hide"><?php echo JText::_('COM_KUNENA_PROFILEBOX_LOGIN'); ?></legend>
						<input class="box-width inputbox form-vertical form-field_simple" type="text" name="username" placeholder="<?php echo JText::_('COM_KUNENA_LOGIN_USERNAME') ?>"/>
						<input class="box-width inputbox form-vertical form-field_simple" type="password" name="password" placeholder="<?php echo JText::_('COM_KUNENA_LOGIN_PASSWORD') ?>" />
						<?php if($this->remember) : ?>
						<input id="klogin-remember" type="checkbox" name="remember" value="yes" />
						<label for="klogin-remember"><?php echo JText::_('COM_KUNENA_LOGIN_REMEMBER_ME'); ?></label>
						<?php endif; ?>
						<button class="kbutton button-type-standard" type="submit"><span><?php echo JText::_('COM_KUNENA_PROFILEBOX_LOGIN'); ?></span></button>
					</fieldset>
				</form>
			</div>
		</li>
		<li class="divider"></li>
		<li class="klogin-password"><a href="<?php echo $this->lostPasswordUrl ?>" rel="nofollow"><?php echo JText::_('COM_KUNENA_PROFILEBOX_FORGOT_PASSWORD') ?></a></li>
		<li class="klogin-username"><a href="<?php echo $this->lostUsernameUrl ?>" rel="nofollow"><?php echo JText::_('COM_KUNENA_PROFILEBOX_FORGOT_USERNAME') ?></a></li>
		<?php if ($this->registerUrl) : ?>
		<li class="klogin-register"><a href="<?php echo $this->registerUrl ?>" rel="nofollow"><?php echo JText::_('COM_KUNENA_PROFILEBOX_CREATE_ACCOUNT') ?></a></li>
		<?php endif ?>

		<?php if ($this->moduleHtml) : ?>
		<li class = "login-modules">
			<?php echo $this->moduleHtml ?>
		</li>
		<?php endif; ?>
	</ul>
</div>
<?php endif ?>
