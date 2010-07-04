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
<div class="kmsgtitle kleft">
	<span class="kmsgtitle<?php echo $this->escape($this->msgsuffix) ?>">
		<?php echo $this->subjectHtml ?>
	</span>
	<span class="kmsgdate" title="<?php echo CKunenaTimeformat::showDate($this->msg->time, 'config_post_dateformat_hover') ?>">
		<?php echo CKunenaTimeformat::showDate($this->msg->time, 'config_post_dateformat') ?>
	</span>
	<span class="kmsgkarma">
		<?php echo $this->userkarma ?>
	</span>
</div>
<div class="kmsgbody">
	<div class="kmsgtext">
		<?php echo $this->messageHtml ?>
	</div>
</div>
<?php $this->displayAttachments() ?>
<div>
	<?php if ($this->signatureHtml) : ?>
	<div class="kmsgsignature">
		<?php echo $this->signatureHtml ?>
	</div>
	<?php endif ?>
</div>
<?php if ( $this->message_quickreply ) : ?>
<div id="kreply<?php echo intval($this->id) ?>_form" class="kreply-form" style="display: none">
	<form action="<?php echo CKunenaLink::GetPostURL(); ?>" method="post" name="postform" enctype="multipart/form-data">
		<input type="hidden" name="parentid" value="<?php echo intval($this->id) ?>" />
		<input type="hidden" name="catid" value="<?php echo intval($this->catid) ?>" />
		<input type="hidden" name="action" value="post" />
		<?php echo JHTML::_( 'form.token' ) ?>
		<?php if ($this->allow_anonymous): ?>
		<input type="text" id="kauthorname" name="authorname" size="35" class="kinputbox postinput" maxlength="35" value="<?php echo $this->escape($this->myname) ?>" /><br />
		<input type="checkbox" id="kanonymous" name="anonymous" value="1" class="kinputbox postinput" <?php if ($this->anonymous) echo 'checked="checked"'; ?> /> <label for="kanonymous"><?php echo JText::_('COM_KUNENA_POST_AS_ANONYMOUS_DESC') ?></label><br />
		<?php else: ?>
		<input type="hidden" name="authorname"  value="<?php echo $this->escape($this->myname) ?>" />
		<?php endif; ?>
		<input type="text" name="subject" size="35" class="inputbox" maxlength="<?php echo intval($this->config->maxsubject); ?>" value="<?php echo  $this->escape($this->resubject) ?>" /><br />
		<textarea class="inputbox" name="message" rows="6" cols="60"></textarea><br />
		<input type="reset" class="kbutton kreply-cancel" name="cancel" value="<?php echo JText::_('COM_KUNENA_CANCEL') ?>" />
		<input type="submit" class="kbutton kreply-submit" name="submit" value="<?php echo JText::_('COM_KUNENA_GEN_CONTINUE') ?>" />
		<small><?php echo JText::_('COM_KUNENA_QMESSAGE_NOTE') ?></small>
	</form>
</div>
<?php endif ?>