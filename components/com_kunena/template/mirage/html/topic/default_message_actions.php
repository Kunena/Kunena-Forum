<?php
/**
 * Kunena Component
 * @package Kunena.Template.Default20
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="innerbox-wrapper innerspacer">
	<?php if(!empty($this->thankyou)): ?>
		<div class="buttonbar" >
			<ul class="message-buttons innerblock">
			<?php
			echo JText::_('COM_KUNENA_THANKYOU').': ';
			echo implode(', ', $this->thankyou);
			if (count($this->thankyou) > 9) echo '...';
			?>
			</ul>
		</div>
	<?php endif ?>
	<div id="message-buttonbar" class="buttonbar innerblock">
		<ul class="message-buttons">
			<?php if (empty($this->message_closed)) : ?>
			<!-- User buttons  -->
			<?php if (!empty($this->message_quickreply)) : ?><li class="button message-quickreply"><?php echo $this->message_quickreply ?></li><?php endif ?>
			<?php if (!empty($this->message_reply)) : ?><li class="button message-reply"><?php echo $this->message_reply ?></li><?php endif ?>
			<?php if (!empty($this->message_quote)) : ?><li class="button message-quote"><?php echo $this->message_quote ?></li><?php endif ?>
			<?php if (!empty($this->message_thankyou)) : ?><li class="button message-thankyou"><?php echo $this->message_thankyou ?></li><?php endif ?>
			<?php if (!empty($this->message_report)) : ?><li class="button message-report"><?php echo $this->message_report ?></li><?php endif ?>
			<?php if (!empty($this->message_edit)) : ?><li class="button message-edit"><?php echo $this->message_edit ?></li><?php endif ?>
			<!-- Moderator buttons  -->
			<?php if (!empty($this->message_moderate)) : ?><li class="button message-moderate"><?php echo $this->message_moderate ?></li><?php endif ?>
			<?php if (!empty($this->message_delete)) : ?><li class="button message-delete"><?php echo $this->message_delete ?></li><?php endif ?>
			<?php if (!empty($this->message_undelete)) : ?><li class="button message-undelete"><?php echo $this->message_undelete ?></li><?php endif ?>
			<?php if (!empty($this->message_permdelete)) : ?><li class="button message-permdelete"><?php echo $this->message_permdelete ?></li><?php endif ?>
			<?php if (!empty($this->message_publish)) : ?><li class="button message-publish"><?php echo $this->message_publish ?></li><?php endif ?>
			<?php else : ?>
			<li><?php echo $this->message_closed; ?></li>
			<?php endif ?>
		</ul>
	</div>
</div>