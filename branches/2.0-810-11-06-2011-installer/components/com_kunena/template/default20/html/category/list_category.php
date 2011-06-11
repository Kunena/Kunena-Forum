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
				<ul class="kcategory">
					<li class="kcategories-row krow-[K=ROW]">
						<table summary="<?php echo JText::_('COM_KUNENA_VIEW_CATEGORY_LIST_DESC') ?>">
							<caption><?php echo $this->escape($this->category->name) ?></caption>
							<tbody>
								<tr>
									<td rowspan="2" class="kcategory-icon">
										<a href="<?php echo $this->categoryURL ?>" title="<?php echo JText::sprintf('COM_KUNENA_VIEW_CATEGORY_LIST_CATEGORY_TITLE', $this->escape($this->category->name)) ?>">[K=CATEGORY_ICON]</a>
									</td>
									<td class="kcategory-body">
										<ul>
											<li class="kcategory-title">
												<h3><a href="<?php echo $this->categoryURL ?>" title="<?php echo JText::sprintf('COM_KUNENA_VIEW_CATEGORY_LIST_CATEGORY_TITLE', $this->escape($this->category->name)) ?>"><?php echo $this->escape($this->category->name) ?></a> [K=CATEGORY_NEW_COUNT]</h3>
												<ul class="kcategory-actions">
													<?php if (!empty($this->categoryRssURL)) : ?>
													<li><a href="<?php echo $this->categoryRssURL ?>" title="<?php echo JText::sprintf('COM_KUNENA_VIEW_CATEGORY_LIST_RSS_TITLE', $this->escape($this->section->name)) ?>"><span class="krss-icon"><?php echo JText::sprintf('COM_KUNENA_VIEW_CATEGORY_LIST_RSS_TITLE', $this->escape($this->section->name)) ?></span></a></li>
													<?php endif ?>
													<?php if ($this->category->locked && !$this->category->isSection()) : ?>
													<li><?php echo $this->getIcon ( 'klocked-icon', JText::_('COM_KUNENA_CATEGORY_LOCKED_TITLE') ) ?></li>
													<?php endif ?>
													<?php if ($this->category->review && !$this->category->isSection()) : ?>
													<li><?php echo $this->getIcon ( 'kreview-icon', JText::_('COM_KUNENA_CATEGORY_REVIEW_TITLE') ) ?></li>
													<?php endif ?>
													<!-- li><a href="#"><span class="ksubscribe-icon" title="Subscribe to all posts in this category">Subscribe to all posts in this category</span></a></li -->
													<?php // TODO: pending messages? ?>
												</ul>
											</li>
											<?php if ($this->category->description) : ?>
											<li class="kcategory-details"><div><?php echo $this->parse($this->category->description) ?></div></li>
											<?php endif ?>
											<?php if ($this->moderators) : ?>
											<li class="kcategory-mods">
												<ul>
													<li class="kcategory-modtitle"><?php echo JText::_('COM_KUNENA_GEN_MODERATORS') ?>:</li>
													<?php foreach ($this->moderators as $moderator) : ?>
													<li class="kcategory-modname kusername-mod"><?php echo $moderator->getLink() ?></li>
													<?php endforeach ?>
												</ul>
											</li>
											<?php endif ?>
										</ul>
									</td>
									<td class="kcategory-topics"><?php echo $this->formatLargeNumber ( $this->category->getTopics() ) ?> <span><?php echo JText::_('COM_KUNENA_GEN_TOPICS') ?></span></td>
									<td class="kcategory-replies"><?php echo $this->formatLargeNumber ( $this->category->getPosts() ) ?> <span><?php echo JText::_('COM_KUNENA_GEN_REPLIES') ?></span></td>
									<!-- td class="kcategory-subs">944 <span>Subscribers</span></td -->
									<td class="kcategory-lastpost">
										<ul>
										<?php if ($this->lastPost) : ?>
											<li class="kcategory-smavatar"><?php echo $this->lastUser->getLink($this->lastUser->getAvatarImage('klist-avatar', 'list')) ?></li>
											<li class="kcategory-smdetails klastpost"><?php echo $this->getLastPostLink($this->category) ?></li>
											<li class="kcategory-smdetails kauthor"><?php echo JText::_('COM_KUNENA_BY').' '.$this->lastUser->getLink($this->lastUserName) ?></li>
											<li class="kcategory-smdetails kdate"><?php echo JText::sprintf('COM_KUNENA_ON_DATE', "[K=DATE:{$this->lastPostTime}]") ?></li>
										<?php else : ?>
											<li class="kcategory-smdetails klastpost"><?php echo JText::_('COM_KUNENA_NO_POSTS'); ?></li>
										<?php endif ?>
										</ul>
									</td>
								</tr>
								<?php if ($this->subcategories) : ?>
								<tr>
									<td colspan="4" class="kcategory-subcats">
										<ul class="kcategory-subcat">
										<?php foreach ( $this->subcategories as $subcategory ) : ?>
										<li class="kcategory-smdetails kcategory-smicon[K=CATEGORY_NEW_SUFFIX:<?php echo $subcategory->id ?>]">
											<h4><?php echo $this->getCategoryLink($subcategory, null, JText::sprintf('COM_KUNENA_VIEW_CATEGORY_LIST_SUBCATEGORY_TITLE', $this->escape($subcategory->name))) ?> [K=CATEGORY_NEW_COUNT:<?php echo $subcategory->id ?>]</h4>
											<span class="kcounts"><?php echo JText::sprintf('COM_KUNENA_VIEW_CATEGORY_LIST_TOPICS_N_REPLIES', $subcategory->getTopics(), $subcategory->getPosts()) ?></span>
										</li>
										<?php endforeach ?>
										</ul>
									</td>
								</tr>
								<?php endif ?>
							</tbody>
						</table>
					</li>
				</ul>