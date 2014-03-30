<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$dateshown = $datehover = '';
if ($this->message->modified_time) {
	$datehover = 'title="'.KunenaDate::getInstance($this->message->modified_time)->toKunena('config_post_dateformat_hover').'"';
	$dateshown = KunenaDate::getInstance($this->message->modified_time)->toKunena('config_post_dateformat' ).' ';
}
?>
<div>
	<?php if ($this->signatureHtml) : ?>
	<div class="kmsgsignature">
		<?php echo $this->signatureHtml ?>
	</div>
	<?php endif ?>
</div>
<div class="kmessage-editmarkup-cover hidden-phone">
	<?php if ($this->message->modified_by && $this->config->editmarkup) : ?>
	<span class="kmessage-editmarkup hidden-phone" <?php echo $datehover ?>>
		<?php echo JText::_('COM_KUNENA_EDITING_LASTEDIT') . ': ' . $dateshown . JText::_('COM_KUNENA_BY') . ' ' . $this->message->getModifier()->getLink() . '.'; ?>
		<?php if ($this->message->modified_reason) echo JText::_('COM_KUNENA_REASON') . ': ' . $this->escape ( $this->message->modified_reason ); ?>
	</span>
	<?php endif ?>
	<?php if (!empty($this->reportMessageLink)) :?>
	<span class="kmessage-informmarkup hidden-phone"><?php echo $this->reportMessageLink ?></span>
	<?php if (!empty($this->ipLink)) : ?>
	<span class="kmessage-informmarkup hidden-phone"><?php echo $this->ipLink ?></span>
	<?php endif ?>
	<?php endif ?>
</div>
<div class="kmessage-buttons-cover">
	<div class="kmessage-buttons-row">
	<?php if (empty($this->message_closed)) : ?>
		<?php echo $this->messageButtons->get('quickreply'); ?>
		<?php echo $this->messageButtons->get('reply'); ?>
		<?php echo $this->messageButtons->get('quote'); ?>
		<?php echo $this->messageButtons->get('edit'); ?>
		<?php echo $this->messageButtons->get('moderate'); ?>
		<?php echo $this->messageButtons->get('delete'); ?>
		<?php echo $this->messageButtons->get('permdelete'); ?>
		<?php echo $this->messageButtons->get('undelete'); ?>
		<?php echo $this->messageButtons->get('publish'); ?>
	<?php else : ?>
		<?php echo $this->message_closed; ?>
		<?php if( !$this->topic->locked ) : ?>
			<?php echo $this->messageButtons->get('edit'); ?>
			<?php echo $this->messageButtons->get('moderate'); ?>
			<?php echo $this->messageButtons->get('delete'); ?>
			<?php echo $this->messageButtons->get('permdelete'); ?>
			<?php echo $this->messageButtons->get('undelete'); ?>
			<?php echo $this->messageButtons->get('publish'); ?>
		<?php endif ?>
	<?php endif ?>
	</div>
</div>
<?php if($this->messageButtons->get('thankyou')): ?>
<div class="kpost-thankyou">
	<?php echo $this->messageButtons->get('thankyou'); ?>
</div>
<?php endif; ?>
<?php if(!empty($this->thankyou)): ?>
<div class="kmessage-thankyou">
<?php
	echo JText::_('COM_KUNENA_THANKYOU').': '.implode(', ', $this->thankyou).' ';
	if ($this->more_thankyou) echo JText::sprintf('COM_KUNENA_THANKYOU_MORE_USERS',$this->more_thankyou);
?>
</div>
<?php endif; ?>
