<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Message
 *
 * @copyright   (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/** @var KunenaForumMessage $message */
$message = $this->message;
$isReply = $this->message->id != $this->topic->first_post_id;
$signature = $this->profile->getSignature();
$attachments = $message->getAttachments();
$avatarname = $this->profile->getname();
$topicStarter = $this->topic->first_post_userid == $this->message->userid;
?>



<small class="text-muted pull-right hidden-phone" style="margin-top:-5px;">
	<span class="icon icon-clock"></span>
	<?php echo $message->getTime()->toSpan('config_post_dateformat', 'config_post_dateformat_hover'); ?>
	<a href="#<?php echo $this->escape($message->id); ?>">#<?php echo $this->location; ?></a>
</small>
<div class="clear-fix"></div>
<div class="horizontal-message">
	<div class="profile-horizontal-top">
	<?php echo $this->subLayout('User/Profile')->set('user', $this->profile)->setLayout('horizontal')->set('topic_starter', $topicStarter); ?>
	</div>
	<div class="horizontal-message-top" data-badger="<?php echo (!$isReply) ? $this->escape($avatarname) . ' created the topic: ' : $this->escape($avatarname) . ' replied the topic: '; ?><?php echo $message->displayField('subject'); ?>">
		<div class="kmessage">
		<div class="horizontal-message-text">
			<p class="kmsg"> <?php echo $message->displayField('message'); ?> </p>
		</div>
	<?php if (!empty($attachments)) : ?>
		<div class="kattach">
			<h5> <?php echo JText::_('COM_KUNENA_ATTACHMENTS'); ?> </h5>
			<ul class="thumbnails">
				<?php foreach ($attachments as $attachment) : ?>
					<li class="span3" style=" text-align: center;">
						<div class="thumbnail"> <?php echo $attachment->getLayout()->render('thumbnail'); ?> <?php echo $attachment->getLayout()->render('textlink'); ?> </div>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
	<?php if ($signature) : ?>
		<div class="ksig">
			<hr>
			<span class="ksignature"><?php echo $signature; ?></span>
		</div>
	<?php endif ?>
	<?php if (!empty($this->reportMessageLink)) : ?>
		<div class="msgfooter">
			<a href="#report" role="button" class="btn-link" data-toggle="modal"><i class="icon-warning"></i> <?php echo JText::_('COM_KUNENA_REPORT') ?></a>

			<div id="report" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<?php echo $this->subRequest('Topic/Report')->set('id', $this->topic->id); ?> </div>
			</div>
			<div class="pull-right">
				<p> <?php echo $this->ipLink; ?> </p>
			</div>
		</div>
	<?php endif; ?>
		</div>
</div>
</div>
<?php if ($message->modified_by && $this->config->editmarkup) :
	$dateshown = $datehover = '';
	if ($message->modified_time) {
		$datehover = 'title="' . KunenaDate::getInstance($message->modified_time)->toKunena('config_post_dateformat_hover') . '"';
		$dateshown = KunenaDate::getInstance($message->modified_time)->toKunena('config_post_dateformat') . ' ';
	} ?>
	<div class="alert alert-info hidden-phone" <?php echo $datehover ?>>
		<?php echo JText::_('COM_KUNENA_EDITING_LASTEDIT') . ': ' . $dateshown . JText::_('COM_KUNENA_BY') . ' ' . $message->getModifier()->getLink() . '.'; ?>
		<?php if ($message->modified_reason) echo JText::_('COM_KUNENA_REASON') . ': ' . $this->escape($message->modified_reason); ?>
	</div>
<?php endif; ?>

<?php if (!empty($this->thankyou)): ?>
	<div class="kmessage-thankyou">
		<?php
		echo JText::_('COM_KUNENA_THANKYOU') . ': ' . implode(', ', $this->thankyou) . ' ';
		if ($this->more_thankyou) echo JText::sprintf('COM_KUNENA_THANKYOU_MORE_USERS', $this->more_thankyou);
		?>
	</div>
<?php endif; ?>
