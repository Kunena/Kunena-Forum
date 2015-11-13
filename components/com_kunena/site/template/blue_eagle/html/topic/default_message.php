<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>

<div class="kmsgbody">
	<div class="kmsgtext">
		<?php echo KunenaHtmlParser::parseBBCode ($this->message->message, $this) ?>
	</div>
</div>
<div>
<?php if (!empty($this->attachments)) : ?>
<div class="kmsgattach">
	<?php echo JText::_('COM_KUNENA_ATTACHMENTS');?>
		<ul class="kfile-attach">
		<?php foreach($this->attachments as $attachment) : ?>
			<li>
				<?php echo $attachment->getThumbnailLink(); ?>
				<span>
					<?php echo $attachment->getTextLink(); ?>
				</span>
			</li>
		<?php endforeach; ?>
		</ul>
	</div>
</div>
<?php elseif ($this->attachs->total > 0  && !$this->me->exists()):
	if ($this->attachs->image > 0  && !$this->config->showimgforguest)
	{
		if ( $this->attachs->image > 1 )
		{
			echo KunenaLayout::factory('BBCode/Image')->set('title', JText::_('COM_KUNENA_SHOWIMGFORGUEST_HIDEIMG_MULTIPLES'))->setLayout('unauthorised');
		}
		else
		{
			echo KunenaLayout::factory('BBCode/Image')->set('title', JText::_('COM_KUNENA_SHOWIMGFORGUEST_HIDEIMG_SIMPLE'))->setLayout('unauthorised');
		}
	}

	if ($this->attachs->file > 0 && !$this->config->showfileforguest)
	{
		if ( $this->attachs->file > 1)
		{
			echo KunenaLayout::factory('BBCode/Image')->set('title', JText::_('COM_KUNENA_SHOWIMGFORGUEST_HIDEFILE_MULTIPLES'))->setLayout('unauthorised');
		}
		else
		{
			echo KunenaLayout::factory('BBCode/Image')->set('title', JText::_('COM_KUNENA_SHOWIMGFORGUEST_HIDEFILE_SIMPLE'))->setLayout('unauthorised');
		}
	}
endif; ?>
<?php if ( $this->quickreply ) : ?>
<div id="kreply<?php echo intval($this->message->id) ?>_form" class="kreply-form" style="display: none">
	<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" name="postform" enctype="multipart/form-data">
		<input type="hidden" name="view" value="topic" />
		<input type="hidden" name="task" value="post" />
		<input type="hidden" name="parentid" value="<?php echo intval($this->message->id) ?>" />
		<input type="hidden" name="catid" value="<?php echo intval($this->category->id) ?>" />
		<?php echo JHtml::_( 'form.token' ) ?>

		<?php if ($this->me->exists() && $this->category->allow_anonymous): ?>
		<input type="text" name="authorname" size="35" class="kinputbox postinput" maxlength="35" value="<?php echo $this->escape($this->profile->getName()) ?>" /><br />
		<input type="checkbox" id="kanonymous<?php echo intval($this->message->id) ?>" name="anonymous" value="1" class="kinputbox postinput" <?php if ($this->category->post_anonymous) echo 'checked="checked"'; ?> /> <label for="kanonymous<?php echo intval($this->message->id) ?>"><?php echo JText::_('COM_KUNENA_POST_AS_ANONYMOUS_DESC') ?></label><br />
		<?php else: ?>
		<input type="hidden" name="authorname" value="<?php echo $this->escape($this->profile->getName()) ?>" />
		<?php endif; ?>
		<?php if ($this->config->askemail && !KunenaFactory::getUser()->id): ?>
			<input type="text" id="email" name="email" size="35" placeholder="<?php echo JText::_('COM_KUNENA_TOPIC_EDIT_PLACEHOLDER_EMAIL') ?>" class="inputbox" maxlength="35" value="" required />
			<?php echo $this->config->showemail == '0' ? JText::_('COM_KUNENA_POST_EMAIL_NEVER') : JText::_('COM_KUNENA_POST_EMAIL_REGISTERED'); ?>
		<?php endif; ?>
		<input type="text" name="subject" size="35" class="inputbox" maxlength="<?php echo intval($this->config->maxsubject); ?>" value="<?php echo  $this->escape($this->message->subject) ?>" /><br />
		<textarea class="inputbox" name="message" rows="6" cols="60"></textarea><br />
		<?php if ($this->topic->authorise('subscribe') && !$this->usertopic->subscribed) : ?>
		<?php if ( !$this->usertopic->subscribed ): ?>
		<input style="float: left; margin-right: 10px;" type="checkbox" name="subscribeMe" id="subscribeMe" value="1" <?php if ($this->config->subscriptionschecked == 1 && $this->me->canSubscribe != 0 || $this->config->subscriptionschecked == 0 && $this->me->canSubscribe == 1 )
		{
			echo 'checked="checked"';
		} ?> />
		<?php endif; ?>
		<i><?php echo JText::_('COM_KUNENA_POST_NOTIFIED'); ?></i>
		<br />
		<?php endif; ?>
		<?php if (!$this->config->allow_change_subject): ?>
			 <input type="hidden" name="subject" value="<?php echo $this->escape($this->message->subject); ?>" />
		<?php endif; ?>
		<input type="submit" class="kbutton kreply-submit" name="submit" value="<?php echo JText::_('COM_KUNENA_SUBMIT') ?>" title="<?php echo (JText::_('COM_KUNENA_EDITOR_HELPLINE_SUBMIT'));?>" />
		<input type="reset" class="kbutton kreply-cancel" name="cancel" value="<?php echo JText::_('COM_KUNENA_CANCEL') ?>" title="<?php echo (JText::_('COM_KUNENA_EDITOR_HELPLINE_CANCEL'));?>" />
		<small><?php echo JText::_('COM_KUNENA_QMESSAGE_NOTE') ?></small>
	</form>
</div>
<?php endif ?>
