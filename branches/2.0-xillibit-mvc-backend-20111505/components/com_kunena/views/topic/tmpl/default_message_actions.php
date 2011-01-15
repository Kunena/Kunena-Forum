<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 *
 **/
defined ( '_JEXEC' ) or die ();

$dateshown = $datehover = '';
if ($this->message->modified_time) {
	$datehover = 'title="'.CKunenaTimeformat::showDate($this->message->modified_time, 'config_post_dateformat_hover').'"';
	$dateshown =  CKunenaTimeformat::showDate($this->message->modified_time, 'config_post_dateformat' ).' ';
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
	<?php if ($this->message->modified_by && $this->config->editmarkup) : ?>
	<span class="kmessage-editmarkup" <?php echo $datehover ?>>
		<?php echo JText::_('COM_KUNENA_EDITING_LASTEDIT') . ': ' . $dateshown . JText::_('COM_KUNENA_BY') . ' ' . CKunenaLink::getProfileLink( $this->message->userid ) . '.'; ?>
		<?php if ($this->message->modified_reason) echo JText::_('COM_KUNENA_REASON') . ': ' . $this->escape ( $this->message->modified_reason ); ?>
	</span>
	<?php endif ?>
	<?php if ($this->config->reportmsg && KunenaFactory::getUser()->exists()) :?>
	<span class="kmessage-informmarkup"><?php echo CKunenaLink::GetReportMessageLink ( intval($this->category->id), intval($this->message->id), JText::_('COM_KUNENA_REPORT') ) ?></span>
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
		<?php echo $this->message_delete; ?>
		<?php echo $this->message_permdelete; ?>
		<?php echo $this->message_undelete; ?>
		<?php echo $this->message_publish; ?>
	<?php else : ?>
		<?php echo $this->message_closed; ?>
	<?php endif ?>
	</div>
</div>
<?php if(!empty($this->message_thankyou)): ?>
<div class="kpost-thankyou">
	<?php echo $this->message_thankyou; ?>
</div>
<?php endif; ?>
<?php if(!empty($this->thankyou)): ?>
<div class="kmessage-thankyou">
<?php
	echo JText::_('COM_KUNENA_THANKYOU').': ';
	echo implode(', ', $this->thankyou);
	if (count($this->thankyou) > 9) echo '...';
?>
</div>
<?php endif; ?>