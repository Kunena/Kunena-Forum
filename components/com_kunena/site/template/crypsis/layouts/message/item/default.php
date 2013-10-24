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

$isReply = $this->message->id != $this->topic->first_post_id;
?>

<div class="chat row-fluid">
	<div class="bubble <?php echo $this->profile->isMyself() ? 'me' : 'you'; ?> span12">
		<h3>
			<?php echo $this->profile->getLink() ?>
			<small>
			<?php if (!$isReply) {
				echo 'Created a new topic.';
			} else {
				echo 'Replied the topic.';
			}
			?>
			</small>
			<small class="pull-right">
				<?php echo KunenaDate::getInstance($this->message->time)->toSpan('config_post_dateformat', 'config_post_dateformat_hover') ?>
				<?php echo $this->numLink ?>
			</small>
		</h3>
	<?php if ($this->message->subject) : ?>
		<h3>
			<?php echo ($isReply ? JText::_('COM_KUNENA_RE').' ' : '') . $this->message->displayField('subject') ?>
		</h3>
	<?php endif; ?>

		<hr />
		<p class="kmsg">
			<?php echo KunenaHtmlParser::parseBBCode ($this->message->message, $this->view) ?>
		</p>
		<hr />
		<?php if (!empty($this->attachments)) : ?>
		<h4>
			<?php echo JText::_('COM_KUNENA_ATTACHMENTS');?>
		</h4>
		<ul class="thumbnails">
			<?php foreach($this->attachments as $attachment) : ?>
				<li class="span4">
					<div class="thumbnail">
						<?php echo $attachment->getThumbnailLink(); ?>
						<?php echo $attachment->getTextLink(); ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php endif; ?>

		<?php if ($this->signatureHtml) : ?>
		<div class="pull-left">
			<?php echo $this->signatureHtml ?>
		</div>
		<?php endif ?>
		<?php if (!empty($this->reportMessageLink)) :?>
			<div class="pull-right">
				<p><?php echo $this->reportMessageLink ?></p>

				<?php if (!empty($this->ipLink)) : ?>
				<p><?php echo $this->ipLink ?></p>
				<?php endif ?>
		</div>
		<div class="clearfix"></div>
		<?php endif ?>
	</div>
</div>
