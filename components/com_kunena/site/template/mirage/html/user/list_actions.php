<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage User
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="kfilterbar">
	<div class="kpaginationbar">
		<?php echo $this->getPagination(7); ?>
	</div>
	<ul class="kfilter-options">
		<li>&nbsp;</li>
		<!-- li class="kfilter-title">Filter users by:</li>
		<li>
			<select size="1" onchange="this.form.submit()" class="kinputbox" id="kfilter-select-attr" name="do">
				<option selected="selected" value="0">Last Registered</option>
				<option value="4">Logged-in</option>
				<option value="4">Rank</option>
				<option value="8">Number of Posts</option>
				<option value="12">Number of Thank Yous</option>
				<option value="24">Moderators</option>
				<option value="48">Administrators</option>
			</select>
		</li>
		<li>
			<input type="text" id="kfilter" class="kinput kautocomplete-off" name="filter" value="">
		</li>
		<li>
			<button class="kfilter-button">Go</button>
		</li -->
	</ul>
</div>