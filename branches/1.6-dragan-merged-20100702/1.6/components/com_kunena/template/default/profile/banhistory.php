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

<div class="kblock banhistory">
	<div class="kheader">
		<h2><span><?php echo JText::sprintf('COM_KUNENA_BAN_BANHISTORYFOR', $this->escape($this->profile->name)); ?></span></h2>
	</div>
	<div class="kcontainer">
		<div class="kbody">
			<table class="<?php echo isset ( $this->objCatInfo->class_sfx ) ? ' kblocktable' . $this->escape($this->objCatInfo->class_sfx) : ''; ?> kblock-ban">
				<thead>
					<tr class="ksth">
						<th class="kid"> # </th>
						<th class="kbanfrom"><?php echo JText::_('COM_KUNENA_BAN_BANNEDFROM'); ?></th>
						<th class="kbanstart"><?php echo JText::_('COM_KUNENA_BAN_STARTTIME'); ?></th>
						<th class="kbanexpire"><?php echo JText::_('COM_KUNENA_BAN_EXPIRETIME'); ?></th>
						<th class="kbancreate"><?php echo JText::_('COM_KUNENA_BAN_CREATEDBY'); ?></th>
						<th class="kbanmodify"><?php echo JText::_('COM_KUNENA_BAN_MODIFIEDBY'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php
					if ( !empty($this->banhistory) ) :
						foreach ($this->banhistory as $userban) :
				?>
				<tr class="krow1">
					<td class="ktd-kcol-first">
						<?php echo $j--; ?>
					</td>
					<td class="ktd-kcol-other">
						<span><?php echo $userban->blocked ? JText::_('COM_KUNENA_BAN_BANLEVEL_JOOMLA') : JText::_('COM_KUNENA_BAN_BANLEVEL_KUNENA') ?></span>
					</td>
					<td class="ktd-kcol-other">
						<span><?php  if( $userban->created_time ) echo CKunenaTimeFormat::showDate($userban->created_time, 'datetime'); ?></span>
					</td>
					<td class="ktd-kcol-other">
						<span><?php echo $userban->isLifetime() ? JText::_('COM_KUNENA_BAN_LIFETIME') : CKunenaTimeFormat::showDate($userban->expiration, 'datetime'); ?></span>
					</td>
					<td class="ktd-kcol-other">
						<span><?php echo CKunenaLink::GetProfileLink ( intval($userban->created_by) ); ?></span>
					</td>
					<td class="ktd-kcol-other">
						<?php if ( $userban->modified_by && $userban->modified_time) { ?>
						<span>
							<?php echo CKunenaLink::GetProfileLink ( intval($userban->modified_by) ); ?>
							<?php echo CKunenaTimeFormat::showDate($userban->modified_time, 'datetime'); } ?>
						</span>
					</td>
				</tr>
				<?php if($userban->reason_public) : ?>
				<tr class="krow2">
					<td colspan="2" class="ktd-kcol-first"><b><?php echo JText::_('COM_KUNENA_BAN_PUBLICREASON'); ?></b> :</td>
					<td colspan="4" class="ktd-kcol-other"><?php echo KunenaParser::parseText ($userban->reason_public); ?></td>
				</tr>
				<?php endif; ?>
				<?php if($userban->reason_private) : ?>
				<tr class="krow2">
					<td colspan="2" class="ktd-kcol-first"><b><?php echo JText::_('COM_KUNENA_BAN_PRIVATEREASON'); ?></b> :</td>
					<td colspan="4" class="ktd-kcol-other"><?php echo KunenaParser::parseText ($userban->reason_private); ?></td>
				</tr>
				<?php endif; ?>
				<?php if (is_array($userban->comments)) foreach ($userban->comments as $comment) : ?>
				<tr class="krow2">
					<td colspan="2" class="ktd-kcol-first"><b><?php echo JText::sprintf('COM_KUNENA_BAN_COMMENT_BY', CKunenaLink::GetProfileLink ( intval($comment->userid) )); ?></b> :</td>
					<td colspan="1" class="ktd-kcol-other"><?php echo CKunenaTimeFormat::showDate($comment->time); ?></td>
					<td colspan="3" class="ktd-kcol-other"><?php echo KunenaParser::parseText ($comment->comment); ?></td>
				</tr>
				<?php endforeach; ?>
				<?php endforeach; ?>
				<?php else : ?>
				<tr class="krow1 ktd-kcol-first">
					<td colspan="6"><?php echo JText::sprintf('COM_KUNENA_BAN_USER_NOHISTORY', $this->escape($this->profile->name)); ?></td>
				</tr>
				<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>