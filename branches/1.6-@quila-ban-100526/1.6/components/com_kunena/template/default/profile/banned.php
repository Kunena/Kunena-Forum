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
<table border="0" cellpadding="5" class="<?php echo isset ( $this->objCatInfo->class_sfx ) ? ' kblocktable' . $this->objCatInfo->class_sfx : ''; ?>">
	<thead>
		<tr class="ksth">
			<th class="view-th ksectiontableheader" width="1%"> # </th>
			<th class="view-th ksectiontableheader" width="20%" style="text-align:center;white-space:nowrap;"><?php echo JText::_('User'); ?></th>
			<th class="view-th ksectiontableheader" width="20%" style="text-align:center;white-space:nowrap;"><?php echo JText::_('Ban Level'); ?></th>
			<th class="view-th ksectiontableheader" width="200" style="text-align:center;white-space:nowrap;"><?php echo JText::_('Start Time'); ?></th>
			<th class="view-th ksectiontableheader" width="200" style="text-align:center;white-space:nowrap;"><?php echo JText::_('Expire Time'); ?></th>
			<th class="view-th ksectiontableheader" width="10%" style="text-align:center;white-space:nowrap;"><?php echo JText::_('Last IP'); ?></th>
			<th class="view-th ksectiontableheader" width="" style="text-align:center;white-space:nowrap;" colspan="5"><?php echo JText::_('Action'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
			<td style="text-align:center;padding: 1px 5px;" width="1%"><a href="#"> <?php echo $j++; ?> </a></td>
			<td style="white-space:nowrap;text-align:center;padding: 1px 5px;"><a href="#"><?php echo CKunenaLink::GetProfileLink ( $userban->userid, $this->config->username ? $userban->name : $userban->username ); ?> </a></td>
			<td style="white-space:nowrap;text-align:center;padding: 1px 5px;;">
				<span><?php if ($userban->bantype == '1') { echo JText::_('Red Ban'); } else { echo JText::_('Blue Ban'); } ?></span>
			</td>
			<td style="white-space:nowrap;text-align:center;padding: 1px 5px;"><span><?php echo $userban->created; ?></span></td>
			<td style="white-space:nowrap;text-align:center;padding: 1px 5px;"><span><?php echo $userban->expiry; ?></span></td>
			<td style="white-space:nowrap;text-align:center;padding: 1px 5px;"><span><a href="#"><?php if ( !empty($userban->ip) ) echo $userban->ip; ?></a></span></td>
			<td style="text-align:center;padding: 5px;">
				<?php if ($userban->bantype == '1') { ?>
				<img class="" src="<?php echo KUNENA_URLICONSPATH . 'banned_red.png'; ?>"  alt="<?php echo JText::_('Blocked'); ?>" title="<?php echo JText::_('Blocked in Joomla (even blocked login)'); ?>" />
				<?php } else { ?>
				<img class="" src="<?php echo KUNENA_URLICONSPATH . 'banned.png'; ?>"  alt="<?php echo JText::_('Banned'); ?>" title="<?php echo JText::_('Banned in Kunena (read-only mode)'); ?>" />
				<?php } ?>
			</td>
			<!--  <td style="text-align:center;padding: 5px;">
				<img class="deleteicon" src="<?php echo KUNENA_URLICONSPATH . 'editban.png';?>"  alt="<?php echo JText::_('Edit ban'); ?>" title="<?php echo JText::_('Edit ban'); ?>" />
			</td>-->
			<?php if ($userban->bantype == '1') { ?>
			<td style="white-space:nowrap;text-align:center;padding: 5px;">
				<a class="profilebanactions" id="banuser" href="#">
				<img class="deleteicon" src="<?php echo KUNENA_URLICONSPATH . 'ban.png';?>"  alt="<?php echo JText::_('Ban user'); ?>" title="<?php echo JText::_('Ban user'); ?>" />
				</a>
			</td>
			<?php } elseif($userban->bantype == '2') { ?>
			<td style="white-space:nowrap;text-align:center;padding: 5px;">
				<a class="profilebanactions" id="blockuser" href="#">
				<img class="deleteicon" src="<?php echo KUNENA_URLICONSPATH . 'blockuser.png';?>"  alt="<?php echo JText::_('Block user'); ?>" title="<?php echo JText::_('Block user'); ?>" />
				</a>
			</td>
			<?php } ?>
			<td style="white-space:nowrap;text-align:center;padding: 5px;">
				<a class="profilebanactions" id="logoutuser" href="#">
				<img class="deleteicon" src="<?php echo KUNENA_URLICONSPATH . 'logoutuser.png';?>"  alt="<?php echo JText::_('Logout user'); ?>" title="<?php echo JText::_('Logout user'); ?>" />
				</a>
			</td>
			<?php if ($userban->bantype == '1') { ?>
			<td style="white-space:nowrap;text-align:center;padding: 5px;">
				<a class="profilebanactions" id="unblock" href="#">
				<img class="deleteicon" src="<?php echo KUNENA_URLICONSPATH . 'unban.png';?>"  alt="<?php echo JText::_('Unblock user'); ?>" title="<?php echo JText::_('Unblock user'); ?>" /></a>
				</a>
			</td>
			<?php } elseif($userban->bantype == '2') { ?>
			<td style="white-space:nowrap;text-align:center;padding: 5px;">
				<a class="profilebanactions" id="unbanuser" href="#">
				<img class="deleteicon" src="<?php echo KUNENA_URLICONSPATH . 'unban.png';?>"  alt="<?php echo JText::_('Unban user'); ?>" title="<?php echo JText::_('Unban user'); ?>" /></a>
				</a>
			</td>
			<?php } ?>
		</tr>
		<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
			<td style="text-align:left;" width="100%" colspan="11">
				<b><?php echo JText::_('Created by'); ?></b> : <?php echo CKunenaLink::GetProfileLink ( $userban->created_userid, $this->config->username ? $userban->creatorname : $userban->creatorusername ); ?>
				<?php if (!empty ($userban->modified_by)):?> | <b><?php echo JText::_('Modified by'); ?></b> : <?php echo CKunenaLink::GetProfileLink ( $userban->modified_by, $this->config->username ? $userban->modifiedname : $userban->modifiedusername ); ?> - <?php echo $banned->modified_date; ?><?php endif; ?>
			</td>
		</tr>
		<?php if($userban->public_reason) { ?>
		<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
			<td style="text-align:left;" width="100%" colspan="11">
				<b><?php echo JText::_('Public Reason'); ?></b> : <?php echo KunenaParser::parseText ($userban->public_reason); ?>
			</td>
		</tr>
		<?php } ?>
		<?php if ( CKunenaTools::isModerator($this->my->id) && $userban->private_reason ) { ?>
		<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
			<td style="text-align:left;" width="100%" colspan="11">
				<b><?php echo JText::_('Private Reason'); ?></b> : <?php echo KunenaParser::parseText ($userban->private_reason); ?>
			</td>
		<?php } ?>
		<input type="hidden" name="thisuserid" value="<?php echo $userban->userid; ?>" />
		<?php }
 } else { ?>
		<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
			<td style="text-align:center;" width="100%" colspan="11">
				<?php echo JText::_('Actually No Banned Users'); ?>
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
