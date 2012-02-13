<?php
/**
 * Kunena Component
 * @package Kunena.Template.Default20
 * @subpackage Category
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<li class="category-row box-hover box-hover_list-row clear">
	<dl>
		<dd class="category-icon">
			<a href="<?php echo $this->categoryURL ?>" title="<?php echo JText::sprintf('COM_KUNENA_VIEW_CATEGORY_LIST_CATEGORY_TITLE', $this->escape($this->category->name)) ?>">[K=CATEGORY_ICON]</a>
		</dd>
		<dd class="category-subject">
			<ul>
				<li class="category-title">
					<h3 class="link-header3"><a href="<?php echo $this->categoryURL ?>" title="<?php echo JText::sprintf('COM_KUNENA_VIEW_CATEGORY_LIST_CATEGORY_TITLE', $this->escape($this->category->name)) ?>"><?php echo $this->escape($this->category->name) ?></a> [K=CATEGORY_NEW_COUNT]</h3>
				</li>
				<?php if ($this->category->description) : ?>
					<li class="category-details"><div><?php echo $this->parse($this->category->description) ?></div></li>
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
		<dd class="category-topics"><?php echo $this->formatLargeNumber ( $this->category->getTopics() ) ?> <span><?php //echo JText::_('COM_KUNENA_GEN_TOPICS') ?></span></dd>
		<dd class="category-replies"><?php echo $this->formatLargeNumber ( $this->category->getPosts() ) ?> <span><?php //echo JText::_('COM_KUNENA_GEN_REPLIES') ?></span></td>
		<!-- td class="kcategory-subs">944 <span>Subscribers</span></td -->
		<dd class="category-lastpost">
			<ul>
				<?php if ($this->lastPost) : ?>
					<?php if ( $this->config->avataroncat ) : ?><li class="kcategory-smavatar"><?php echo $this->lastUser->getLink($this->lastUser->getAvatarImage('klist-avatar', 'list')) ?></li><?php endif ?>
					<li class="kcategory-smdetails klastpost"><?php echo $this->getLastPostLink($this->category) ?></li>
					<li class="kcategory-smdetails kauthor"><?php echo JText::_('COM_KUNENA_BY').' '.$this->lastUser->getLink($this->lastUserName) ?></li>
					<li class="kcategory-smdetails kdate"><?php echo JText::sprintf('COM_KUNENA_ON_DATE', "[K=DATE:{$this->lastPostTime}]") ?></li>
					<?php else : ?>
					<li class="kcategory-smdetails klastpost"><?php echo JText::_('COM_KUNENA_NO_POSTS'); ?></li>
				<?php endif ?>
			</ul>
		</dd>
	</dl>
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
</li>
