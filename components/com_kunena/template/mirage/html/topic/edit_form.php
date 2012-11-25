<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tooltip');
JHtml::_('kunenafile.uploader', 'kuploader');
?>
<div class="kmodule topic-edit_form">
	<div class="kbox-wrapper kbox-full">
		<div class="topic-edit_form-kbox kbox kbox-color kbox-border kbox-border_radius kbox-border_radius-vchild kbox-shadow">
			<div class="headerbox-wrapper kbox-full">
				<div class="header">
					<h2 class="header"><span><?php echo $this->escape($this->title)?></span></h2>
				</div>
			</div>
			<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic') ?>" enctype="multipart/form-data" name="postform" method="post" id="postform" class="postform form-validate">
				<input type="hidden" name="view" value="topic" />
				<?php if ($this->message->exists()) : ?>
				<input type="hidden" name="task" value="edit" />
				<input type="hidden" name="mesid" value="<?php echo intval($this->message->id) ?>" />
				<?php else: ?>
				<input type="hidden" name="task" value="post" />
				<input type="hidden" name="parentid" value="<?php echo intval($this->message->parent) ?>" />
				<?php endif ?>
				<?php if (empty($this->selectcatlist)) : ?>
				<input type="hidden" name="catid" value="<?php echo intval($this->topic->category_id) ?>" />
				<?php endif ?>
				<?php if ($this->catid && $this->catid != $this->message->catid) : ?>
				<input type="hidden" name="return" value="<?php echo intval($this->catid) ?>" />
				<?php endif ?>
				<?php echo JHtml::_( 'form.token' ) ?>
				<div class="detailsbox-wrapper innerspacer kbox-full">
					<div class="detailsbox kbox-border kbox-border_radius kbox-shadow">
						<ul class="list-unstyled kform postmessage-list clearfix">
							<?php if (isset($this->selectcatlist)): ?>
							<li id="kpost-category" class="postmessage-row kbox-hover kbox-hover_list-row">
								<div class="form-label">
									<div class="innerspacer-left kbox-full">
										<label for="kpostcatid"><?php echo JText::_('COM_KUNENA_CATEGORY')?></label>
									</div>
								</div>
								<div class="form-field">
									<div class="innerspacer kbox-full">
										<?php echo $this->selectcatlist ?>
									</div>
								</div>
							</li>
							<?php endif ?>
							<?php if ($this->message->userid) : ?>
							<li style="display: none" id="kanynomous-check" class="postmessage-row kbox-hover kbox-hover_list-row">
								<div class="form-label">
									<div class="innerspacer-left kbox-full">
										<label for="kanonymous">
											<?php echo JText::_('COM_KUNENA_POST_AS_ANONYMOUS') ?>
										</label>
									</div>
								</div>
								<div class="form-field">
									<div class="innerspacer kbox-full">
										<input type="checkbox" value="1" name="anonymous" id="kanonymous" class="hasTip" title="<?php echo JText::_('COM_KUNENA_POST_AS_ANONYMOUS') ?> :: <?php echo JText::_('COM_KUNENA_POST_AS_ANONYMOUS_CHECK') ?>" <?php if ($this->post_anonymous) echo 'checked="checked"' ?> />
										<div class="kform-note"><?php echo JText::_('COM_KUNENA_POST_AS_ANONYMOUS_DESC') ?></div>
									</div>
								</div>
							</li>
							<?php endif ?>
							<li style="display: none" id="kanynomous-check-name" class="postmessage-row kbox-hover kbox-hover_list-row">
								<div class="form-label">
									<div class="innerspacer-left kbox-full">
										<label for="kauthorname">
											<?php echo JText::_('COM_KUNENA_GEN_NAME') ?>
										</label>
									</div>
								</div>
								<div class="form-field">
									<div class="innerspacer kbox-full">
										<input type="text" value="<?php echo $this->escape($this->message->name) ?>" maxlength="35" class="inputbox postinput required hasTip" size="35" name="authorname" id="kauthorname" disabled="disabled" title="<?php echo JText::_('COM_KUNENA_GEN_NAME') ?> :: <?php echo JText::_('COM_KUNENA_MESSAGE_ENTER_NAME') ?>" />
									</div>
								</div>
							</li>
							<?php if ($this->config->askemail && !$this->me->exists()) : ?>
							<li id="kanynomous-email" class="postmessage-row kbox-hover kbox-hover_list-row">
								<div class="form-label">
									<div class="innerspacer-left kbox-full">
										<label for="kauthorname">
											<?php echo JText::_('COM_KUNENA_GEN_EMAIL') ?>
										</label>
									</div>
								</div>
								<div class="form-field">
									<div><input type="text" value="<?php echo $this->escape($this->message->email) ?>" maxlength="35" class="inputbox postinput required hasTip" size="35" name="password" id="kpassword" title="<?php echo JText::_('COM_KUNENA_GEN_EMAIL') ?> :: <?php echo JText::_('COM_KUNENA_MESSAGE_ENTER_EMAIL') ?>" /></div>
									<div><?php echo $this->config->showemail == '0' ? JText::_('COM_KUNENA_POST_EMAIL_NEVER') : JText::_('COM_KUNENA_POST_EMAIL_REGISTERED') ?></div>
								</div>
							</li>
							<?php endif ?>
							<li class="post-subject postmessage-row kbox-hover kbox-hover_list-row">
								<div class="form-label">
									<div class="innerspacer-left kbox-full">
										<label for="ksubject">
											<?php echo JText::_('COM_KUNENA_GEN_SUBJECT') ?>
										</label>
									</div>
								</div>
								<div class="form-field">
									<div class="innerspacer kbox-full">
										<input type="text" value="<?php echo $this->escape($this->message->subject) ?>" maxlength="<?php echo $this->escape($this->config->maxsubject) ?>" size="35" id="ksubject" name="subject" class="kbox-width inputbox postinput required hasTip" title="<?php echo JText::_('COM_KUNENA_GEN_SUBJECT') ?> :: <?php echo JText::_('COM_KUNENA_ENTER_SUBJECT') ?>" />
									</div>
								</div>
							</li>
							<?php if ($this->message->parent==0 && $this->config->topicicons) : ?>
							<li class="post-topicicons postmessage-row kbox-hover kbox-hover_list-row">
								<div class="form-label">
									<div class="innerspacer-left kbox-full">
										<label for="topic_emoticon_default">
											<?php echo JText::_('COM_KUNENA_GEN_TOPIC_ICON') ?>
										</label>
									</div>
								</div>
								<div class="form-field">
									<div class="innerspacer kbox-full">
										<ul class="list-unstyled checkbox-outline">
											<?php foreach ($this->topicIcons as $id=>$icon) : ?>
											<li class="hasTip checkbox-outline-item" title="Topic icon :: <?php echo $this->escape(ucfirst($icon->name)) ?>">
												<input type="radio" name="topic_emoticon" id="topic_emoticon_<?php echo $this->escape($icon->name) ?>" value="<?php echo $icon->id ?>" <?php echo !empty($icon->checked) ? ' checked="checked" ':'' ?> />
												<label for="topic_emoticon_<?php echo $this->escape($icon->name) ?>"><img src="<?php echo $this->ktemplate->getTopicIconIndexPath($icon->id, true) ?>" alt="" border="0" /></label>
											</li>
											<?php endforeach ?>
										</ul>
									</div>
								</div>
							</li>
							<?php endif ?>
							<?php $this->displayTemplateFile('topic', 'edit', 'editor') ?>
							<li class="postmessage-row kbox-hover kbox-hover_list-row">
								<div class="form-label">
									<div class="innerspacer-left kbox-full">
										<label for="kupload">
											<?php echo JText::_('COM_KUNENA_EDITOR_ATTACHMENTS') ?>
										</label>
									</div>
								</div>
								<div id="kuploader" class="form-field hasTip" title="<?php echo JText::_('COM_KUNENA_FILE_EXTENSIONS_ALLOWED')?>::<?php echo JText::sprintf('COM_KUNENA_UPLOAD_MAX_IMAGE_SIZE', $this->config->imagesize)?> - <?php echo $this->escape(implode(', ', $this->allowedExtensions)) ?> - <?php echo JText::sprintf('COM_KUNENA_UPLOAD_MAX_FILE_SIZE', $this->config->filesize)?>" >
									<div class="innerspacer kbox-full">
										<input id="kupload" class="kupload" name="kattachment" type="file" />
									</div>
								</div>
							</li>
							<?php if ($this->config->keywords && $this->me->isModerator ( $this->topic->getCategory() ) ) : ?>
							<li class="postmessage-row kbox-hover kbox-hover_list-row">
								<div class="form-label">
									<div class="innerspacer-left kbox-full">
										<label for="ktags">
											<?php echo JText::_('COM_KUNENA_EDITOR_TOPIC_TAGS') ?>
										</label>
									</div>
								</div>
								<div class="form-field">
									<div class="innerspacer kbox-full">
										<input type="text" value="<?php echo $this->escape($this->topic->getKeywords(false, ', ')) ?>" maxlength="100" size="35" id="ktags" name="tags" class="kbox-width inputbox postinput hasTip" title="<?php echo JText::_('COM_KUNENA_EDITOR_TOPIC_TAGS') ?> :: <?php echo JText::_('COM_KUNENA_EDITOR_TOPIC_TAGS_ADD_COMMAS') ?>" />
									</div>
								</div>
							</li>
							<?php endif ?>
							<?php if ($this->config->userkeywords && $this->me->userid) : ?>
							<li class="postmessage-row kbox-hover kbox-hover_list-row">
								<div class="form-label">
									<div class="innerspacer-left kbox-full">
										<label for="kmytags">
											<?php echo JText::_('COM_KUNENA_EDITOR_TOPIC_TAGS_OWN') ?>
										</label>
									</div>
								</div>
								<div class="form-field">
									<div class="innerspacer-left kbox-full">
										<input type="text" value="<?php echo $this->escape($this->topic->getKeywords($this->me->userid, ', ')) ?>" maxlength="100" size="35" id="kmytags" name="mytags" class="kbox-width inputbox postinput hasTip" title="<?php echo JText::_('COM_KUNENA_EDITOR_TOPIC_TAGS_OWN') ?> :: <?php echo JText::_('COM_KUNENA_EDITOR_TOPIC_TAGS_ADD_COMMAS') ?>" />
									</div>
								</div>
							</li>
							<?php endif ?>
							<?php if ($this->canSubscribe()) : ?>
							<li class="postmessage-row kbox-hover kbox-hover_list-row">
								<div class="form-label">
									<div class="innerspacer-left kbox-full">
										<label for="ksubscribe-me">
											<?php echo JText::_('COM_KUNENA_POST_SUBSCRIBE') ?>
										</label>
									</div>
								</div>
								<div class="form-field">
									<div class="innerspacer-left kbox-full">
										<label for="ksubscribe-me" class="hasTip" title="<?php echo JText::_('COM_KUNENA_POST_SUBSCRIBE') ?> :: <?php echo JText::_('COM_KUNENA_POST_NOTIFIED') ?>">
											<input type="checkbox" value="1" name="subscribe-me" id="ksubscribe-me" <?php if ($this->subscriptionschecked == 1) echo 'checked="checked"' ?> />
												<i><?php echo JText::_('COM_KUNENA_POST_NOTIFIED') ?></i>
										</label>
									</div>
								</div>
							</li>
							<?php endif ?>
							<?php if (!empty($this->captchaHtml)) : ?>
							<li class="postmessage-row kbox-hover kbox-hover_list-row">
								<div class="form-label">
									<div class="innerspacer-left kbox-full">
										<label>
											<?php echo JText::_('COM_KUNENA_CAPDESC') ?>
										</label>
									</div>
								</div>
								<div class="form-field">
									<div class="innerspacer kbox-full">
										<?php echo $this->captchaHtml ?>
									</div>
								</div>
							</li>
							<?php endif ?>
						</ul>
					</div>
				</div>
				<div class="footerkbox-wrapper innerspacer-bottom">
					<div class="footerkbox">
						<ul class="list-unstyled buttonbar buttons-category hcenter">
							<li class="item-button">
								<button class="kbutton button-type-comm button-type-standard hasTip" type="submit" title="<?php echo JText::_('COM_KUNENA_SUBMIT').' :: '.JText::_('COM_KUNENA_EDITOR_HELPLINE_SUBMIT') ?>"><span><?php echo JText::_('COM_KUNENA_SUBMIT') ?></span></button>
							</li>
							<li class="item-button">
								<button class="kbutton button-type-user hasTip" type="button" title="<?php echo JText::_('COM_KUNENA_PREVIEW').' :: '.JText::_('COM_KUNENA_EDITOR_HELPLINE_PREVIEW') ?>" onclick="kToggleOrSwapPreview('kbbcode-preview-bottom')"><span><?php echo JText::_('COM_KUNENA_PREVIEW') ?></span></button>
							</li>
							<li  class="item-button">
								<button class="kbutton button-type-standard hasTip" type="button" title="<?php echo JText::_('COM_KUNENA_CANCEL').' :: '.JText::_('COM_KUNENA_EDITOR_HELPLINE_CANCEL') ?>" onclick="javascript:window.history.back();"><span><?php echo JText::_('COM_KUNENA_CANCEL') ?></span></button>
							</li>
						</ul>
					</div>
				</div>
			</form>
		</div>
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
<?php $this->displayThreadHistory () ?>
