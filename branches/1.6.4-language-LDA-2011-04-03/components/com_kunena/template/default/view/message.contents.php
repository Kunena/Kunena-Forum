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
?>

<div class="kmsgbody">
	<div class="kmsgtext">
		<?php echo $this->messageHtml ?>
	</div>
</div>
<?php $this->displayAttachments() ?>
<?php if ( $this->message_quickreply ) : ?>
<div id="kreply<?php echo intval($this->id) ?>_form" class="kreply-form" style="display: none">
	<form action="<?php echo CKunenaLink::GetPostURL(); ?>" method="post" name="postform" enctype="multipart/form-data">
		<input type="hidden" name="parentid" value="<?php echo intval($this->id) ?>" />
		<input type="hidden" name="catid" value="<?php echo intval($this->catid) ?>" />
		<input type="hidden" name="action" value="post" />
		<?php echo JHTML::_( 'form.token' ) ?>
		<?php if ($this->allow_anonymous): ?>
		<input type="text" name="authorname" size="35" class="kinputbox postinput" maxlength="35" value="<?php echo $this->escape($this->myname) ?>" /><br />
		<input type="checkbox" id="kanonymous<?php echo intval($this->id) ?>" name="anonymous" value="1" class="kinputbox postinput" <?php if ($this->anonymous) echo 'checked="checked"'; ?> /> <label for="kanonymous<?php echo intval($this->id) ?>"><?php echo JText::_('COM_KUNENA_POST_AS_ANONYMOUS_DESC') ?></label><br />
		<?php else: ?>
		<input type="hidden" name="authorname" value="<?php echo $this->escape($this->myname) ?>" />
		<?php endif; ?>
		<input type="text" name="subject" size="35" class="inputbox" maxlength="<?php echo intval($this->config->maxsubject); ?>" value="<?php echo  $this->escape($this->resubject) ?>" /><br />
		<textarea class="inputbox" name="message" rows="6" cols="60"></textarea><br />
		<?php if ($this->my->id && $this->config->allowsubscriptions && $this->cansubscribe) : ?>
			<?php if ($this->config->subscriptionschecked == 1) : ?>
				<input type="checkbox" name="subscribeMe" value="1" checked="checked" />
				<i><?php echo JText::_('COM_KUNENA_POST_NOTIFIED'); ?></i>
				<?php else : ?>
				<input type="checkbox" name="subscribeMe" value="1" />
				<i><?php echo JText::_('COM_KUNENA_POST_NOTIFIED'); ?></i>
			<?php endif; ?><br />
		<?php endif; ?>
		<input type="submit" class="kbutton kreply-submit" name="submit" value="<?php echo JText::_('COM_KUNENA_GEN_CONTINUE') ?>" title="<?php echo (JText::_('COM_KUNENA_EDITOR_HELPLINE_SUBMIT'));?>" />
		<input type="reset" class="kbutton kreply-cancel" name="cancel" value="<?php echo JText::_('COM_KUNENA_CANCEL') ?>" title="<?php echo (JText::_('COM_KUNENA_EDITOR_HELPLINE_CANCEL'));?>" />
		<small><?php echo JText::_('COM_KUNENA_QMESSAGE_NOTE') ?></small>
	</form>
</div>
<?php endif ?>