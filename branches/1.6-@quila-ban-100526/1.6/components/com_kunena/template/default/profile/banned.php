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
$j=1;
$this->_app->setUserState('com_kunena.banreturnurl', CKunenaLink::GetProfileURL($this->my->id));
?>

<h2><?php echo JText::_('Actually banned users'); ?></h2>
	<?php
		if ( $this->displayUsersBanned() ) {
			foreach ($this->displayUsersBanned() as $userban) {
	?>
<form action="index.php" method="post" name="kBanActionsProfile" id="kBanActionsProfile">
<table border="0" cellpadding="5" class="<?php echo isset ( $this->objCatInfo->class_sfx ) ? ' kblocktable' . $this->objCatInfo->class_sfx : ''; ?> kblock-ban">
	<thead>
		<tr class="ksth">
			<th width="1%"> # </th>
			<th width="20%"><?php echo JText::_('COM_KUNENA_BAN_BANNEDUSER'); ?></th>
			<th width="20%"><?php echo JText::_('COM_KUNENA_BAN_BANLEVEL'); ?></th>
			<th width="200"><?php echo JText::_('COM_KUNENA_BAN_STARTTIME'); ?></th>
			<th width="200"><?php echo JText::_('COM_KUNENA_BAN_EXPIRETIME'); ?></th>
			<th width="10%"><?php echo JText::_('COM_KUNENA_BAN_LASTIP'); ?></th>
			<th colspan="5"><?php echo JText::_('COM_KUNENA_BAN_BANACTION'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
			<td class="firstrow" width="1%"><?php echo $j++; ?></td>
			<td class="firstrow"><a href="#"><?php echo CKunenaLink::GetProfileLink ( $userban->userid, $this->config->username ? $userban->username : $userban->name ); ?> </a></td>
			<td class="firstrow">
				<span><?php if ( $userban->bantype == 1) { echo JText::_('COM_KUNENA_BAN_BANLEVEL_RED'); } elseif ( $userban->bantype == 2 ) { echo JText::_('COM_KUNENA_BAN_BANLEVEL_BLUE'); } ?></span>
			</td>
			<td class="firstrow"><span><?php echo $userban->created; ?></span></td>
			<td class="firstrow"><span><?php echo $userban->expiry == '0000-00-00 00:00:00' ? JText::_('COM_KUNENA_BAN_LIFETIME') : $userban->expiry; ?></span></td>
			<td class="firstrow"><span><?php if ( !empty($userban->ip) ) echo $userban->ip; ?></span></td>
			<td class="iconrow">
				<?php if ($userban->bantype == 1) { ?>
				<img src="<?php echo KUNENA_URLICONSPATH . 'banned_red.png'; ?>"  alt="<?php echo JText::_('COM_KUNENA_BAN_BLOCKED'); ?>" title="<?php echo JText::_('COM_KUNENA_BAN_BANLEVEL_RED_DESC'); ?>" />
				<?php } elseif ($userban->bantype == 2) { ?>
				<img class="" src="<?php echo KUNENA_URLICONSPATH . 'banned.png'; ?>"  alt="<?php echo JText::_('COM_KUNENA_BAN_BANNED'); ?>" title="<?php echo JText::_('COM_KUNENA_BAN_BANLEVEL_BLUE_DESC'); ?>" />
				<?php } else { ?>
				<img src="<?php echo KUNENA_URLICONSPATH . 'banned_gray.png'; ?>"  alt="<?php echo JText::_('COM_KUNENA_BAN_BANNED_IP'); ?>" title="<?php echo JText::_('COM_KUNENA_BAN_BANLEVEL_GRAY_DESC'); ?>" />
				<?php } ?>
			</td>
			<!--  <td style="text-align:center;padding: 5px;">
				<img class="deleteicon" src="<?php echo KUNENA_URLICONSPATH . 'editban.png';?>"  alt="<?php echo JText::_('COM_KUNENA_BAN_EDIT_BAN'); ?>" title="<?php echo JText::_('COM_KUNENA_BAN_EDIT_BAN'); ?>" />
			</td>-->
			<?php if ($userban->bantype == '1') { ?>
			<td class="iconrow">
				<a class="profilebanactions" id="banuser" href="#">
				<img class="deleteicon" src="<?php echo KUNENA_URLICONSPATH . 'ban.png';?>"  alt="<?php echo JText::_('COM_KUNENA_BAN_BAN_USER'); ?>" title="<?php echo JText::_('COM_KUNENA_BAN_BAN_USER'); ?>" />
				</a>
			</td>
			<?php } elseif($userban->bantype == '2') { ?>
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
			<?php if ($userban->bantype == '1') { ?>
			<td class="iconrow">
				<a class="profilebanactions" id="unblock" href="#">
				<img class="deleteicon" src="<?php echo KUNENA_URLICONSPATH . 'unban.png';?>"  alt="<?php echo JText::_('COM_KUNENA_BAN_UNBLOCK_USER'); ?>" title="<?php echo JText::_('COM_KUNENA_BAN_UNBLOCK_USER'); ?>" /></a>
				</a>
			</td>
			<?php } elseif($userban->bantype == '2') { ?>
			<td class="iconrow">
				<a class="profilebanactions" id="unbanuser" href="#">
				<img class="deleteicon" src="<?php echo KUNENA_URLICONSPATH . 'unban.png';?>"  alt="<?php echo JText::_('COM_KUNENA_BAN_UNBAN_USER'); ?>" title="<?php echo JText::_('COM_KUNENA_BAN_UNBAN_USER'); ?>" /></a>
				</a>
			</td>
			<?php } ?>
		</tr>
		<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
			<td colspan="11">
				<b><?php echo JText::_('COM_KUNENA_BAN_CREATEDBY'); ?></b> : <?php echo CKunenaLink::GetProfileLink ( $userban->created_userid, $this->config->username ? $userban->creatorusername : $userban->creatorname ); ?>
				<?php if (!empty ($userban->modified_by)):?> | <b><?php echo JText::_('COM_KUNENA_BAN_MODIFIEDBY'); ?></b> : <?php echo CKunenaLink::GetProfileLink ( $userban->modified_by, $this->config->username ? $userban->modifiedusername : $userban->modifiedname ); ?> - <?php echo $banned->modified_date; ?><?php endif; ?>
			</td>
		</tr>
		<?php if($userban->public_reason) { ?>
		<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
			<td colspan="11">
				<b><?php echo JText::_('COM_KUNENA_BAN_PUBLICREASON'); ?></b> : <?php echo KunenaParser::parseText ($userban->public_reason); ?>
			</td>
		</tr>
		<?php } ?>
		<?php if ( CKunenaTools::isModerator($this->my->id) && $userban->private_reason ) { ?>
		<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
			<td colspan="11">
				<b><?php echo JText::_('COM_KUNENA_BAN_PRIVATEREASON'); ?></b> : <?php echo KunenaParser::parseText ($userban->private_reason); ?>
			</td>
		<?php } ?>
		<input type="hidden" name="thisuserid" value="<?php echo $userban->userid; ?>" />
		<?php }
 } else { ?>
		<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
			<td class="firstrow" colspan="11">
				<?php echo JText::_('COM_KUNENA_BAN_ACTUALLY_NOBANNED'); ?>
			</td>
		</tr>
	<?php }
	 ?>
	</tbody>
</table>

<input type="hidden" id="kbanprofileinputdo" name="do" value="" />
<input type="hidden" name="option" value="com_kunena" />
<input type="hidden" name="func" value="banprofileactions" />
</form>
