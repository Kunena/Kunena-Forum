<?php
/**
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="forumlist">
	<div class="inner">
		<span class="corners-top"><span></span></span>
			<ul class="topiclist">
				<li class="header">
					<dl class="icon">
						<dt><?php echo $this->escape($this->topic->subject)?></dt>
						<dd>&nbsp;</dd>
					</dl>
				</li>
			</ul>
			<div class="tksection-desc">
				<?php echo JText::_ ( 'COM_KUNENA_POST_TOPIC_HISTORY_MAX' ) . ' ' . $this->escape($this->config->historylimit) . ' <em>' . JText::_ ( 'COM_KUNENA_POST_TOPIC_HISTORY_LAST' ).'</em>'?>
			</div>

<div class="inner inner-odd" style="height:400px;overflow-y:scroll;">
<?php foreach ( $this->history as $this->message ):
		$profile = KunenaFactory::getUser(intval($this->message->userid));
?>
	<div class="inner inner-odd">
		<span class=""><span></span></span>
			<ul class="topiclist">
				<li class="header">
					<dl class="icon">
						<dt>
						<?php // FIXME : Missing translations ?>
						<span class="tk-preview-msgtitle" title="<?php echo JText::_('Date and Time of Post'); ?>">
							<?php echo KunenaDate::getInstance($this->message->time)->toSpan('config_post_dateformat','config_post_dateformat_hover') ?>
						</span>
						</dt>
						<dd class="topics" style="float:right;">
						<?php // FIXME : Missing translations ?>
							<span style="white-space:nowrap;" class="tk-view-msgid"><a name="<?php echo intval($this->message->id) ?>"></a><?php echo $this->getNumLink($this->message->id,$this->replycount--) ?></span>
						</dd>
					</dl>
				</li>
			</ul>
		<dl id="profilebox-post" class="postprofile-left" style="padding-top:10px;">

	<dt class="view-avatar">
	<span class="author"><?php echo $profile->getLink($this->message->name); ?></span><br /><br />
		<span class="kavatar">
			<?php
			$avatar = $profile->getAvatarImage ('kavatar', 'post'); if ($avatar) : ?>
			<?php echo $profile->getLink($avatar); ?>
			<?php endif?>
		</span>
	</dt>

		</dl>
			<div class="postbody tk-avleft" style="padding: 10px; width:77%">
				<div class="postbackground-left">
					<h3><?php echo KunenaHtmlParser::parseBBCode( $this->message->subject, $this ) ?></h3>
				</div>
				<div class="tk-msgcontent" style="overflow: hidden;min-height:20px;">
				<?php echo KunenaHtmlParser::parseBBCode( $this->message->message, $this ) ?>
						<?php
						$this->attachments = $this->message->getAttachments();
						if (!empty($this->attachments)) : ?>
						<ul class="kpost-post-body">
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
						</ul>
						<?php endif; ?>
				</div>
			</div>
			<div class="clr"></div>
		<span class=""><span></span></span>
	</div>
<?php endforeach ?>
</div>
		<span class="corners-bottom"><span></span></span>
	</div>
</div>