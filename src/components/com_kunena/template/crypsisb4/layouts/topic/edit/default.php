<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsisb4
 * @subpackage      Topic
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

// Load scripts to handle fileupload process
Text::script('COM_KUNENA_UPLOADED_LABEL_INSERT_ALL_BUTTON');
Text::script('COM_KUNENA_EDITOR_INSERT');
Text::script('COM_KUNENA_EDITOR_IN_MESSAGE');
Text::script('COM_KUNENA_GEN_REMOVE_FILE');
Text::sprintf('COM_KUNENA_UPLOADED_LABEL_ERROR_REACHED_MAX_NUMBER_FILES', $this->config->attachment_limit, ['script' => true]);
Text::script('COM_KUNENA_UPLOADED_LABEL_UPLOAD_BUTTON');
Text::script('COM_KUNENA_UPLOADED_LABEL_PROCESSING_BUTTON');
Text::script('COM_KUNENA_UPLOADED_LABEL_ABORT_BUTTON');
Text::script('COM_KUNENA_UPLOADED_LABEL_DRAG_AND_DROP_OR_BROWSE');
Text::script('COM_KUNENA_EDITOR_BOLD');
Text::script('COM_KUNENA_EDITOR_COLORS');
Text::script('COM_KUNENA_EDITOR_OLIST');
Text::script('COM_KUNENA_EDITOR_TABLE');
Text::script('COM_KUNENA_EDITOR_ITALIC');
Text::script('COM_KUNENA_EDITOR_UNDERL');
Text::script('COM_KUNENA_EDITOR_STRIKE');
Text::script('COM_KUNENA_EDITOR_SUB');
Text::script('COM_KUNENA_EDITOR_SUP');
Text::script('COM_KUNENA_EDITOR_CODE');
Text::script('COM_KUNENA_EDITOR_QUOTE');
Text::script('COM_KUNENA_EDITOR_SPOILER');
Text::script('COM_KUNENA_EDITOR_CONFIDENTIAL');
Text::script('COM_KUNENA_EDITOR_HIDE');
Text::script('COM_KUNENA_EDITOR_RIGHT');
Text::script('COM_KUNENA_EDITOR_LEFT');
Text::script('COM_KUNENA_EDITOR_CENTER');
Text::script('COM_KUNENA_EDITOR_HR');
Text::script('COM_KUNENA_EDITOR_FONTSIZE_SELECTION');
Text::script('COM_KUNENA_EDITOR_LINK');
Text::script('COM_KUNENA_EDITOR_EBAY');
Text::script('COM_KUNENA_EDITOR_MAP');
Text::script('COM_KUNENA_EDITOR_POLL_SETTINGS');
Text::script('COM_KUNENA_EDITOR_VIDEO');
Text::script('COM_KUNENA_EDITOR_VIDEO_PROVIDER');
Text::script('COM_KUNENA_EDITOR_IMAGELINK');
Text::script('COM_KUNENA_EDITOR_EMOTICONS');
Text::script('COM_KUNENA_EDITOR_TWEET');
Text::script('COM_KUNENA_EDITOR_INSTAGRAM');
Text::script('COM_KUNENA_EDITOR_SOUNDCLOUD');
Text::script('COM_KUNENA_EDITOR_COLOR_BLACK');
Text::script('COM_KUNENA_EDITOR_COLOR_ORANGE');
Text::script('COM_KUNENA_EDITOR_COLOR_RED');
Text::script('COM_KUNENA_EDITOR_COLOR_BLUE');
Text::script('COM_KUNENA_EDITOR_COLOR_PURPLE');
Text::script('COM_KUNENA_EDITOR_COLOR_GREEN');
Text::script('COM_KUNENA_EDITOR_COLOR_WHITE');
Text::script('COM_KUNENA_EDITOR_COLOR_GRAY');
Text::script('COM_KUNENA_EDITOR_ULIST');
Text::script('COM_KUNENA_EDITOR_LIST');
Text::script('COM_KUNENA_EDITOR_SIZE_VERY_VERY_SMALL');
Text::script('COM_KUNENA_EDITOR_SIZE_VERY_SMALL');
Text::script('COM_KUNENA_EDITOR_SIZE_SMALL');
Text::script('COM_KUNENA_EDITOR_SIZE_NORMAL');
Text::script('COM_KUNENA_EDITOR_SIZE_BIG');
Text::script('COM_KUNENA_EDITOR_SIZE_SUPER_BIGGER');
Text::script('COM_KUNENA_EDITOR_DIALOG_POLLS_PROPERTIES');
Text::script('COM_KUNENA_EDITOR_DIALOG_VIDEO_PROPERTIES');
Text::script('COM_KUNENA_EDITOR_DIALOG_MAPS_PROPERTIES');
Text::script('COM_KUNENA_EDITOR_DIALOG_BASIC_SETTINGS');
Text::script('COM_KUNENA_POLL_ADD_POLL_OPTION');
Text::script('COM_KUNENA_POLL_REMOVE_POLL_OPTION');
Text::script('COM_KUNENA_POLL_TIME_TO_LIVE');
Text::script('COM_KUNENA_POLL_TITLE');

