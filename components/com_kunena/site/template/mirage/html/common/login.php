<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<?php if ($this->login) : ?>

	<ul class="list-unstyled login login-guest dropdown-menu">
		<li>
			<div class="login-form">
				<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user') ?>" method="post" class="form login">
					<input type="hidden" name="task" value="login" />
					<?php echo $this->displayFormToken() ?>

					<fieldset>
						<legend class="legend-hide"><?php echo JText::_('COM_KUNENA_PROFILEBOX_LOGIN'); ?></legend>
						<input class="kbox-width inputbox form-vertical form-field_simple" type="text" name="username" placeholder="<?php echo JText::_('COM_KUNENA_LOGIN_USERNAME') ?>"/>
						<input class="kbox-width inputbox form-vertical form-field_simple" type="password" name="password" placeholder="<?php echo JText::_('COM_KUNENA_LOGIN_PASSWORD') ?>" />
						<button class="kbutton button-type-comm" type="submit"><span><?php echo JText::_('COM_KUNENA_PROFILEBOX_LOGIN'); ?></span></button>
						<?php if($this->remember) : ?>
						<input class="klogin-remember" type="checkbox" id="klogin-remember" name="remember" value="1" />
						<label for="klogin-remember"><?php echo JText::_('COM_KUNENA_LOGIN_REMEMBER_ME'); ?></label>
						<?php endif; ?>
					</fieldset>
				</form>
			</div>
		</li>
		<li class="divider"></li>
		<li class="klogin-password"><a class="link-dropdown" href="<?php echo $this->lostPasswordUrl ?>" rel="nofollow"><?php echo JText::_('COM_KUNENA_PROFILEBOX_FORGOT_PASSWORD') ?></a></li>
		<li class="klogin-username"><a class="link-dropdown" href="<?php echo $this->lostUsernameUrl ?>" rel="nofollow"><?php echo JText::_('COM_KUNENA_PROFILEBOX_FORGOT_USERNAME') ?></a></li>
		<?php if ($this->registerUrl) : ?>
		<li class="klogin-register"><a class="link-dropdown" href="<?php echo $this->registerUrl ?>" rel="nofollow"><?php echo JText::_('COM_KUNENA_PROFILEBOX_CREATE_ACCOUNT') ?></a></li>
		<?php endif ?>

		<?php if ($this->moduleHtml) : ?>
		<li class = "login-modules">
			<?php echo $this->moduleHtml ?>
		</li>
		<?php endif; ?>
	</ul>

<?php endif ?>