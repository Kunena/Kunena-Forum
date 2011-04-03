<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<form enctype="multipart/form-data" name="postform" method="post" id="postform" class="postform form-validate" action="#">
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

	<div class="ksection">
		<h2 class="kheader"><span><?php echo $this->escape($this->title)?></span></h2>

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
					<input type="checkbox" value="1" name="anonymous" id="kanonymous" class="hasTip" title="Anonymous Post :: Check if you want post as Anonymous" <?php if ($this->category->post_anonymous) echo 'checked="checked"'; ?> />
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
					<input type="text" value="<?php echo $this->escape($this->message->name) ?>" maxlength="35" class="kinputbox postinput required hasTip" size="35" name="authorname" id="kauthorname" disabled="disabled" title="Name :: Enter Your Name" />
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
					<input type="text" value="<?php echo $this->escape($this->message->email) ?>" maxlength="35" class="kinputbox postinput required hasTip" size="35" name="password" id="kpassword" title="Name :: Enter Your Email" />
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
					<input type="text" value="<?php echo $this->escape($this->message->subject) ?>" maxlength="<?php echo $this->escape($this->config->maxsubject) ?>" size="35" id="ksubject" name="subject" class="kinputbox postinput required hasTip" title="Subject :: Enter Subject" />
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
						<?php foreach ($this->topicIcons as $topicIcon) : ?>
						<li class="hasTip" title="Topic icon :: <?php echo $this->escape(ucfirst($topicIcon->name)) ?>">
							<input type="radio" name="topic_emoticon" id="topic_emoticon_<?php echo $this->escape($topicIcon->name) ?>" value="<?php echo $this->escape($topicIcon->id) ?>" <?php echo $this->topic->icon_id == $topicIcon->id ? ' checked="checked" ':'' ?> />
							<label for="topic_emoticon_<?php echo $this->escape($topicIcon->name) ?>"><img src="<?php echo $topicIcon->url ?>" alt="" border="0" /></label>
						</li>
						<?php endforeach ?>
					</ul>
				</div>
			</li>
			<?php endif ?>

			<?php echo $this->loadTemplate('editor'); ?>

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
							<input type="button" value="Add File" class="kfile-input-button kbutton" />
							<input id="kupload" class="kfile-input hidden" name="kattachment" type="file" />
						</div>
						<a href="#" class="kattachment-remove kbutton" style="display: none">Remove File</a>
						<a href="#" class="kattachment-insert kbutton" style="display: none">Insert</a>
					</div>
					<?php $this->displayAttachments($this->message); ?>
				</div>
			</li>

			<?php if ($this->me->isModerator ( $this->message->catid ) ) : ?>
			<li class="kpostmessage-row krow-even">
				<div class="kform-label">
					<label for="ktags">
						<?php echo JText::_('COM_KUNENA_EDITOR_TOPIC_TAGS') ?>
					</label>
				</div>
				<div class="kform-field">
					<input type="text" value="<?php echo $this->escape($this->topic->getKeywords(false, ', ')); ?>" maxlength="100" size="35" id="ktags" name="tags" class="kinputbox postinput hasTip" title="<?php echo JText::_('COM_KUNENA_EDITOR_TOPIC_TAGS') ?> :: Separate with comma" />
				</div>
			</li>
			<?php endif; ?>

			<?php if ($this->my->id) : ?>
			<li class="kpostmessage-row krow-even">
				<div class="kform-label">
					<label for="kmytags">
						<?php echo JText::_('COM_KUNENA_EDITOR_TOPIC_TAGS_OWN') ?>
					</label>
				</div>
				<div class="kform-field">
					<input type="text" value="<?php echo $this->escape($this->topic->getKeywords($this->my->id, ', ')); ?>" maxlength="100" size="35" id="kmytags" name="mytags" class="kinputbox postinput hasTip" title="<?php echo JText::_('COM_KUNENA_EDITOR_TOPIC_TAGS_OWN') ?> :: Separate with comma" />
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
			<button class="kbutton hasTip" type="submit" title="<?php echo JText::_('COM_KUNENA_GEN_CONTINUE').' :: '.JText::_('COM_KUNENA_EDITOR_HELPLINE_SUBMIT') ?>"><?php echo JText::_('COM_KUNENA_GEN_CONTINUE') ?></button>
			<button class="kbutton hasTip" type="button" title="<?php echo JText::_('COM_KUNENA_GEN_CANCEL').' :: '.JText::_('COM_KUNENA_EDITOR_HELPLINE_CANCEL') ?>" onclick="javascript:window.history.back();"><?php echo JText::_('COM_KUNENA_GEN_CANCEL') ?></button>
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
<?php $this->displayThreadHistory (); ?>