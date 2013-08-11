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
	<form action="<?php echo $this->URL ?>" name="kfilter" method="post">
	<ul class="kfilter-options">
		<?php // TODO: better place? ?>
		<li class="kfilter-total">
			<strong><?php echo intval($this->total) ?></strong>
			<?php echo JText::_('COM_KUNENA_TOPICS')?>
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
				<?php $this->displayTimeFilter() ?>
		</li>
		<li>
		<button class="kfilter-button" style="display:none">Go</button>
		</li>
	</ul>
	</form>
</div>
