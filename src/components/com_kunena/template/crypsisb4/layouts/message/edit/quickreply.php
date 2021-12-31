<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsisb4
 * @subpackage      Layout.Message
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$message = $this->message;

if (!$message->isAuthorised('reply'))
{
	return;
}

$author = isset($this->author) ? $this->author : $message->getAuthor();

$topic = isset($this->topic) ? $this->topic : $message->getTopic();

$category = isset($this->category) ? $this->category : $message->getCategory();

$config = isset($this->config) ? $this->config : KunenaFactory::getConfig();

$me = isset($this->me) ? $this->me : KunenaUserHelper::getMyself();

// Load caret.js always before atwho.js script and use it for autocomplete, emojiis...
$this->addStyleSheet('jquery.atwho.css');
$this->addScript('jquery.caret.js');
$this->addScript('jquery.atwho.js');

$this->addScriptOptions('com_kunena.kunena_topicicontype', '');
$this->addScriptOptions('com_kunena.kunena_quickreplymesid', $message->displayField('id'));

$this->addScript('edit.js');

if (KunenaFactory::getTemplate()->params->get('formRecover'))
{
	$this->addScript('sisyphus.js');
}

$template = KunenaTemplate::getInstance();
$quick    = $template->params->get('quick');
$editor   = $template->params->get('editor');

if ($me->canDoCaptcha() && KunenaConfig::getInstance()->quickreply)
{
	$this->captchaDisplay = $template->recaptcha($message->id);
	$this->captchaEnabled = true;
}
?>

