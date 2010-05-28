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
$i=1;
$j=1;
?>

<h2><?php echo JText::_('COM_KUNENA_BAN_BANHISTORYFOR'); ?> <?php echo $this->profile->name; ?></h2>
<table border="0" cellpadding="5" class="<?php echo isset ( $this->objCatInfo->class_sfx ) ? ' kblocktable' . $this->objCatInfo->class_sfx : ''; ?> kblock-ban">
	<thead>
	<tr class="ksth">
		<th width="1%"> # </th>
		<th width="20%"><?php echo JText::_('COM_KUNENA_BAN_BANNEDUSER'); ?></th>
		<th width="20%"><?php echo JText::_('COM_KUNENA_BAN_BANLEVEL'); ?></th>
		<th width="200"><?php echo JText::_('COM_KUNENA_BAN_STARTTIME'); ?></th>
		<th width="200"><?php echo JText::_('COM_KUNENA_BAN_EXPIRETIME'); ?></th>
		<th width="10%"><?php echo JText::_('COM_KUNENA_BAN_LASTIP'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
		if ( $this->getBanHistory() ) {
			foreach ($this->getBanHistory() as $userban) {
	?>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td class="firstrow" width="1%">
			<?php echo $j++; ?>
		</td>
		<td class="firstrow">
			<?php echo $this->config->username ? $userban->username : $userban->name; ?>
		</td>
		<td class="firstrow">
			<span><?php if ( $userban->bantype == 1 ) { echo JText::_('COM_KUNENA_BAN_BLOCKED'); } elseif ( $userban->bantype == 2 ) { echo JText::_('COM_KUNENA_BAN_BANNED'); } ?> </span>
		</td>
		<td class="firstrow">
			<span><?php  if( $userban->ban_start != '0000-00-00 00:00:00' ) echo $userban->ban_start; ?> </span>
		</td>
		<td class="firstrow">
			<span><?php echo $userban->expiry == '0000-00-00 00:00:00' ? JText::_('COM_KUNENA_BAN_LIFETIME') : $userban->expiry; ?> </span>
		</td>
		<td class="firstrow">
			<span><?php if (!empty($userban->ip)) echo $userban->ip; ?> </span>
		</td>	
	</tr>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td colspan="9">
			<b><?php echo JText::_('COM_KUNENA_BAN_CREATEDBY'); ?></b> : <?php echo CKunenaLink::GetProfileLink ( $userban->created_userid, $this->config->username ? $userban->creatorusername : $userban->creatorname ); ?>  <?php echo $userban->created; ?>
			<?php if ( $userban->modified_by && $userban->modified_date) { ?>&nbsp;|&nbsp;
			<b><?php echo JText::_('COM_KUNENA_BAN_MODIFIEDBY'); ?></b> : <?php echo CKunenaLink::GetProfileLink ( $userban->modified_by, $this->config->username ? $userban->modifiedusername : $userban->modifiedname ); ?>  <?php echo $userban->modified_date; } ?>
		</td>
	</tr>
	<?php if($userban->public_reason) { ?>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td colspan="9"><b><?php echo JText::_('COM_KUNENA_BAN_PUBLICREASON'); ?></b> : <?php echo KunenaParser::parseText ($userban->public_reason); ?></td>
	</tr>
	<?php } ?>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td colspan="9"><b><?php echo JText::_('COM_KUNENA_BAN_PRIVATEREASON'); ?></b> : <?php echo KunenaParser::parseText ($userban->private_reason); ?></td>
	</tr>
<?php }
		} else { ?>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td class="firstrow" colspan="9"><?php echo $this->profile->name; ?> <?php echo JText::_('COM_KUNENA_BAN_ACTUALLY_NOHISTORY'); ?></td>
	</tr>
<?php } ?>
</tbody>
</table>
