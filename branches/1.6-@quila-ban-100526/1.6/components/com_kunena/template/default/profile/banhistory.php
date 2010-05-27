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

<h2><?php echo JText::_('Ban history for (this user)'); ?></h2>
<table border="0" cellpadding="5" class="<?php echo isset ( $this->objCatInfo->class_sfx ) ? ' kblocktable' . $this->objCatInfo->class_sfx : ''; ?>">
	<thead>
	<tr class="ksth">
		<th class="view-th ksectiontableheader" width="1%"> # </th>
		<th class="view-th ksectiontableheader" width="20%" style="text-align:center;white-space:nowrap;"><?php echo JText::_('Banned User'); ?></th>
		<th class="view-th ksectiontableheader" width="20%" style="text-align:center;white-space:nowrap;"><?php echo JText::_('Ban Level'); ?></th>
		<th class="view-th ksectiontableheader" width="200" style="text-align:center;white-space:nowrap;"><?php echo JText::_('Start Time'); ?></th>
		<th class="view-th ksectiontableheader" width="200" style="text-align:center;white-space:nowrap;"><?php echo JText::_('Expire Time'); ?></th>
		<th class="view-th ksectiontableheader" width="10%" style="text-align:center;white-space:nowrap;"><?php echo JText::_('IP'); ?></th>
		<!--<th class="view-th ksectiontableheader" width="5%">&nbsp;</th>
		<th class="view-th ksectiontableheader" width="5%">&nbsp;</th>
		<th class="view-th ksectiontableheader" width="5%">&nbsp;</th>-->
	</tr>
	</thead>
	<tbody>
	<?php
		if ( $this->getBanHistory() ) {
			foreach ($this->getBanHistory() as $userban) {
	?>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td style="text-align:center;padding: 1 10px;" width="1%"><?php echo $j++; ?> </td>
		<td style="white-space:nowrap;text-align:center;padding: 1 10px;"> <?php echo $this->config->username ? $userban->name : $userban->username; ?> </td>
		<td style="white-space:nowrap;text-align:center;padding: 1 10px;"><span><?php if ( $userban->bantype == 1 ) { echo 'blocked'; } elseif ( $userban->bantype == 2 ) { echo 'banned'; }; ?> </span></td>
		<td style="white-space:nowrap;text-align:center;padding: 1 10px;"><span><?php  if( $userban->ban_start != '0000-00-00 00:00:00' ) echo $userban->ban_start; ?> </span></td>
		<td style="white-space:nowrap;text-align:center;padding: 1 10px;"><span><?php echo $userban->expiry == '0000-00-00 00:00:00' ? 'at life' : $userban->expiry; ?> </span></td>
		<td style="white-space:nowrap;text-align:center;padding: 1 10px;"><span><?php if (!empty($userban->ip)) echo $userban->ip; ?> </span></td>
		<!--<td style="text-align:center;padding: 1 10px;"><a href="<?php //echo $this->userfilesurl.$userfile;?>"><img class="downloadicon" src="<?php echo KUNENA_URLICONSPATH . 'banned_red.png';?>"  alt="<?php echo JText::_('COM_KUNENA_ATTACH'); ?>" title="<?php //echo JText::_('COM_KUNENA_ATTACH'); ?>" /></a></td>
		<td style="text-align:center;padding: 1 10px;"><a href="<?php //CKunenaAttachments::deleteFile($userfile);?>"><img class="deleteicon" src="<?php echo KUNENA_URLICONSPATH . 'edit.png';?>"  alt="<?php echo JText::_('COM_KUNENA_ATTACH'); ?>" title="<?php //echo JText::_('COM_KUNENA_ATTACH'); ?>" /></a></td>
		<td style="white-space:nowrap;text-align:center;padding: 1 10px;"><a href="<?php //CKunenaAttachments::deleteFile($userfile);?>"><img class="deleteicon" src="<?php echo KUNENA_URLICONSPATH . 'delete.png';?>"  alt="<?php echo JText::_('COM_KUNENA_ATTACH'); ?>" title="<?php //echo JText::_('COM_KUNENA_ATTACH'); ?>" /></a></td>-->
	</tr>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td style="text-align:left;" width="100%" colspan="9"><b><?php echo JText::_('Created by'); ?></b> <?php echo $this->config->username ? $userban->creatorname : $userban->creatorusername; ?> <?php echo $userban->created; ?>  <?php if ( $userban->modified_by && $userban->modified_date) { ?> | <b>Modified by</b> : <?php echo $this->config->username ? $userban->modifiedname : $userban->modifiedusername; ?>  <?php echo $userban->modified_date; } ?></td>
	</tr>
	<?php if($userban->public_reason) { ?>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td style="text-align:left;" width="100%" colspan="9"><b><?php echo JText::_('Public Reason'); ?></b> : <?php echo $userban->public_reason; ?></td>
	</tr>
	<?php } ?>
	<?php if ( CKunenaTools::isModerator($this->my->id) && $userban->private_reason ) { ?>
	<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
		<td style="text-align:left;" width="100%" colspan="9"><b><?php echo JText::_('Private Reason'); ?></b> : <?php echo $userban->private_reason ?></td>
	</tr>
	<?php } ?>
<?php }
		} else { ?>
	<!--<tr class="ksectiontableentry<?php //echo ($i^=1)+1;?>">
		<td style="text-align:center;" width="100%" colspan="9"><?php echo JText::_('Actually No Banned Users'); ?></td>
	</tr>-->
<?php } ?>
</tbody>
</table>
