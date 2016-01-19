<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Template.Crypsis
 * @subpackage  Topic
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die ();

$k = 0;
?>
<div class="pull-right">
	<div class="btn btn-small" data-toggle="collapse" data-target="#history">X</div>
</div>
<h2>
	<?php echo JText::_('COM_KUNENA_POST_TOPIC_HISTORY') ?>:
	<?php echo $this->escape($this->topic->subject) ?>
</h2>

<div id="history" class="collapse in">
	<p>
		<?php echo JText::_('COM_KUNENA_POST_TOPIC_HISTORY_MAX') . ' ' . $this->escape($this->config->historylimit) . ' ' . JText::_('COM_KUNENA_POST_TOPIC_HISTORY_LAST') ?>
	</p>
	<?php foreach ($this->history as $this->message) : ?>

		<div class="row-fluid">
			<div class="span2 center">
				<ul class="unstyled center profilebox">
					<li>
						<strong><?php echo $this->message->getAuthor()->getLink() ?></strong>
					</li>
					<li>
						<?php
						$profile    = KunenaFactory::getUser(intval($this->message->userid));
						$useravatar = $profile->getAvatarImage('img-polaroid', 'profile');
						if ($useravatar) :
							echo $this->message->getAuthor()->getLink($useravatar);
						endif;
						?>
					</li>
				</ul>
			</div>
			<div class="span10">
				<small class="text-muted pull-right hidden-phone" style="margin-top:-5px;">
					<span class="icon icon-clock"></span> <?php echo $this->message->getTime()->toSpan('config_post_dateformat', 'config_post_dateformat_hover'); ?> <?php echo $this->getNumLink($this->message->id, $this->replycount--) ?>
				</small>
				<div class="badger-left badger-info khistory" data-badger="<?php echo $this->message->displayField('subject'); ?>">
					<div class="kmessage">
						<p class="kmsg"><?php echo KunenaHtmlParser::parseBBCode($this->message->message, $this) ?></p>
					</div>
					<?php
					$attachments = $this->message->getAttachments();
					if (!empty($attachments)) : ?>
						<div class="kattach">
							<h4><?php echo JText::_('COM_KUNENA_ATTACHMENTS'); ?></h4>
							<ul class="thumbnails">
								<?php foreach ($attachments as $attachment) : ?>
									<li class="span4">
										<div class="thumbnail">
											<?php echo $attachment->getLayout()->render('thumbnail'); ?>
											<?php echo $attachment->getLayout()->render('textlink'); ?>
										</div>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
</div>
