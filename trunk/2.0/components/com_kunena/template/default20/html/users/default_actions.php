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
				<li class="kfilter-title">Filter users by:</li>
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
				<!-- li>
					<input type="text" autocomplete="off" id="kfilter" class="kinput" name="filter" value="">
				</li -->
				<li>
					<button class="kfilter-button">Go</button>
				</li>
			</ul>
		</div>