<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Topic
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;
$topicStarter = $this->topic->first_post_userid == $this->message->userid;
$template = KunenaTemplate::getInstance();
$direction = $template->params->get('avatarPosition');

if ($direction === "left") : ?>
	<div class="row-fluid message message-<?php echo $this->message->getState(); ?>">
		<div class="span2 hidden-phone">
			<?php echo $this->subLayout('User/Profile')->set('user', $this->profile)->setLayout('default')->set('topic_starter', $topicStarter)->set('category_id', $this->category->id); ?>
		</div>
		<div class="span10">
			<?php echo $this->subLayout('Message/Item')->setProperties($this->getProperties()); ?>
			<?php echo $this->subRequest('Message/Item/Actions')->set('mesid', $this->message->id); ?>
			<?php echo $this->subLayout('Message/Edit')->set('message', $this->message)->set('captchaEnabled', $this->captchaEnabled)->setLayout('quickreply'); ?>
		</div>
	</div>
<?php elseif ($direction === "right") : ?>
	<div class="row-fluid message message-<?php echo $this->message->getState(); ?>">
		<div class="span10">
			<?php echo $this->subLayout('Message/Item')->setProperties($this->getProperties()); ?>
			<?php echo $this->subRequest('Message/Item/Actions')->set('mesid', $this->message->id); ?>
			<?php echo $this->subLayout('Message/Edit')->set('message', $this->message)->set('captchaEnabled', $this->captchaEnabled)->setLayout('quickreply'); ?>
		</div>
		<div class="span2 hidden-phone">
			<?php echo $this->subLayout('User/Profile')->set('user', $this->profile)->setLayout('default')->set('topic_starter', $topicStarter)->set('category_id', $this->category->id); ?>
		</div>
	</div>
<?php elseif ($direction === "top") : ?>
	<div class="row-fluid message message-<?php echo $this->message->getState(); ?>">
		<div class="span12" style="margin-left: 0;">
			<?php echo $this->subLayout('Message/Item/Top')->setProperties($this->getProperties()); ?>
			<?php echo $this->subRequest('Message/Item/Actions')->set('mesid', $this->message->id); ?>
			<?php echo $this->subLayout('Message/Edit')->set('message', $this->message)->set('captchaEnabled', $this->captchaEnabled)->setLayout('quickreply'); ?>
		</div>
	</div>
<?php elseif ($direction === "bottom") : ?>
	<div class="row-fluid message message-<?php echo $this->message->getState(); ?>">
		<div class="span12" style="margin-left: 0;">
			<?php echo $this->subLayout('Message/Item/Bottom')->setProperties($this->getProperties()); ?>
			<?php echo $this->subRequest('Message/Item/Actions')->set('mesid', $this->message->id); ?>
			<?php echo $this->subLayout('Message/Edit')->set('message', $this->message)->set('captchaEnabled', $this->captchaEnabled)->setLayout('quickreply'); ?>
		</div>
	</div>

<?php endif; ?>

<?php echo $this->subLayout('Widget/Module')->set('position', 'kunena_msg_' . $this->message->replynum); ?>