$this->addScriptOptions('com_kunena.imageheight', $this->config->imageheight);
$this->addScriptOptions('com_kunena.imagewidth', $this->config->imagewidth);

HTMLHelper::_('jquery.ui');
$this->addScript('load-image.all.min.js');
$this->addScript('canvas-to-blob.min.js');
$this->addScript('jquery.iframe-transport.js');
$this->addScript('jquery.fileupload.js');
$this->addScript('jquery.fileupload-process.js');
$this->addScript('jquery.fileupload-image.js');
$this->addScript('jquery.fileupload-audio.js');
$this->addScript('jquery.fileupload-video.js');
$this->addScript('jquery.fileupload-validate.js');
$this->addScript('assets/js/upload.main.js');
$this->addStyleSheet('fileupload.css');

$this->k = 0;

$this->addScriptOptions('com_kunena.kunena_upload_files_set_inline', KunenaRoute::_('index.php?option=com_kunena&view=topic&task=setinline&format=json&' . Session::getFormToken() . '=1', false));
$this->addScriptOptions('com_kunena.kunena_upload_files_rem_inline_attachment', KunenaRoute::_('index.php?option=com_kunena&view=topic&task=removeinlineonattachment&format=json&' . Session::getFormToken() . '=1', false));
$this->addScriptOptions('com_kunena.kunena_upload_files_rem', KunenaRoute::_('index.php?option=com_kunena&view=topic&task=removeattachments&format=json&' . Session::getFormToken() . '=1', false));
$this->addScriptOptions('com_kunena.kunena_upload_files_preload_for_edit', KunenaRoute::_('index.php?option=com_kunena&view=topic&task=loadattachments&format=json&' . Session::getFormToken() . '=1', false));
$this->addScriptOptions('com_kunena.kunena_upload_files_maxfiles', $this->config->attachment_limit);
$this->addScriptOptions('com_kunena.kunena_upload_files_action', $this->action);
$this->addScriptOptions('com_kunena.icons.upload', KunenaIcons::upload());
$this->addScriptOptions('com_kunena.icons.trash', KunenaIcons::delete());
$this->addScriptOptions('com_kunena.icons.attach', KunenaIcons::attach());

$this->ktemplate = KunenaFactory::getTemplate();
$topicicontype   = $this->ktemplate->params->get('topicicontype');
$me              = isset($this->me) ? $this->me : KunenaUserHelper::getMyself();

// If polls are enabled, load also poll JavaScript.
if ($this->config->pollenabled)
{
    Text::script('COM_KUNENA_POLL_OPTION_NAME');
    Text::script('COM_KUNENA_EDITOR_HELPLINE_OPTION');
    $this->addScript('assets/js/poll.js');
}

$this->addScriptOptions('com_kunena.kunena_topicicontype', $topicicontype);
$this->addScriptOptions('com_kunena.allow_edit_poll', $this->config->allow_edit_poll);

echo $this->subLayout('Widget/Lightbox');

