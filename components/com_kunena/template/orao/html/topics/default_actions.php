<?php
/**
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>

<div class="forumlist tk-clear tk-latestx">
	<div class="catinner">
		<span class="corners-top"><span></span></span>
			<ul class="topiclist forums">
				<li class="rowfull tk-latestx">
					<dl class="icon">
						<dt style="">
							<?php echo JText::_('COM_KUNENA_PROFILEBOX_SHOW_LATEST_POSTS'); ?>
						</dt>
						<dd class="tk-selecttime">
						<form name="kfilter" method="post" action="<?php echo $this->URL ?>">
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
						</form>
						</dd>
						<dd class="tk-pagination">
							<?php echo $this->getPagination(7) ?>
						</dd>
						<dd class="tk-total" style="margin-left:20px;border:0;">
							<strong><?php echo intval($this->total)?></strong> <?php echo JText::_('COM_KUNENA_DISCUSSIONS')?>
						</dd>
					</dl>
				</li>
			</ul>
		<span class="corners-bottom"><span></span></span>
	</div>
</div>