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
			<ul class="kfilter-options">
				<li class="kfilter-title">Filter posts by:</li>
				<li>
					<select size="1" onchange="this.form.submit()" class="kinputbox" id="kfilter-select-attr" name="do">
						<option selected="selected" value="0">Most Recent</option>
						<option value="4">Most Popular</option>
						<option value="4">Most Responses</option>
						<option value="8">Number of Views</option>
						<option value="12">Number of Thank Yous</option>
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