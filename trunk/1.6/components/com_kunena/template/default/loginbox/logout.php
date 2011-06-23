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

$logout = CKunenaLogin::getlogoutFields();
$private = KunenaFactory::getPrivateMessaging();
$PMCount = $private->getUnreadCount($this->my->id);
$PMlink = $private->getInboxLink($PMCount ? JText::sprintf('COM_KUNENA_PMS_INBOX_NEW', $PMCount) : JText::_('COM_KUNENA_PMS_INBOX'));
?>
<div class="kblock kpbox">
	<div class="kcontainer" id="kprofilebox">
		<div class="kbody">
<table class="kprofilebox">
	<tbody>
		<tr class="krow1">
			<?php if ($avatar) : ?>
			<td class="kprofilebox-left">
				<?php echo CKunenaLink::GetProfileLink ( intval($this->user->id), $avatar ); ?>
			</td>
			<?php endif; ?>
			<td class="kprofileboxcnt">
				<ul class="kprofilebox-link">
					<?php if ($PMlink) : ?>
						<li><?php echo $PMlink; ?></li>
					<?php endif ?>
					<?php
					$user_fields = @explode ( ',', $this->config->annmodid );
					if (in_array ( $this->my->id, $user_fields ) || $this->my->usertype == 'Administrator' || $this->my->usertype == 'Super Administrator') {
						$is_editor = true; } else { $is_editor = false; }
					if ($is_editor) : ?>
						<li><a href="<?php echo CKunenaLink::GetAnnouncementURL ( 'show' ); ?>"><?php echo JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS'); ?></a></li>
					<?php endif; ?>
				</ul>
				<ul class="kprofilebox-welcome">
					<li><?php echo JText::_('COM_KUNENA_PROFILEBOX_WELCOME'); ?>, <strong><?php echo CKunenaLink::GetProfileLink ( intval($this->user->id), $this->escape($this->kunena_username) ); ;?></strong></li>
					<li class="kms"><strong><?php echo JText::_('COM_KUNENA_MYPROFILE_LASTVISITDATE'); ?>:</strong> <span title="<?php echo CKunenaTimeformat::showDate($this->user->lastvisitDate, 'ago', 'utc'); ?>"><?php echo CKunenaTimeformat::showDate($this->user->lastvisitDate, 'date_today', 'utc'); ?></span></li>
					<?php if ($logout) : ?>
					<li>
					<form action="<?php echo KunenaRoute::_(KUNENA_LIVEURLREL) ?>" method="post" name="login">
						<input type="submit" name="submit" class="kbutton" value="<?php echo JText::_('COM_KUNENA_PROFILEBOX_LOGOUT'); ?>" />
						<input type="hidden" name="option" value="<?php echo $logout['option']; ?>" />
						<?php if (!empty($logout['view'])) : ?>
						<input type="hidden" name="view" value="<?php echo $logout['view']; ?>" />
						<?php endif; ?>
						<input type="hidden" name="task" value="<?php echo $logout['task']; ?>" />
						<input type="hidden" name="<?php echo $logout['field_return']; ?>" value="<?php echo $return; ?>" />
						<?php echo JHTML::_ ( 'form.token' ); ?>
					</form>
					</li>
					<?php endif; ?>
				</ul>
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