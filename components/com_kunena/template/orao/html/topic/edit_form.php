<?php
/**
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

JHTML::_('behavior.formvalidation');
JHTML::_('behavior.tooltip');
// FIXME : Move style to css file
?>

<div id="tk-preview-message" class="tk-preview-message" style="margin-bottom:15px;">
<div style="background:#eee; padding:5px;">
<dl id="system-message" style="margin-bottom: 10px;margin-top: 5px;">
<dt class="message"><?php echo JText::_('COM_KUNENA_GEN_MESSAGE') ?></dt>
<dd class="message message fade">
	<ul>
		<li>
		<?php echo JText::_('COM_KUNENA_EDITOR_PREVIEW_NOT_PUBLISHED'); ?>
		</li>
	</ul>
</dd>
</dl>
	<div class="inner inner-odd">
		<span class=""><span></span></span>
			<ul class="topiclist">
				<li class="header">
					<dl class="icon">
						<dt>
						<span class="tk-preview-msgtitle" title="<?php echo JText::_('COM_KUNENA_MESSAGE_DATETIME'); ?>">
							<?php echo JText::_('COM_KUNENA_MESSAGE_DATETIME'); ?>
						</span>
						</dt>
						<dd class="topics" style="float:right;">
							<span style="white-space:nowrap;" class="tk-view-msgid" title="<?php echo JText::_('COM_KUNENA_MESSAGE_ID'); ?>"><?php echo $this->message->id ? $this->message->id : JText::_('COM_KUNENA_MESSAGE_ID'); ?></span>
						</dd>
					</dl>
				</li>
			</ul>
		<dl id="profilebox-post" class="postprofile-left" style="padding-top:10px;">

	<dt class="view-avatar">
	<span class="author"><?php echo $this->escape( $this->me->username )?></span><br /><br />
		<span class="kavatar">
			<?php echo $this->me->getLink($this->me->getAvatarImage('klist-avatar', 'post')) ?>
		</span>
	</dt>

		</dl>
			<div class="postbody tk-avleft" style="padding: 10px; width:77%">
				<div class="postbackground-left">
					<h3><?php echo JText::_('COM_KUNENA_MESSAGE_SUBJECT'); ?></h3>
				</div>
				<div class="tk-msgcontent" style="overflow: hidden;min-height:20px;">
					<div id="kbbcode-preview" style="display:none; border:0;overflow-x: hidden;overflow-y: auto;width:100%;background: none repeat scroll 0 0 transparent;margin-top: 0px;"></div>
				</div>
			</div>
			<div class="clr"></div>
		<span class=""><span></span></span>
	</div>
</div>
</div>

<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" enctype="multipart/form-data" name="postform" method="post" id="postform" class="postform form-validate">
	<input type="hidden" name="view" value="topic" />
	<?php if ($this->message->exists()) : ?>
	<input type="hidden" name="task" value="edit" />
	<input type="hidden" name="mesid" value="<?php echo intval($this->message->id) ?>" />
	<?php else: ?>
	<input type="hidden" name="task" value="post" />
	<input type="hidden" name="parentid" value="<?php echo intval($this->message->parent) ?>" />
	<?php endif; ?>
	<?php if (empty($this->selectcatlist)) : ?>
	<input type="hidden" name="catid" value="<?php echo intval($this->topic->category_id) ?>" />
	<?php endif; ?>
	<?php if ($this->catid && $this->catid != $this->message->catid) : ?>
	<input type="hidden" name="return" value="<?php echo intval($this->catid) ?>" />
	<?php endif; ?>
	<?php echo JHTML::_( 'form.token' ); ?>


<div class="forumlist">
	<div class="inner">
		<span class="corners-top"><span></span></span>
			<ul class="topiclist">
				<li class="header">
					<dl class="icon">
						<dt><?php echo $this->escape($this->title)?></dt>
						<dd>&nbsp;</dd>
					</dl>
				</li>
			</ul>

		<ul class="kform kpostmessage clearfix">

			<?php if (isset($this->selectcatlist)): ?>
			<li id="kpost-category" class="kpostmessage-row krow-odd">
				<div class="kform-label">
					<label for="kpostcatid"><?php echo JText::_('COM_KUNENA_POST_IN_CATEGORY')?></label>
				</div>
				<div class="kform-field">
					<?php echo $this->selectcatlist ?>
				</div>
			</li>
			<?php endif; ?>

			<?php if ($this->message->userid) : ?>
			<li style="display: none" id="kanynomous-check" class="kpostmessage-row krow-odd">
				<div class="kform-label">
					<label for="kanonymous">
						<?php echo JText::_('COM_KUNENA_POST_AS_ANONYMOUS'); ?>
					</label>
				</div>
				<div class="kform-field">
					<input type="checkbox" value="1" name="anonymous" id="kanonymous" class="hasTip" title="<?php echo JText::_('COM_KUNENA_POST_AS_ANONYMOUS') ?> :: <?php echo JText::_('COM_KUNENA_POST_AS_ANONYMOUS_CHECK') ?>" <?php if ($this->category->post_anonymous) echo 'checked="checked"'; ?> />
					<div class="kform-note"><?php echo JText::_('COM_KUNENA_POST_AS_ANONYMOUS_DESC'); ?></div>
				</div>
			</li>
			<?php endif; ?>

			<li style="display: none" id="kanynomous-check-name" class="kpostmessage-row krow-even">
				<div class="kform-label">
					<label for="kauthorname">
						<?php echo JText::_('COM_KUNENA_GEN_NAME'); ?>
					</label>
				</div>
				<div class="kform-field">
					<input type="text" value="<?php echo $this->escape($this->message->name) ?>" maxlength="35" class="kinputbox postinput required hasTip" size="35" name="authorname" id="kauthorname" disabled="disabled" title="<?php echo JText::_('COM_KUNENA_GEN_NAME') ?> :: <?php echo JText::_('COM_KUNENA_MESSAGE_ENTER_NAME') ?>" />
				</div>
			</li>

			<?php if ($this->config->askemail && !$this->me->exists()) : ?>
			<li id="kanynomous-email" class="kpostmessage-row krow-even">
				<div class="kform-label">
					<label for="kauthorname">
						<?php echo JText::_('COM_KUNENA_GEN_EMAIL'); ?>
					</label>
				</div>
				<div class="kform-field">
					<input type="text" value="<?php echo $this->escape($this->message->email) ?>" maxlength="35" class="kinputbox postinput required hasTip" size="35" name="password" id="kpassword" title="<?php echo JText::_('COM_KUNENA_GEN_EMAIL') ?> :: <?php echo JText::_('COM_KUNENA_MESSAGE_ENTER_EMAIL') ?>" />
				</div>
			</li>
			<?php endif; ?>

			<li id="kpost-subject" class="kpostmessage-row krow-odd">
				<div class="kform-label">
					<label for="ksubject">
						<?php echo JText::_('COM_KUNENA_GEN_SUBJECT'); ?>
					</label>
				</div>
				<div class="kform-field">
					<input type="text" value="<?php echo $this->escape($this->message->subject) ?>" maxlength="<?php echo $this->escape($this->config->maxsubject) ?>" size="35" id="ksubject" name="subject" class="kinputbox postinput required hasTip" title="<?php echo JText::_('COM_KUNENA_GEN_SUBJECT') ?> :: <?php echo JText::_('COM_KUNENA_ENTER_SUBJECT') ?>" />
				</div>
			</li>
			<?php if ($this->message->parent==0 && $this->config->topicicons) : ?>
			<li id="kpost-topicicons" class="kpostmessage-row krow-even">
				<div class="kform-label">
					<label for="topic_emoticon_default">
						<?php echo JText::_('COM_KUNENA_GEN_TOPIC_ICON'); ?>
					</label>
				</div>
				<div class="kform-field">
					<ul>
						<?php foreach ($this->topicIcons as $id=>$icon) : ?>
						<li class="hasTip" title="Topic icon :: <?php echo $this->escape(ucfirst($icon->name)) ?>">
							<input type="radio" name="topic_emoticon" id="topic_emoticon_<?php echo $this->escape($icon->name) ?>" value="<?php echo $icon->id ?>" <?php echo !empty($icon->checked) ? ' checked="checked" ':'' ?> />
							<label for="topic_emoticon_<?php echo $this->escape($icon->name) ?>"><img src="<?php echo $this->ktemplate->getTopicIconIndexPath($icon->id, true) ?>" alt="" border="0" /></label>
						</li>
						<?php endforeach ?>
					</ul>
				</div>
			</li>
			<?php endif ?>

			<?php echo $this->loadTemplateFile('editor'); ?>

			<li class="kpostmessage-row krow-odd">
				<div class="kform-label">
					<label for="kupload">
						<?php echo JText::_('COM_KUNENA_EDITOR_ATTACHMENTS') ?>
					</label>
				</div>
				<div class="kform-field">
					<div id="kattachment-id" class="kattachment">
						<span class="kattachment-id-container"></span>
						<input class="kfile-input-textbox" type="text" readonly="readonly" />
						<div class="kfile-hide hasTip" title="<?php echo JText::_('COM_KUNENA_FILE_EXTENSIONS_ALLOWED')?> :: <?php echo $this->escape($this->config->imagetypes); ?>,<?php echo $this->escape($this->config->filetypes) ?>" >
							<input type="button" value="<?php echo JText::_('COM_KUNENA_EDITOR_ADD_FILE')?>" class="kfile-input-button tk-add-button" />
							<input id="kupload" class="kfile-input hidden" name="kattachment" type="file" />
						</div>
						<a href="#" class="kattachment-remove tk-remove-button" style="display: none"><?php echo JText::_('COM_KUNENA_GEN_REMOVE_FILE') ?></a>
						<a href="#" class="kattachment-insert tk-insert-button" style="display: none"><?php echo JText::_('COM_KUNENA_EDITOR_INSERT') ?></a>
					</div>
					<?php $this->displayAttachments($this->message); ?>
				</div>
			</li>

			<?php if ($this->config->keywords && $this->me->isModerator ( $this->message->catid ) ) : ?>
			<li class="kpostmessage-row krow-even">
				<div class="kform-label">
					<label for="ktags">
						<?php echo JText::_('COM_KUNENA_EDITOR_TOPIC_TAGS') ?>
					</label>
				</div>
				<div class="kform-field">
					<input type="text" value="<?php echo $this->escape($this->topic->getKeywords(false, ', ')); ?>" maxlength="100" size="35" id="ktags" name="tags" class="kinputbox postinput hasTip" title="<?php echo JText::_('COM_KUNENA_EDITOR_TOPIC_TAGS') ?> :: <?php echo JText::_('COM_KUNENA_EDITOR_TOPIC_TAGS_ADD_COMMAS') ?>" />
				</div>
			</li>
			<?php endif; ?>

			<?php if ($this->config->userkeywords && $this->me->userid) : ?>
			<li class="kpostmessage-row krow-even">
				<div class="kform-label">
					<label for="kmytags">
						<?php echo JText::_('COM_KUNENA_EDITOR_TOPIC_TAGS_OWN') ?>
					</label>
				</div>
				<div class="kform-field">
					<input type="text" value="<?php echo $this->escape($this->topic->getKeywords($this->me->userid, ', ')); ?>" maxlength="100" size="35" id="kmytags" name="mytags" class="kinputbox postinput hasTip" title="<?php echo JText::_('COM_KUNENA_EDITOR_TOPIC_TAGS_OWN') ?> :: <?php echo JText::_('COM_KUNENA_EDITOR_TOPIC_TAGS_ADD_COMMAS') ?>" />
				</div>
			</li>
			<?php endif; ?>

			<?php if ($this->canSubscribe()) : ?>
			<li class="kpostmessage-row krow-odd">
				<div class="kform-label">
					<label for="ksubscribe-me">
						<?php echo JText::_('COM_KUNENA_POST_SUBSCRIBE'); ?>
					</label>
				</div>
				<div class="kform-field">
					<label for="ksubscribe-me" class="hasTip" title="<?php echo JText::_('COM_KUNENA_POST_SUBSCRIBE'); ?> :: <?php echo JText::_('COM_KUNENA_POST_NOTIFIED'); ?>">
						<input type="checkbox" value="1" name="subscribe-me" id="ksubscribe-me" <?php if ($this->config->subscriptionschecked == 1) echo 'checked="checked"' ?> />
							<i><?php echo JText::_('COM_KUNENA_POST_NOTIFIED'); ?></i>
					</label>
				</div>
			</li>
			<?php endif; ?>
		</ul>

		<div class="kpost-buttons">
		<span id="tk-preview-message-title" class="">
			<input type="button" name="preview" class="tk-preview-button tk-tips" onclick="kToggleOrSwapPreview('kbbcode-preview-bottom')"
			value="<?php echo (' ' . JText::_('COM_KUNENA_PREVIEW') . ' ');?>" title="<?php echo (JText::_('COM_KUNENA_EDITOR_HELPLINE_PREVIEW'));?>:: " />
		</span>
			<button class="tk-submit-button hasTip" type="submit" title="<?php echo JText::_('COM_KUNENA_GEN_CONTINUE').' :: '.JText::_('COM_KUNENA_EDITOR_HELPLINE_SUBMIT') ?>"><?php echo JText::_('COM_KUNENA_GEN_CONTINUE') ?></button>
			<button class="tk-cancel-button hasTip" type="button" title="<?php echo JText::_('COM_KUNENA_GEN_CANCEL').' :: '.JText::_('COM_KUNENA_EDITOR_HELPLINE_CANCEL') ?>" onclick="javascript:window.history.back();"><?php echo JText::_('COM_KUNENA_GEN_CANCEL') ?></button>
		</div>

		<span class="corners-bottom"><span></span></span>
	</div>
</div>


<?php
if (!$this->message->name) {
	echo '<script type="text/javascript">document.postform.authorname.focus();</script>';
} else if (!$this->topic->subject) {
	echo '<script type="text/javascript">document.postform.subject.focus();</script>';
} else {
	echo '<script type="text/javascript">document.postform.message.focus();</script>';
}
?>
</form>

<div id="history">
<?php $this->displayThreadHistory (); ?>
</div>

<script type="text/javascript">
//<![CDATA[
var preview=new switchcontent('tk-preview-message', 'div')
preview.setStatus(' ', ' ')
preview.setColor('black', '')
preview.setPersist(false)
preview.collapsePrevious(true)
preview.init()
// ]]>
</script>