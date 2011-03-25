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
				<ul class="kpage">
					<!-- Loop this LI for each page -->
					<li class="kpage-title">Page:</li>
					<li class="kpage-active">1</li>
					<li><a href="#" title="Page 2 of this topic">2</a></li>
					<li><a href="#" title="Page 3 of this topic">3</a></li>
					<li><a href="#" title="Page 4 of this topic">4</a></li>
					<li><a href="#" title="Page 5 of this topic">5</a></li>
					<li><a href="#" title="Page 6 of this topic">6</a></li>
					<li><a href="#" title="Page 7 of this topic">7</a></li>
					<li><a href="#" title="Page 8 of this topic">8</a></li>
					<li class="kpage-more">...</li>
					<li><a href="#" title="Page 14 of this topic">14</a></li>
				</ul>
			</div>
			<ul class="kfilter-options">
				<li class="kfilter-title">Filter posts by:</li>
				<li>
					<select size="1" onchange="this.form.submit()" class="kinputbox" id="kfilter-select-cat" name="do">
						<option selected="selected" value="AllForumsCategories">All Forum Categories</option>
						<option value="Category1">Category 1</option>
						<option value="Category2">Category 2</option>
						<option value="Category3">Category 3</option>
					</select>
				</li>
				<li>
					<select size="1" onchange="this.form.submit()" class="kinputbox" id="kfilter-select-time" name="do">
						<option selected="selected" value="0">Since last visit</option>
						<option value="4">4 Hours</option>
						<option value="8">8 Hours</option>
						<option value="12">12 Hours</option>
						<option value="24">24 Hours</option>
						<option value="48">48 Hours</option>
						<option value="168">Week</option>
						<option value="720">Month</option>
						<option value="8760">Year</option>
					</select>
				</li>
				<li>
					<button class="kfilter-button">Go</button>
				</li>
			</ul>
		</div>