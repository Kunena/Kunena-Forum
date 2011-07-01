<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="ksection">
	<h2 class="kheader"><a rel="khistory">Topic History of: <?php echo $this->escape($this->topic->subject)?></a></h2>
	<p class="kheader-desc"><?php echo JText::_ ( 'COM_KUNENA_POST_TOPIC_HISTORY_MAX' ) . ' ' . $this->escape($this->config->historylimit) . ' <em>' . JText::_ ( 'COM_KUNENA_POST_TOPIC_HISTORY_LAST' ).'</em>'?></p>
	<div id="khistory" class="kdetailsbox">
		<ul class="kposts">
			<?php foreach ( $this->history as $this->message ):
				$profile = KunenaFactory::getUser(intval($this->message->userid));
			?>
			<li>
				<ul class="kposthead">
					<li class="kposthead-replytitle"><h3><?php echo KunenaDate::getInstance($this->message->time)->toSpan('config_post_dateformat','config_post_dateformat_hover') ?></h3></li>
					<li class="kposthead-postid" ><a name="<?php echo intval($this->message->id) ?>"></a><?php echo $this->getNumLink($this->message->id,$this->replycount--) ?></li>
				</ul>
				<ul class="kpost-user-details">
					<?php $avatar = $profile->getAvatarImage ('kavatar', 'category'); if ($avatar) : ?>
					<li class="kcategory-smavatar"><?php echo $profile->getLink($avatar); ?></li>
					<?php endif; ?>
					<li class="kcategory-smdetails kauthor"><?php echo $profile->getLink($this->message->name); ?></li>
				</ul>
				<div class="kpost-container">
					<ul class="kpost-post-body">
						<li class="kpost-body">
							<?php echo KunenaHtmlParser::parseBBCode( $this->message->message, $this ) ?>
						</li>
						<?php
						$this->attachments = $this->message->getAttachments();
						if (!empty($this->attachments)) : ?>
						<li class="kpost-body-attach">
							<span class="kattach-title"><?php echo JText::_('COM_KUNENA_ATTACHMENTS') ?></span>
							<ul>
								<?php foreach($this->attachments as $attachment) : ?>
								<li class="kattach-details">
									<?php echo $attachment->getThumbnailLink(); ?> <span><?php echo $attachment->getTextLink(); ?></span>
								</li>
								<?php endforeach; ?>
							</ul>
							<div class="clr"></div>
						</li>
						<?php endif; ?>
					</ul>
				</div>
			</li>
			<?php endforeach ?>
		</ul>
	</div>
</div>