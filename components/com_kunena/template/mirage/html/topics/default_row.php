<?php
/**
 * Kunena Component
 * @package Kunena.Template.Default20
 * @subpackage Topics
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<li class="topics-row box-hover">
	<dl>
		<dd class="topic-icon">[K=TOPIC_ICON]</dd>
		<dd class="topic-subject">
			<ul>
				<li class="ktopic-title">
					<h3><?php echo $this->getTopicLink($this->topic, null, null) ?></h3>
					<ul class="ktopic-actions">
						<li>[K=TOPIC_NEW_COUNT]</li>
						<li><?php if ($this->topic->locked) echo $this->getIcon ( 'klocked-icon', JText::_('COM_KUNENA_TOPIC_IS_LOCKED') ) ?></li>
						<!-- li><a href="#"><span class="ksubscribe-icon">Subscribe to this topic.</span></a></li -->
					</ul>
				</li>
				<?php if (!empty($this->categoryLink)) : ?>
				<li class="ktopic-category"><?php echo JText::_('COM_KUNENA_CATEGORY') ?> <?php echo $this->categoryLink ?></li>
				<?php endif ?>
				<li class="ktopic-details"><?php echo JText::sprintf('COM_KUNENA_TOPIC_STARTED_ON_DATE_BY_USER', "[K=DATE:{$this->firstPostTime}]", $this->firstPostAuthor->getLink($this->firstUserName)) ?></li>
				<li>
					<div class="kpagination-topic">
						<?php echo $this->topic->getPagination(0, $this->config->messages_per_page, 3)->getPagesLinks() ?>
					</div>
				</li>
				<li>
					<?php if ($this->config->keywords) : ?>
						<ul class="ktopic-tags">
							<?php if (!empty($this->keywords)) : ?>
								<li class="ktopic-tag-title"><?php echo JText::sprintf('COM_KUNENA_TOPIC_TAGS', $this->keywords) ?></li>
							<?php else: ?>
								<li class="ktopic-tag-title"><?php echo JText::_('COM_KUNENA_TOPIC_NO_TAGS') ?></li>
							<?php endif ?>
						</ul>
					<?php endif ?>
				</li>
			</ul>
		</dd>
		<!-- <td class="ktopic-status-icons"><span class="ktopic-attach">Attachment</span><span class="ktopic-sticky">Sticky</span></td> -->
		<dd class="topic-replies"><?php echo $this->formatLargeNumber ( $this->topic->getReplies() ); ?></dd>
		<dd class="topic-views"><?php echo $this->formatLargeNumber ( $this->topic->getHits() );?></dd>
		<!-- td class="ktopic-subs">22 <span>Subscribers</span></td -->
		<dd class="topic-lastpost">
			<ul>
				<?php if ( $this->config->avataroncat ) : ?><li class="ktopic-smavatar"><?php echo $this->lastPostAuthor->getLink($this->lastPostAuthor->getAvatarImage('klist-avatar', 'list')) ?></li><?php endif ?>
				<li class="ktopic-smdetails klastpost"><?php echo $this->getTopicLink ( $this->topic, 'last', 'Last post' ) ?> <?php echo JText::_('COM_KUNENA_BY').' '.$this->lastPostAuthor->getLink($this->lastUserName) ?></li>
				<li class="ktopic-smdetails kdate"><?php echo JText::sprintf('COM_KUNENA_ON_DATE', "[K=DATE:{$this->lastPostTime}]") ?></li>
			</ul>
		</dd>
		<?php if ($this->topicActions) : ?>
		<dd class="topic-checkbox">
			<input type="checkbox" class="kmoderate-topic-checkbox" name="topics[<?php echo $this->topic->id?>]" value="1" />
		</dd>
		<?php endif ?>
	</dl>
</li>