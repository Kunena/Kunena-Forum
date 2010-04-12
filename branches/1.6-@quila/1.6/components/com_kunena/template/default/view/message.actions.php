<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();
?>
<div class="kmessage_editMarkUp_cover">
	<?php if ($this->msg->modified_by) : ?>
	<span class="kmessage_editMarkUp" title="<?php echo CKunenaTimeformat::showDate($this->msg->modified_time, 'config_post_dateformat_hover') ?>">
		<?php echo JText::_('COM_KUNENA_EDITING_LASTEDIT') . ': ' . CKunenaTimeformat::showDate($this->msg->modified_time, 'config_post_dateformat' ) . ' '
		. JText::_('COM_KUNENA_BY') . ' ' . ($this->config->username ? $this->msg->modified_username : $this->msg->modified_name) . '.'; ?>
	<?php if ($this->msg->modified_reason) echo JText::_('COM_KUNENA_REASON') . ': ' . $this->escape ( stripslashes ( $this->msg->modified_reason ) ); ?>
	</span>
	<?php endif ?>
	<?php if ($this->config->reportmsg && $this->my->id) :?>
	<span class="kmessage_informMarkUp"><?php echo CKunenaLink::GetReportMessageLink ( $this->catid, $this->id, JText::_('COM_KUNENA_REPORT') ) ?></span>
	<?php endif ?>
	<?php if (!empty ( $this->ipLink )) : ?>
	<span class="kmessage_informMarkUp"><?php echo $this->ipLink ?></span>
	<?php endif ?>
</div>
<div class="kmessage_buttons_cover">
	<div class="kmessage_buttons_row">
			<?php if (empty( $this->message_closed )) : ?>
				<?php echo $this->message_quickreply; ?>
				<?php echo $this->message_reply; ?>
				<?php echo $this->message_quote; ?>
				<?php echo $this->message_edit; ?>
				<?php echo $this->message_merge; ?>
				<?php echo $this->message_split; ?>
				<?php echo $this->message_delete; ?>
				<?php echo $this->message_move; ?>
				<?php echo $this->message_publish; ?>
			<?php else : ?>
				<?php echo $this->message_closed; ?>
			<?php endif ?>
	</div>
</div>