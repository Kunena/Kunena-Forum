<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Message
 *
 * @copyright   (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

$isReply = $this->message->id != $this->topic->first_post_id;
$signature = $this->profile->getSignature();
$attachments = $this->message->getAttachments();
?>

<div class="chat row-fluid">
	<div class="bubble <?php echo $this->profile->isMyself() ? 'me' : 'you'; ?> span12">
		<h3>
			<?php echo $this->profile->getLink(); ?>
			<small>
				<?php echo (!$isReply) ? 'Created the topic.' : 'Replied the topic.'; ?>
			</small>
			<small class="pull-right">
				<?php echo $this->message->getTime()->toSpan('config_post_dateformat', 'config_post_dateformat_hover'); ?>
				<a href="#<?php echo $this->message->id; ?>">#<?php echo $this->location; ?></a>
			</small>
		</h3>

		<?php if ($this->message->subject) : ?>
		<h3>
			<?php echo ($isReply ? JText::_('COM_KUNENA_RE').' ' : '') . $this->message->displayField('subject'); ?>
		</h3>
		<?php endif; ?>

		<p class="kmsg">
			<?php echo $this->message->displayField('message'); ?>
		</p>

		<?php if (!empty($attachments)) : ?>
		<h4>
			<?php echo JText::_('COM_KUNENA_ATTACHMENTS'); ?>
		</h4>
		<ul class="thumbnails">

			<?php foreach($attachments as $attachment) : ?>
				<li class="span4">
					<div class="thumbnail">
						<?php echo $attachment->getThumbnailLink(); ?>
						<?php echo $attachment->getTextLink(); ?>
					</div>
				</li>
			<?php endforeach; ?>

		</ul>
		<?php endif; ?>


		<?php if ($signature) : ?>
		<div class="pull-left">
			<?php echo $signature; ?>
		</div>
		<?php endif ?>

		<?php if (!empty($this->reportMessageLink)) : ?>
		<div class="pull-left">
			<p>
				<i class="icon-warning-sign"></i>
				<?php echo $this->reportMessageLink; ?>
			</p>
		</div>
		<?php endif; ?>

		<?php if (!empty($this->ipLink)) : ?>
		<div class="pull-right">
			<p>
				<?php echo $this->ipLink; ?>
			</p>
		</div>
		<?php endif; ?>

		<div class="clearfix"></div>
	</div>
</div>
