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
<div class="kunena-msgtitle left">
	<span class="msgtitle<?php echo $this->msgsuffix ?>">
		<?php echo $this->subject ?>
	</span>
	<span class="msgdate" title="<?php echo CKunenaTimeformat::showDate($this->msg->time, 'config_post_dateformat_hover') ?>">
		<?php echo CKunenaTimeformat::showDate($this->msg->time, 'config_post_dateformat') ?>
	</span>
	<span class="msgkarma">
		<?php echo $this->userkarma ?>
	</span>
</div>
<div>
	<div class="msgtext">
		<?php echo $this->message ?>
	</div>
</div>
<?php $this->displayAttachments() ?>
<div>
	<?php if ($this->signature) : ?>
	<div class="msgsignature">
		<?php echo $this->signature ?>
	</div>
	<?php endif ?>
</div>
<?php if ( $this->message_quickreply ) : ?>
<div id="kreply<?php echo $this->id ?>_form" class="kreply_form" style="display: none">
	<?php
	?>
	<form action="<?php echo CKunenaLink::GetPostURL(); ?>" method="post" name="postform" enctype="multipart/form-data">
		<input type="hidden" name="parentid" value="<?php echo $this->id ?>" />
		<input type="hidden" name="catid" value="<?php echo $this->catid ?>" />
		<input type="hidden" name="action" value="post" />
		<?php echo JHTML::_( 'form.token' ) ?>
		<?php if ($this->allow_anonymous): ?>
		<input type="text" id="kauthorname" name="authorname" size="35" class="kinputbox postinput" maxlength="35" value="<?php echo $this->myname ?>" /><br />
		<input type="checkbox" id="kanonymous" name="anonymous" value="1" class="kinputbox postinput" <?php if ($this->anonymous) echo 'checked="checked"'; ?> /> <label for="kanonymous"><?php echo JText::_('COM_KUNENA_POST_AS_ANONYMOUS_DESC') ?></label><br />
		<?php else: ?>
		<input type="hidden" name="authorname"  value="<?php echo $this->myname ?>" />
		<?php endif; ?>
		<input type="text" name="subject" size="35" class="inputbox" maxlength="<?php echo $this->config->maxsubject; ?>" value="<?php echo  $this->escape($this->resubject) ?>" /><br />
		<textarea class="inputbox" name="message" rows="6" cols="60"></textarea><br />
		<input type="reset" class="kbutton kreply_cancel" name="cancel" value="<?php echo JText::_('COM_KUNENA_CANCEL') ?>" />
		<input type="submit" class="kbutton kreply_submit" name="submit" value="<?php echo JText::_('COM_KUNENA_GEN_CONTINUE') ?>" />
		<small><?php echo JText::_('COM_KUNENA_QMESSAGE_NOTE') ?></small>
	</form>
</div>
<?php endif ?>
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