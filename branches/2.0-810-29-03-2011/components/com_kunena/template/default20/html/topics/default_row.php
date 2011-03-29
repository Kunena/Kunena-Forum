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
					<li class="ktopics-row [K=ROW:krow-]">
						<table summary="List of the latest forum topics">
							<caption><?php echo $this->escape($this->topic->subject) ?></caption>
							<tbody>
								<tr>
									<td class="ktopic-icon">[K=TOPIC_ICON]</td>
									<td class="ktopic-body">
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
											<!-- li>
												<ul class="ktopic-tags">
													<li class="ktopic-tags-title">Topic Tags:</li>
													<li><a href="#">support</a></li>
													<li><a href="#">templates</a></li>
													<li><a href="#">css</a></li>
												</ul>
											</li -->
										</ul>
									</td>
									<td class="ktopic-status-icons">
										<?php if ($this->topic->attachments) echo $this->getIcon ( 'ktopic-attach', JText::_('COM_KUNENA_TOPIC_HAS_ATTACHMENTS') ) ?>
										<?php if ($this->topic->ordering) echo $this->getIcon ( 'ktopic-sticky', JText::_('COM_KUNENA_TOPIC_IS_STICKY') ) ?>
									</td>
									<!-- <td class="ktopic-status-icons"><span class="ktopic-attach">Attachment</span><span class="ktopic-sticky">Sticky</span></td> -->
									<td class="ktopic-replies"><?php echo $this->formatLargeNumber ( $this->topic->getReplies() ); ?><span><?php echo JText::_('COM_KUNENA_GEN_REPLIES') ?></span></td>
									<td class="ktopic-views"><?php echo $this->formatLargeNumber ( $this->topic->getHits() );?> <span><?php echo JText::_('COM_KUNENA_GEN_HITS');?></span></td>
									<!-- td class="ktopic-subs">22 <span>Subscribers</span></td -->
									<td class="ktopic-lastpost">
										<ul>
											<li class="ktopic-smavatar"><?php echo $this->lastPostAuthor->getLink($this->lastPostAuthor->getAvatarImage('klist-avatar', 'list')) ?></li>
											<li class="ktopic-smdetails klastpost"><?php echo $this->getTopicLink ( $this->topic, 'last', 'Last post' ) ?> <?php echo JText::_('COM_KUNENA_BY').' '.$this->lastPostAuthor->getLink($this->lastUserName) ?></li>
											<li class="ktopic-smdetails kdate"><?php echo JText::sprintf('COM_KUNENA_ON_DATE', "[K=DATE:{$this->lastPostTime}]") ?></li>
										</ul>
									</td>
									<?php if ($this->topicActions) : ?>
									<td class="ktopic-topic-checkbox">
										<input type="checkbox" class="kmoderate-topic-checkbox" name="topics[<?php echo $this->topic->id?>]" value="1">
									</td>
									<?php endif ?>
								</tr>
								</tbody>
							</table>
						</li>