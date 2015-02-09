<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

JHtml::_('behavior.tooltip');
JHtml::_('behavior.keepalive');

// Load scripts to handle fileupload process
JText::script('COM_KUNENA_EDITOR_INSERT');
JText::script('COM_KUNENA_GEN_REMOVE_FILE');
JText::sprintf('COM_KUNENA_UPLOADED_LABEL_ERROR_REACHED_MAX_NUMBER_FILES', $this->config->attachment_limit, array('script' => true));
JText::script('COM_KUNENA_UPLOADED_LABEL_UPLOAD_BUTTON');
JText::script('COM_KUNENA_UPLOADED_LABEL_PROCESSING_BUTTON');
JText::script('COM_KUNENA_UPLOADED_LABEL_ABORT_BUTTON');
JText::script('COM_KUNENA_UPLOADED_LABEL_DRAG_AND_DROP_OR_BROWSE');

$this->addScript('js/jquery.ui.widget.js');
$this->addScript('js/load-image.min.js');
$this->addScript('js/canvas-to-blob.min.js');
$this->addScript('js/jquery.iframe-transport.js');
$this->addScript('js/jquery.fileupload.js');
$this->addScript('js/jquery.fileupload-process.js');
$this->addScript('js/jquery.fileupload-image.js');
$this->addScript('js/upload.main.js');
$this->addStyleSheet('css/fileupload.css');
$this->addStyleSheet('css/fileupload-ui.css');

$editor = KunenaBbcodeEditor::getInstance();
$editor->initialize('id');

$this->addScriptDeclaration("window.addEvent('domready', function() {
	if ( typeof pollcategoriesid != 'undefined' ) {
		var catid = $('kcategory_poll').get('value');
		if ( pollcategoriesid[catid] !== undefined ) {
			kbbcode.addFunction('Poll', function() {
				kToggleOrSwap('kbbcode-poll-options');
			}, {'id': 'kbbcode-poll-button',
				'class': 'kbbcode-poll-button',
				'title': Joomla.JText._('COM_KUNENA_EDITOR_POLL'),
				'alt': Joomla.JText._('COM_KUNENA_EDITOR_HELPLINE_POLL')});

		} else {
			kbbcode.addFunction('Poll', function() {
				kToggleOrSwap('kbbcode-poll-options');
			}, {'id': 'kbbcode-poll-button',
				'class': 'kbbcode-poll-button',
				'style':'display: none;',
				'title': Joomla.JText._('COM_KUNENA_EDITOR_POLL'),
				'alt': Joomla.JText._('COM_KUNENA_EDITOR_HELPLINE_POLL')});
		}
	}
	kEditorInitialize();
});");

$this->k=0;

$this->addScriptDeclaration("kunena_upload_files_rem = '" . KunenaRoute::_('index.php?option=com_kunena&view=topic&task=removeattachments&format=json&' . JSession::getFormToken() . '=1', false) . "'");
$this->addScriptDeclaration("kunena_upload_files_preload = '" . KunenaRoute::_('index.php?option=com_kunena&view=topic&task=loadattachments&format=json&' . JSession::getFormToken() . '=1', false) . "'");
$this->addScriptDeclaration("kunena_upload_files_maxfiles = '" . $this->config->attachment_limit . "' ");

// If polls are enabled, load also poll JavaScript.
if ($this->config->pollenabled)
{
	JText::script('COM_KUNENA_POLL_OPTION_NAME');
	JText::script('COM_KUNENA_EDITOR_HELPLINE_OPTION');
	$this->addScript('poll.js');
}
// Load caret.js always before atwho.js script and use it for autocomplete, emojiis...
$this->addScript('js/caret.js');
$this->addScript('js/atwho.js');
$this->addStyleSheet('css/atwho.css');

$this->addScript('js/edit.js');
?>

