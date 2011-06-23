<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();
require_once (KUNENA_PATH_LIB . '/kunena.login.php');
$this->user = JFactory::getUser();
$this->config = KunenaFactory::getConfig ();
$type = CKunenaLogin::getType ();
$return = CKunenaLogin::getReturnURL ( $type );
$avatar = CKunenaLogin::getMyAvatar();
$login = CKunenaLogin::getloginFields();
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
				<?php if ($login) : ?>
				<form action="<?php echo KunenaRoute::_(KUNENA_LIVEURLREL) ?>" method="post" name="login">
					<div class="input">
						<span>
							<?php echo JText::_('COM_KUNENA_A_USERNAME'); ?>
							<input type="text" name="<?php echo $login['field_username']; ?>" class="inputbox ks" alt="username" size="18" />
						</span>
						<span>
							<?php echo JText::_('COM_KUNENA_PASS'); ?>
							<input type="password" name="<?php echo $login['field_password']; ?>" class="inputbox ks" size="18" alt="password" /></span>
						<span>
							<?php if(JPluginHelper::isEnabled('system', 'remember')) : ?>
							<?php echo JText::_('COM_KUNENA_LOGIN_REMEMBER_ME');  ?>
							<input type="checkbox" name="remember" alt="" value="yes" />
							<?php endif; ?>
							<input type="submit" name="submit" class="kbutton" value="<?php echo JText::_('COM_KUNENA_PROFILEBOX_LOGIN'); ?>" />
							<input type="hidden" name="option" value="<?php echo $login['option']; ?>" />
							<?php if (!empty($login['view'])) : ?>
							<input type="hidden" name="view" value="<?php echo $login['view']; ?>" />
							<?php endif; ?>
							<input type="hidden" name="task" value="<?php echo $login['task']; ?>" />
							<input type="hidden" name="<?php echo $login['field_return']; ?>" value="<?php echo $return; ?>" />
							<?php echo JHTML::_ ( 'form.token' ); ?>
						</span>
					</div>
					<div class="klink-block">
						<span class="kprofilebox-pass">
							<?php echo CKunenaLogin::getLostPasswordLink (); ?>
						</span>
						<span class="kprofilebox-user">
							<?php echo CKunenaLogin::getLostUserLink ();?>
						</span>
						<?php
						$registration = CKunenaLogin::getRegisterLink ();
						if ($registration) : ?>
						<span class="kprofilebox-register">
							<?php echo $registration ?>
						</span>
						<?php endif; ?>
					</div>
				</form>
				<?php endif; ?>
			</td>
				<?php if (JDocumentHTML::countModules ( 'kunena_profilebox' )) : ?>
			<td class = "kprofilebox-right">
				<div class="kprofilebox-modul">
					<?php CKunenaTools::showModulePosition( 'kunena_profilebox' ); ?>
				</div>
			</td>
			<?php endif; ?>
		</tr>
	</tbody>
</table>
		</div>
	</div>
</div>