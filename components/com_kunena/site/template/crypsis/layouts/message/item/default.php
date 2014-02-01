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
$avatarname = $this->profile->getname();
?>
<small class="text-muted pull-right hidden-phone" style="margin-top:-10px;">
			<span class="icon icon-clock"></span>
				<?php echo $this->message->getTime()->toSpan('config_post_dateformat', 'config_post_dateformat_hover'); ?>
				<a href="#<?php echo $this->message->id; ?>">#<?php echo $this->location; ?></a>
</small>
<div class="badger-left badger-info" data-badger="<?php echo (!$isReply) ? $avatarname .' created the topic: ' : $avatarname . ' replied the topic: '; ?>
				<?php if ($this->message->subject) : ?>
			<?php echo $this->message->displayField('subject'); ?>
		<?php endif; ?> ">
			
		
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
			<a href="#report" role="button" class="btn-link" data-toggle="modal"><i class="icon-warning"></i> <?php echo JText::_('COM_KUNENA_REPORT')?></a>
			<div id="report" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<?php echo $this->subRequest('Topic/Report')->set('id', $this->topic->id);?>
			</div>
			</div>
		<?php endif; ?>

		<?php if (!empty($this->ipLink)) : ?>
		<div class="pull-right">
			<p>
				<?php echo $this->ipLink; ?>
			</p>
		</div>
		<?php endif; ?>
	
	<?php if(!empty($this->thankyou)): ?>
		<span class="kmessage-thankyou">
			<?php
				echo JText::_('COM_KUNENA_THANKYOU').': '.implode(', ', $this->thankyou).' ';
				if ($this->more_thankyou) echo JText::sprintf('COM_KUNENA_THANKYOU_MORE_USERS',$this->more_thankyou);
			?>
		</span>
	<?php endif; ?>
</div>