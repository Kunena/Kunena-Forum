<?php
/**
 * Kunena Component
 * @package Kunena.Template.Default20
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
// TODO: add missing module position
?>
<div class="dropdown-menu block">
	<ul class="klogin-guest">
		<li class="klogin-welcome kguest"><?php echo JText::_('COM_KUNENA_PROFILEBOX_WELCOME') ?>, <?php echo JText::_('COM_KUNENA_PROFILEBOX_GUEST') ?></li>
		<?php if ($this->login->enabled()) : ?>
		<li class="klogin-form">
			<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" class="kform">
				<input type="hidden" name="view" value="user" />
				<input type="hidden" name="task" value="login" />
				[K=TOKEN]

				<fieldset>
					<legend class="klegend"><?php echo JText::_('COM_KUNENA_PROFILEBOX_LOGIN'); ?></legend>
					<input type="text" name="username" id="kusername" class="kinputbox" placeholder="<?php echo JText::_('COM_KUNENA_LOGIN_USERNAME') ?>"/>
					<input type="password" name="password" id="kpassword" class="kinputbox" placeholder="<?php echo JText::_('COM_KUNENA_LOGIN_PASSWORD') ?>" />
					<button type="submit" value="Log in" class="kbutton"><?php echo JText::_('COM_KUNENA_PROFILEBOX_LOGIN'); ?></button>
				</fieldset>
			</form>
		</li>
		<li class="klogin-user">
			<ul>
				<li class="klogin-password"><?php echo CKunenaLink::GetHrefLink($this->lostpassword, JText::_('COM_KUNENA_PROFILEBOX_FORGOT_PASSWORD')) ?></li>
				<li class="klogin-username"><?php echo CKunenaLink::GetHrefLink($this->lostusername, JText::_('COM_KUNENA_PROFILEBOX_FORGOT_USERNAME')) ?></li>
				<?php if ($this->register) : ?>
				<li class="klogin-register"><?php echo CKunenaLink::GetHrefLink($this->register, JText::_('COM_KUNENA_PROFILEBOX_CREATE_ACCOUNT')) ?></li>
				<?php endif ?>
			</ul>
		</li>
		<?php endif ?>
	</ul>
</div>
