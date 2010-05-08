<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();

require_once (KUNENA_PATH_LIB . DS . 'kunena.login.php');
$this->config = & CKunenaConfig::getInstance ();
$type = CKunenaLogin::getType ();
$return = CKunenaLogin::getReturnURL ( $type );
$login = CKunenaLogin::getloginFields();

?>
<div class="k_bt_cvr1">
<div class="k_bt_cvr2">
<div class="k_bt_cvr3">
<div class="k_bt_cvr4">
<div class="k_bt_cvr5">
<table class="kblocktable" border="0" cellspacing="0" cellpadding="10" width="100%">
	<thead>
		<tr>
			<th colspan="2">
				<div class="ktitle_cover km">
					<a class="ktitle kl"><?php echo JText::_('COM_KUNENA_FORUM_MESSAGE'); ?></a>
				</div>
			</th>
		</tr>
	</thead>
	<tbody>
	<?php if ($login) : ?>
		<tr class="ksth">
			<th colspan="2" class="ksectiontableheader" align="left">
				<?php echo JText::_('COM_KUNENA_LOGIN_FORUM') ?>
			</th>
		</tr>
		<tr>
			<td align="center" class="kforum-login">
<table border="0" cellspacing="0" cellpadding="0" width="60%">
	<thead>
		<tr>
			<th colspan="2">
				<div class="ktitle_cover km">
					<a class="ktitle kl"><?php echo JText::_('COM_KUNENA_PROFILEBOX_LOGIN'); ?></a>
				</div>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr class="kforum-login">
			<td class="kforum-login">
			<form action="index.php" method="post" name="login" id="form-login">
			<table width="100%" cellspacing="1" cellpadding="5">
				<tbody>
					<tr>
						<td align="left" class="kforum-login-box">
						<table cellspacing="1" cellpadding="4" align="center" style="width: 100%;">
							<tbody>
								<tr>
									<td valign="top"><b><?php echo JText::_('COM_KUNENA_A_USERNAME'); ?></b></td>
									<td><input type="text" name="<?php echo $login['field_username']; ?>" class="inputbox ks" alt="username" size="40" /> <br />
									<span class="kprofilebox_link">
										<?php echo CKunenaLogin::getLostUserLink ();?>
									</span>
									</td>
								</tr>
								<tr>
									<td valign="top"><b><?php echo JText::_('COM_KUNENA_PASS'); ?></b>
									</td>
									<td>
										<input type="password" name="<?php echo $login['field_password']; ?>" class="inputbox ks" size="40" alt="password" value=""/>
										<br/>
										<span class="kprofilebox_link">
											<?php echo CKunenaLogin::getLostPasswordLink (); ?>
										</span>
									</td>
								</tr>
								<?php
								if (JPluginHelper::isEnabled ( 'system', 'remember' )) :
									?>
								<tr>
									<td></td>
									<td>
										<input type="checkbox" name="<?php echo $login['field_remember']; ?>" value="yes" alt="Remember Me" />
										<span><?php echo JText::_('COM_KUNENA_PROFILEBOX_REMEMBER_ME'); ?></span>
									</td>
								</tr>
								<?php endif;
								?>
							</tbody>
						</table>
						</td>
					</tr>
					<tr>
			<td align="left">
				<input type="submit" name="Submit" class="kbutton" value="<?php echo JText::_('COM_KUNENA_PROFILEBOX_LOGIN'); ?>" />
				<input type="hidden" name="option" value="<?php echo $login['option']; ?>" />
				<input type="hidden" name="task" value="<?php echo $login['task']; ?>" />
				<input type="hidden" name="<?php echo $login['field_return']; ?>" value="<?php echo $return; ?>" />
				<?php echo JHTML::_( 'form.token' ); ?>
			</td>
					</tr>
				</tbody>
			</table>
			</form>
			</td>
		</tr>
	<?php endif; ?>
	</tbody>
</table>
<?php
$registration = CKunenaLogin::getRegisterLink ();
if ($registration) :
?>
<table border="0" cellspacing="0" cellpadding="0" width="60%">
	<thead>
		<tr>
			<th colspan="2">
				<div class="ktitle_cover km">
					<a class="ktitle kl"><?php echo JText::_('COM_KUNENA_PROFILEBOX_REGISTER'); ?></a>
				</div>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr class="ksth fbm">
		</tr>
		<tr class="kforum-login">
			<td class="kforum-login">
			<form action="index.php" method="post" name="login" id="form-login">
			<table width="100%" cellspacing="1" cellpadding="5">
				<tbody>
					<tr>
						<td align="left">
						<p><?php
						$usersConfig = &JComponentHelper::getParams ( 'com_users' );
						if ($usersConfig->get ( 'allowUserRegistration' )) {
						echo JText::_('COM_KUNENA_LOGIN_MESSAGE');
						} else {
						echo JText::_('COM_KUNENA_REG_NOTALLOWED'); }
						?></p>
						<p align="center"><?php
						if ($this->config->enablerulespage) {
							?><span class="kprofilebox_link">
						<?php echo CKunenaLink::GetRulesLink(JText::_('COM_KUNENA_FORUM_RULES') ); ?></span>
						<?php
						}
						?> &nbsp;&nbsp;&nbsp;&nbsp; <?php
						if ($this->config->enablehelppage) {
							?><span class="kprofilebox_link">
							<?php echo CKunenaLink::GetHelpLink(JText::_('COM_KUNENA_FORUM_HELP') ); ?></span>
						<?php
						}
						?></p>
						</td>
					</tr>
					<tr>
						<td align="left">
							<span class = "kbutton"><?php echo $registration; ?></span>
						</td>
					</tr>
				</tbody>
			</table>
			</form>
			</td>
		</tr>
	</tbody>
</table>
<?php endif; ?>
			</td>
		</tr>
	</tbody>
</table>
</div>
</div>
</div>
</div>
</div>