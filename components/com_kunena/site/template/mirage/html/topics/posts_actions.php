<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Topics
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="kfilterbar">
	<div class="kpaginationbar">
		<?php echo $this->getPagination(7) ?>
	</div>
	<ul class="list-unstyled kfilter-options">
		<li class="kfilter-title"><?php echo JText::_('COM_KUNENA_FILTER_MESSAGES_BY'); ?>:</li>
		<li>
			<select size="1" onchange="this.form.submit()" class="kinputbox" id="kfilter-select-attr" name="do">
				<option selected="selected" value="0"><?php echo JText::_('COM_KUNENA_SHOW_LAST') ?></option>
				<option value="4"><?php echo JText::_('COM_KUNENA_STAT_POPULAR') ?></option>
				<option value="4"><?php echo JText::_('COM_KUNENA_TOPIC_ACTIONS_MOST_ANSWERS') ?></option>
				<option value="8"><?php echo JText::_('COM_KUNENA_SEARCH_SORTBY_VIEWS'); ?></option>
				<option value="12"><?php echo JText::_('COM_KUNENA_TOPIC_ACTIONS_NUMBER_THANK_YOUS') ?></option>
			</select>
		</li>
		<!-- li>
			<input type="text" autocomplete="off" id="kfilter" class="kinput" name="filter" value="">
		</li -->
		<li>
			<button class="kfilter-button">Go</button>
		</li>
	</ul>
</div>