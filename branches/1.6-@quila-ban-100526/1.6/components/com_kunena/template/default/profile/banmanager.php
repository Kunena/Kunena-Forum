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
?>

<h2><?php echo JText::_('COM_KUNENA_BAN_BANMANAGER'); ?></h2>
<table border="0" cellpadding="5" class="<?php echo isset ( $this->objCatInfo->class_sfx ) ? ' kblocktable' . $this->objCatInfo->class_sfx : ''; ?> kblock-ban">
	<thead>
		<tr class="ksth">
			<th width="1%"> # </th>
			<th width="20%"><?php echo JText::_('COM_KUNENA_BAN_BANNEDUSER'); ?></th>
			<th width="15%"><?php echo JText::_('COM_KUNENA_BAN_BANNEDFROM'); ?></th>
			<th width="32%"><?php echo JText::_('COM_KUNENA_BAN_STARTTIME'); ?></th>
			<th width="32%"><?php echo JText::_('COM_KUNENA_BAN_EXPIRETIME'); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
		if ( $this->bannedusers ) {
			foreach ($this->bannedusers as $userban) {
				$bantext = $userban->blocked ? JText::_('COM_KUNENA_BAN_UNBLOCK_USER') : JText::_('COM_KUNENA_BAN_UNBAN_USER');
				$j++;
	?>
		<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
			<td><?php echo $j; ?></td>
			<td><a href="#"><?php echo CKunenaLink::GetProfileLink ( $userban->userid ); ?> </a></td>
			<td>
				<span><?php echo $userban->blocked ? JText::_('COM_KUNENA_BAN_BANLEVEL_JOOMLA') : JText::_('COM_KUNENA_BAN_BANLEVEL_KUNENA'); } ?></span>
			</td>
			<td>
				<span><?php echo CKunenaTimeFormat::showDate($userban->created_time, 'datetime'); ?></span>
			</td>
			<td>
				<span><?php echo $userban->isLifetime() ? JText::_('COM_KUNENA_BAN_LIFETIME') : CKunenaTimeFormat::showDate($userban->expiration, 'datetime'); ?></span>
			</td>
		</tr>
		<?php } else { ?>
		<tr class="ksectiontableentry<?php echo ($i^=1)+1;?>">
			<td colspan="5">
				<?php echo JText::_('COM_KUNENA_BAN_NO_BANNED_USERS'); ?>
			</td>
		</tr>
	<?php }
	 ?>
	</tbody>
</table>