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
$j=count($this->banhistory);
?>

<div>
<h2><?php echo JText::sprintf('COM_KUNENA_BAN_BANHISTORYFOR', $this->escape($this->profile->name)); ?></h2>
<table class="<?php echo isset ( $this->objCatInfo->class_sfx ) ? ' kblocktable' . $this->escape($this->objCatInfo->class_sfx) : ''; ?> kblock-ban">
	<thead>
		<tr class="ksth">
			<th width="2%"> # </th>
			<th width="14%"><?php echo JText::_('COM_KUNENA_BAN_BANNEDFROM'); ?></th>
			<th width="20%"><?php echo JText::_('COM_KUNENA_BAN_STARTTIME'); ?></th>
			<th width="20%"><?php echo JText::_('COM_KUNENA_BAN_EXPIRETIME'); ?></th>
			<th width="20%"><?php echo JText::_('COM_KUNENA_BAN_CREATEDBY'); ?></th>
			<th width="24%"><?php echo JText::_('COM_KUNENA_BAN_MODIFIEDBY'); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
		if ( !empty($this->banhistory) ) :
			foreach ($this->banhistory as $userban) :
	?>
	<tr class="ksectiontableentry1">
		<td width="1%">
			<?php echo $j--; ?>
		</td>
		<td>
			<span><?php echo $userban->blocked ? JText::_('COM_KUNENA_BAN_BANLEVEL_JOOMLA') : JText::_('COM_KUNENA_BAN_BANLEVEL_KUNENA') ?></span>
		</td>
		<td>
			<span><?php  if( $userban->created_time ) echo CKunenaTimeFormat::showDate($userban->created_time, 'datetime'); ?></span>
		</td>
		<td>
			<span><?php echo $userban->isLifetime() ? JText::_('COM_KUNENA_BAN_LIFETIME') : CKunenaTimeFormat::showDate($userban->expiration, 'datetime'); ?></span>
		</td>
		<td>
			<span><?php echo CKunenaLink::GetProfileLink ( intval($userban->created_by) ); ?></span>
		</td>
		<td>
			<?php if ( $userban->modified_by && $userban->modified_time) { ?>
			<span>
				<?php echo CKunenaLink::GetProfileLink ( intval($userban->modified_by) ); ?>
				<?php echo CKunenaTimeFormat::showDate($userban->modified_time, 'datetime'); } ?>
			</span>
		</td>
	</tr>
	<?php if($userban->reason_public) : ?>
	<tr class="ksectiontableentry2">
		<td colspan="2"><b><?php echo JText::_('COM_KUNENA_BAN_PUBLICREASON'); ?></b> :</td>
		<td colspan="4"><?php echo KunenaParser::parseText ($userban->reason_public); ?></td>
	</tr>
	<?php endif; ?>
	<?php if($userban->reason_private) : ?>
	<tr class="ksectiontableentry2">
		<td colspan="2"><b><?php echo JText::_('COM_KUNENA_BAN_PRIVATEREASON'); ?></b> :</td>
		<td colspan="4"><?php echo KunenaParser::parseText ($userban->reason_private); ?></td>
	</tr>
	<?php endif; ?>
	<?php if (is_array($userban->comments)) foreach ($userban->comments as $comment) : ?>
	<tr class="ksectiontableentry2">
		<td colspan="2"><b><?php echo JText::sprintf('COM_KUNENA_BAN_COMMENT_BY', CKunenaLink::GetProfileLink ( intval($comment->userid) )); ?></b> :</td>
		<td colspan="1"><?php echo CKunenaTimeFormat::showDate($comment->time); ?></td>
		<td colspan="3"><?php echo KunenaParser::parseText ($comment->comment); ?></td>
	</tr>
	<?php endforeach; ?>
	<?php endforeach; ?>
<?php else : ?>
	<tr class="ksectiontableentry1">
		<td colspan="6"><?php echo JText::sprintf('COM_KUNENA_BAN_USER_NOHISTORY', $this->escape($this->profile->name)); ?></td>
	</tr>
<?php endif; ?>
</tbody>
</table>
</div>