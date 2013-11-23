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
	<div class="bubble span12">
		<h5>
			<?php echo $this->profile->getLink(); ?>
			<small>
				<?php echo (!$isReply) ? 'created the topic:' : 'replied the topic'; ?>
				<?php if ($this->message->subject) : ?>
			<?php echo $this->message->displayField('subject'); ?>
		<?php endif; ?>
			</small>
			<small class="pull-right">
				<?php echo $this->message->getTime()->toSpan('config_post_dateformat', 'config_post_dateformat_hover'); ?>
				<a href="#<?php echo $this->message->id; ?>">#<?php echo $this->location; ?></a>
			</small>
		</h5>

		<hr>
		
		<p class="kmsg">
			<?php echo $this->message->displayField('message'); ?>
		</p>

		<?php if (!empty($attachments)) : ?>
		<h5>
			<?php echo JText::_('COM_KUNENA_ATTACHMENTS'); ?>
		</h5>
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
		<h5>
		<div>
			<p><?php echo $signature; ?></p>
		</div>
		</h5>
		<?php endif ?>

		<?php if (!empty($this->reportMessageLink)) : ?>
		<h5>
		<div class="pull-left">
			<p>
				<i class="icon-warning"></i>
				<?php echo $this->reportMessageLink; ?>
			</p>
		</div>
		</h5>
		<?php endif; ?>

		<?php if (!empty($this->ipLink)) : ?>
		<h5>
		<div class="pull-right">
			<p>
				<?php echo $this->ipLink; ?>
			</p>
		</div>
		</h5>
		<?php endif; ?>

		<div class="clearfix"></div>
	</div>
</div>
