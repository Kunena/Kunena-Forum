<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Message
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

// @var KunenaLayout $this


// @var KunenaForumMessage  $message  Message to reply to.

$message = $this->message;

if (!$message->isAuthorised('reply'))
{
	return;
}

// @var KunenaUser  $author  Author of the message.

$author = isset($this->author) ? $this->author : $message->getAuthor();
// @var KunenaForumTopic  $topic Topic of the message.

$topic = isset($this->topic) ? $this->topic : $message->getTopic();
// @var KunenaForumCategory  $category  Category of the message.

$category = isset($this->category) ? $this->category : $message->getCategory();
// @var KunenaConfig  $config  Kunena configuration.

$config = isset($this->config) ? $this->config : KunenaFactory::getConfig();
// @var KunenaUser  $me  Current user.

$me = isset($this->me) ? $this->me : KunenaUserHelper::getMyself();

$this->addScript('assets/js/edit.js');

if (KunenaFactory::getTemplate()->params->get('formRecover'))
{
	$this->addScript('assets/js/sisyphus.js');
}

// Fixme: can't get the controller working on this
if ($me->canDoCaptcha() )
{
	if (JPluginHelper::isEnabled('captcha'))
	{
		$plugin = JPluginHelper::getPlugin('captcha');
		$params = new JRegistry($plugin[0]->params);

		$captcha_pubkey = $params->get('public_key');
		$catcha_privkey = $params->get('private_key');

		if (!empty($captcha_pubkey) && !empty($catcha_privkey))
		{
			JPluginHelper::importPlugin('captcha');
			$dispatcher = JDispatcher::getInstance();
			$result = $dispatcher->trigger('onInit', 'dynamic_recaptcha_' . $this->message->id);
			$output = $dispatcher->trigger('onDisplay', array(null, 'dynamic_recaptcha_' . $this->message->id,
				'class="controls g-recaptcha" data-sitekey="' . $captcha_pubkey . '" data-theme="light"'));
			$this->quickcaptchaDisplay = $output[0];
			$this->quickcaptchaEnabled = $result[0];
		}
	}
}

$template = KunenaTemplate::getInstance();
$quick = $template->params->get('quick');
?>

<?php if ($quick == 1) : ?>
<div class="modal fade" id="kreply<?php echo $message->displayField('id'); ?>_form" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display:none;" data-backdrop="false">
	<div class="modal-dialog" role="document">
		<div class="modal-content">

<?php elseif ($quick == 0) : ?>
<div class="col-md-12 qreplyform" id="kreply<?php echo $message->displayField('id'); ?>_form"  style="position: relative; top: 10px; left: -20px; right: -10px; width:100%; z-index: 1;display: none;">
	<div class="panel panel-default">
		<div class="panel-body">
