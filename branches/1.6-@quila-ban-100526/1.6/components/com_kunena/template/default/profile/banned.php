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
?>

<h2><?php echo JText::_('Actually banned users'); ?></h2>
	<?php
		$kid = 0;
		//foreach ($this->ban as $banned) {
			if ( ($banned->created != '0000-00-00 00:00:00') && $banned->enabled = 1 ) {
	?>
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
			<td style="text-align:center;padding: 1px 5px;" width="1%"><a href="#"> <?php echo $banned->id; ?> </a></td>
			<td style="white-space:nowrap;text-align:center;padding: 1px 5px;"><a href="#"><?php echo CKunenaLink::GetProfileLink ( $banned->userid, $banned->username ); ?> </a></td>
			<td style="white-space:nowrap;text-align:center;padding: 1px 5px;;">
				<span><?php if ($banned->bantype == '1') { echo JText::_('Blue Ban'); } else { echo JText::_('Red Ban'); } ?></span>
			</td>
			<td style="white-space:nowrap;text-align:center;padding: 1px 5px;"><span><?php echo $banned->created; ?></span></td>
			<td style="white-space:nowrap;text-align:center;padding: 1px 5px;"><span><?php echo $banned->expiry; ?></span></td>
			<td style="white-space:nowrap;text-align:center;padding: 1px 5px;"><span><a href="#"><?php echo $banned->ip; ?></a></span></td>
			<td style="text-align:center;padding: 5px;">
				<?php if ($banned->bantype == '1') { ?>
				<img class="" src="<?php echo KUNENA_URLICONSPATH . 'banned.png'; ?>"  alt="<?php echo JText::_('Banned'); ?>" title="<?php echo JText::_('Banned in Kunena (read-only mode)'); ?>" />
				<?php } else { ?>
				<img class="" src="<?php echo KUNENA_URLICONSPATH . 'banned_red.png'; ?>"  alt="<?php echo JText::_('Blocked'); ?>" title="<?php echo JText::_('Blocked in Joomla (even blocked login)'); ?>" />
				<?php } ?>
			</td>
			<td style="text-align:center;padding: 5px;">
				<img class="deleteicon" src="<?php echo KUNENA_URLICONSPATH . 'editban.png';?>"  alt="<?php echo JText::_('Edit ban'); ?>" title="<?php echo JText::_('Edit ban'); ?>" />
			</td>
			<td style="white-space:nowrap;text-align:center;padding: 5px;">
				<img class="deleteicon" src="<?php echo KUNENA_URLICONSPATH . 'blockuser.png';?>"  alt="<?php echo JText::_('Block user'); ?>" title="<?php echo JText::_('Block user'); ?>" />
			</td>
			<td style="white-space:nowrap;text-align:center;padding: 5px;">
				<img class="deleteicon" src="<?php echo KUNENA_URLICONSPATH . 'logoutuser.png';?>"  alt="<?php echo JText::_('Logout user'); ?>" title="<?php echo JText::_('Logout user'); ?>" />
			</td>
			<td style="white-space:nowrap;text-align:center;padding: 5px;"><a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','userUnban')">
				<img class="deleteicon" src="<?php echo KUNENA_URLICONSPATH . 'unban.png';?>"  alt="<?php echo JText::_('Unban user'); ?>" title="<?php echo JText::_('Unban user'); ?>" /></a>
			</td>
		</tr>
		<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
			<td style="text-align:left;" width="100%" colspan="11">
				<b><?php echo JText::_('Created by'); ?></b> : <?php echo CKunenaLink::GetProfileLink ( $banned->created_userid, $banned->username ); ?>
				<?php if (!empty ($banned->modified_by)):?> | <b><?php echo JText::_('Modified by'); ?></b> : <?php echo CKunenaLink::GetProfileLink ( $banned->modified_by, $banned->username ); ?> - <?php echo $banned->modified_date; ?><?php endif; ?>
			</td>
		</tr>
		<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
			<td style="text-align:left;" width="100%" colspan="11">
				<b><?php echo JText::_('Public Reason'); ?></b> : <?php echo KunenaParser::parseText ($banned->public_reason); ?>
			</td>
		</tr>
		<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
			<td style="text-align:left;" width="100%" colspan="11">
				<b><?php echo JText::_('Private Reason'); ?></b> : <?php echo KunenaParser::parseText ($banned->private_reason); ?>
			</td>
		<?php $kid++; ?>
	<?php //} else { ?>
		<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
			<td style="text-align:center;" width="100%" colspan="11">
				<?php echo JText::_('Actually No Banned Users'); ?>
			</td>
		</tr>
	<?php } 
	//} ?>
	</tbody>
</table>
