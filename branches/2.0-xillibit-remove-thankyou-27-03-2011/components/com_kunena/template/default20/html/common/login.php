<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
// TODO: add missing module position
?>
		<div class="klogin">
			<ul class="klogin-guest">
				<li class="klogin-welcome kguest"><?php echo JText::_('COM_KUNENA_PROFILEBOX_WELCOME') ?>, <?php echo JText::_('COM_KUNENA_PROFILEBOX_GUEST') ?></li>
				<?php if ($this->login) : ?>
				<li class="klogin-form">
					<form action="" method="post" class="kform">
						<fieldset>
							<legend class="klegend"><?php echo JText::_('COM_KUNENA_PROFILEBOX_LOGIN'); ?></legend>
							<label for="kusername"><?php echo JText::_('COM_KUNENA_LOGIN_USERNAME') ?></label>
							<input type="text" name="<?php echo $this->login['field_username']; ?>" id="kusername" class="kinputbox" />
							<label for="kpassword"><?php echo JText::_('COM_KUNENA_LOGIN_PASSWORD') ?></label>
							<input type="password" name="<?php echo $this->login['field_password']; ?>" id="kpassword" class="kinputbox" />
							<label for="kremember"><?php echo JText::_('COM_KUNENA_LOGIN_REMEMBER_ME') ?></label>
							<input type="checkbox" id="kremember" class="kcheckbox" name="remember" value="yes" />
							<button type="submit" value="Log in" class="kbutton"><?php echo JText::_('COM_KUNENA_PROFILEBOX_LOGIN'); ?></button>
							<input type="hidden" name="option" value="<?php echo $this->login['option']; ?>" />
							<?php if (!empty($this->login['view'])) : ?>
							<input type="hidden" name="view" value="<?php echo $this->login['view']; ?>" />
							<?php endif; ?>
							<input type="hidden" name="task" value="<?php echo $this->login['task']; ?>" />
							<input type="hidden" name="<?php echo $this->login['field_return']; ?>" value="<?php echo $this->return; ?>" />
							<?php echo JHTML::_ ( 'form.token' ); ?>
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