<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" class="form-horizontal"
	  id="postform" name="postform" enctype="multipart/form-data">
	<input type="hidden" name="view" value="topic" />
	<input id="kcategory_poll" type="hidden" name="kcategory_poll" value="<?php echo $this->message->catid; ?>" />
	<input id="kpreview_url" type="hidden" name="kpreview_url" value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic&layout=edit&format=raw', false) ?>" />
	<?php if ($this->message->exists()) : ?>
	<input type="hidden" name="task" value="edit" />
	<input id="kmessageid" type="hidden" name="mesid" value="<?php echo intval($this->message->id) ?>" />
	<?php else: ?>
		<input type="hidden" name="task" value="post" />
		<input type="hidden" name="parentid" value="<?php echo intval($this->message->parent) ?>" />
	<?php endif; ?>
	<?php if (!isset($this->selectcatlist)) : ?>
		<input type="hidden" name="catid" value="<?php echo intval($this->message->catid) ?>" />
	<?php endif; ?>
	<?php if ($this->category->id && $this->category->id != $this->message->catid) : ?>
		<input type="hidden" name="return" value="<?php echo intval($this->category->id) ?>" />
	<?php endif; ?>
	<input type="hidden" id="kunena_upload" name="kunena_upload" value="<?php echo intval($this->message->catid) ?>" />
	<input type="hidden" id="kunena_upload_files_url" value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic&task=upload&format=json&' . JSession::getFormToken() . '=1', false) ?>" />
	<?php echo JHtml::_( 'form.token' ); ?>

	<h2>
		<?php echo $this->escape($this->headerText)?>
	</h2>

	<div class="well">
		<div class="row-fluid column-row">
			<div class="span12 column-item" >
				<fieldset>
					<?php if (isset($this->selectcatlist)): ?>
						<div class="control-group">
							<!-- Username -->
							<label class="control-label"><?php echo JText::_('COM_KUNENA_CATEGORY')?></label>
							<div class="controls"> <?php echo $this->selectcatlist?> </div>
						</div>
					<?php endif; ?>
					<?php if ($this->message->userid) : ?>
						<div class="control-group" id="kanynomous-check" <?php if (!$this->category->allow_anonymous): ?>style="display:none;"<?php endif; ?>>
							<label class="control-label"><?php echo JText::_('COM_KUNENA_POST_AS_ANONYMOUS'); ?></label>
							<div class="controls">
								<input type="checkbox" id="kanonymous" name="anonymous" value="1" <?php if ($this->post_anonymous) echo 'checked="checked"'; ?> />
								<label for="kanonymous"><?php echo JText::_('COM_KUNENA_POST_AS_ANONYMOUS_DESC'); ?></label>
							</div>
						</div>
					<?php endif; ?>
					<div class="control-group" id="kanynomous-check-name"
						 <?php if ( $this->me->userid && !$this->category->allow_anonymous ): ?>style="display:none;"<?php endif; ?>>
						<label class="control-label"><?php echo JText::_('COM_KUNENA_GEN_NAME'); ?></label>
						<div class="controls">
							<input type="text" id="kauthorname" name="authorname" size="35" placeholder="<?php echo JText::_('COM_KUNENA_TOPIC_EDIT_PLACEHOLDER_AUTHORNAME') ?>" class="input-xlarge" maxlength="35" value="<?php echo $this->escape($this->message->name);?>" required />
						</div>
					</div>
					<?php if ($this->config->askemail && !$this->me->userid) : ?>
						<div class="control-group">
							<label class="control-label"><?php echo JText::_('COM_KUNENA_GEN_EMAIL');?></label>
							<div class="controls">
								<input type="text" id="email" name="email"	size="35" placeholder="<?php echo JText::_('COM_KUNENA_TOPIC_EDIT_PLACEHOLDER_EMAIL') ?>" class="input-xlarge" maxlength="35" value="<?php echo !empty($this->message->email) ? $this->escape($this->message->email) : '' ?>" required />
								<br />
								<?php echo $this->config->showemail == '0' ? JText::_('COM_KUNENA_POST_EMAIL_NEVER') : JText::_('COM_KUNENA_POST_EMAIL_REGISTERED'); ?> </div>
						</div>
					<?php endif; ?>
					<div class="control-group">
						<label class="control-label"><?php echo JText::_('COM_KUNENA_GEN_SUBJECT'); ?></label>
						<div class="controls">
							<input class="span12" type="text" placeholder="<?php echo JText::_('COM_KUNENA_TOPIC_EDIT_PLACEHOLDER_SUBJECT') ?>" name="subject" id="subject" maxlength="<?php echo $this->escape($this->config->maxsubject); ?>" value="<?php echo $this->escape($this->message->subject); ?>" tabindex="1" required />
						</div>
					</div>
					<?php if (!empty($this->topicIcons)) : ?>
						<div class="control-group">
							<label class="control-label"><?php echo JText::_('COM_KUNENA_GEN_TOPIC_ICON'); ?></label>

							<div class="controls controls-select">
								<?php foreach ($this->topicIcons as $id => $icon): ?>
									<span class="kiconsel">
										<input type="radio" id="radio<?php echo $icon->id ?>" name="topic_emoticon" value="<?php echo $icon->id ?>" <?php echo !empty($icon->checked) ? ' checked="checked" ' : '' ?> />
										<label class="radio inline" for="radio<?php echo $icon->id ?>"><img src="<?php echo $this->template->getTopicIconIndexPath($icon->id, true); ?>" alt="" border="0"/> </label>
									</span>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endif; ?>
					<?php
					// Show bbcode editor
					echo $this->subLayout('Topic/Edit/Editor')->setProperties($this->getProperties());
					?>
					<?php if ($this->allowedExtensions) : ?>
					<div class="control-group krow<?php echo 1 + $this->k^=1;?>" id="kpost-attachments">
						<label class="control-label"><?php echo JText::_('COM_KUNENA_EDITOR_ATTACHMENTS'); ?></label>
						<div class="controls">
							<span class="label label-info"><?php echo JText::_('COM_KUNENA_FILE_EXTENSIONS_ALLOWED')?>: <?php echo $this->escape(implode(', ', $this->allowedExtensions)) ?></span><br /><br />
							<span class="label label-info"><?php echo JText::_('COM_KUNENA_UPLOAD_MAX_FILES_WEIGHT')?>: <?php echo $this->config->filesize != 0 ? round($this->config->filesize / 1024, 1): $this->config->filesize ?> <?php echo JText::_('COM_KUNENA_UPLOAD_ATTACHMENT_FILE_WEIGHT_MB') ?> <?php echo JText::_('COM_KUNENA_UPLOAD_MAX_IMAGES_WEIGHT')?>: <?php echo $this->config->imagesize != 0 ? round($this->config->imagesize / 1024, 1): $this->config->imagesize ?> <?php echo JText::_('COM_KUNENA_UPLOAD_ATTACHMENT_FILE_WEIGHT_MB') ?></span><br /><br />
							<!-- The fileinput-button span is used to style the file input field as button -->
							<span class="btn btn-primary fileinput-button">
								<i class="icon-plus"></i>
								<span><?php echo JText::_('COM_KUNENA_UPLOADED_LABEL_ADD_FILES_BUTTON') ?></span>
								<!-- The file input field used as target for the file upload widget -->
								<input id="fileupload" type="file" name="file" multiple>
							</span>
							<br>
							<br>
							<!-- The container for the uploaded files -->
							<div id="files" class="files"></div>
							<span id="klabel_info_drop_browse" class="label label-info"><?php echo JText::_('COM_KUNENA_UPLOADED_LABEL_DRAG_AND_DROP_OR_BROWSE') ?></span>
							<br>
						</div>
					</div>
					<?php endif; ?>
					<?php if ($this->config->keywords && $this->me->isModerator ( $this->topic->getCategory() ) ) : ?>
						<div class="control-group">
							<label class="control-label"><?php echo JText::_('COM_KUNENA_EDITOR_TOPIC_TAGS') ?></label>
							<div class="controls">
								<input type="text" class="kinputbox postinput" name="tags" id="tags" size="35" maxlength="100" value="<?php echo $this->escape($this->topic->getKeywords(false, ', ')); ?>" />
							</div>
						</div>
					<?php endif; ?>
					<?php if ($this->config->userkeywords && $this->me->userid) : ?>
						<div class="control-group">
							<label class="control-label"><?php echo JText::_('COM_KUNENA_EDITOR_TOPIC_TAGS_OWN') ?></label>
							<div class="controls">
								<input type="text" class="kinputbox postinput" name="mytags" id="mytags" size="35" maxlength="100" value="<?php echo $this->escape($this->topic->getKeywords($this->me->userid, ', ')); ?>" />
							</div>
						</div>
					<?php endif; ?>
					<?php if ($this->canSubscribe) : ?>
						<div class="control-group">
							<label class="control-label"><?php echo JText::_('COM_KUNENA_POST_SUBSCRIBE'); ?></label>
							<div class="controls">
								<input style="float: left; margin-right: 10px;" type="checkbox" name="subscribeMe" id="subscribeMe" value="1" <?php if ($this->subscriptionschecked == 1) echo 'checked="checked"' ?> />
								<label class="string optional" for="subscribeMe"><?php echo JText::_('COM_KUNENA_POST_NOTIFIED'); ?></label>
							</div>
						</div>
					<?php endif; ?>
					<?php if (!empty($this->captchaHtml)) : ?>
						<div class="control-group">
							<label class="control-label"><?php echo JText::_('COM_KUNENA_CAPDESC'); ?></label>
							<div class="controls"> <?php echo $this->captchaHtml ?> </div>
						</div>
					<?php endif; ?>
					<div class="center">
						<input type="submit" name="ksubmit" class="btn btn-primary"
							   value="<?php echo (' ' . JText::_('COM_KUNENA_SUBMIT') . ' ');?>"
							   title="<?php echo (JText::_('COM_KUNENA_EDITOR_HELPLINE_SUBMIT'));?>" tabindex="4" />
						<input id="kbutton-preview" type="button" name="preview" class="btn"
							   value="<?php echo (' ' . JText::_('COM_KUNENA_PREVIEW') . ' ');?>"
							   title="<?php echo (JText::_('COM_KUNENA_EDITOR_HELPLINE_PREVIEW'));?>:: "tabindex="3" />
						<input type="button" name="cancel" class="btn"
							   value="<?php echo (' ' . JText::_('COM_KUNENA_CANCEL') . ' ');?>"
							   onclick="javascript:window.history.back();"
							   title="<?php echo (JText::_('COM_KUNENA_EDITOR_HELPLINE_CANCEL'));?>" tabindex="5" />
					</div>
				</fieldset>
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
	<div id="kattach-list"></div>
</form>
<?php
if ($this->config->showhistory && $this->topic->exists())
	echo $this->subRequest('Topic/Form/History', new JInput(array('id'=>$this->topic->id)));
?>
