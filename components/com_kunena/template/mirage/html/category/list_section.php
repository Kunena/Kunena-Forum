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
<?php if (!empty($this->categories [$this->section->id])) : ?>
	<div class="box-module">
		<div class="block-wrapper box-color box-border box-border_radius box-shadow">
			<div class="<?php echo $this->getClass('block', $this->escape($this->section->class_sfx)) ?>" id="block-<?php echo intval($this->section->id) ?>">
				<div class="headerbox-wrapper box-full">
					<div class="header fl">
						<h2 class="header link-header2">
							<a class="section" href="<?php echo $this->sectionURL ?>" rel="ksection-detailsbox-<?php echo intval($this->section->id) ?>">
								<?php echo $this->escape($this->section->name) ?>
							</a>
						</h2>
						<?php if ($this->section->description) : ?>
							<div class="header-desc"><?php echo $this->parse($this->section->description) ?></div>
						<?php endif ?>
					</div>
					<div class="header fr">
					</div>
				</div>
				<div class="detailsbox-wrapper">
					<div class="category detailsbox box-full box-border box-border_radius box-shadow" id="category-<?php echo intval($this->section->id) ?>">
						<ul class="category-list">
							<li class="header box-hover_header-row">
								<dl>
									<dd class="category-icon">
									</dd>
									<dd class="category-subject">
										<span><?php echo JText::_('Subject') ?></<span>
									</dd>
									<dd class="category-topics">
										<span><?php echo JText::_('COM_KUNENA_GEN_TOPICS') ?></<span>
									</dd>
									<dd class="category-replies">
										<span><?php echo JText::_('COM_KUNENA_GEN_REPLIES') ?></<span>
									</dd>
									<dd class="category-lastpost">
										<span><?php echo JText::_('Last Post') ?></<span>
									</dd>
								</dl>
							</li>
						</ul>
						<ul class="category-list">
							<?php 
							foreach ( $this->categories [$this->section->id] as $category ) {
							echo $this->displayCategory($category);
							}
							?>
						</ul>
					</div>
				</div>
				<?php if (!empty($this->sectionMarkReadURL)) : ?>
					<div class="modbox-wrapper">
						<div class="modbox">
							<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" name="kunenaMarkAllRead" method="post">
								<input type="hidden" name="view" value="category" />
								<input type="hidden" name="task" value="markread" />
								<?php echo JHTML::_( 'form.token' ); ?>
								<input type="submit" class="header-link" value="<?php echo JText::_('COM_KUNENA_VIEW_CATEGORY_LIST_MARKALL'); ?>" />
							</form>
						</div>
					</div>
				<?php endif ?>
			</div>
		</div>
	</div>
<div class="spacer"></div>
<?php endif ?>