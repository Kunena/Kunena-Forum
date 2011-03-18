<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
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
					<td class="kcol-first kid">
						<?php echo $j--; ?>
					</td>
					<td class="kcol-mid  kbanfrom">
						<span><?php echo $userban->blocked ? JText::_('COM_KUNENA_BAN_BANLEVEL_JOOMLA') : JText::_('COM_KUNENA_BAN_BANLEVEL_KUNENA') ?></span>
					</td>
					<td class="kcol-mid kbanstart">
						<span><?php  if( $userban->created_time ) echo CKunenaTimeFormat::showDate($userban->created_time, 'datetime'); ?></span>
					</td>
					<td class="kcol-mid kbanexpire">
						<span><?php echo $userban->isLifetime() ? JText::_('COM_KUNENA_BAN_LIFETIME') : CKunenaTimeFormat::showDate($userban->expiration, 'datetime'); ?></span>
					</td>
					<td class="kcol-mid kbancreate">
						<span><?php echo CKunenaLink::GetProfileLink ( intval($userban->created_by) ); ?></span>
					</td>
					<td class="kcol-mid kbanmodify">
						<?php if ( $userban->modified_by && $userban->modified_time) { ?>
						<span>
							<?php echo CKunenaLink::GetProfileLink ( intval($userban->modified_by) ); ?>
							<?php echo CKunenaTimeFormat::showDate($userban->modified_time, 'datetime'); } ?>
						</span>
					</td>
				</tr>
				<?php if($userban->reason_public) : ?>
				<tr class="krow2">
					<td colspan="2" class="kcol-first kpublic-reason-label"><b><?php echo JText::_('COM_KUNENA_BAN_PUBLICREASON'); ?></b> :</td>
					<td colspan="4" class="kcol-mid  kpublic-reason-field"><?php echo KunenaParser::parseText ($userban->reason_public); ?></td>
				</tr>
				<?php endif; ?>
				<?php if($userban->reason_private) : ?>
				<tr class="krow2">
					<td colspan="2" class="kcol-first kprivate-reason-label"><b><?php echo JText::_('COM_KUNENA_BAN_PRIVATEREASON'); ?></b> :</td>
					<td colspan="4" class="kcol-mid kprivate-reason-field"><?php echo KunenaParser::parseText ($userban->reason_private); ?></td>
				</tr>
				<?php endif; ?>
				<?php if (is_array($userban->comments)) foreach ($userban->comments as $comment) : ?>
				<tr class="krow2">
					<td colspan="2" class="kcol-first kcommentby-label"><b><?php echo JText::sprintf('COM_KUNENA_BAN_COMMENT_BY', CKunenaLink::GetProfileLink ( intval($comment->userid) )); ?></b> :</td>
					<td colspan="1" class="kcol-mid kcommenttime-field"><?php echo CKunenaTimeFormat::showDate($comment->time); ?></td>
					<td colspan="3" class="kcol-mid kcomment-field"><?php echo KunenaParser::parseText ($comment->comment); ?></td>
				</tr>
				<?php endforeach; ?>
				<?php endforeach; ?>
				<?php else : ?>
				<tr class="krow1">
					<td colspan="6" class="kcol-first"><?php echo JText::sprintf('COM_KUNENA_BAN_USER_NOHISTORY', $this->escape($this->profile->name)); ?></td>
				</tr>
				<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>