<?php if ($quick == 1)
:
	?>
	<div class="modal fade quickr" id="kreply<?php echo $message->displayField('id'); ?>_form" role="dialog"
		 aria-labelledby="myLargeModalLabel"
		 aria-hidden="true" style="display:none;" data-backdrop="false">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title">
						<?php echo Text::sprintf('COM_KUNENA_REPLYTO_X', $author->getLink()); ?>
					</h3>
					<button type="button" class="close kreply-cancel" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="card-body">
					<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic'); ?>"
						  method="post"
						  enctype="multipart/form-data" name="postform" id="postform" class="form-horizontal">
						<input type="hidden" name="task" value="post"/>
						<input type="hidden" name="parentid" value="<?php echo $message->displayField('id'); ?>"/>
						<input type="hidden" name="catid" value="<?php echo $category->displayField('id'); ?>"/>
						<?php
						if (!$config->allow_change_subject || $me->isModerator())
	:
							?>
							<input type="hidden" name="subject"
								   value="<?php echo $this->escape($this->message->subject); ?>"/>
						<?php endif; ?>
						<?php
						if ($me->exists())
	:
							?>
							<input type="hidden" id="kurl_users" name="kurl_users"
								   value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&layout=listmention&format=raw') ?>"/>
						<?php endif; ?>
						<?php echo HTMLHelper::_('form.token'); ?>

						<?php if (!$me->exists())
	:
							?>
							<div class="form-group">
								<label class="col-md-12 control-label" style="padding:0;">
									<?php echo Text::_('COM_KUNENA_GEN_NAME'); ?>:
								</label>
								<input type="text" name="authorname" class="form-control" maxlength="35"
									   placeholder="<?php echo Text::_('COM_KUNENA_GEN_NAME'); ?>" value=""
									   required/>
							</div>
						<?php endif; ?>

						<?php if ($config->askemail && !$me->exists())
	:
							?>
							<div class="form-group">
								<?php echo $config->showemail == '0' ? Text::_('COM_KUNENA_POST_EMAIL_NEVER') : Text::_('COM_KUNENA_POST_EMAIL_REGISTERED'); ?>
								<input type="text" id="email" name="email"
									   placeholder="<?php echo Text::_('COM_KUNENA_TOPIC_EDIT_PLACEHOLDER_EMAIL') ?>"
									   class="inputbox col-md-12 form-control" maxlength="45" value=""
									   required/>
							</div>
						<?php endif; ?>

						<div class="form-group">
							<label for="kanonymous<?php echo intval($message->id); ?>"
								   class="col-md-12 control-label" style="padding:0;">
								<?php echo Text::_('COM_KUNENA_GEN_SUBJECT'); ?>:
							</label>
							<input type="text" id="subject" name="subject" class="form-control"
								   maxlength="<?php echo $template->params->get('SubjectLengthMessage'); ?>"
								   <?php
									if (!$config->allow_change_subject && !$me->isModerator())
	:
										?>disabled<?php
									endif; ?>
								   value="<?php echo $message->displayField('subject'); ?>"/>
						</div>

						<div class="form-group">
							<label class="col-md-12 control-label" style="padding:0;">
								<?php echo Text::_('COM_KUNENA_MESSAGE'); ?>:
							</label>
							<?php if ($editor == 1)
	{
								echo $this->subLayout('Widget/Editor')->setLayout('wysibb_quick')->set('message', $this->message)->set('config', $config);
							}
							else
	{
								echo '<textarea class="qreply form-control qrlocalstorage' . $message->displayField("id") . '" id="editor" name="message" rows="6" cols="60" placeholder="' . Text::_('COM_KUNENA_ENTER_MESSAGE') . '"></textarea>';
							} ?>
						</div>

						<?php if ($topic->isAuthorised('subscribe'))
	:
							?>
							<div class="form-check">
								<input type="checkbox" class="form-check-input" style="float: left; margin-right: 10px;" id="subscribeMe" name="subscribeMe" value="1" <?php if ($config->subscriptionschecked == 1 && $me->canSubscribe != 0 || $config->subscriptionschecked == 0 && $me->canSubscribe == 1)
								{
									echo 'checked="checked"';
								} ?>>
								<label class="form-check-label" for="subscribeMe"><?php echo Text::_('COM_KUNENA_POST_NOTIFIED'); ?></label>
							</div>
						<?php endif; ?>

						<?php if ($me->exists() && $category->allow_anonymous)
						:
							?>
							<div class="form-check">
								<input type="checkbox" class="kinputbox postinput form-check-input" id="kanonymous<?php echo $message->displayField('id'); ?>" name="anonymous" value="1" <?php if ($category->post_anonymous)
									{
										echo 'checked="checked"';
									} ?>>
								<label class="form-check-label" for="kanonymous<?php echo $message->displayField('id'); ?>"><?php echo Text::_('COM_KUNENA_POST_AS_ANONYMOUS_DESC'); ?></label>
							</div>
						<?php endif; ?>
						<a href="<?php echo Route::_('index.php?option=com_kunena&view=topic&layout=reply&catid=' . $message->catid . '&id=' . $message->thread . '&mesid=' . $message->id . '&Itemid=' . KunenaRoute::getItemID()) ?>"
						   role="button" class="btn btn-outline-primary border btn-small btn-link float-right"
						   rel="nofollow"><?php echo Text::_('COM_KUNENA_GO_TO_EDITOR'); ?></a>
						<br/>

						<?php if (!empty($this->captchaEnabled))
						:
							?>
							<div class="control-group">
								<?php echo $this->captchaDisplay; ?>
							</div>
						<?php endif; ?>

						<div class="k-footer">
							<small><?php echo Text::_('COM_KUNENA_QMESSAGE_NOTE'); ?></small>
							<input type="submit" class="btn btn-outline-primary border kreply-submit" name="submit"
								   value="<?php echo Text::_('COM_KUNENA_SUBMIT'); ?>"
								   title="<?php echo Text::_('COM_KUNENA_EDITOR_HELPLINE_SUBMIT');
									?>"/>
							<?php // TODO: remove data on cancel. ?>
							<input type="reset" name="reset" class="btn btn-outline-primary border kreply-cancel"
								   value="<?php echo ' ' . Text::_('COM_KUNENA_CANCEL') . ' '; ?>"
								   title="<?php echo Text::_('COM_KUNENA_EDITOR_HELPLINE_CANCEL'); ?>"
								   data-dismiss="modal" aria-hidden="true"/>
						</div>
						<input type="hidden" id="kurl_emojis" name="kurl_emojis"
							   value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic&layout=listemoji&format=raw') ?>"/>
						<input type="hidden" id="kemojis_allowed" name="kemojis_allowed"
							   value="<?php echo $config->disemoticons ?>"/>
					</form>
				</div>
			</div>
		</div>
	</div>
