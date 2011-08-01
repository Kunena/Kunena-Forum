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
<div class="post tk-posts">
	<div class="inner inner-odd">
		<span class=""><span></span></span>
			<ul class="topiclist">
				<li class="header">
					<dl class="icon">
						<dt>
							<?php echo JText::sprintf("[K=DATE:{$this->message->time}]") ?>
						</dt>
						<dd class="topics" style="float:right;">
							<span class="tk-view-msgid"># <?php echo $this->escape($this->message->id) ?></span>
						</dd>
					</dl>
				</li>
			</ul>
		<dl id="profilebox-post" class="postprofile-left">
		<dt>
		</dt>
		<dd>
		<div class="tk-posts-avatar">
			<?php $avatar = $this->postAuthor->getAvatarImage ('kavatar', 'post'); if ($avatar) : ?>
			<?php echo $this->postAuthor->getLink($avatar); ?>
			<?php endif ?>
			<?php //echo $this->postAuthor->getLink($this->postAuthor->getAvatarImage('kavatar', 'post')) ?>
			<?php //echo $this->topic->avatar; ?>
		</div>
		</dd>
		</dl>
			<div class="postbody tk-posts-postbody">
				<div class="postbackground-left">
					<h3>
						<?php echo $this->getTopicLink($this->topic, $this->message, $this->escape($this->message->subject)) ?>
						<span class="tk-posts-topicicon">[K=TOPIC_ICON]</span>
					</h3>
				</div>
				<div class="tk-msgcontent">
					<div class="msgcontent">
						<?php echo KunenaHtmlParser::parseBBCode ($this->message->message);?>
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
				<?php if ( !empty($this->attachmentslist[$this->message->id]) ) $this->displayAttachments($this->attachmentslist[$this->message->id]); ?>
				</div>
			</div>
			<div class="clr"></div>
			<div class="msgaction-left">

			<dl class="tk-posts-links"  style="padding:5px;">
				<dd style="float:left;padding:0;margin:0;">
					<?php echo JText::_('COM_KUNENA_CATEGORY') ?> <?php echo $this->categoryLink ?>
				</dd>
				<dd class="tk-pagination">
					<?php echo $this->topic->getPagination(0, $this->config->messages_per_page, 3)->getPagesLinks() ?>
				</dd>
			</dl>
			<?php
			//echo CKunenaLink::GetThreadLink ( 'view', $message->catid, $message->id, CKunenaTools::showButton ( 'reply', JText::_('Post') ), JText::_('Go to Post'), 'follow', 'kicon-button kbuttoncomm btn-left' ).'&nbsp;&nbsp;';
			?>
			<?php
			//echo CKunenaLink::GetThreadLink ( 'view', $firstpost->catid, $firstpost->id, CKunenaTools::showButton ( 'reply', JText::_('Topic') ), JText::_('Go to Topic'), 'follow', 'kicon-button kbuttoncomm btn-left' ).'&nbsp;&nbsp;';
			?>
			<?php
			//echo CKunenaLink::GetCategoryLink ( 'showcat', $message->catid, CKunenaTools::showButton ( 'reply', JText::_('Category') ), JText::_('Go to Category'), 'kicon-button kbuttoncomm btn-left', JText::_('Go to Category').': '.$message->catname);
			?>
			</div>
		<span class=""><span></span></span>
	</div>
</div>
