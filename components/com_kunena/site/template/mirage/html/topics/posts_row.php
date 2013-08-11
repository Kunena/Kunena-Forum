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
?>
<li class="posts-row [K=ROW:krow-] kbox-hover kbox-hover_list-row box-full item-row">
	<dl class="list-unstyled">
		<dd class="post-topic">
			<div class="innerspacer-column">
				<?php echo $this->getTopicLink ( $this->topic, 'unread', '[K=TOPIC_ICON]', null, 'fl' ) ?>
				<ul class="kcontent-32 list-unstyled">
					<li class="kpost-title">
						<h3><?php echo $this->getTopicLink($this->topic, $this->message, $this->escape($this->message->subject)) ?></h3>
					</li>
					<li class="ktopic-details"><?php echo JText::sprintf('COM_KUNENA_TOPIC_STARTED_ON_DATE_BY_USER', "[K=DATE:{$this->firstPostTime}]", $this->firstPostAuthor->getLink($this->firstUserName)) ?></li>
					<li class="kpost-smavatar"><?php echo $this->postAuthor->getLink($this->postAuthor->getAvatarImage('kavatar', 'list')) ?></li>
					<li class="kpost-details kauthor"><?php echo JText::_('COM_KUNENA_BY').' '.$this->postAuthor->getLink($this->message->name) ?></li>
					<li class="kpost-details kdate"><?php echo JText::sprintf('COM_KUNENA_ON_DATE', "[K=DATE:{$this->message->time}]") ?></li>
				</ul>
			</div>
		</dd>
		<dd class="post-category">
			<div class="innerspacer-column">
				<ul>
					<li class="ktopic-title">
						<h3><?php echo $this->getTopicLink($this->topic, null, null) ?></h3>
						<ul class="ktopic-actions">
							<li>[K=TOPIC_NEW_COUNT]</li>
							<li><?php if ($this->topic->locked) echo $this->getIcon ( 'klocked-icon', JText::_('COM_KUNENA_TOPIC_IS_LOCKED') ) ?></li>
							<!-- li><a href="#"><span class="ksubscribe-icon">Subscribe to this topic.</span></a></li -->
						</ul>
						<div class="clr"></div>
					</li>
					<?php if (!empty($this->categoryLink)) : ?>
					<li class="ktopic-category"><?php echo JText::sprintf('COM_KUNENA_CATEGORY_X', $this->categoryLink) ?></li>
					<?php endif ?>
					<li class="ktopic-details"><?php echo JText::sprintf('COM_KUNENA_TOPIC_STARTED_ON_DATE_BY_USER', "[K=DATE:{$this->firstPostTime}]", $this->firstPostAuthor->getLink($this->firstUserName)) ?></li>
					<li>
						<div class="kpagination-topic">
							<?php echo $this->topic->getPagination(0, $this->config->messages_per_page, 3)->getPagesLinks() ?>
						</div>
					</li>
				</ul>
			</div>
		</dd>
		<?php if ($this->postActions) : ?>
		<dd class="post-checkbox">
			<div class="innerspacer-column">
				<input type="checkbox" value="1" name="posts[<?php echo $this->message->id?>]" class="kcheck kmoderate-topic-checkbox" />
			</div>
		</dd>
		<?php endif ?>
	</dl>
</li>