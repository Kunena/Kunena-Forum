<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Topics
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$this->topic_interval ^= 1;
?>
<li class="topic-row topic-row-<?php echo $this->topic_rowclass [$this->topic_interval], !empty($this->topic->class_sfx) ? ' topic-row-' . $this->escape($this->topic_rowclass [$this->topic_interval]) . $this->escape($this->topic->class_sfx) : '' ?> kbox-hover kbox-hover_list-row box-full item-row">
	<dl class="list-unstyled list-column">
		<dd class="topic-subject item-column">
			<div class="innerspacer-column">
				<?php // FIXME: broken ?>
				<?php echo $this->getTopicLink ( $this->topic, 'unread', '[K=TOPIC_ICON]', null, 'fl' ) ?>
				<ul class="kcontent-32 list-unstyled">
					<li class="topic-title">
						<h3 class="link-header3"><?php echo $this->getTopicLink($this->topic, null, null) ?></h3>
						<ul class="list-unstyled ktopic-actions">
							<li>[K=TOPIC_NEW_COUNT]</li>
							<li><?php if ($this->topic->locked) echo $this->getIcon ( 'klocked-icon', JText::_('COM_KUNENA_TOPIC_IS_LOCKED') ) ?></li>
							<!-- li><a href="#"><span class="ksubscribe-icon">Subscribe to this topic.</span></a></li -->
						</ul>
					</li>
					<?php if (!empty($this->categoryLink)) : ?>
					<li class="topic-category"><?php echo JText::sprintf('COM_KUNENA_CATEGORY_X', $this->categoryLink) ?></li>
					<?php endif ?>
					<li class="topic-details"><?php echo JText::sprintf('COM_KUNENA_TOPIC_STARTED_ON_DATE_BY_USER', "[K=DATE:{$this->firstPostTime}]", $this->firstPostAuthor->getLink($this->firstUserName)) ?></li>
					<li>
						<div class="topic-kpagination kpagination">
							<?php echo $this->topic->getPagination(0, $this->config->messages_per_page, 3)->getPagesLinks() ?>
						</div>
					</li>
					<li>
						<?php if (!empty($this->keywords)) : ?>
						<ul class="list-unstyled ktopic-tags">
							<li class="ktopic-tag-title"><?php echo JText::sprintf('COM_KUNENA_TOPIC_TAGS', $this->keywords) ?></li>
						</ul>
						<?php endif ?>
					</li>
				</ul>
			</div>
		</dd>
		<!-- <td class="ktopic-status-icons"><span class="ktopic-attach">Attachment</span><span class="ktopic-sticky">Sticky</span></td> -->
		<dd class="topic-replies item-column">
			<div class="innerspacer-column">
				<span class="number"><?php echo $this->formatLargeNumber($this->topic->getReplies()); ?></span><br />
				<span class=""><?php echo JText::_('COM_KUNENA_GEN_REPLIES') ?></span>
			</div>
		</dd>
		<dd class="topic-views item-column">
			<div class="innerspacer-column">
				<span class="number"><?php echo $this->formatLargeNumber($this->topic->getHits()); ?></span><br />
				<span class=""><?php echo JText::_('COM_KUNENA_GEN_HITS') ?></span>
			</div>
		</dd>
		<!-- td class="ktopic-subs">22 <span>Subscribers</span></td -->
		<dd class="topic-lastpost item-column">
			<div class="innerspacer-column">
				<?php if ( $this->config->avataroncat ) : ?><?php echo $this->lastPostAuthor->getLink($this->lastPostAuthor->getAvatarImage('klist-avatar kavatar kavatar-32 fl', 'list')) ?><?php endif ?>
				<ul class="kcontent-36 list-unstyled">
					<li class="topic-lastpost-post"><?php echo $this->getTopicLink ( $this->topic, 'last', 'Last post' ) ?></li>
					<li class="topic-author-post"><?php echo JText::_('COM_KUNENA_BY').' '.$this->lastPostAuthor->getLink($this->lastUserName) ?></li>
					<li class="topic-date-post"><?php echo JText::sprintf('COM_KUNENA_ON_DATE', "[K=DATE:{$this->lastPostTime}]") ?></li>
				</ul>
			</div>
		</dd>
		<?php if ($this->topicActions) : ?>
		<dd class="topic-checkbox item-column">
			<div class="innerspacer-column">
				<input type="checkbox" class="kcheck kmoderate-topic-checkbox" name="topics[<?php echo $this->topic->id?>]" value="1" />
			</div>
		</dd>
		<?php endif ?>
	</dl>
</li>
