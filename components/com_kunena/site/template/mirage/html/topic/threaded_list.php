<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="kmodule topic-threaded_list">
	<div class="kbox-wrapper kbox-full">
		<div class="topic-threaded_list-kbox kbox kbox-color kbox-border kbox-border_radius kbox-border_radius-vchild kbox-shadow kbox-animate">
			<div class="headerbox-wrapper kbox-full">
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
							<?php if ( $this->me->userid == $this->topic->first_post_userid || $this->me->isModerator($this->topic->getCategory()) ): ?><li class="topic-taglist-edit"><a href="#" id="edit_keywords" class="link"><?php echo JText::_('COM_KUNENA_TOPIC_TAGS_ADD_EDIT') ?></a></li><?php endif ?>
						</ul>
					<?php endif ?>
				</div>
			</div>
			<?php echo $this->displayTopicActions(); ?>
			<div class="innerbox-wrapper kbox-full">
				<div class="topic_threaded-detailsbox detailsbox">
					<ul class="list-unstyled topic-posts">
						<?php $this->displayMessage($this->state->get('item.mesid'), $this->messages[$this->state->get('item.mesid')], 'message') ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>


<div class="kmodule topic-threaded_list">
	<div class="kbox-wrapper">
		<div class="topic-threaded_list-kbox kbox kbox-color kbox-border kbox-border_radius kbox-border_radius-vchild kbox-shadow">
			<div class="headerbox-wrapper box-full">
				<div class="header">
					<h2 class="header"><?php echo JText::sprintf('COM_KUNENA_TOPIC_REPLIES_TITLE', $this->escape($this->topic->subject)) ?></h2>
				</div>
			</div>
			<div class="detailsbox-wrapper innerspacer box-full">
				<div class="topic_threaded-detailsbox detailsbox kbox-full kbox-border kbox-border_radius kbox-shadow">
					<ul class="list-unstyled topic_threaded_tree-list">
						<li class="header kbox-hover_header-row kbox-full">
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
						<?php foreach ($this->messages as $id=>$message) { $this->displayMessage($id, $message, 'row'); } ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>

