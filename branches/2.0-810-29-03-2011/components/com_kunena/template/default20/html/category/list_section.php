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
		<div class="<?php echo $this->getClass('ksection', $this->escape($this->section->class_sfx)) ?>" id="ksection-<?php echo intval($this->section->id) ?>">
			<?php if (!empty($this->sectionRssURL)) : ?>
			<a href="<?php echo $this->sectionRssURL ?>" title="<?php echo JText::sprintf('COM_KUNENA_VIEW_CATEGORY_LIST_RSS_TITLE', $this->escape($this->section->name)) ?>"><span class="krss-icon"><?php echo JText::sprintf('COM_KUNENA_VIEW_CATEGORY_LIST_RSS_TITLE', $this->escape($this->section->name)) ?></span></a>
			<?php endif ?>
			<?php if (!empty($this->sectionMarkReadURL)) : ?>
         <a href="<?php echo $this->sectionMarkReadURL ?>" class="kheader-link"><?php echo JText::_('COM_KUNENA_VIEW_CATEGORY_LIST_MARKALL') ?> &raquo;</a>
			<?php endif ?>
			<a href="<?php echo $this->sectionURL ?>" class="ksection-headericon"><?php echo $this->getImage('icon-section.png') ?></a>
			<h2 class="kheader"><a href="<?php echo $this->sectionURL ?>" rel="ksection-detailsbox-<?php echo intval($this->section->id) ?>"><?php echo $this->escape($this->section->name) ?></a></h2>
			<?php if ($this->section->description) : ?>
			<div class="kheader-desc"><?php echo $this->parse($this->section->description) ?></div>
			<?php endif ?>
			<div class="kdetailsbox" id="ksection-detailsbox-<?php echo intval($this->section->id) ?>">
				<?php
				if (!empty($this->categories [$this->section->id])) {
					foreach ( $this->categories [$this->section->id] as $category ) {
						echo $this->displayCategory($category);
					}
				}
				?>
			</div>
			<div class="clr"></div>
		</div>