<?php endif;?>
			<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic'); ?>" method="post"
				enctype="multipart/form-data" name="postform" id="postform" class="form-horizontal">
				<input type="hidden" name="task" value="post" />
				<input type="hidden" name="parentid" value="<?php echo $message->displayField('id'); ?>" />
				<input type="hidden" name="catid" value="<?php echo $category->displayField('id'); ?>" />
				<?php if (!$config->allow_change_subject) : ?>
					<input type="hidden" name="subject" value="<?php echo $this->escape($this->message->subject); ?>" />
				<?php endif; ?>
				<?php echo JHtml::_('form.token'); ?>

				<div class="modal-header">
					<button type="button" class="close kreply-cancel" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
					<h3>
						<?php echo JText::sprintf('COM_KUNENA_REPLYTO_X', $author->getLink()); ?>
					</h3>
				</div>

				<div class="modal-body">
					<?php if (!$me->exists()) : ?>
						<div class="form-group">
							<label class="col-md-12 control-label" style="padding:0;">
								<?php echo JText::_('COM_KUNENA_GEN_NAME'); ?>:
							</label>
							<input type="text" name="authorname" class="form-control" maxlength="35" placeholder="<?php echo JText::_('COM_KUNENA_GEN_NAME'); ?>" value="" required />
						</div>
					<?php endif; ?>

					<?php if ($config->askemail && !$me->exists()): ?>
						<div class="form-group">
							<?php echo $config->showemail == '0' ? JText::_('COM_KUNENA_POST_EMAIL_NEVER') : JText::_('COM_KUNENA_POST_EMAIL_REGISTERED'); ?>
							<input type="text" id="email" name="email" placeholder="<?php echo JText::_('COM_KUNENA_TOPIC_EDIT_PLACEHOLDER_EMAIL') ?>" class="inputbox col-md-12 form-control" maxlength="45" value="" required />
						</div>
					<?php endif; ?>

					<div class="form-group">
						<label for="kanonymous<?php echo intval($message->id); ?>" class="col-md-12 control-label" style="padding:0;">
							<?php echo JText::_('COM_KUNENA_GEN_SUBJECT'); ?>:
						</label>
						<input type="text" id="subject" name="subject" class="form-control"
								maxlength="<?php echo $template->params->get('SubjectLengthMessage'); ?>"
								<?php if (!$config->allow_change_subject && !$me->isModerator()): ?>disabled<?php endif; ?>
								value="<?php echo $message->displayField('subject'); ?>" />
					</div>
					<div class="form-group">
						<label class="col-md-12 control-label" style="padding:0;">
							<?php echo JText::_('COM_KUNENA_MESSAGE'); ?>:
						</label>
						<textarea class="qreply form-control" id="kbbcode-message" name="message" rows="6" cols="60" placeholder="<?php echo JText::_('COM_KUNENA_ENTER_MESSAGE') ?>"></textarea>
					</div>

					<?php if ($topic->isAuthorised('subscribe')) : ?>
					<div class="clearfix"></div>
					<div class="control-group">
						<div id="mesubscribe">
							<input style="float: left; margin-right: 10px;" type="checkbox" name="subscribeMe" id="subscribeMe" value="1" <?php if ($config->subscriptionschecked == 1 && $me->canSubscribe != 0 || $config->subscriptionschecked == 0 && $me->canSubscribe == 1)
							{
								echo 'checked="checked"';
							} ?> />
							<label class="string optional col-md-12 control-label" style="padding:0;" for="subscribeMe"><?php echo JText::_('COM_KUNENA_POST_NOTIFIED'); ?></label>
						</div>
					</div>
					<?php endif; ?>
					<?php if ($me->exists() && $category->allow_anonymous) : ?>
					<div class="control-group">
						<div class="controls">
						<input type="checkbox" id="kanonymous<?php echo $message->displayField('id'); ?>" name="anonymous"
							value="1" class="kinputbox postinput form-control" <?php if ($category->post_anonymous) echo 'checked="checked"'; ?> />
						<label for="kanonymous<?php echo intval($message->id); ?>">
							<?php echo JText::_('COM_KUNENA_POST_AS_ANONYMOUS_DESC'); ?>
						</label>
						</div>
					</div>
					<?php endif; ?>
					<a href="index.php?option=com_kunena&view=topic&layout=reply&catid=<?php echo $message->catid;?>&id=<?php echo $message->thread;?>&mesid=<?php echo $message->id;?>&Itemid=<?php echo KunenaRoute::getItemID();?>" role="button" class="btn btn-default btn-small btn-link pull-right" rel="nofollow"><?php echo JText::_('COM_KUNENA_GO_TO_EDITOR'); ?></a>
					<br />
				</div>
				<?php if (!empty($this->quickcaptchaEnabled)) : ?>
					<div class="control-group">
						<?php echo $this->quickcaptchaDisplay;?>
					</div>
				<?php endif; ?>
				<div class="modal-footer">
					<small><?php echo JText::_('COM_KUNENA_QMESSAGE_NOTE'); ?></small>
					<input type="submit" class="btn btn-primary kreply-submit" name="submit"
					       value="<?php echo JText::_('COM_KUNENA_SUBMIT'); ?>"
					       title="<?php echo (JText::_('COM_KUNENA_EDITOR_HELPLINE_SUBMIT')); ?>" />
					<?php //TODO: remove data on cancel. ?>
					<input type="reset" name="reset" class="btn btn-default kreply-cancel"
						value="<?php echo (' ' . JText::_('COM_KUNENA_CANCEL') . ' ');?>"
						title="<?php echo (JText::_('COM_KUNENA_EDITOR_HELPLINE_CANCEL'));?>" data-dismiss="modal" aria-hidden="true" />
				</div>
				<input type="hidden" id="kurl_emojis" name="kurl_emojis" value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic&layout=listemoji&format=raw') ?>" />
				<input type="hidden" id="kemojis_allowed" name="kemojis_allowed" value="<?php echo $config->disemoticons ?>" />
			</form>
		</div>
	</div>
</div>
