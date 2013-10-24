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

$k = 0;
?>
<div class="pull-right">
	<div class="btn btn-small" data-toggle="collapse" data-target="#history">X</div>
</div>
<h2>
	<?php echo JText::_('COM_KUNENA_POST_TOPIC_HISTORY' )?>:
	<?php echo $this->escape($this->topic->subject) ?>
</h2>

<div id="history" class="collapse in">
	<p>
		<?php echo JText::_ ( 'COM_KUNENA_POST_TOPIC_HISTORY_MAX' ) . ' ' . $this->escape($this->config->historylimit) . ' ' . JText::_ ( 'COM_KUNENA_POST_TOPIC_HISTORY_LAST' )?>
	</p>
	<?php foreach ( $this->history as $this->message ) : ?>

	<div class="row-fluid">
		<div class="span2 center">
			<h4>
				<?php echo $this->message->getAuthor()->getLink() ?>
			</h4>
			<div>
				<?php
					$profile = KunenaFactory::getUser(intval($this->message->userid));
					$useravatar = $profile->getAvatarImage('img-polaroid','profile');
					if ($useravatar) :
						echo $this->message->getAuthor()->getLink( $useravatar );
					endif;
					?>
			</div>
		</div>
		<div class="span10">
			<div class="well well-small">
				<div class="pull-right">
					<?php echo $this->getNumLink($this->message->id,$this->replycount--) ?>
				</div>
				<div>
					<?php echo KunenaDate::getInstance($this->message->time)->toSpan('config_post_dateformat', 'config_post_dateformat_hover') ?>
				</div>
				<hr />
				<div class="kmsgtext">
					<?php echo KunenaHtmlParser::parseBBCode($this->message->message, $this) ?>
				</div>
				<hr />
				<?php
				$attachments = $this->message->getAttachments();
				if (!empty($attachments)) : ?>
					<div class="row-fluid">
						<h4>
							<?php echo JText::_('COM_KUNENA_ATTACHMENTS');?>
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
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
<?php endforeach; ?>
</div>
