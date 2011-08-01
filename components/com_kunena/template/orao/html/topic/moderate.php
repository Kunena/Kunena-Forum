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

JHTML::_('behavior.formvalidation');
JHTML::_('behavior.tooltip');
// TODO : Missed all functions
?>
<div class="forumlist">
	<div class="inner">
		<span class="corners-top"><span></span></span>
			<ul class="topiclist">
				<li class="header">
					<dl class="icon">
						<dt><?php echo JText::_('Moderate Topic') ?>: <?php echo $this->escape( $this->topic->subject ); ?></dt>
						<dd>&nbsp;</dd>
					</dl>
				</li>
			</ul>
			<div class="tksection-desc">
				<?php echo JText::_ ( 'Category:' ) ?> <?php echo JText::_ ( 'Welcome Mat' ) ?>
			</div>

			<div class="kdetailsbox kmod-detailsbox" id="kmod-detailsbox" >
			<form name="myform" method="post" action="#">
				<ul class="kmod-postlist">
					<li>
						<div class="kmod-container">
<?php if (isset($this->message)) : ?>
<div class="post clearboth">
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
		<dl id="profilebox-post" class="postprofile-left">

	<dt class="view-avatar">
	<span class="author"><?php echo $this->escape($this->message->name); ?></span><br />
		<span class="kavatar">
			<?php echo $this->user->getAvatarImage('kavatar', 'post'); ?>
		</span><br /><br />
		<span>
			<?php echo JText::_('COM_KUNENA_MODERATE_THIS_USER');?>
			<?php if ( $this->message->userid) : ?>:<br /><br />
			<button>
				<?php echo CKunenaLink::GetModerateUserLink( intval($this->message->userid), $this->escape($this->message->name).' ('.intval($this->message->userid).')' ); ?>
			</button>
			<?php else : ?>:
			<strong><?php echo JText::_('COM_KUNENA_USERNAME_ANONYMOUS'); ?></strong>
			<?php endif; ?>
		</span>
	</dt>

		</dl>
			<div class="postbody tk-avleft" style="padding: 10px; width:77%">
				<div class="postbackground-left">
					<h3><?php echo $this->escape( $this->topic->subject ); ?></h3>
				</div>
				<div class="tk-msgcontent" style="overflow: hidden;min-height:100px;">
				<?php echo KunenaHtmlParser::parseBBCode( $this->message->message, $this ) ?>
						<?php /*
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
						<?php endif;*/ ?>
				</div>
			</div>
			<div class="clr"></div>
		<span class=""><span></span></span>
	</div>
</div>
							<?php if (!empty($this->replies)) : ?>
							<ul>
								<li><label for="kmoderate-mode-selected" class="hasTip" title="<?php echo JText::_ ( 'COM_KUNENA_MODERATION_MOVE_SELECTED' ); ?> :: "><input type="radio" value="0" checked="checked" name="mode" id="kmoderate-mode-selected"><?php echo JText::_ ( 'COM_KUNENA_MODERATION_MOVE_SELECTED' ); ?></label></li>
								<li><label for="kmoderate-mode-newer" class="hasTip" title="<?php echo JText::sprintf ( 'COM_KUNENA_MODERATION_MOVE_NEWER', $this->escape($this->replies) ); ?> :: "><input type="radio" value="2" name="mode" id="kmoderate-mode-newer"><?php echo JText::sprintf ( 'COM_KUNENA_MODERATION_MOVE_NEWER', $this->escape($this->replies) ); ?></label></li>
							</ul>
							<br/>
							<?php endif; ?>
						<?php endif; ?>
							<div class="modcategorieslist">
								<label for="kmod_categories1"><?php echo JText::_ ( 'COM_KUNENA_MODERATION_DEST_CATEGORY' );?></label>
								<?php echo $this->categorylist ?>
							</div>

							<div class="modtopicslist">
								<label for="kmod_targettopic1"><?php echo JText::_ ( 'COM_KUNENA_MODERATION_DEST_TOPIC' ); ?></label>
								<?php echo $this->topiclist ?>
							</div>

							<div class="kmod_subject">
								<label for="kmod_topicsubject1"><?php echo JText::_ ( 'COM_KUNENA_MODERATION_TITLE_DEST_SUBJECT' ); ?></label>
								<input id="kmod_topicsubject1" type="text" value="<?php echo $this->escape( !isset($this->message) ? $this->topic->subject : $this->message->subject ); ?>" name="subject[1]" class="input hasTip" size="50" title="<?php echo JText::_ ( 'COM_KUNENA_MODERATION_TITLE_DEST_SUBJECT' ); ?> :: Enter New Subject" />
							</div>

							<div class="clr"></div>
							<label for="kmod_shadow1" class="hasTip" title="Leave shadow topic pointing to new location :: "><input id="kmod_shadow1" type="checkbox" value="1" name="shadow[1]">Leave shadow topic pointing to new location</label>
						</div>
					</li>

				</ul>

				<div class="kpost-buttons">
					<button title="Click here to save" type="submit" class="tk-submit-button"> Submit </button>
					<button onclick="javascript:window.history.back();" title="Click here to cancel" type="button" class="tk-cancel-button"> Cancel </button>
				</div>

				<div class="clr"></div>
			</form>
			</div>



		<span class="corners-bottom"><span></span></span>
	</div>
</div>