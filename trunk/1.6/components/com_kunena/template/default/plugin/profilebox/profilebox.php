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
$this->user = JFactory::getUser();
$this->config = & CKunenaConfig::getInstance ();
$type = CKunenaLogin::getType ();
$return = CKunenaLogin::getReturnURL ( $type );
$avatar = CKunenaLogin::getMyAvatar();

if ($type == 'logout') {
$logout = CKunenaLogin::getlogoutFields();
	?>
	<table class="kprofilebox" id="kprofilebox">
		<tbody id="topprofilebox_tbody">
			<tr class="ksectiontableentry1">
				<?php
				if ($avatar) : ?>
				<td class="kprofilebox-left center" width="1%">
					<?php echo $avatar; ?>
				</td>
				<?php endif; ?>
				<td class="kprofileboxcnt left">
					<ul class="kprofilebox_link">
						<li><?php echo CKunenaLink::GetShowLatestLink(JText::_('COM_KUNENA_PROFILEBOX_SHOW_LATEST_POSTS')); ?></li>
						<li><?php echo CKunenaLink::GetSearchLink ('search', '', 0, 0, JText::_('COM_KUNENA_SEARCH_ADVSEARCH') ); ?></li>
						<?php
						$user_fields = @explode ( ',', $this->config->annmodid );
						if (in_array ( $this->my->id, $user_fields ) || $this->my->usertype == 'Administrator' || $this->my->usertype == 'Super Administrator') {
							$is_editor = true; } else { $is_editor = false; }
						if ($is_editor) { ?>
							<li><a href="<?php echo CKunenaLink::GetAnnouncementURL ( 'show' ); ?>"><?php echo JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS'); ?></a></li>
						<?php } ?>
					</ul>
					<ul class="kprofilebox_welcome">
						<li><?php echo JText::_('COM_KUNENA_PROFILEBOX_WELCOME'); ?>, <strong><?php echo CKunenaLink::GetProfileLink ( $this->user->id, $this->kunena_username ); ;?></strong></li>
						<li class="kms"><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_LASTVISITDATE'); ?>:</strong> <span title="<?php echo CKunenaTimeformat::showDate($this->user->lastvisitDate, 'ago', 'utc'); ?>"><?php echo CKunenaTimeformat::showDate($this->user->lastvisitDate, 'date_today', 'utc'); ?></span></li>
						<?php if ($logout) : ?>
						<li>
						<form action="<?php echo KUNENA_LIVEURLREL ?>" method="post" name="login">
							<input type="submit" name="submit" class="kbutton" value="<?php echo JText::_('COM_KUNENA_PROFILEBOX_LOGOUT'); ?>" />
							<input type="hidden" name="option" value="<?php echo $logout['option']; ?>" />
							<input type="hidden" name="task" value="<?php echo $logout['task']; ?>" />
							<input type="hidden" name="<?php echo $logout['field_return']; ?>" value="<?php echo $return; ?>" />
						</form>
						</li>
						<?php endif; ?>
					</ul>
				</td>
					<?php if (JDocumentHTML::countModules ( 'kunena_profilebox' )) { ?>
				<td class = "kprofilebox-right">
					<div class="kprofilebox_modul">
						<?php CKunenaTools::showModulePosition( 'kunena_profilebox' ); ?>
					</div>
				</td>
					<?php } ?>
			</tr>
		</tbody>
	</table>
<?php
} else {
$login = CKunenaLogin::getloginFields();
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="kprofilebox">
	<tbody id="topprofilebox_tbody">
		<tr class="ksectiontableentry1">
			<td valign="top" class="kprofileboxcnt" align="left">
				<div class="k_guest">
					<?php echo JText::_('COM_KUNENA_PROFILEBOX_WELCOME'); ?>,
					<b><?php echo JText::_('COM_KUNENA_PROFILEBOX_GUEST'); ?></b>
				</div>
				<?php if ($login) : ?>
				<form action="<?php echo KUNENA_LIVEURLREL ?>" method="post" name="login">
					<div class="input">
						<span>
							<?php echo JText::_('COM_KUNENA_A_USERNAME'); ?>
							<input type="text" name="<?php echo $login['field_username']; ?>" class="inputbox ks" alt="username" size="18" />
						</span>
						<span>
							<?php echo JText::_('COM_KUNENA_PASS'); ?>
							<input type="password" name="<?php echo $login['field_password']; ?>" class="inputbox ks" size="18" alt="password" /></span>
						<span>
							<input type="submit" name="submit" class="kbutton" value="<?php echo JText::_('COM_KUNENA_PROFILEBOX_LOGIN'); ?>" />
							<input type="hidden" name="option" value="<?php echo $login['option']; ?>" />
							<input type="hidden" name="task" value="<?php echo $login['task']; ?>" />
							<input type="hidden" name="<?php echo $login['field_return']; ?>" value="<?php echo $return; ?>" /> <?php echo JHTML::_ ( 'form.token' ); ?>
						</span>
					</div>
					<div class="link_block">
						<span class="kprofilebox_link">
							<?php echo CKunenaLogin::getLostPasswordLink (); ?>
						</span>
						<span class="kprofilebox_link">
							<?php echo CKunenaLogin::getLostUserLink ();?>
						</span>
						<?php
						$registration = CKunenaLogin::getRegisterLink ();
						if ($registration) : ?>
						<span class="kprofilebox_link">
							<?php echo $registration ?>
						</span>
							<?php endif; ?>
					</div>
				</form>
				<?php endif; ?>
			</td>
				<?php if (JDocumentHTML::countModules ( 'kunena_profilebox' )) { ?>
			<td class = "kprofilebox-right">
				<div class="kprofilebox_modul">
					<?php CKunenaTools::showModulePosition( 'kunena_profilebox' ); ?>
				</div>
			</td>
			<?php } ?>
		</tr>
	</tbody>
</table>
<?php
}
?>
