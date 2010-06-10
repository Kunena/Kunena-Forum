<?php
/**
 * @version $Id: editavatar.php 2406 2010-05-04 06:16:56Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 **/
defined( '_JEXEC' ) or die();
JHTML::_('behavior.tooltip');
$i=1;
$j=0;
$this->_app->setUserState('com_kunena.banreturnurl', CKunenaLink::GetProfileURL($this->my->id));
?>

<h2><?php echo JText::_('COM_KUNENA_BAN_BANMANAGER'); ?></h2>
<form action="index.php" method="post" name="kBanActionsProfile" id="kBanActionsProfile">
<table border="0" cellpadding="5" class="<?php echo isset ( $this->objCatInfo->class_sfx ) ? ' kblocktable' . $this->objCatInfo->class_sfx : ''; ?> kblock-ban">
	<thead>
		<tr class="ksth">
			<th width="1%"> # </th>
			<th width="20%"><?php echo JText::_('COM_KUNENA_BAN_BANNEDUSER'); ?></th>
			<th width="20%"><?php echo JText::_('COM_KUNENA_BAN_BANLEVEL'); ?></th>
			<th width="32%"><?php echo JText::_('COM_KUNENA_BAN_STARTTIME'); ?></th>
			<th width="32%"><?php echo JText::_('COM_KUNENA_BAN_EXPIRETIME'); ?></th>
			<th width="5%" colspan="5"><?php echo JText::_('COM_KUNENA_BAN_BANACTIONS'); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
		if ( $this->bannedusers ) {
			foreach ($this->bannedusers as $userban) {
				$j++;
	?>
		<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
			<td class="firstrow" width="1%"><?php echo $j; ?></td>
			<td class="firstrow"><a href="#"><?php echo CKunenaLink::GetProfileLink ( $userban->userid, $this->escape($userban->username) ); ?> </a></td>
			<td class="firstrow">
				<span><?php echo $userban->blocked ? JText::_('COM_KUNENA_BAN_BANLEVEL_JOOMLA') : JText::_('COM_KUNENA_BAN_BANLEVEL_KUNENA'); } ?></span>
			</td>
			<td class="firstrow"><span><?php echo CKunenaTimeFormat::showDate($userban->created_time); ?></span></td>
			<td class="firstrow">
				<span>
					<input class="inputbox keditenable<?php echo $j; ?>" type="text" maxlength="15" disabled="disabled" name="kban_expireTime<?php echo $j; ?>"
						id="Expire_Time" value="<?php echo $userban->expiration ? JText::_('COM_KUNENA_BAN_LIFETIME') : CKunenaTimeFormat::showDate($userban->expiration); ?>" />
					<img class="keditenable<?php echo $j; ?>" src="templates/system/images/calendar.png" style="display:none;" alt="Calendar" onclick="showCalendar('Expire_Time','%Y-%m-%d');$('Expire_Time').removeProperty('style');" />
				</span>
			</td>
			<td class="iconrow">
				<?php if ($userban->blocked) { ?>
				<img src="<?php echo KUNENA_URLICONSPATH . 'banned_red.png'; ?>"  alt="<?php echo JText::_('COM_KUNENA_BAN_BLOCKED'); ?>" title="<?php echo JText::_('COM_KUNENA_BAN_BANLEVEL_JOOMLA_DESC'); ?>" />
				<?php } elseif (!$userban->blocked) { ?>
				<img class="" src="<?php echo KUNENA_URLICONSPATH . 'banned.png'; ?>"  alt="<?php echo JText::_('COM_KUNENA_BAN_BANNED'); ?>" title="<?php echo JText::_('COM_KUNENA_BAN_BANLEVEL_KUNENA_DESC'); ?>" />
				<?php } ?>
			</td>
			 <td style="text-align:center;padding: 5px;" id="kbanedititem">
				<img class="deleteicon keditbango" id="<?php echo $j; ?>" src="<?php echo KUNENA_URLICONSPATH . 'editban.png';?>"  alt="<?php echo JText::_('COM_KUNENA_BAN_EDIT_BAN'); ?>" title="<?php echo JText::_('COM_KUNENA_BAN_EDIT_BAN'); ?>" />
			</td>
			<?php if (!$userban->blocked) { ?>
			<td class="iconrow">
				<a class="profilebanactions" id="banuser" href="#">
				<img class="deleteicon" src="<?php echo KUNENA_URLICONSPATH . 'ban.png';?>"  alt="<?php echo JText::_('COM_KUNENA_BAN_BAN_USER'); ?>" title="<?php echo JText::_('COM_KUNENA_BAN_BAN_USER'); ?>" />
				</a>
			</td>
			<?php } elseif($userban->blocked) { ?>
			<td class="iconrow">
				<a class="profilebanactions" id="blockuser" href="#">
				<img class="deleteicon" src="<?php echo KUNENA_URLICONSPATH . 'blockuser.png';?>"  alt="<?php echo JText::_('COM_KUNENA_BAN_BLOCK_USER'); ?>" title="<?php echo JText::_('COM_KUNENA_BAN_BLOCK_USER'); ?>" />
				</a>
			</td>
			<?php } ?>
			<td class="iconrow">
				<a class="profilebanactions" id="logoutuser" href="#">
				<img class="deleteicon" src="<?php echo KUNENA_URLICONSPATH . 'logoutuser.png';?>"  alt="<?php echo JText::_('COM_KUNENA_BAN_LOGOUT_USER'); ?>" title="<?php echo JText::_('COM_KUNENA_BAN_LOGOUT_USER'); ?>" />
				</a>
			</td>
			<?php if ($userban->blocked) { ?>
			<td class="iconrow">
				<a class="profilebanactions" id="unblock" href="#">
				<img class="deleteicon" src="<?php echo KUNENA_URLICONSPATH . 'unban.png';?>"  alt="<?php echo JText::_('COM_KUNENA_BAN_UNBLOCK_USER'); ?>" title="<?php echo JText::_('COM_KUNENA_BAN_UNBLOCK_USER'); ?>" />
				</a>
			</td>
			<?php } elseif(!$userban->blocked) { ?>
			<td class="iconrow">
				<a class="profilebanactions" id="unbanuser" href="#">
				<img class="deleteicon" src="<?php echo KUNENA_URLICONSPATH . 'unban.png';?>"  alt="<?php echo JText::_('COM_KUNENA_BAN_UNBAN_USER'); ?>" title="<?php echo JText::_('COM_KUNENA_BAN_UNBAN_USER'); ?>" />
				</a>
			</td>
			<?php } ?>
			<td style="display:none;" class="iconrow keditenable<?php echo $j; ?>">
				<a class="profilebanactions" id="validatechanges" href="#">
				<img class="deleteicon" src="<?php echo KUNENA_URLICONSPATH . 'logoutuser.png';?>"  alt="<?php echo JText::_('COM_KUNENA_BAN_VALIDATE_CHANGE'); ?>" title="<?php echo JText::_('COM_KUNENA_BAN_VALIDATE_CHANGE'); ?>" />
				</a>
			</td>
		</tr>
		<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
			<td colspan="11">
				<b><?php echo JText::_('COM_KUNENA_BAN_CREATEDBY'); ?></b> : <?php echo CKunenaLink::GetProfileLink ( $userban->created_by, $this->escape($userban->created_username) ); ?>
				<?php if (!empty ($userban->modified_by)):?> | <b><?php echo JText::_('COM_KUNENA_BAN_MODIFIEDBY'); ?></b> : <?php echo CKunenaLink::GetProfileLink ( $userban->modified_by, $this->escape($userban->modified_username) ); ?> - <?php echo CKunenaTimeFormat::showDate($banned->modified_date); ?><?php endif; ?>
			</td>
		</tr>
		<?php if($userban->reason_public) { ?>
		<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
			<td colspan="11">
				<b><?php echo JText::_('COM_KUNENA_BAN_PUBLICREASON'); ?></b> : <input class="keditenable<?php echo $j; ?>" type="text" name="kban_publicreason<?php echo $j; ?>" disabled="disabled" value="<?php echo KunenaParser::parseText ($userban->reason_public); ?>" />
			</td>
		</tr>
		<?php } ?>
		<?php if ( CKunenaTools::isModerator($this->my->id) && $userban->reason_private ) { ?>
		<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
			<td colspan="11">
				<b><?php echo JText::_('COM_KUNENA_BAN_PRIVATEREASON'); ?></b> : <input class="keditenable<?php echo $j; ?>" type="text" name="kban_privatereason<?php echo $j; ?>" disabled="disabled" value="<?php echo KunenaParser::parseText ($userban->reason_private); ?>" />
				<input type="hidden" name="thisuserid" value="<?php echo $userban->userid; ?>" />
			</td>
		</tr>
		<?php } ?>
		<?php } else { ?>
		<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
			<td class="firstrow" colspan="11">
				<?php echo JText::_('COM_KUNENA_BAN_NO_BANNED_USERS'); ?>
			</td>
		</tr>
	<?php }
	 ?>
	</tbody>
</table>

<input type="hidden" id="kbanprofileinputdo" name="do" value="" />
<input type="hidden" id="kbanprofilevaluedo" name="kbanvaluedo" value="" />
<input type="hidden" name="option" value="com_kunena" />
<input type="hidden" name="func" value="banprofileactions" />
</form>
