<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();
?>
<div class="kblock kpbox">
	<div class="kcontainer" id="kprofilebox">
		<div class="kbody">
<table class="kprofilebox">
	<tbody>
		<tr class="krow1">
			<td valign="top" class="kprofileboxcnt">
				<div class="k_guest">
					<?php echo JText::_('COM_KUNENA_PROFILEBOX_WELCOME'); ?>,
					<b><?php echo JText::_('COM_KUNENA_PROFILEBOX_GUEST'); ?></b>
				</div>
				<?php if ($this->login) : ?>
				<form action="<?php echo KUNENA_LIVEURLREL ?>" method="post" name="login">
					<div class="input">
						<span>
							<?php echo JText::_('COM_KUNENA_A_USERNAME'); ?>
							<input type="text" name="<?php echo $this->login['field_username']; ?>" class="inputbox ks" alt="username" size="18" />
						</span>
						<span>
							<?php echo JText::_('COM_KUNENA_PASS'); ?>
							<input type="password" name="<?php echo $this->login['field_password']; ?>" class="inputbox ks" size="18" alt="password" /></span>
						<span>
							<?php if(JPluginHelper::isEnabled('system', 'remember')) : ?>
							<?php echo JText::_('COM_KUNENA_LOGIN_REMEMBER_ME');  ?>
							<input type="checkbox" name="remember" alt="" value="yes" />
							<?php endif; ?>
							<input type="submit" name="submit" class="kbutton" value="<?php echo JText::_('COM_KUNENA_PROFILEBOX_LOGIN'); ?>" />
							<input type="hidden" name="option" value="<?php echo $this->login['option']; ?>" />
							<?php if (!empty($this->login['view'])) : ?>
							<input type="hidden" name="view" value="<?php echo $this->login['view']; ?>" />
							<?php endif; ?>
							<input type="hidden" name="task" value="<?php echo $this->login['task']; ?>" />
							<input type="hidden" name="<?php echo $this->login['field_return']; ?>" value="<?php echo $this->return; ?>" />
							<?php echo JHTML::_ ( 'form.token' ); ?>
						</span>
					</div>
					<div class="klink-block">
						<span class="kprofilebox-pass">
							<?php echo CKunenaLink::GetHrefLink($this->lostpassword, JText::_('COM_KUNENA_PROFILEBOX_FORGOT_PASSWORD')) ?>
						</span>
						<span class="kprofilebox-user">
							<?php echo CKunenaLink::GetHrefLink($this->lostusername, JText::_('COM_KUNENA_PROFILEBOX_FORGOT_USERNAME')) ?>
						</span>
						<?php
						if ($this->register) : ?>
						<span class="kprofilebox-register">
							<?php echo CKunenaLink::GetHrefLink($this->register, JText::_('COM_KUNENA_PROFILEBOX_CREATE_ACCOUNT')) ?>
						</span>
						<?php endif; ?>
					</div>
				</form>
				<?php endif; ?>
			</td>
			<?php if ($this->moduleHtml) : ?>
			<td class = "kprofilebox-right">
				<div class="kprofilebox-modul">
					<?php $this->moduleHtml; ?>
				</div>
			</td>
			<?php endif; ?>
		</tr>
	</tbody>
</table>
		</div>
	</div>
</div>