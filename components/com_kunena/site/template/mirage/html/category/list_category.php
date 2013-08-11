<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Category
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$this->category_interval ^= 1;
?>

<li class="category-row category-row-<?php echo $this->category_rowclass [$this->category_interval], !empty($this->category->class_sfx) ? ' category-row-' . $this->escape($this->category_rowclass [$this->category_interval]) . $this->escape($this->category->class_sfx) : '' ?> kbox-animate kbox-full">
	<div class="kbox-hover_list-row kbox-animate">
		<dl class="category-list list-unstyled list-column kbox-full">
			<!--<dd class="category-icon">
			</dd>-->
			<dd class="category-subject item-column">
				<div class="innerspacer-column">
					<a class="fl" href="<?php echo $this->category->getUrl() ?>" title="<?php echo JText::sprintf('COM_KUNENA_VIEW_CATEGORY_LIST_CATEGORY_TITLE', $this->escape($this->category->name)) ?>">[K=CATEGORY_ICON]</a>
					<ul class="kcontent-32 list-unstyled">
						<li class="category-title">
							<h3 class="link-header3"><a href="<?php echo $this->category->getUrl() ?>" title="<?php echo JText::sprintf('COM_KUNENA_VIEW_CATEGORY_LIST_CATEGORY_TITLE', $this->escape($this->category->name)) ?>"><?php echo $this->escape($this->category->name) ?></a> [K=CATEGORY_NEW_COUNT]</h3>
						</li>
						<?php if ($this->category->description) : ?>
							<li class="category-details"><div><?php echo $this->displayCategoryField('description') ?></div></li>
						<?php endif ?>
						<?php if ($this->moderators) : ?>
							<li class="kcategory-mods">
								<ul>
									<li class="kcategory-modtitle"><?php echo JText::_('COM_KUNENA_MODERATORS') ?>:</li>
									<?php foreach ($this->moderators as $moderator) : ?>
										<li class="kcategory-modname kusername-mod"><?php echo $moderator->getLink() ?></li>
									<?php endforeach ?>
								</ul>
							</li>
						<?php endif ?>
					</ul>
				</div>
			</dd>
			<dd class="category-topics item-column">
				<div class="innerspacer-column">
					<span class="number"><?php echo $this->formatLargeNumber($this->category->getTopics()); ?></span><br />
					<span class=""><?php echo JText::_('COM_KUNENA_TOPICS') ?></span>
				</div>
			</dd>
			<dd class="category-replies item-column">
				<div class="innerspacer-column">
					<span class="number"><?php echo $this->formatLargeNumber($this->category->getReplies()); ?></span><br />
					<span class=""><?php echo JText::_('COM_KUNENA_GEN_REPLIES') ?></span>
				</div>
			</dd>
			<!-- td class="kcategory-subs">944 <span>Subscribers</span></td -->
			<dd class="category-lastpost item-column">
				<div class="innerspacer-column">
				<?php if ($this->lastPost) : ?>
					<?php if ( $this->config->avataroncat ) : ?><?php echo $this->lastUser->getLink($this->lastUser->getAvatarImage('klist-avatar  kavatar kavatar-32 fl', 'list')) ?><?php endif ?>
					<ul class="kcontent-36 list-unstyled">
						<?php //TODO: Fix KunenaHtmlParser function reference. ?>
						<li class="category-lastpost-topic"><?php echo $this->getLastPostLink($this->category, null, KunenaHtmlParser::parseText($this->category->getLastTopic()->subject, 40), 'link') ?></li>
						<li class="category-lastpost-author"><?php echo JText::_('COM_KUNENA_BY').' '.$this->lastUser->getLink($this->lastUserName) ?></li>
						<li class="category-lastpost-date"><?php echo JText::sprintf('COM_KUNENA_ON_DATE', "[K=DATE:{$this->lastPostTime}]") ?></li>
					</ul>
				<?php else : ?>
					<ul class="list-unstyled">
						<li class="kcategory-smdetails klastpost"><?php echo JText::_('COM_KUNENA_NO_POSTS'); ?></li>
					</ul>
				<?php endif ?>
				</div>
			</dd>
		</dl>
		<?php if ($this->subcategories) : ?>
			<div class="innerspacer-horizontal-inline">
				<div class="divider-horizontal-inline divider-horizontal-inline-dotted"></div>
			</div>
			<div class="innerspacer-column kbox-full">
				<ul class="kcontent-32 list-unstyled kcategory-subcat-list">
					<?php foreach ( $this->subcategories as $subcategory ) : ?>
						<li class="kcategory-smdetails kcategory-smicon[K=CATEGORY_NEW_SUFFIX:<?php echo $subcategory->id ?>]">
							<h4 class="kcontent-16"><?php echo $this->getCategoryLink($subcategory, null, JText::sprintf('COM_KUNENA_VIEW_CATEGORY_LIST_SUBCATEGORY_TITLE', $this->escape($subcategory->name))) ?> [K=CATEGORY_NEW_COUNT:<?php echo $subcategory->id ?>]<span class="kcounts"><?php echo JText::sprintf('COM_KUNENA_VIEW_CATEGORY_LIST_TOPICS_N_REPLIES', $subcategory->getTopics(), $subcategory->getReplies()) ?></span></h4>
						</li>
					<?php endforeach ?>
				</ul>
			</div>
		<?php endif ?>
	</div>
</li>
