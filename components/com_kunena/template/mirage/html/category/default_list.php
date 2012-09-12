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

$this->topic_interval = 0;
$this->topic_rowclass = array ("even", "odd");
?>
<div class="kmodule category-default_list">
	<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topics') ?>" method="post">
		<?php echo JHtml::_( 'form.token' ); ?>

	<div class="kbox-wrapper kbox-full">
		<div class="category-default_list-kbox kbox kbox-full kbox-color kbox-border kbox-border_radius kbox-border_radius-vchild kbox-shadow kbox-animate">
			<div class="headerbox-wrapper kbox-full">
				<div class="header">
					<h2 class="header link-header2">
						CATEGORY:
						<a class="categories" title="Category Header" rel="ksection-detailsbox">
							<?php echo $this->displayCategoryField('name') ?>
						</a>
					</h2>
					<?php if ($this->category->headerdesc) : ?>
					<div class="header-desc"><?php echo $this->displayCategoryField('headerdesc') ?></div>
					<?php endif ?>
				</div>
			</div>
			<?php echo $this->displayCategoryActions(); ?>
			<div class="innerbox-wrapper innerspacer-top kbox-full">
				<div class="topic-default_list-kpagination kpagination">
					<?php echo $this->getPagination(7); ?>
				</div>
			</div>
			<div class="detailsbox-wrapper innerspacer kbox-full">
				<div class="topic detailsbox kbox-full kbox-border kbox-border_radius kbox-shadow">
					<ul class="topic-list list-unstyled list-row">
						<li class="header kbox-hover_header-row kbox-full item-row">
							<dl class="list-unstyled list-column">
								<!--<dd class="topic-icon">
								</dd>-->
								<dd class="topic-subject item-column">
									<div class="innerspacer-header">
										<?php // FIXME: translate ?>
										<span class="bold"><?php echo JText::_('Subject') ?></span>
									</div>
								</dd>
								<dd class="topic-replies item-column">
										<div class="innerspacer-header">
										<span class="bold"><?php echo JText::_('COM_KUNENA_GEN_REPLIES') ?></span>
									</div>
								</dd>
								<dd class="topic-views item-column">
										<div class="innerspacer-header">
										<span class="bold"><?php echo JText::_('COM_KUNENA_GEN_HITS') ?></span>
									</div>
								</dd>
								<dd class="topic-lastpost item-column">
									<div class="innerspacer-header">
										<?php // FIXME: translate ?>
										<span class="bold"><?php echo JText::_('Last Post') ?></span>
									</div>
								</dd>
								<?php if ($this->topicActions) : ?>
								<dd class="topic-checkbox item-column">
									<div class="innerspacer-header">
										<input type="checkbox" value="0" name="" class="kcheckall moderate-topic-checkall" />
									</div>
								</dd>
								<?php endif ?>
							</dl>
						</li>
					</ul>
					<ul class="topic-list list-unstyled list-row">
						<?php if (empty($this->topics) && !$this->category->isSection()) : ?>
						<li class="topics-row kbox-hover kbox-hover_list-row item-row">
							<dl class="list-unstyled list-column">
								<dd class="topic-none item-column">
									<div class="innerspacer-column">
										<?php echo JText::_('COM_KUNENA_VIEW_CATEGORY_NO_TOPICS'); ?>
									</div>
								</dd>
							</dl>
						</li>
						<?php else : $this->displayRows(); endif; ?>
					</ul>
				</div>
			</div>
			<?php if ($this->topicActions) : ?>
			<div class="modbox-wrapper innerspacer-bottom">
				<div class="modbox">
					<button class="kbutton button-type-mod fr" type="submit"><span><?php echo JText::_('COM_KUNENA_TOPICS_MODERATION_PERFORM'); ?></span></button>
					<?php echo $this->displayTopicActions('class="inputbox form-horizontal fr" size="1"', 'kmoderate-select') ?>
				</div>
			</div>
			<?php endif ?>
		</div>
	</div>
	</form>
</div>

