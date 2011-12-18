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
				<li class="row categorysuffix-<?php echo intval($this->category->id);?>">
					<dl class="icon <?php if ($this->me->userid != 0) { ?>caticon[K=CATEGORY_NEW_SUFFIX:<?php echo intval($this->category->id) ?>]<?php } else { echo 'caticon-notlogin';}?>">
						<dt class="catname">
							<span class="cattitle">
								<a href="<?php echo $this->categoryURL ?>" title="<?php echo JText::sprintf('COM_KUNENA_VIEW_CATEGORY_LIST_CATEGORY_TITLE', $this->escape($this->category->name)) ?>"><?php echo $this->escape($this->category->name) ?></a> [K=CATEGORY_NEW_COUNT]
							</span>
							<?php if (!empty($this->categoryRssURL)) : ?>
								<a href="<?php echo $this->categoryRssURL ?>" title="<?php echo JText::sprintf('COM_KUNENA_VIEW_CATEGORY_LIST_RSS_TITLE', $this->escape($this->section->name)) ?>"><span class="krss-icon" style="display:none;"></span></a>
							<?php endif ?>
							<?php // TODO: some cool ajax functions?
							if( $this->me->userid != 0 ): ?>
								<a href="#" title="<?php echo JText::_('Mark topics as read'); ?>"><span class="kmarkread-icon" style="margin-right:3px; display:none;"></span></a>
								<a href="#" title="<?php echo JText::_('Subscribe Now'); ?>"><span class="ksubs-icon" style="margin-right:3px;display:none;"></span></a>
							<?php /*?>
								<a href="" title="<?php echo JText::_('Start New Topic'); ?>"><span class="knewtopic-icon" style="margin-right:3px;"></span></a>
								<a href="" title="<?php echo JText::_('New Posts'); ?>"><span class="knewposts-icon" style="margin-right:3px;"></span></a>
							<?php */?>
							<?php endif ?>
							<?php if ($this->category->description) : ?>
							<span class="catdesc">
								<?php echo $this->parse($this->category->description) ?>
							</span>
							<?php endif ?>
							<?php // FIXME : Seems that dont work ?>
							<?php if ($this->moderators && $this->params->get('showModlist') == '1') : ?>
							<?php foreach ($this->moderators as $moderator) : ?>
							<span class="tkthead-moderators ks">
								<?php echo $moderator->getLink() ?>
							</span>
							<?php endforeach ?>
							<?php endif; ?>
							<?php // TODO: pending messages? ?>
							<?php /*if (! empty ( $this->pending [$category->id] )) : ?>
							<span class="tkalert ks">
								<?php echo CKunenaLink::GetCategoryReviewListLink ( intval($category->id), intval($this->pending [$category->id]) . ' ' . JText::_('COM_KUNENA_SHOWCAT_PENDING'), 'nofollow' ); ?>
							</span>
							<?php endif;*/ ?>
							<?php if ($this->params->get('countcolumnShow') == 0):?>
							<span class="tk-activity-count">
								<?php echo JText::_('COM_KUNENA_GEN_TOPICS');?>: <b><?php echo $this->formatLargeNumber ( $this->category->getTopics() ) ?></b>
								&nbsp;<?php echo JText::_('COM_KUNENA_GEN_REPLIES');?>: <b><?php echo $this->formatLargeNumber ( $this->category->getPosts() ) ?></b>
							</span>
							<?php endif; ?>
							<?php if ($this->category->review && !$this->category->isSection()) : ?>
								<?php echo $this->getIcon ( 'kforummoderated', JText::_('COM_KUNENA_GEN_MODERATED') ); ?>
							<?php endif; ?>
							<?php if ($this->category->locked && !$this->category->isSection()) : ?>
								<?php echo $this->getIcon ( 'kforumlocked', JText::_('COM_KUNENA_GEN_LOCKED_FORUM') ); ?>
		 					<?php endif; ?>
						</dt>
						<?php if ($this->params->get('countcolumnShow') != 0):?>
						<dd class="topics">
						 	<span>
								<?php echo $this->formatLargeNumber ( $this->category->getTopics() ) ?>
							</span>
						</dd>
						<dd class="posts">
							<span>
								<?php echo $this->formatLargeNumber ( $this->category->getPosts() ) ?>
							</span>
						</dd>
						<?php endif;?>
						<?php if ($this->lastPost) : ?>
						<dd class="lastpost tk-lastpost">
							<?php if ( $this->config->avataroncat ) : ?>
							<span class="tklatest-avatar">
								<?php echo $this->lastUser->getLink($this->lastUser->getAvatarImage('klist-avatar', 'list')) ?>
							</span>
							<?php endif ?>
							<span>
								<b><?php echo $this->getLastPostLink($this->category) ?></b>
							</span>
							<?php
								echo '<br /><span class="nowrap ks">'.JText::_('COM_KUNENA_BY') . ' ';
								echo $this->lastUser->getLink($this->lastUserName).'</span>';
								echo '<br /><span class="nowrap ks">' . JText::sprintf('COM_KUNENA_ON_DATE', "[K=DATE:{$this->lastPostTime}]") . '</span>';
							?>
						</dd>
						<?php else : ?>
						<dd class="lastpost nopost tk-lastpost">
							<span>
								<?php echo JText::_('COM_KUNENA_NO_POSTS'); ?>
							</span>
						</dd>
						<?php endif ?>
				<?php if ($this->subcategories) : ?>
				<?php // TODO : Move style to CSS file ?>
				<dd class="tk-subcategories" style="padding: 5px 0 5px 0px;margin:  0px 0 0px 45px;clear:left;border-left:0px;width:90%;">
					<div>
					<?php foreach ( $this->subcategories as $subcategory ) : ?>
						<span class="tkchild-name tkchild-column-<?php echo $this->params->get('numChildcolumn')?> kcategory-smicon[K=CATEGORY_NEW_SUFFIX:<?php echo $subcategory->id ?>]">
							<?php echo $this->getCategoryLink($subcategory, null, JText::sprintf('COM_KUNENA_VIEW_CATEGORY_LIST_SUBCATEGORY_TITLE', $this->escape($subcategory->name))) ?> [K=CATEGORY_NEW_COUNT:<?php echo $subcategory->id ?>]
							<?php echo '<span class="tkchild-count">( ' . $subcategory->getTopics() . " / " . $subcategory->getPosts() . ' )</span>'; ?>
						</span>
					<?php endforeach; ?>
					</div>
				</dd>
				<?php endif; ?>
					</dl>
				</li>