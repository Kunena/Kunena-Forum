<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Topics
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<li class="topic-row kbox-hover kbox-hover_list-row box-full">
	<dl class="list-unstyled">
		<!--<dd class="topic-icon">[K=TOPIC_ICON]</dd>-->
		<dd class="topic-subject">
			<div class="kcontent-32">
				<a class="fl" href="<?php echo $this->topic->getUrl() ?>" title="<?php echo JText::sprintf('COM_KUNENA_VIEW_CATEGORY_LIST_CATEGORY_TITLE', $this->escape($this->topic->name)) ?>">[K=TOPIC_ICON]</a>
				<ul class="list-unstyled">
					<li class="topic-title">
						<h3 class="link-header3"><?php echo $this->getTopicLink($this->topic, null, null) ?></h3>
						<ul class="list-unstyled ktopic-actions">
							<li>[K=TOPIC_NEW_COUNT]</li>
							<li><?php if ($this->topic->locked) echo $this->getIcon ( 'klocked-icon', JText::_('COM_KUNENA_TOPIC_IS_LOCKED') ) ?></li>
							<!-- li><a href="#"><span class="ksubscribe-icon">Subscribe to this topic.</span></a></li -->
						</ul>
					</li>
					<?php if (!empty($this->categoryLink)) : ?>
					<li class="topic-category"><?php echo JText::_('COM_KUNENA_CATEGORY') ?> <?php echo $this->categoryLink ?></li>
					<?php endif ?>
					<li class="topic-details"><?php echo JText::sprintf('COM_KUNENA_TOPIC_STARTED_ON_DATE_BY_USER', "[K=DATE:{$this->firstPostTime}]", $this->firstPostAuthor->getLink($this->firstUserName)) ?></li>
					<li>
						<div class="kpagination-topic">
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
		<dd class="topic-replies"><span class="number"><?php echo $this->topic->getReplies(); ?></span></dd>
		<dd class="topic-views"><span class="number"><?php echo $this->topic->getHits();?></span></dd>
		<!-- td class="ktopic-subs">22 <span>Subscribers</span></td -->
		<dd class="topic-lastpost">
			<div class="kcontent-32">
				<?php if ( $this->config->avataroncat ) : ?><?php echo $this->lastPostAuthor->getLink($this->lastPostAuthor->getAvatarImage('klist-avatar kavatar-32', 'list')) ?><?php endif ?>
				<ul class="list-unstyled">
					<li class="ktopic-smdetails klastpost"><?php echo $this->getTopicLink ( $this->topic, 'last', 'Last post' ) ?> <?php echo JText::_('COM_KUNENA_BY').' '.$this->lastPostAuthor->getLink($this->lastUserName) ?></li>
					<li class="ktopic-smdetails kdate"><?php echo JText::sprintf('COM_KUNENA_ON_DATE', "[K=DATE:{$this->lastPostTime}]") ?></li>
				</ul>
			</div>
		</dd>
		<?php if ($this->topicActions) : ?>
		<dd class="topic-checkbox">
			<input type="checkbox" class="kmoderate-topic-checkbox" name="topics[<?php echo $this->topic->id?>]" value="1" />
		</dd>
		<?php endif ?>
	</dl>
</li>