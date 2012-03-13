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
<div class="kmodule category-default_list">
	<div class="kbox-wrapper kbox-full">
		<div class="category-default_list-kbox kbox kbox-full kbox-color kbox-border kbox-border_radius kbox-border_radius-vchild kbox-shadow">
			<div class="headerbox-wrapper kbox-full">
				<div class="header">
					<h2 class="header link-header2">
						<a class="categories" title="Category Header" rel="ksection-detailsbox">
							<?php echo JText::_('COM_KUNENA_VIEW_CATEGORY_DEFAULT_TOPICS') ?>
						</a>
					</h2>
					<?php if ($this->category->headerdesc) : ?>
					<div class="header-desc"><?php echo $this->displayCategoryField('headerdesc') ?></div>
					<?php endif ?>
				</div>
			</div>
			<?php echo $this->displayCategoryActions(); ?>
			<div class="innerbox-wrapper innerspacer kbox-full">
				<?php echo $this->getPagination(7); ?>
			</div>
			<div class="detailsbox-wrapper innerspacer kbox-full">
				<div class="topic detailsbox kbox-full kbox-border kbox-border_radius kbox-shadow">
					<ul class="list-unstyled topic-list">
						<li class="header kbox-hover_header-row clear">
							<dl class="list-unstyled">
								<dd class="topic-icon">
								</dd>
								<dd class="topic-subject">
									<?php // FIXME: translate ?>
									<span class="bold"><?php echo JText::_('Subject') ?></span>
								</dd>
								<dd class="topic-replies">
									<span class="bold"><?php echo JText::_('COM_KUNENA_GEN_REPLIES') ?></span>
								</dd>
								<dd class="topic-views">
									<span class="bold"><?php echo JText::_('COM_KUNENA_GEN_HITS') ?></span>
								</dd>
								<dd class="topic-lastpost">
									<?php // FIXME: translate ?>
									<span class="bold"><?php echo JText::_('Last Post') ?></span>
								</dd>
								<?php if ($this->topicActions) : ?>
								<dd class="topic-checkbox">
									<input type="checkbox" value="0" name="" class="moderate-topic-checkall" />
								</dd>
								<?php endif ?>
							</dl>
						</li>
					</ul>
					<ul class="list-unstyled topic-list">
						<?php if (empty($this->topics) && !$this->category->isSection()) : ?>
						<li class="topics-row kbox-hover kbox-hover_list-row">
							<?php echo JText::_('COM_KUNENA_VIEW_CATEGORY_NO_TOPICS'); ?>
						</li>
						<?php else : $this->displayRows(); endif; ?>
					</ul>
				</div>
			</div>
			<?php if ($this->topicActions) : ?>
			<div class="modbox-wrapper innerspacer-bottom">
				<div class="modbox">
					<?php echo $this->displayTopicActions('class="inputbox" size="1"', 'kmoderate-select') ?>
				</div>
			</div>
			<?php endif ?>
		</div>
	</div>
</div>
<div class="spacer"></div>
