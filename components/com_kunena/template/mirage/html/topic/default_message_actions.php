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
<div class="innerblock-wrapper">
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
	<div id="message-buttonbar" class="buttonbar innverblock">
		<ul class="message-buttons">
			<?php if (empty($this->message_closed)) : ?>
			<!-- User buttons  -->
			<?php if (!empty($this->message_quickreply)) : ?><li class="button message-quickreply"><dd class="buttonbox-hover"><?php echo $this->message_quickreply ?></dd></li><?php endif ?>
			<?php if (!empty($this->message_reply)) : ?><li class="button message-reply"><dd class="buttonbox-hover"><?php echo $this->message_reply ?></dd></li><?php endif ?>
			<?php if (!empty($this->message_quote)) : ?><li class="button message-quote"><dd class="buttonbox-hover"><?php echo $this->message_quote ?></dd></li><?php endif ?>
			<?php if (!empty($this->message_thankyou)) : ?><li class="button message-thankyou"><dd class="buttonbox-hover"><?php echo $this->message_thankyou ?></dd></li><?php endif ?>
			<?php if (!empty($this->message_report)) : ?><li class="button message-report"><dd class="buttonbox-hover"><?php echo $this->message_report ?></dd></li><?php endif ?>
			<?php if (!empty($this->message_edit)) : ?><li class="button message-edit"><dd class="buttonbox-hover"><?php echo $this->message_edit ?></dd></li><?php endif ?>
			<!-- Moderator buttons  -->
			<?php if (!empty($this->message_moderate)) : ?><li class="button message-moderate"><dd class="buttonbox-hover"><?php echo $this->message_moderate ?></dd></li><?php endif ?>
			<?php if (!empty($this->message_delete)) : ?><li class="button message-delete"><dd class="buttonbox-hover"><?php echo $this->message_delete ?></dd></li><?php endif ?>
			<?php if (!empty($this->message_undelete)) : ?><li class="button message-undelete"><dd class="buttonbox-hover"><?php echo $this->message_undelete ?></dd></li><?php endif ?>
			<?php if (!empty($this->message_permdelete)) : ?><li class="button message-permdelete"><dd class="buttonbox-hover"><?php echo $this->message_permdelete ?></dd></li><?php endif ?>
			<?php if (!empty($this->message_publish)) : ?><li class="button message-publish"><dd class="buttonbox-hover"><?php echo $this->message_publish ?></dd></li><?php endif ?>
			<?php else : ?>
			<li><?php echo $this->message_closed; ?></li>
			<?php endif ?>
		</ul>
	</div>
</div>