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

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();
$dateshown = $datehover = '';
if ($this->msg->modified_time) {
	$datehover = 'title="'.CKunenaTimeformat::showDate($this->msg->modified_time, 'config_post_dateformat_hover').'"';
	$dateshown =  CKunenaTimeformat::showDate($this->msg->modified_time, 'config_post_dateformat' ).' ';
}
?>
<div>
	<?php if ($this->signatureHtml) : ?>
	<div class="kmsgsignature">
		<?php echo $this->signatureHtml ?>
	</div>
	<?php endif ?>
</div>
<div class="kmessage-editmarkup-cover">
	<?php if ($this->msg->modified_by && $this->config->editmarkup) : ?>
	<span class="kmessage-editmarkup" <?php echo $datehover ?>>
		<?php echo JText::_('COM_KUNENA_EDITING_LASTEDIT') . ': ' . $dateshown . JText::_('COM_KUNENA_BY') . ' ' . ($this->config->username ? $this->escape ( $this->msg->modified_username ) : $this->escape ( $this->msg->modified_name ) ) . '.'; ?>
		<?php if ($this->msg->modified_reason) echo JText::_('COM_KUNENA_REASON') . ': ' . $this->escape ( $this->msg->modified_reason ); ?>
	</span>
	<?php endif ?>
	<?php if ($this->config->reportmsg && $this->my->id) :?>
	<span class="kmessage-informmarkup"><?php echo CKunenaLink::GetReportMessageLink ( intval($this->catid), intval($this->id), JText::_('COM_KUNENA_REPORT') ) ?></span>
	<?php endif ?>
	<?php if (!empty ( $this->ipLink )) : ?>
	<span class="kmessage-informmarkup"><?php echo $this->ipLink ?></span>
	<?php endif ?>
</div>
<div class="kmessage-buttons-cover">
	<div class="kmessage-buttons-row">
	<?php if (empty( $this->message_closed )) : ?>
		<?php echo $this->message_quickreply; ?>
		<?php echo $this->message_reply; ?>
		<?php echo $this->message_quote; ?>
		<?php echo $this->message_edit; ?>
		<?php echo $this->message_moderate; ?>
		<?php echo $this->message_move; ?>
		<?php echo $this->message_merge; ?>
		<?php echo $this->message_split; ?>
		<?php echo $this->message_delete; ?>
		<?php echo $this->message_permdelete; ?>
		<?php echo $this->message_undelete; ?>
		<?php echo $this->message_publish; ?>
	<?php else : ?>
		<?php echo $this->message_closed; ?>
	<?php endif ?>
	</div>
</div>