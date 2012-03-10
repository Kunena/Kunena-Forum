<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="kmodule">
	<div class="box-wrapper">
		<div class="topic_threaded-kbox kbox box-color box-border box-border_radius box-border_radius-child box-shadow">
			<div class="headerbox-wrapper">
				<div class="header">
					<!-- a href="#" title="Topic RSS Feed"><span class="krss-icon">Topic RSS Feed</span></a -->
					<!-- a href="#" title="View Subscribers of this Topic" class="ktopic-subsc">4 Subscribers</a -->
					<h2 class="header"><?php echo JText::_('COM_KUNENA_TOPIC') ?> <a href="#" rel="ktopic-detailsbox"><?php echo $this->escape($this->topic->subject) ?></a></h2>
					<?php if ( $this->config->keywords ) : ?>
						<ul class="list-unstyled topic-taglist">
							<?php if (!empty($this->keywords)) : ?>
								<li class="topic-taglist-title"><?php echo JText::sprintf('COM_KUNENA_TOPIC_TAGS', $this->keywords) ?></li>
							<?php else: ?>
								<li class="topic-taglist-title"><?php echo JText::_('COM_KUNENA_TOPIC_NO_TAGS') ?></li>
							<?php endif ?>
							<?php if ( $this->me->userid == $this->topic->first_post_userid || intval($this->me->isModerator('global')) ): ?><li class="topic-taglist-edit"><a href="#" id="edit_keywords" class="link"><?php echo JText::_('COM_KUNENA_TOPIC_TAGS_ADD_EDIT') ?></a></li><?php endif ?>
						</ul>
					<?php endif ?>
				</div>
			</div>
			<div class="innerbox-wrapper innerspacer">
				<?php echo $this->displayTopicActions(0); ?>
			</div>
			<div class="innerbox-wrapper">
				<div class="topic_threaded-detailsbox detailsbox">
					<ul class="list-unstyled topic-posts">
						<?php $this->displayMessage($this->state->get('item.mesid'), $this->messages[$this->state->get('item.mesid')], 'message') ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="spacer"></div>

<div class="box-module">
	<div class="block-wrapper box-color box-border box-border_radius box-shadow">
		<div class="topic_threaded block">
			<div class="headerbox-wrapper">
				<div class="header">
					<h2 class="header"><?php echo JText::sprintf('COM_KUNENA_TOPIC_REPLIES_TITLE', $this->escape($this->topic->subject)) ?></h2>
				</div>
			</div>
			<div class="detailsbox-wrapper innerspacer">
				<div class="topic_threaded-detailsbox detailsbox box-full box-border box-border_radius box-shadow">
					<ul class="list-unstyled topic_threaded_tree-list">
						<li class="header box-hover_header-row clear">
							<dl class="list-unstyled">
								<dd class="topic_threaded_tree-post">
									<span class="bold"><?php echo JText::_('Post') ?></span>
								</dd>
								<dd class="topic_threaded_tree-author">
									<span class="bold"><?php echo JText::_('Author') ?></span>
								</dd>
								<dd class="topic_threaded_tree-time">
									<span class="bold"><?php echo JText::_('Time') ?></span>
								</dd>
							</dl>
						</li>
					</ul>
					<ul class="list-unstyled topic_threaded_tree-list">
						<?php foreach ( $this->messages as $id=>$message ) $this->displayMessage($id, $message, 'row') ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="spacer"></div>
