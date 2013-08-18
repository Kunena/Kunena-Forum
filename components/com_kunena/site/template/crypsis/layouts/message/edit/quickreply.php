<?php
/**
 * Kunena Component
 * @package Kunena.Template.Crypsis
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

/** @var KunenaLayout $this */

/** @var KunenaForumMessage  $message  Message to reply to. */
$message = $this->message;
/** @var KunenaUser  $author  Author of the message. */
$author = isset($this->author) ? $this->author : $message->getAuthor();
/** @var KunenaForumTopic  $topic Topic of the message. */
$topic = isset($this->topic) ? $this->topic : $message->getTopic();
/** @var KunenaForumCategory  $category  Category of the message. */
$category = isset($this->category) ? $this->category : $message->getCategory();
/** @var KunenaConfig  $config  Kunena configuration. */
$config = isset($this->config) ? $this->config : KunenaFactory::getConfig();
/** @var KunenaUser  $me  Current user. */
$me = isset($this->me) ? $this->me : KunenaUserHelper::getMyself();
?>

<div class="modal hide fade" id="kreply<?php echo $message->displayField('id') ?>_form" data-backdrop="false" style="position: relative; top: 10px; left: 0; right: -10px; width:auto; margin:0; z-index: 1;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3>Reply to <?php echo $author->getLink() ?></h3>
	</div>
	<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topic') ?>" method="post" enctype="multipart/form-data" name="postform" id="postform">
		<input type="hidden" name="task" value="post" />
		<input type="hidden" name="parentid" value="<?php echo $message->displayField('id') ?>" />
		<input type="hidden" name="catid" value="<?php echo $category->displayField('id') ?>" />
		<?php echo JHtml::_( 'form.token' ) ?>

		<div class="modal-body">
		<?php if ($me->exists() && $category->allow_anonymous): ?>
		<input type="text" name="authorname" size="35" class="span12" maxlength="35" value="<?php echo $this->escape($me->getName()) ?>" />
		<input type="checkbox" id="kanonymous<?php echo $message->displayField('id') ?>" name="anonymous" value="1" class="kinputbox postinput" <?php if ($category->post_anonymous) echo 'checked="checked"'; ?> />
		<label for="kanonymous<?php echo intval($message->id) ?>"><?php echo JText::_('COM_KUNENA_POST_AS_ANONYMOUS_DESC') ?></label>
		<?php else: ?>
		<input type="hidden" name="authorname" value="<?php echo $this->escape($me->getName()) ?>" />
		<?php endif; ?>
		<input type="text" name="subject" size="35" class="inputbox" maxlength="<?php echo intval($config->maxsubject); ?>" value="<?php echo $message->displayField('subject') ?>" />
		<textarea class="span12" name="message" rows="6" cols="60"></textarea>
		<?php if ($topic->authorise('subscribe')) : ?>
		<input type="checkbox" name="subscribeMe" value="1" <?php echo ($config->subscriptionschecked == 1) ? 'checked="checked"' : '' ?> />
		<i><?php echo JText::_('COM_KUNENA_POST_NOTIFIED'); ?></i> <br />
		<?php endif; ?>
		</div>
		<div class="modal-footer">
			<input type="submit" class="btn kreply-submit" name="submit" value="<?php echo JText::_('COM_KUNENA_SUBMIT') ?>" title="<?php echo (JText::_('COM_KUNENA_EDITOR_HELPLINE_SUBMIT'));?>" />
			<button class="btn" data-dismiss="modal" aria-hidden="true" title="<?php echo (JText::_('COM_KUNENA_EDITOR_HELPLINE_CANCEL'));?>"><?php echo JText::_('COM_KUNENA_CANCEL') ?></button>
			<small><?php echo JText::_('COM_KUNENA_QMESSAGE_NOTE') ?></small>
		</div>
	</form>
</div>
