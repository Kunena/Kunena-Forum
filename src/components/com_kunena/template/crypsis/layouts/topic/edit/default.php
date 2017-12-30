<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Template.Crypsis
 * @subpackage  Topic
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');

// Load scripts to handle fileupload process
JText::script('COM_KUNENA_UPLOADED_LABEL_INSERT_ALL_BUTTON');
JText::script('COM_KUNENA_EDITOR_INSERT');
JText::script('COM_KUNENA_EDITOR_IN_MESSAGE');
JText::script('COM_KUNENA_GEN_REMOVE_FILE');
JText::sprintf('COM_KUNENA_UPLOADED_LABEL_ERROR_REACHED_MAX_NUMBER_FILES', $this->config->attachment_limit, array('script' => true));
JText::script('COM_KUNENA_UPLOADED_LABEL_UPLOAD_BUTTON');
JText::script('COM_KUNENA_UPLOADED_LABEL_PROCESSING_BUTTON');
JText::script('COM_KUNENA_UPLOADED_LABEL_ABORT_BUTTON');
JText::script('COM_KUNENA_UPLOADED_LABEL_DRAG_AND_DROP_OR_BROWSE');
JText::script('COM_KUNENA_EDITOR_BOLD');
JText::script('COM_KUNENA_EDITOR_COLORS');
JText::script('COM_KUNENA_EDITOR_UNORDERED_LIST');
JText::script('COM_KUNENA_EDITOR_TABLE');
JText::script('COM_KUNENA_EDITOR_LINK');
JText::script('COM_KUNENA_EDITOR_EBAY');
JText::script('COM_KUNENA_EDITOR_MAP');
JText::script('COM_KUNENA_EDITOR_POLL_SETTING');
JText::script('COM_KUNENA_EDITOR_TWEET');

