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
		<div class="ksection">
			<h2 class="kheader">
				<a href="#" title="Category Header" rel="ksection-detailsbox">
					<?php echo JText::_('COM_KUNENA_VIEW_CATEGORY_DEFAULT_TOPICS') ?>
				</a>
			</h2>
			<?php if ($this->category->headerdesc) : ?>
			<div class="kheader-desc"><?php echo $this->parse($this->category->headerdesc) ?></div>
			<?php endif ?>
			<div class="kdetailsbox" id="ktopics-detailsbox">
				<ul class="ktopics">
					<?php if (empty($this->topics) && empty($this->subcategories)) : ?>
					<li class="ktopics-row krow-odd">
						<?php echo JText::_('COM_KUNENA_VIEW_CATEGORY_NO_TOPICS'); ?>
					</li>
					<?php else : $this->displayRows(); endif ?>
				</ul>
			</div>
			<div class="clr"></div>
		</div>
		<?php if ($this->topicActions) : ?>
		<div id="ksection-modbox">
			<?php echo JHTML::_('select.genericlist', $this->topicActions, 'task', 'class="kinputbox" size="1"', 'value', 'text', 0, 'kmoderate-select'); ?>
			<input type="checkbox" value="0" name="" class="kmoderate-topic-checkall" />
		</div>
		<?php endif ?>