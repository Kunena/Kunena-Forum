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
					<li class="kposts-row [K=ROW:krow-]">
						<table summary="List of all forum categories with posts and replies of each">
							<tbody>
								<tr>
									<td class="kpost-body">
										<ul>
											<li class="kpost-title">
												<h3><a href="#" title="View Topic Title"><?php echo $this->escape($this->message->subject) ?></a></h3>
												<ul class="kpost-actions">
													<li>[K=POST_NEW]</li>
												</ul>
												<div class="clr"></div>
											</li>
											<li class="kpost-smavatar"><?php echo $this->postAuthor->getLink($this->postAuthor->getAvatarImage('kavatar', 'list')) ?></li>
											<li class="kpost-details kauthor"><?php echo JText::_('COM_KUNENA_BY').' '.$this->postAuthor->getLink($this->message->name) ?></li>
											<li class="kpost-details kdate"><?php echo JText::sprintf('COM_KUNENA_ON_DATE', "[K=DATE:{$this->message->time}]") ?></li>
										</ul>
									</td>
									<td class="ktopic-icon">[K=TOPIC_ICON]</td>
									<td class="kpost-topic">
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
											<li class="ktopic-category"><?php echo JText::_('COM_KUNENA_CATEGORY') ?> <?php echo $this->categoryLink ?></li>
											<?php endif ?>
											<li class="ktopic-details"><?php echo JText::sprintf('COM_KUNENA_TOPIC_STARTED_ON_DATE_BY_USER', "[K=DATE:{$this->firstPostTime}]", $this->firstPostAuthor->getLink($this->firstUserName)) ?></li>
											<li>
												<div class="kpagination-topic">
													<?php echo $this->topic->getPagination(0, $this->config->messages_per_page, 3)->getPagesLinks() ?>
												</div>
											</li>
										</ul>
									</td>
									<?php if ($this->postActions) : ?>
									<td class="kpost-checkbox">
										<input type="checkbox" value="0" name="" class="kmoderate-post-checkbox" />
									</td>
									<?php endif ?>
								</tr>
							</tbody>
						</table>
					</li>