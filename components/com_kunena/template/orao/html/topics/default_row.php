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
				<li class="<?php echo ($this->topic->ordering != 0) ? 'sticky' : ''; ?> row">
					<dl class="icon topicicon<?php echo $this->topic->unread ? '-new':'-nonew'; ?>">
						<dt class="topics">
						<span class ="topicicon">
							[K=TOPIC_ICON]
						</span>
						<?php if (($this->topic->hits > $this->params->get('hotTopic'))): echo $this->getIcon('hoticon'); endif ?>
						<?php if ($this->topic->getUserTopic()->favorite): echo $this->getIcon('favoriteicon'); endif ?>
						<?php if ($this->params->get('authorAvatar') == '1') : ?>
						<span class ="tk-topic-author">
							<?php echo $this->firstPostAuthor->getLink($this->firstPostAuthor->getAvatarImage('klist-avatar', 'list')) ?>
						</span>
						<?php endif ?>
						<span class="threadtitle">
							<?php
								if ($this->topic->icon_id == 7) :
									echo '<span class="status solved" title="'.$this->params->get('solvedText').'">'.$this->params->get('solvedText').'</span>';
								endif;
								if ($this->topic->locked != 0) :
									echo '<span class="status closed" title="'.$this->params->get('closedText').'">'.$this->params->get('closedText').'</span>';
								endif;
								if ($this->topic->ordering != 0) :
									echo '<span class="status stickystatus" title="'.$this->params->get('stickystatusText').'">'.$this->params->get('stickystatusText').'</span>';
								endif; ?>
							<span class="tk-tips">
								<?php echo $this->getTopicLink($this->topic, null, null) ?>
							</span>
							<?php if ($this->topic->hold == 1) : ?>
								<span class="unaprovedicons">
									<?php echo $this->getImage ( 'icons/unaproved_picc.png', JText::_('Unapproved') ); ?>
								</span>
							<?php endif; ?>
							<?php if ($this->topic->hold == 2) : ?>
								<span class="deletedicons">
									<?php echo $this->getImage ( 'icons/deleted_picc.png', JText::_('Deleted') ); ?>
								</span>
							<?php endif; ?>
							<?php if ($this->topic->ordering != 0) : ?>
								<span class="stickyicons">
									<?php  echo $this->getImage ( 'icons/sticky.png', JText::_('Sticky') ); ?>
								</span>
							<?php endif; ?>

							<?php /*if ($this->topic->posted) : ?>
								<span class="myanswericons tk-tips" title="<?php echo JText::_('You have answered in this topic')?>">
									<?php  echo $this->getImage ( 'icons/myanswer.png', JText::_('My Answers') ); ?>
								</span>
							<?php endif;*/?>

							<?php if ($this->firstPostAuthor == $this->me) : ?>
								<span class="mytopicicons tk-tips" title="<?php echo JText::_("You have started this topic")?>">
									<?php  echo $this->getImage ( 'icons/mytopic.png', JText::_('My Topic') ); ?>
								</span>
							<?php endif; ?>
						</span>

						<br />
						<span class="tk-recent-info">
						<?php if ($this->topic->attachments) {
								echo $this->getIcon('attachicons', JText::_('COM_KUNENA_TOPIC_HAS_ATTACHMENTS'));
							}
							/*if (CKunenaPolls::get_poll_data($this->topic->poll->id) ) {
								echo isset ( $kunena_icons ['pollicon'] ) ? '<img  class="pollicon" src="' . KUNENA_URLICONSPATH . $kunena_icons ['pollicon'] . '" border="0" alt="' . JText::_('COM_KUNENA_EDITOR_POLL') . '" />' : '<img class="pollicon" src="' . KUNENA_URLICONSPATH . 'pollicon.png"  alt="' . JText::_('COM_KUNENA_EDITOR_POLL') . '" title="' . JText::_('COM_KUNENA_EDITOR_POLL') . '" />';
							}*/ ?>
						<?php if (!empty($this->categoryLink)) : ?>
							<span class="ktopic-category"> <?php echo JText::_('COM_KUNENA_TEMPLATE_IN') . ' ' . $this->categoryLink; ?>
							</span><br />
						<?php endif ?>
							<span class="tk-post-time">
								<?php echo JText::sprintf('COM_KUNENA_TOPIC_STARTED_ON_DATE_BY_USER', "[K=DATE:{$this->firstPostTime}]", $this->firstPostAuthor->getLink($this->firstUserName)) ?>&nbsp;
							</span>
						<?php if ($this->params->get('countcolumnShow') == '0'):?>
						<?php if (empty($this->categoryLink)) : ?><br /><?php endif?>
							:: <span class="tk-numbers">
								&nbsp;<?php echo JText::_('COM_KUNENA_GEN_REPLIES'); ?>: <b><?php echo $this->formatLargeNumber ( $this->topic->getReplies() ); ?></b>
								&nbsp;<?php echo JText::_('COM_KUNENA_GEN_HITS');?>: <b><?php echo $this->formatLargeNumber ( $this->topic->getHits() );?></b>
							</span>
						<?php endif;?>

							<strong class="pagination">
								<span>
									<?php echo $this->topic->getPagination(0, $this->config->messages_per_page, 3)->getPagesLinks() ?>
								</span>
							</strong>
						</span>
						</dt>
						<?php if ($this->params->get('countcolumnShow') != 0):?>
						<dd class="topics">
							<span>
							<?php echo $this->formatLargeNumber ( $this->topic->getReplies() ); ?>
							</span>
						</dd>
						<dd class="views">
							<span>
							<?php echo $this->formatLargeNumber ( $this->topic->getHits() );?>
							</span>
						</dd>
						<?php endif; ?>
						<dd class="lastpost tk-recent-lastpost">
						<?php if ($this->config->avataroncat) :?>
						<span class="topic_latest_post_avatar">
							<?php echo $this->lastPostAuthor->getLink($this->lastPostAuthor->getAvatarImage('klist-avatar', 'list')) ?>
						</span>
						<?php endif ?>
						<span class="tk-latestpost">
							<?php echo $this->getTopicLink ( $this->topic, 'last', 'Last post' ) ?> <?php echo JText::_('COM_KUNENA_BY').' '.$this->lastPostAuthor->getLink($this->lastUserName) ?>
						</span>
						<br />
						<span class="topic_date">
							<?php echo JText::sprintf("[K=DATE:{$this->lastPostTime}]") ?>
						</span>
						</dd>
						<?php if ($this->topicActions) : ?>
						<dd class="moderation">
							<input type="checkbox" class="kmoderate-topic-checkbox KCHECK" name="topics[<?php echo $this->topic->id ?>]" value="1" />
						</dd>
						<?php endif; ?>
					</dl>
				</li>