JFactory::getDocument()->addScriptDeclaration('
	var imageheight = ' . $this->config->imageheight . ';
	var imagewidth = ' . $this->config->imagewidth . ';
');

JHtml::_('jquery.ui');
$this->addScript('assets/js/load-image.min.js');
$this->addScript('assets/js/canvas-to-blob.min.js');
$this->addScript('assets/js/jquery.iframe-transport.js');
$this->addScript('assets/js/jquery.fileupload.js');
$this->addScript('assets/js/jquery.fileupload-process.js');
$this->addScript('assets/js/jquery.fileupload-image.js');
$this->addScript('assets/js/upload.main.js');
$this->addStyleSheet('assets/css/fileupload.css');

$this->addScript('assets/js/markitup.js');

$editor = KunenaBbcodeEditor::getInstance();
$editor->initialize();

$this->addScript('assets/js/markitup.editor.js');
$this->addScript('assets/js/markitup.set.js');

$this->k = 0;

$this->addScriptDeclaration("kunena_upload_files_rem = '" . KunenaRoute::_('index.php?option=com_kunena&view=topic&task=removeattachments&format=json&' . JSession::getFormToken() . '=1', false) . "';");
$this->addScriptDeclaration("kunena_upload_files_preload = '" . KunenaRoute::_('index.php?option=com_kunena&view=topic&task=loadattachments&format=json&' . JSession::getFormToken() . '=1', false) . "';");
$this->addScriptDeclaration("kunena_upload_files_maxfiles = '" . $this->config->attachment_limit . "';");

// If polls are enabled, load also poll JavaScript.

if ($this->config->pollenabled)
{
	JText::script('COM_KUNENA_POLL_OPTION_NAME');
	JText::script('COM_KUNENA_EDITOR_HELPLINE_OPTION');
	$this->addScript('assets/js/poll.js');
}

$this->addScript('assets/js/pollcheck.js');

$this->addStyleSheet('assets/css/bootstrap.datepicker.css');
$this->addScript('assets/js/bootstrap.datepicker.js');

// Load caret.js always before atwho.js script and use it for autocomplete, emojiis...
$this->addScript('assets/js/jquery.caret.js');
$this->addScript('assets/js/jquery.atwho.js');
$this->addStyleSheet('assets/css/jquery.atwho.css');

$this->ktemplate = KunenaFactory::getTemplate();
$topicicontype = $this->ktemplate->params->get('topicicontype');

$this->addScriptDeclaration("kunena_topicicontype = '" . $topicicontype . "';");

$this->addScript('assets/js/edit.js');

if (KunenaFactory::getTemplate()->params->get('formRecover'))
{
	$this->addScript('assets/js/sisyphus.js');
}
?>

	<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" class="form-horizontal form-validate"
		id="postform" name="postform" enctype="multipart/form-data" data-page-identifier="1">
		<input type="hidden" name="view" value="topic" />
		<input id="kurl_topicons_request" type="hidden" value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic&layout=topicicons&format=raw', false); ?>" />
		<input id="kcategory_poll" type="hidden" name="kcategory_poll" value="<?php echo $this->message->catid; ?>" />
		<input id="kpreview_url" type="hidden" name="kpreview_url" value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic&layout=edit&format=raw', false) ?>" />
		<?php if (!$this->config->allow_change_subject) : ?>
			<input type="hidden" name="subject" value="<?php echo $this->escape($this->message->subject); ?>" />
		<?php endif; ?>
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
		<?php if ($this->message->getTopic()->first_post_id == $this->message->id && $this->message->getTopic()->getPoll()->id) : ?>
			<input type="hidden" id="poll_exist_edit" name="poll_exist_edit" value="<?php echo intval($this->message->getTopic()->getPoll()->id) ?>" />
		<?php endif; ?>
		<input type="hidden" id="kunena_upload" name="kunena_upload" value="<?php echo intval($this->message->catid) ?>" />
		<input type="hidden" id="kunena_upload_files_url" value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic&task=upload&format=json&' . JSession::getFormToken() . '=1', false) ?>" />
		<?php echo JHtml::_('form.token'); ?>

		<h1>
			<?php echo $this->escape($this->headerText) ?>
		</h1>

		<div class="well">
			<div class="row-fluid column-row">
				<div class="span12 column-item">
					<fieldset>
						<?php if (isset($this->selectcatlist)) : ?>
							<div class="control-group">
								<!-- Username -->
								<label class="control-label"><?php echo JText::_('COM_KUNENA_CATEGORY') ?></label>

								<div class="controls"> <?php echo $this->selectcatlist ?> </div>
							</div>
						<?php endif; ?>
						<?php if ($this->message->userid) : ?>
							<div class="control-group" id="kanynomous-check" <?php if (!$this->message->getCategory()->allow_anonymous) : ?>style="display:none;"<?php endif; ?>>
								<label class="control-label"><?php echo JText::_('COM_KUNENA_POST_AS_ANONYMOUS'); ?></label>
								<div class="controls">
									<input type="checkbox" id="kanonymous" name="anonymous" value="1" <?php if ($this->post_anonymous)
									{
										echo 'checked="checked"';
									} ?> />
									<label for="kanonymous"><?php echo JText::_('COM_KUNENA_POST_AS_ANONYMOUS_DESC'); ?></label>
								</div>
							</div>
						<?php endif; ?>
						<div class="control-group" id="kanynomous-check-name"
							<?php if ($this->me->userid && !$this->category->allow_anonymous) : ?>style="display:none;"<?php endif; ?>>
							<div class="alert alert-info"><?php echo JText::_('COM_KUNENA_GEN_INFO_GUEST_CANNOT_EDIT_DELETE_MESSAGE'); ?></div>

							<label class="control-label"><?php echo JText::_('COM_KUNENA_GEN_NAME'); ?></label>
							<div class="controls">
								<input type="text" id="kauthorname" name="authorname" size="35" placeholder="<?php echo JText::_('COM_KUNENA_TOPIC_EDIT_PLACEHOLDER_AUTHORNAME') ?>" class="input-xxlarge" maxlength="35" tabindex="4" value="<?php echo $this->escape($this->message->name); ?>"/>
						<!-- Encourage guest user to login or register -->
						<?php
						$login = '<a class="btn-link" href="' . JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode((string)JUri::getInstance())) . '"> ' . JText::_('JLOGIN') . '</a>';
						$register = ' ' . JText::_('COM_KUNENA_LOGIN_OR') . ' <a class="btn-link" href="index.php?option=com_users&view=registration">' . JText::_('JREGISTER') . '</a>';
						echo JText::sprintf('COM_KUNENA_LOGIN_PLEASE_SKIP', $login, $register);
						?>
							</div>
						</div>
						<?php if ($this->config->askemail && !$this->me->userid) : ?>
							<div class="control-group">
								<label class="control-label"><?php echo JText::_('COM_KUNENA_GEN_EMAIL'); ?></label>

								<div class="controls">
									<input type="text" id="email" name="email" size="35" placeholder="<?php echo JText::_('COM_KUNENA_TOPIC_EDIT_PLACEHOLDER_EMAIL') ?>" class="input-xxlarge" maxlength="45" tabindex="5" value="<?php echo !empty($this->message->email) ? $this->escape($this->message->email) : '' ?>" />
									<br />
									<?php echo $this->config->showemail == '0' ? JText::_('COM_KUNENA_POST_EMAIL_NEVER') : JText::_('COM_KUNENA_POST_EMAIL_REGISTERED'); ?>
								</div>
							</div>
						<?php endif; ?>
						<div class="control-group" id="kpost-subject">
							<label class="control-label"><?php echo JText::_('COM_KUNENA_GEN_SUBJECT'); ?></label>

							<div class="controls">
								<?php if (!$this->config->allow_change_subject && $this->topic->exists() && !KunenaUserHelper::getMyself()->isModerator($this->message->getCategory())) : ?>
									<input class="span12" type="text"  id="subject" value="<?php echo $this->escape($this->message->subject); ?>" disabled/>
								<?php else : ?>
									<input class="span12" type="text" placeholder="<?php echo JText::_('COM_KUNENA_TOPIC_EDIT_PLACEHOLDER_SUBJECT') ?>" name="subject" id="subject" maxlength="<?php echo $this->escape($this->ktemplate->params->get('SubjectLengthMessage')); ?>" tabindex="6" value="<?php echo $this->escape($this->message->subject); ?>"  />
								<?php endif; ?>
							</div>
						</div>
						<?php if (!empty($this->topicIcons)) : ?>
							<div class="control-group" id="kpost-topicicons">
								<label class="control-label"><?php echo JText::_('COM_KUNENA_GEN_TOPIC_ICON'); ?></label>
								<div id="iconset_inject" class="controls controls-select">
									<div class="span12" id="iconset_topic_list">
										<?php foreach ($this->topicIcons as $id => $icon): ?>
										<input type="radio" id="radio<?php echo $icon->id ?>" name="topic_emoticon" value="<?php echo $icon->id ?>" <?php echo !empty($icon->checked) ? ' checked="checked" ' : '' ?> />
										<?php if ($this->config->topicicons && $topicicontype == 'B2') : ?>
											<label class="radio inline" for="radio<?php echo $icon->id; ?>"><span class="icon icon-<?php echo $icon->b2; ?> icon-topic" aria-hidden="true"></span>
										<?php elseif ($this->config->topicicons && $topicicontype == 'fa') : ?>
											<label class="radio inline" for="radio<?php echo $icon->id; ?>"><i class="fa fa-<?php echo $icon->fa; ?> glyphicon-topic fa-2x"></i>
										<?php else : ?>
											<label class="radio inline" for="radio<?php echo $icon->id; ?>"><img src="<?php echo $icon->relpath; ?>" alt="<?php echo $icon->name; ?>" border="0" />
										<?php endif; ?>
											</label>
										<?php endforeach; ?>
									</div>
								</div>
							</div>
						<?php endif; ?>
						<?php
						// Show bbcode editor
						echo $this->subLayout('Topic/Edit/Editor')->setProperties($this->getProperties());
						?>
						<?php if ($this->message->exists() && $this->config->editmarkup) : ?>
							<div class="control-group" id="modified_reason">
								<label class="control-label"><?php echo(JText::_('COM_KUNENA_EDITING_REASON')) ?></label>

								<div class="controls">
									<input class="input-xxlarge" name="modified_reason" maxlength="200" type="text"
									       value="<?php echo $this->message->modified_reason; ?>" title="reason" placeholder="<?php echo(JText::_('COM_KUNENA_EDITING_ENTER_REASON')) ?>"/>
								</div>
							</div>
						<?php endif; ?>
						<?php if ($this->allowedExtensions) : ?>
							<div class="control-group krow<?php echo 1 + $this->k ^= 1; ?>" id="kpost-attachments">
								<label class="control-label"></label>
								<div class="controls">
									<button class="btn" id="kshow_attach_form" type="button"><?php echo KunenaIcons::attach() . ' ' . JText::_('COM_KUNENA_EDITOR_ATTACHMENTS'); ?></button>
									<div id="kattach_form" style="display: none;">
										<span class="label label-info"><?php echo JText::_('COM_KUNENA_FILE_EXTENSIONS_ALLOWED') ?>: <?php echo $this->escape(implode(', ', $this->allowedExtensions)) ?></span><br /><br />
										<span class="label label-info"><?php echo JText::_('COM_KUNENA_UPLOAD_MAX_FILES_WEIGHT') ?>: <?php echo $this->config->filesize != 0 ? round($this->config->filesize / 1024, 1) : $this->config->filesize ?> <?php echo JText::_('COM_KUNENA_UPLOAD_ATTACHMENT_FILE_WEIGHT_MB') ?> <?php echo JText::_('COM_KUNENA_UPLOAD_MAX_IMAGES_WEIGHT') ?>: <?php echo $this->config->imagesize != 0 ? round($this->config->imagesize / 1024, 1) : $this->config->imagesize ?> <?php echo JText::_('COM_KUNENA_UPLOAD_ATTACHMENT_FILE_WEIGHT_MB') ?></span><br /><br />
										<!-- The fileinput-button span is used to style the file input field as button -->
										<span class="btn btn-primary fileinput-button">
											<?php echo KunenaIcons::plus();?>
											<span><?php echo JText::_('COM_KUNENA_UPLOADED_LABEL_ADD_FILES_BUTTON') ?></span>
											<!-- The file input field used as target for the file upload widget -->
											<input id="fileupload" type="file" name="file" multiple>
										</span>
										<button id="insert-all" class="btn btn-primary" type="submit" style="display:none;">
											<?php echo KunenaIcons::upload();?>
											<span><?php echo JText::_('COM_KUNENA_UPLOADED_LABEL_INSERT_ALL_BUTTON') ?></span>
										</button>
										<button id="remove-all" class="btn btn-danger" type="submit" style="display:none;">
											<?php echo KunenaIcons::cancel();?>
											<span><?php echo JText::_('COM_KUNENA_UPLOADED_LABEL_REMOVE_ALL_BUTTON') ?></span>
										</button>
										<!-- The container for the uploaded files -->
										<div id="files" class="files"></div>
										<div id="dropzone">
											<div class="dropzone">
												<div class="default message">
													<span id="klabel_info_drop_browse"><?php echo JText::_('COM_KUNENA_UPLOADED_LABEL_DRAG_AND_DROP_OR_BROWSE') ?></span>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						<?php endif; ?>
						<?php if ($this->canSubscribe) : ?>
							<div class="control-group" id="kpost-subscribe">
								<label class="control-label"><?php echo JText::_('COM_KUNENA_POST_SUBSCRIBE'); ?></label>

								<div class="controls">
									<input style="float: left; margin-right: 10px;" type="checkbox" name="subscribeMe" id="subscribeMe" value="1" <?php if ($this->subscriptionschecked == 1 && $this->me->canSubscribe != 0 || $this->subscriptionschecked == 0 && $this->me->canSubscribe == 1)
									{
										echo 'checked="checked"';
									} ?> />
									<label class="string optional" for="subscribeMe"><?php echo JText::_('COM_KUNENA_POST_NOTIFIED'); ?></label>
								</div>
							</div>
						<?php endif; ?>
						<?php if (!empty($this->captchaEnabled)) : ?>
							<div class="control-group">
								<?php echo $this->captchaDisplay;?>
							</div>
						<?php endif; ?>

					</fieldset>
				</div>
			</div>
		</div>
		<div class="center">
			<button id="form_submit_button" type="submit" class="btn btn-success" tabindex="8">
				<?php echo KunenaIcons::save();?>
				<?php echo(' ' . JText::_('COM_KUNENA_SUBMIT') . ' '); ?>
			</button>
			<button type="reset" class="btn" onclick="window.history.back();" tabindex="10">
				<?php echo KunenaIcons::cancel();?>
				<?php echo(' ' . JText::_('COM_KUNENA_CANCEL') . ' '); ?>
			</button>
		</div>
		<?php
		if (!$this->message->name)
		{
			echo '<script type="text/javascript">document.postform.authorname.focus();</script>';
		}
		else
		{
			if (!$this->topic->subject)
			{
				echo '<script type="text/javascript">document.postform.subject.focus();</script>';
			}
			else
			{
				echo '<script type="text/javascript">document.postform.message.focus();</script>';
			}
		}
		?>
		<div id="kattach-list"></div>
	</form>
<?php
if ($this->config->showhistory && $this->topic->exists())
{
	echo $this->subRequest('Topic/Form/History', new JInput(array('id' => $this->topic->id)));
}