<?php elseif ($quick == 0)
:
	?>
	<div class="modal fade" style="position: relative; top: 10px; right: -10px; width:100%; z-index: 1;" id="kreply<?php echo $message->displayField('id'); ?>_form" tabindex="-1" role="dialog" aria-labelledby="kreply<?php echo $message->displayField('id'); ?>_formLabel" aria-hidden="true" data-backdrop="false">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="kreply<?php echo $message->displayField('id'); ?>_formLabel"><?php echo Text::sprintf('COM_KUNENA_REPLYTO_X', $author->getLink()); ?></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic'); ?>"
						  method="post"
						  enctype="multipart/form-data" name="postform" id="postform" class="form-horizontal">
						<input type="hidden" name="task" value="post"/>
						<input type="hidden" name="parentid" value="<?php echo $message->displayField('id'); ?>"/>
						<input type="hidden" name="catid" value="<?php echo $category->displayField('id'); ?>"/>
						<?php
						if (!$config->allow_change_subject || $me->isModerator())
						:
							?>
							<input type="hidden" name="subject"
								   value="<?php echo $this->escape($this->message->subject); ?>"/>
						<?php endif; ?>
						<?php
						if ($me->exists())
						:
							?>
							<input type="hidden" id="kurl_users" name="kurl_users"
								   value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&layout=listmention&format=raw') ?>"/>
						<?php endif; ?>
						<?php echo HTMLHelper::_('form.token'); ?>

						<?php if (!$me->exists())
						:
							?>
							<div class="form-group">
								<label class="col-md-12 control-label" style="padding:0;">
									<?php echo Text::_('COM_KUNENA_GEN_NAME'); ?>:
								</label>
								<input type="text" name="authorname" class="form-control" maxlength="35"
									   placeholder="<?php echo Text::_('COM_KUNENA_GEN_NAME'); ?>" value=""
									   required/>
							</div>
						<?php endif; ?>

						<?php if ($config->askemail && !$me->exists())
						:
							?>
							<div class="form-group">
								<?php echo $config->showemail == '0' ? Text::_('COM_KUNENA_POST_EMAIL_NEVER') : Text::_('COM_KUNENA_POST_EMAIL_REGISTERED'); ?>
								<input type="text" id="email" name="email"
									   placeholder="<?php echo Text::_('COM_KUNENA_TOPIC_EDIT_PLACEHOLDER_EMAIL') ?>"
									   class="inputbox col-md-12 form-control" maxlength="45" value=""
									   required/>
								</div>
							<?php endif; ?>

							<div class="form-group">
								<label for="kanonymous<?php echo intval($message->id); ?>"
									   class="col-md-12 control-label" style="padding:0;">
									<?php echo Text::_('COM_KUNENA_GEN_SUBJECT'); ?>:
								</label>
								<input type="text" id="subject" name="subject" class="form-control"
									   maxlength="<?php echo $template->params->get('SubjectLengthMessage'); ?>"
									   <?php
										if (!$config->allow_change_subject && !$me->isModerator())
										:
											?>disabled<?php
										endif; ?>
									   value="<?php echo $message->displayField('subject'); ?>"/>
							</div>
							<div class="form-group">
								<label class="col-md-12 control-label" style="padding:0;">
									<?php echo Text::_('COM_KUNENA_MESSAGE'); ?>:
								</label>
								<?php if ($editor == 1)
								{
									echo $this->subLayout('Widget/Editor')->setLayout('wysibb_quick')->set('message', $this->message)->set('config', $config);
								}
								else
								{
									echo '<textarea class="qreply form-control qrlocalstorage' . $message->displayField("id") . '" id="editor" name="message" rows="6" cols="60" placeholder="' . Text::_('COM_KUNENA_ENTER_MESSAGE') . '"></textarea>';
								} ?>
							</div>

							<?php if ($topic->isAuthorised('subscribe'))
							:
								?>
								<div class="form-check">
									<input type="checkbox" class="form-check-input" style="float: left; margin-right: 10px;" id="subscribeMe" name="subscribeMe" value="1" <?php if ($config->subscriptionschecked == 1 && $me->canSubscribe != 0 || $config->subscriptionschecked == 0 && $me->canSubscribe == 1)
									{
										echo 'checked="checked"';
									} ?>>
									<label class="form-check-label" for="subscribeMe"><?php echo Text::_('COM_KUNENA_POST_NOTIFIED'); ?></label>
								</div>
							<?php endif; ?>
							<?php
							if ($me->exists() && $category->allow_anonymous)
							:
							?>
							<div class="form-check">
								<input type="checkbox" class="kinputbox postinput form-check-input" id="kanonymous<?php echo $message->displayField('id'); ?>" name="anonymous" value="1" <?php if ($category->post_anonymous)
									{
										echo 'checked="checked"';
									} ?>>
								<label class="form-check-label" for="kanonymous<?php echo $message->displayField('id'); ?>"><?php echo Text::_('COM_KUNENA_POST_AS_ANONYMOUS_DESC'); ?></label>
							</div>
						<?php endif; ?>
						<div class="form-group">
							<a href="<?php echo Route::_('index.php?option=com_kunena&view=topic&layout=reply&catid=' . $message->catid . '&id=' . $message->thread . '&mesid=' . $message->id . '&Itemid=' . KunenaRoute::getItemID()) ?>"
						   role="button" class="btn btn-outline-primary border btn-small btn-link float-right gotoeditor"
						   id="qrlocalstorage<?php echo $message->displayField('id'); ?>"
						   rel="nofollow"><?php echo Text::_('COM_KUNENA_GO_TO_EDITOR'); ?></a>
							<br/>
						</div>
						<?php if (!empty($this->captchaEnabled))
						:
							?>
							<div class="control-group">
								<?php echo $this->captchaDisplay; ?>
							</div>
						<?php endif; ?>

						<input type="hidden" id="kurl_emojis" name="kurl_emojis"
							   value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic&layout=listemoji&format=raw') ?>"/>
						<input type="hidden" id="kemojis_allowed" name="kemojis_allowed"
							   value="<?php echo $config->disemoticons ?>"/>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo Text::_('COM_KUNENA_CANCEL'); ?></button>
					<button type="button" class="btn btn-primary"><?php echo Text::_('COM_KUNENA_SUBMIT'); ?></button>
				</div>
			</div>
		</div>
	</div>
<?php endif;