if (KunenaFactory::getTemplate()->params->get('formRecover'))
{
    $this->addScript('sisyphus.js');
}
?>
	<div id="modal_confirm_template_category" class="modal fade" tabindex="-1" role="dialog"
	     aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h3 id="myModalLabel"><?php echo Text::_('COM_KUNENA_MODAL_BOX_CATEGORY_TEMPLATE_TEXT_TITLE'); ?></h3>
				</div>
				<div class="modal-body">
					<p><?php echo Text::_('COM_KUNENA_MODAL_BOX_CATEGORY_TEMPLATE_TEXT_DESC'); ?></p>
				</div>
				<div class="modal-footer">
					<button class="btn btn-outline-primary border" data-dismiss="modal"
					        aria-hidden="true"><?php echo Text::_('COM_KUNENA_MODAL_BOX_CATEGORY_TEMPLATE_TEXT_CLOSE'); ?></button>
					<button class="btn btn-outline-primary border"
					        id="modal_confirm_erase"><?php echo Text::_('COM_KUNENA_MODAL_BOX_CATEGORY_TEMPLATE_TEXT_BUTTON_REPLACE'); ?></button>
					<button class="btn btn-outline-primary border"
					        id="modal_confirm_erase_keep_old"><?php echo Text::_('COM_KUNENA_MODAL_BOX_CATEGORY_TEMPLATE_TEXT_BUTTON_REPLACE_KEEP'); ?></button>
				</div>
			</div>
		</div>
	</div>

	<h1>
		<?php echo $this->escape($this->headerText) ?>
	</h1>

	<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post"
	      class="form-validate" role="form"
	      id="postform" name="postform" enctype="multipart/form-data" data-page-identifier="1">
		<input type="hidden" name="view" value="topic"/>
		<input id="kurl_topicons_request" type="hidden"
		       value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic&layout=topicicons&format=raw', false); ?>"/>
		<input id="kurl_category_template_text" type="hidden"
		       value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic&layout=categorytemplatetext&format=raw', false); ?>"/>
		<input id="kcategory_poll" type="hidden" name="kcategory_poll" value="<?php echo $this->message->catid; ?>"/>
		<input id="kpreview_url" type="hidden" name="kpreview_url"
		        value="<?php echo KunenaRoute::_('/index.php?option=com_kunena&view=topic&layout=edit&format=raw') ?>"/>
		<?php if (!$this->config->allow_change_subject)
			:
			?>
			<input type="hidden" name="subject" value="<?php echo $this->escape($this->message->subject); ?>"/>
		<?php endif; ?>
		<?php
		if ($this->message->exists())
			:
			?>
			<input type="hidden" name="task" value="edit"/>
			<input id="kmessageid" type="hidden" name="mesid" value="<?php echo intval($this->message->id) ?>"/>
		<?php else

			:
			?>
			<input type="hidden" name="task" value="post"/>
			<input type="hidden" name="parentid" value="<?php echo intval($this->message->parent) ?>"/>
		<?php endif; ?>
		<?php
		if (!isset($this->selectcatlist))
			:
			?>
			<input type="hidden" name="catid" value="<?php echo intval($this->message->catid) ?>"/>
		<?php endif; ?>
		<?php
		if ($this->category->id > 0):?>
			<input type="hidden" id="poll_catid" value="<?php echo intval($this->message->catid) ?>"/>
		<?php endif; ?>
		<?php
		if ($this->category->id && $this->category->id != $this->message->catid)
			:
			?>
			<input type="hidden" name="return" value="<?php echo intval($this->category->id) ?>"/>
		<?php endif; ?>
		<?php
		if ($this->message->getTopic()->first_post_id == $this->message->id && $this->message->getTopic()->getPoll()->id)
			:
			?>
			<input type="hidden" id="poll_exist_edit" name="poll_exist_edit"
			       value="<?php echo intval($this->message->getTopic()->getPoll()->id) ?>"/>
			<input type="hidden" id="ckeditor_dialog_polltitle" name="ckeditor_dialog_polltitle"
			       value="<?php echo $this->message->getTopic()->getPoll()->title ?>"/>
			<input type="hidden" id="ckeditor_dialog_polltimetolive" name="ckeditor_dialog_polltimetolive"
			       value="<?php echo $this->message->getTopic()->getPoll()->polltimetolive ?>"/>
			<?php foreach($this->message->getTopic()->getPoll()->getOptions() as $index => $option): ?>
				<input type="hidden" class="ckeditor_dialog_polloption" id="ckeditor_dialog_polloption<?php echo $index; ?>" name="ckeditor_dialog_polloption<?php echo $index; ?>"
				   value="<?php echo $option->text; ?>"/>
			<?php endforeach; ?>
		<?php endif; ?>
		<input type="hidden" id="kunena_upload" name="kunena_upload"
		       value="<?php echo intval($this->message->catid) ?>"/>
		<input type="hidden" id="kunena_upload_files_url"
		       value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic&task=upload&format=json&' . Session::getFormToken() . '=1', false) ?>"/>
		<?php if ($this->me->exists())
			:
			?>
			<input type="hidden" id="kurl_users" name="kurl_users"
			       value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&layout=listmention&format=raw') ?>"/>
		<?php endif; ?>
		<?php echo HTMLHelper::_('form.token'); ?>

		<div class="shadow-lg p-3 mb-5 rounded">

			<?php if (isset($this->selectcatlist)) : ?>
				<div class="form-group row">
					<!-- Material input -->
					<label for="inputCatlist"
					       class="col-sm-2 col-form-label"><?php echo Text::_('COM_KUNENA_CATEGORY') ?></label>
					<div class="col-md-10">
						<div class="md-form mt-0">
							<div class="controls"> <?php echo $this->selectcatlist ?> </div>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if ($this->category->allow_anonymous && !$this->me->userid) : ?>
				<div class="alert alert-info"><?php echo Text::_('COM_KUNENA_GEN_INFO_GUEST_CANNOT_EDIT_DELETE_MESSAGE'); ?></div>
				<div class="form-group row" id="kanynomous-check-name">
					<label for="kauthorname"
					       class="col-sm-2 col-form-label"><?php echo Text::_('COM_KUNENA_GEN_NAME'); ?></label>
					<div class="col-md-10">
						<input type="text" id="kauthorname" name="authorname"
						       placeholder="<?php echo Text::_('COM_KUNENA_TOPIC_EDIT_PLACEHOLDER_AUTHORNAME') ?>"
						       class="form-control" maxlength="35" tabindex="4"
						       value="<?php echo $this->escape($this->message->name); ?>"/>
						<!-- Encourage guest user to login or register -->
						<?php
						$login    = '<a class="btn-link" href="' . Route::_('index.php?option=com_users&view=login&return=' . base64_encode((string) Uri::getInstance())) . '"> ' . Text::_('JLOGIN') . '</a>';
						$register = ' ' . Text::_('COM_KUNENA_LOGIN_OR') . ' <a class="btn-link" href="index.php?option=com_users&view=registration">' . Text::_('JREGISTER') . '</a>';
						echo Text::sprintf('COM_KUNENA_LOGIN_PLEASE_SKIP', $login, $register);
						?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ($this->config->askemail && !$this->me->userid) : ?>
				<div class="form-group row">
					<!-- Material input -->
					<label for="email"
					       class="col-sm-2 col-form-label"><?php echo Text::_('COM_KUNENA_GEN_EMAIL'); ?></label>
					<div class="col-md-10">
						<div class="md-form mt-0">
							<input type="text" id="email" name="email" size="35"
							       placeholder="<?php echo Text::_('COM_KUNENA_TOPIC_EDIT_PLACEHOLDER_EMAIL') ?>"
							       class="form-control" maxlength="45" tabindex="5"
							       value="<?php echo !empty($this->message->email) ? $this->escape($this->message->email) : '' ?>"
							       required/>
							<br/>
							<?php echo $this->config->showemail == '0' ? Text::_('COM_KUNENA_POST_EMAIL_NEVER') : Text::_('COM_KUNENA_POST_EMAIL_REGISTERED'); ?>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<div class="form-group row">
				<label for="subject"
				       class="col-sm-2 col-form-label"><?php echo Text::_('COM_KUNENA_GEN_SUBJECT'); ?></label>
				<div class="col-md-10">
					<?php if (!$this->config->allow_change_subject && $this->topic->exists() && !KunenaUserHelper::getMyself()->isModerator($this->message->getCategory())) : ?>
						<input class="form-control" type="text" name="subject" id="subject"
						       value="<?php echo $this->escape($this->message->subject); ?>" disabled/>
					<?php else : ?>
						<input class="form-control" type="text"
						       placeholder="<?php echo Text::_('COM_KUNENA_TOPIC_EDIT_PLACEHOLDER_SUBJECT') ?>"
						       name="subject" id="subject"
						       maxlength="<?php echo $this->escape($this->ktemplate->params->get('SubjectLengthMessage')); ?>"
						       tabindex="6" value="<?php echo $this->escape($this->message->subject); ?>"/>
					<?php endif; ?>
				</div>
			</div>

			<?php if (!empty($this->topicIcons)) : ?>
				<div class="form-group row" id="kpost-topicicons">
					<!-- Material input -->
					<label for="inputIcon"
					       class="col-sm-2 col-form-label"><?php echo Text::_('COM_KUNENA_GEN_TOPIC_ICON'); ?></label>
					<div class="col-md-10">
						<div id="iconset_inject" class="controls controls-select">
							<div id="iconset_topic_list">
								<?php foreach ($this->topicIcons as $id => $icon) : ?>
								<input type="radio" id="radio<?php echo $icon->id ?>" name="topic_emoticon"
								       value="<?php echo $icon->id ?>" <?php echo !empty($icon->checked) ? ' checked="checked" ' : '' ?> />
									<?php if ($this->config->topicicons && $topicicontype == 'B4') : ?>
										<label class="radio inline" for="radio<?php echo $icon->id; ?>">
										<?php if (!$this->category->iconset) : $this->category->iconset = 'default'; endif; ?>
										<img src="<?php echo Uri::root() . 'media/kunena/topic_icons/' .  $this->category->iconset . '/user/svg/' . $icon->b4; ?>"
												alt="<?php echo $icon->name; ?>" width="32" height="32"/>
									<?php elseif ($this->config->topicicons && $topicicontype == 'fa') : ?>
										<label class="radio inline" for="radio<?php echo $icon->id; ?>"><i
												class="fa fa-<?php echo $icon->fa; ?> glyphicon-topic fa-2x"></i>
									<?php else : ?>
										<label class="radio inline" for="radio<?php echo $icon->id; ?>"><img
													src="<?php echo $icon->relpath; ?>"
													alt="<?php echo $icon->name; ?>"
													border="0"/>
									<?php endif; ?>
										</label>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php

				echo $this->subLayout('Widget/Editor')
					->setLayout($this->editorType)
					->set('message', $this->message)
					->set('config', $this->config)
					->set('poll', $this->message->getTopic()->getPoll())
					->set('allow_polls', $this->topic->getCategory()->allow_polls)
					->set('template', $this->ktemplate)
					->set('me', $this->me);
			?>
		</div>

		<?php if ($this->message->exists() && $this->config->editmarkup) : ?>
			<h2>
				<?php echo Text::_('COM_KUNENA_EDITING_REASON') ?>
			</h2>
			<div class="shadow-lg pl-5 pt-3 pb-1 mb-5 rounded">
				<div class="form-group row" id="modified-reason">
					<div class="col-md-10">
						<input class="form-control" name="modified_reason"
						       maxlength="200"
						       type="text"
						       value="<?php echo $this->modified_reason; ?>" title="reason"
						       placeholder="<?php echo Text::_('COM_KUNENA_EDITING_ENTER_REASON') ?>"/>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php if ($this->allowedExtensions && $this->UserCanPostImage) : ?>
			<h2>
				<?php echo Text::_('COM_KUNENA_EDITOR_ATTACHMENTS'); ?>
			</h2>
			<!-- Placeholder to display errors messages or exception for attachments  -->
			<div id="kattachments-message-container" aria-live="polite"></div>
			<!-- End of placeholder -->
			<div class="shadow-lg p-3 mb-5 rounded">
				<div class="form-group row krow<?php echo 1 + $this->k ^= 1; ?> p-3 mb-5" id="kpost-attachments">
					<div class="controls">
						<div id="kattach_form">
							<span class="label label-info"><?php echo Text::_('COM_KUNENA_FILE_EXTENSIONS_ALLOWED') ?>
								: <?php echo $this->escape(implode(', ', $this->allowedExtensions)) ?></span><br/><br/>
							<span class="label label-info"><?php echo Text::_('COM_KUNENA_UPLOAD_MAX_FILES_WEIGHT') ?>
								: <?php echo $this->config->filesize != 0 ? round($this->config->filesize / 1024, 1) : $this->config->filesize ?> <?php echo Text::_('COM_KUNENA_UPLOAD_ATTACHMENT_FILE_WEIGHT_MB') ?> <?php echo Text::_('COM_KUNENA_UPLOAD_MAX_IMAGES_WEIGHT') ?>
								: <?php echo $this->config->imagesize != 0 ? round($this->config->imagesize / 1024, 1) : $this->config->imagesize ?> <?php echo Text::_('COM_KUNENA_UPLOAD_ATTACHMENT_FILE_WEIGHT_MB') ?>
							</span>
							<br/>
							<br/>
							<!-- The fileinput-button span is used to style the file input field as button -->
							<span class="btn btn-outline-primary fileinput-button">
								<?php echo KunenaIcons::plus(); ?>
								<span><?php echo Text::_('COM_KUNENA_UPLOADED_LABEL_ADD_FILES_BUTTON') ?></span>
								<!-- The file input field used as target for the file upload widget -->
								<input id="fileupload" type="file" name="file" multiple>
							</span>
							<button id="insert-all" class="btn btn-outline-primary" type="submit"
							        style="display:none;">
								<?php echo KunenaIcons::upload(); ?>
								<span><?php echo Text::_('COM_KUNENA_UPLOADED_LABEL_INSERT_ALL_BUTTON') ?></span>
							</button>
							<button id="remove-all" class="btn btn-outline-danger" type="submit"
							        style="display:none;">
								<?php echo KunenaIcons::cancel(); ?>
								<span><?php echo Text::_('COM_KUNENA_UPLOADED_LABEL_REMOVE_ALL_BUTTON') ?></span>
							</button>
							<div class="clearfix"></div>
							<br/>
							<div id="progress" class="progress progress-striped" style="display: none;">
								<div class="bar"></div>
							</div>
							<!-- The container for the uploaded files -->
							<div id="files" class="files"></div>
							<div id="dropzone">
								<div class="dropzone">
									<div class="default message">
										<span id="klabel_info_drop_browse">
											<?php echo Text::_('COM_KUNENA_UPLOADED_LABEL_DRAG_AND_DROP_OR_BROWSE') ?>
										</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php if ($this->canSubscribe): ?>
			<h2>
				<?php echo Text::_('COM_KUNENA_POST_SUBSCRIBE'); ?>
			</h2>
			<div class="shadow-lg pl-5 pt-3 pb-1 mb-5 rounded">
				<div class="form-group row" id="kpost-subscribe">
					<div class="controls">
						<div class="custom-control custom-checkbox">
							<input type="checkbox" class="custom-control-input" name="subscribeMe" id="subscribeMe"
							       value="1" <?php if ($this->subscriptionschecked)
							{
								echo 'checked="checked"';
							} ?>/>
							<label class="custom-control-label"
							       for="subscribeMe"><?php echo Text::_('COM_KUNENA_POST_NOTIFIED'); ?></label>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php if ($this->message->userid && $this->message->getCategory()->allow_anonymous): ?>
			<h2>
				<?php echo Text::_('COM_KUNENA_POST_AS_ANONYMOUS'); ?>
			</h2>
			<div class="shadow-lg pl-5 pt-3 pb-1 mb-5 rounded">
				<div class="form-group row" id="kanynomous-check">
					<div class="controls">
						<div class="custom-control custom-checkbox">
							<input type="checkbox" class="custom-control-input" id="kanonymous" name="anonymous"
							       value="1" <?php if ($this->post_anonymous)
							{
								echo 'checked="checked"';
							} ?>/>
							<label class="custom-control-label"
							       for="kanonymous"><?php echo Text::_('COM_KUNENA_POST_AS_ANONYMOUS_DESC'); ?></label>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php if (!empty($this->captchaEnabled)): ?>
			<h2>
				<?php echo Text::_('COM_KUNENA_RECAPTCHA'); ?>
			</h2>
			<div class="shadow-lg p-3 mb-5 rounded">
				<div class="form-group row">
					<div class="controls">
						<?php echo $this->captchaDisplay; ?>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<div class="form-group row">
			<div class="col-md-10 center">
				<?php if($this->ktemplate->params->get('editor') == 'wysibb'): ?>
					<input type="submit" class="btn btn-success form-validate" name="submit"
				       value="<?php echo Text::_('COM_KUNENA_SUBMIT'); ?>"
				       title="<?php echo Text::_('COM_KUNENA_EDITOR_HELPLINE_SUBMIT'); ?>"/>
				<?php else: ?>
					<button id="form_submit_button" name="submit" type="submit" class="btn btn-success form-validate" tabindex="8">
						<?php echo KunenaIcons::save(); ?>
						<?php echo ' ' . Text::_('COM_KUNENA_SUBMIT') . ' '; ?>
					</button>
				<?php endif; ?>

				<button type="reset" class="btn btn-outline-primary btn-md" onclick="window.history.back();"
				        tabindex="10">
					<?php echo KunenaIcons::delete(); ?>
					<?php echo ' ' . Text::_('COM_KUNENA_CANCEL') . ' '; ?>
				</button>
			</div>
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
				if ($this->config->allow_change_subject)
				{
					echo '<script type="text/javascript">document.postform.subject.focus();</script>';
				}
			}
			else
			{
				echo '<script type="text/javascript">document.postform.message.focus();</script>';
			}
		}
		?>
		<div id="kattach-list"></div>
		<div id="poll_options">
			<!-- Placeholder for polls options if inserted in message -->
		</div>
	</form>
<?php
if ($this->config->showhistory && $this->topic->exists())
{
	echo $this->subRequest('Topic/Form/History', new Joomla\Input\Input(['id' => $this->topic->id]));
}
