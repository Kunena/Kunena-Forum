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
		<div class="kfilterbar">
			<div class="kpaginationbar">
				<?php echo $this->getPagination(7) ?>
			</div>
			<form name="kfilter" method="post" action="<?php echo $this->URL ?>">
			<ul class="kfilter-options">
				 <li style="border-right: 1px solid #BCBCBC;">
					<strong><?php echo intval($this->total) ?></strong>
					<?php echo JText::_('COM_KUNENA_DISCUSSIONS')?>
				</li>
				<!-- li class="kfilter-title">Filter posts by:</li>
				<li>
					<select size="1" onchange="this.form.submit()" class="kinputbox" id="kfilter-select-cat" name="do">
						<option selected="selected" value="AllForumsCategories">All Forum Categories</option>
						<option value="Category1">Category 1</option>
						<option value="Category2">Category 2</option>
						<option value="Category3">Category 3</option>
					</select>
				</li -->
					<li>
						<?php
						// make the select list for time selection
						$timesel[] = JHTML::_('select.option', -1, JText::_('COM_KUNENA_SHOW_ALL'));
						$timesel[] = JHTML::_('select.option', 0, JText::_('COM_KUNENA_SHOW_LASTVISIT'));
						$timesel[] = JHTML::_('select.option', 4, JText::_('COM_KUNENA_SHOW_4_HOURS'));
						$timesel[] = JHTML::_('select.option', 8, JText::_('COM_KUNENA_SHOW_8_HOURS'));
						$timesel[] = JHTML::_('select.option', 12, JText::_('COM_KUNENA_SHOW_12_HOURS'));
						$timesel[] = JHTML::_('select.option', 24, JText::_('COM_KUNENA_SHOW_24_HOURS'));
						$timesel[] = JHTML::_('select.option', 48, JText::_('COM_KUNENA_SHOW_48_HOURS'));
						$timesel[] = JHTML::_('select.option', 168, JText::_('COM_KUNENA_SHOW_WEEK'));
						$timesel[] = JHTML::_('select.option', 720, JText::_('COM_KUNENA_SHOW_MONTH'));
						$timesel[] = JHTML::_('select.option', 8760, JText::_('COM_KUNENA_SHOW_YEAR'));
						echo JHTML::_('select.genericlist', $timesel, 'sel', 'class="kinputbox" onchange="this.form.submit()" size="1"', 'value', 'text', $this->state->get('list.time'), 'kfilter-select-time');
						?>
				</li>
				<li>
					<button class="kfilter-button">Go</button>
				</li>
			</ul>
			</form>
		</div>