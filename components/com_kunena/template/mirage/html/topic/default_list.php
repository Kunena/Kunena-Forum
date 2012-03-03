<?php
/**
 * Kunena Component
 * @package Kunena.Template.Default20
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="box-module">
	<div class="block-wrapper box-color box-border box-border_radius box-shadow">
		<div class="topic block">
			<div class="headerbox-wrapper">
				<div class="header">
					<!-- a href="#" title="Topic RSS Feed"><span class="krss-icon">Topic RSS Feed</span></a -->
					<!-- a href="#" title="View Subscribers of this Topic" class="ktopic-subsc">4 Subscribers</a -->
					<h2 class="header link-header2"><?php echo JText::_('COM_KUNENA_TOPIC') ?> <a class="section" href="#" rel="topic-detailsbox"><?php echo $this->escape($this->topic->subject) ?></a></h2>
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
			<div class="innerbox-wrapper innerspacer">
				<?php echo $this->getPagination(4); ?>
			</div>
			<div class="innerbox-wrapper">
				<div class="topic detailsbox">
					<ul class="list-unstyled topic-posts">
						<?php foreach ( $this->messages as $id=>$message ) $this->displayMessage($id, $message, 'message') ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="spacer"></div>