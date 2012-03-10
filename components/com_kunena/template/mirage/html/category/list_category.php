<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Category
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<li class="category-row box-hover box-hover_list-row clear">
	<dl class="list-unstyled">
		<!--<dd class="category-icon">
		</dd>-->
		<dd class="category-subject">
			<a class="fl" href="<?php echo $this->category->getUrl() ?>" title="<?php echo JText::sprintf('COM_KUNENA_VIEW_CATEGORY_LIST_CATEGORY_TITLE', $this->escape($this->category->name)) ?>">[K=CATEGORY_ICON]</a>
			<ul class="list-unstyled fr">
				<li class="category-title">
					<h3 class="link-header3"><a href="<?php echo $this->category->getUrl() ?>" title="<?php echo JText::sprintf('COM_KUNENA_VIEW_CATEGORY_LIST_CATEGORY_TITLE', $this->escape($this->category->name)) ?>"><?php echo $this->escape($this->category->name) ?></a> [K=CATEGORY_NEW_COUNT]</h3>
				</li>
				<?php if ($this->category->description) : ?>
					<li class="category-details"><div><?php echo $this->displayCategoryField('description') ?></div></li>
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
		</dd>
		<dd class="category-topics"><span class="number"><?php echo $this->category->getTopics() ?></span></dd>
		<dd class="category-replies"><span class="number"><?php echo $this->category->getReplies() ?> </span></dd>
		<!-- td class="kcategory-subs">944 <span>Subscribers</span></td -->
		<dd class="category-lastpost">
			<ul class="list-unstyled">
				<?php if ($this->lastPost) : ?>
					<?php if ( $this->config->avataroncat ) : ?><li class="category-lastpost-avatar"><?php echo $this->lastUser->getLink($this->lastUser->getAvatarImage('klist-avatar', 'list')) ?></li><?php endif ?>
					<li class="category-lastpost-topic"><?php echo $this->getLastPostLink($this->category) ?></li>
					<li class="category-lastpost-author"><?php echo JText::_('COM_KUNENA_BY').' '.$this->lastUser->getLink($this->lastUserName) ?></li>
					<li class="category-lastpost-date"><?php echo JText::sprintf('COM_KUNENA_ON_DATE', "[K=DATE:{$this->lastPostTime}]") ?></li>
					<?php else : ?>
					<li class="kcategory-smdetails klastpost"><?php echo JText::_('COM_KUNENA_NO_POSTS'); ?></li>
				<?php endif ?>
			</ul>
		</dd>
	</dl>
	<?php if ($this->subcategories) : ?>
	<dl>
		<dd class="kcategory-subcats">
			<ul class="list-unstyled kcategory-subcat">
				<?php foreach ( $this->subcategories as $subcategory ) : ?>
					<li class="kcategory-smdetails kcategory-smicon[K=CATEGORY_NEW_SUFFIX:<?php echo $subcategory->id ?>]">
						<h4><?php echo $this->getCategoryLink($subcategory, null, JText::sprintf('COM_KUNENA_VIEW_CATEGORY_LIST_SUBCATEGORY_TITLE', $this->escape($subcategory->name))) ?> [K=CATEGORY_NEW_COUNT:<?php echo $subcategory->id ?>]</h4>
						<span class="kcounts"><?php echo JText::sprintf('COM_KUNENA_VIEW_CATEGORY_LIST_TOPICS_N_REPLIES', $subcategory->getTopics(), $subcategory->getReplies()) ?></span>
					</li>
				<?php endforeach ?>
			</ul>
		</dd>
	</dl>
	<?php endif ?>
</li>
