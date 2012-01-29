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
<div class="block-wrapper box-color box-border box-border_radius">
	<div class="block">
		<div class="headerbox-wrapper">
			<div class="header">
				<h2 class="header">
					<a href="#" title="Category Header" rel="ksection-detailsbox">
						<?php echo JText::_('COM_KUNENA_VIEW_CATEGORY_DEFAULT_TOPICS') ?>
					</a>
				</h2>
				<?php if ($this->category->headerdesc) : ?>
					<div class="header-desc"><?php echo $this->parse($this->category->headerdesc) ?></div>
				<?php endif ?>
			</div>
		</div>
		<div class="detailsbox-wrapper">
			<div class="topic detailsbox">
				<ul class="topic-list">
					<li class="header">
						<dl>
							<dd class="topic-icon">
							</dd>
							<dd class="topic-subject">
								<span><?php echo JText::_('Subject') ?></span>
							</dd>
							<dd class="topic-replies">
								<span><?php echo JText::_('COM_KUNENA_GEN_REPLIES') ?></span>
							</dd>
							<dd class="topic-views">
								<span><?php echo JText::_('COM_KUNENA_GEN_HITS') ?></span>
							</dd>
							<dd class="topic-lastpost">
								<span><?php echo JText::_('Last Post') ?></span>
							</dd>
						</dl>
					</li>
				</ul>
				<ul class="topic-list">
					<?php if (empty($this->topics) && empty($this->subcategories)) : ?>
						<li class="topics-row">
							<?php echo JText::_('COM_KUNENA_VIEW_CATEGORY_NO_TOPICS'); ?>
						</li>
					<?php else : $this->displayRows(); endif ?>
				</ul>
			</div>
		</div>
		<?php if ($this->topicActions) : ?>
			<div id="section-modbox">
				<?php echo JHTML::_('select.genericlist', $this->topicActions, 'task', 'class="kinputbox" size="1"', 'value', 'text', 0, 'kmoderate-select'); ?>
				<input type="checkbox" value="0" name="" class="kmoderate-topic-checkall" />
			</div>
	<?php endif ?>
	</div>
</div>
<div class="spacer"></div>