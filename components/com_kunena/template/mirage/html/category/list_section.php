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
<?php if (!empty($this->categories [$this->section->id])) : ?>
	<div class="kmodule category-list_section">
		<div class="kbox-wrapper kbox-full">
			<div class="category-list_section-kbox kbox kbox-full kbox-color kbox-border kbox-border_radius kbox-border_radius-vchild kbox-shadow <?php echo $this->getClass('', $this->escape($this->section->class_sfx)) ?>" id="block-<?php echo $this->displaySectionField('id') ?>">
				<div class="headerbox-wrapper kbox-full">
					<div class="header fl">
						<h2 class="header link-header2">
							<a class="section" href="<?php echo $this->sectionURL ?>" rel="ksection-detailskbox-<?php echo $this->displaySectionField('id') ?>">
								<?php echo $this->escape($this->section->name) ?>
							</a>
						</h2>
						<?php if ($this->section->description) : ?>
							<div class="header-desc"><?php echo $this->displaySectionField('description') ?></div>
						<?php endif ?>
					</div>
					<div class="header fr">
					</div>
				</div>
				<div class="detailsbox-wrapper innerspacer kbox-full">
					<div class="category detailsbox kbox-full kbox-border kbox-border_radius kbox-shadow" id="category-<?php echo intval($this->section->id) ?>">
						<ul class="category-list">
							<li class="header kbox-hover_header-row">
								<dl>
									<!--<dd class="category-icon">
									</dd>-->
									<dd class="category-subject">
										<div class="innerspacer-header">
											<span class="bold"><?php echo JText::_('Category') ?></span>
										</div>
									</dd>
									<dd class="category-topics">
										<div class="innerspacer-header">
											<span class="bold"><?php echo JText::_('COM_KUNENA_GEN_TOPICS') ?></span>
										</div>
									</dd>
									<dd class="category-replies">
										<div class="innerspacer-header">
											<span class="bold"><?php echo JText::_('COM_KUNENA_GEN_REPLIES') ?></span>
										</div>
									</dd>
									<dd class="category-lastpost">
										<div class="innerspacer-header">
											<span class="bold"><?php echo JText::_('Last Post') ?></span>
										</div>
									</dd>
								</dl>
							</li>
						</ul>
						<ul class="category-list">
							<?php foreach ( $this->categories [$this->section->id] as $category ) $this->displayCategory($category) ?>
						</ul>
					</div>
				</div>
				<?php if (!empty($this->sectionButtons)) : ?>
					<div class="modbox-wrapper innerspacer-bottom">
						<div class="modbox">
							<?php echo implode(' ', $this->sectionButtons) ?>
						</div>
					</div>
				<?php endif ?>
			</div>
		</div>
	</div>

<?php endif ?>
