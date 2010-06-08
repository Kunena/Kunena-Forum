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

<div>
<h2><?php echo JText::sprintf('COM_KUNENA_BAN_BANHISTORYFOR', $this->escape($this->profile->name)); ?></h2>
<table border="0" cellpadding="5" class="<?php echo isset ( $this->objCatInfo->class_sfx ) ? ' kblocktable' . $this->objCatInfo->class_sfx : ''; ?> kblock-ban">
	<thead>
	<tr class="ksth">
		<th width="2%"> # </th>
		<th width="20%"><?php echo JText::_('COM_KUNENA_BAN_BANLEVEL'); ?></th>
		<th width="34%"><?php echo JText::_('COM_KUNENA_BAN_STARTTIME'); ?></th>
		<th width="34%"><?php echo JText::_('COM_KUNENA_BAN_EXPIRETIME'); ?></th>
		<th width="10%"><?php echo JText::_('COM_KUNENA_BAN_LASTIP'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
		if ( !empty($this->banhistory) ) {
			foreach ($this->banhistory as $userban) {
	?>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td class="firstrow" width="1%">
			<?php echo $j++; ?>
		</td>
		<td class="firstrow">
			<span><?php echo $userban->blocked ? JText::_('COM_KUNENA_BAN_BLOCKED') : JText::_('COM_KUNENA_BAN_BANNED') ?> </span>
		</td>
		<td class="firstrow">
			<span><?php  if( $userban->created_time ) echo CKunenaTimeFormat::showDate($userban->created_time); ?> </span>
		</td>
		<td class="firstrow">
			<span><?php echo $userban->expiration ? JText::_('COM_KUNENA_BAN_LIFETIME') : CKunenaTimeFormat::showDate($userban->expiration); ?> </span>
		</td>
		<td class="firstrow">
			<span><?php if ($userban->ip) echo $this->escape($userban->ip); ?> </span>
		</td>
	</tr>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td colspan="5">
			<b><?php echo JText::_('COM_KUNENA_BAN_CREATEDBY'); ?></b> : <?php echo CKunenaLink::GetProfileLink ( $userban->created_by, $this->escape($userban->created_username) ); ?>  <?php echo CKunenaTimeFormat::showDate($userban->created_time); ?>
			<?php if ( $userban->modified_by && $userban->modified_time) { ?>&nbsp;|&nbsp;
			<b><?php echo JText::_('COM_KUNENA_BAN_MODIFIEDBY'); ?></b> : <?php echo CKunenaLink::GetProfileLink ( $userban->modified_by, $this->escape($userban->modified_username) ); ?>  <?php echo CKunenaTimeFormat::showDate($userban->modified_time); } ?>
		</td>
	</tr>
	<?php if($userban->reason_public) { ?>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td colspan="5"><b><?php echo JText::_('COM_KUNENA_BAN_PUBLICREASON'); ?></b> : <?php echo KunenaParser::parseText ($userban->reason_public); ?></td>
	</tr>
	<?php } ?>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td colspan="5"><b><?php echo JText::_('COM_KUNENA_BAN_PRIVATEREASON'); ?></b> : <?php echo KunenaParser::parseText ($userban->reason_private); ?></td>
	</tr>
<?php }
		} else { ?>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td class="firstrow" colspan="5"><?php echo JText::sprintf('COM_KUNENA_BAN_USER_NOHISTORY', $this->escape($this->profile->name)); ?></td>
	</tr>
<?php } ?>
</tbody>
</table>
</div>