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
<div class="post tk-posts">
	<div class="inner inner-odd">
		<span class=""><span></span></span>
			<ul class="topiclist">
				<li class="header">
					<dl class="icon">
						<dt>
							<?php echo JText::sprintf("[K=DATE:{$this->message->time}]") ?>&nbsp;/&nbsp;<?php echo JText::sprintf('COM_KUNENA_TOPIC_STARTED_ON_DATE_BY_USER', "[K=DATE:{$this->topicTime}]", $this->topicAuthor->getLink()) ?>
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
				<div>
					<?php echo JText::_('COM_KUNENA_BY').' '.$this->author->getLink() ?>
				</div><br />
				<div class="tk-posts-avatar">
					<?php echo $this->author->getLink($this->author->getAvatarImage('kavatar', 'post')) ?>
				</div>
			</dd>
		</dl>
		<div class="postbody tk-posts-postbody">
			<div class="postbackground-left">
				<h3><?php echo $this->getTopicLink($this->topic, $this->message, $this->subjectHtml) ?>
					<span class="tk-posts-topicicon">[K=TOPIC_ICON]</span>
				</h3>
			</div>
			<div class="tk-msgcontent">
				<div class="msgcontent">
					<?php echo $this->messageHtml ?>
				</div>
			</div>
			</div>
			<div class="clr"></div>
			<div class="msgaction-left">
				<dl class="tk-posts-links"  style="padding:5px;">
					<?php if (!empty($this->categoryLink)) : ?>
					<dd style="float:left;padding:0;margin:0;">
						<?php echo JText::_('COM_KUNENA_CATEGORY') ?> <?php echo $this->categoryLink ?>
					</dd>
					<?php endif;?>
					<dd class="tk-pagination">
						<?php echo $this->topic->getPagination(false, $this->config->messages_per_page, 3)->getPagesLinks() ?>
					</dd>
				</dl>
			</div>
		<span class=""><span></span></span>
	</div>
</div>