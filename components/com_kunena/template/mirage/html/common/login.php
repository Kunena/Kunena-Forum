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
// TODO: add missing module position
?>
<div class="login dropdown-menu">
	<ul class="list-unstyled login-guest">
		<?php if ($this->login) : ?>
			<li>
				<div class="login-form">
					<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" class="kform">
						<input type="hidden" name="view" value="user" />
						<input type="hidden" name="task" value="login" />
						[K=TOKEN]
	
						<fieldset>
							<legend class="legend-hide"><?php echo JText::_('COM_KUNENA_PROFILEBOX_LOGIN'); ?></legend>
							<input id="kusername" class="box-width inputbox form-vertical form-field_simple" type="text" name="username" placeholder="<?php echo JText::_('COM_KUNENA_LOGIN_USERNAME') ?>"/>
							<input id="kpassword" class="box-width inputbox form-vertical form-field_simple" type="password" name="password" placeholder="<?php echo JText::_('COM_KUNENA_LOGIN_PASSWORD') ?>" />
							<button class="kbutton button-type-standard" type="submit" value="Log in"><span><?php echo JText::_('COM_KUNENA_PROFILEBOX_LOGIN'); ?></span></button>
						</fieldset>
					</form>
				</div>
			</li>
			<li class="divider"></li>
			<li class="klogin-password"><?php echo CKunenaLink::GetHrefLink($this->lostpassword, JText::_('COM_KUNENA_PROFILEBOX_FORGOT_PASSWORD')) ?></li>
			<li class="klogin-username"><?php echo CKunenaLink::GetHrefLink($this->lostusername, JText::_('COM_KUNENA_PROFILEBOX_FORGOT_USERNAME')) ?></li>
			<?php if ($this->register) : ?>
				<li class="klogin-register"><?php echo CKunenaLink::GetHrefLink($this->register, JText::_('COM_KUNENA_PROFILEBOX_CREATE_ACCOUNT')) ?></li>
			<?php endif ?>
		<?php endif ?>
	</ul>
</div>
