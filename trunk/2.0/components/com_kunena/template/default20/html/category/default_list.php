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
			<a href="#" title="Announcement RSS Feed"><span class="krss-icon">Category RSS Feed</span></a>
			<a href="#" class="kheader-link" title="Mark all categories read">Mark this category read</a>
			<a href="#" class="ksection-headericon"><img src="images/icon-section.png" alt="" /></a>
			<h2 class="kheader"><a href="#" title="Category Header" rel="ksection-detailsbox">Category Header</a></h2>
			<p class="kheader-desc">Category Header Description</p>
			<div class="kdetailsbox" id="ktopics-detailsbox">
				<ul class="ktopics">
					<?php include 'default_row.php'; ?>
				</ul>
			</div>
			<div class="clr"></div>
		</div>
		<div id="ksection-modbox">
			<select size="1" class="kinputbox" id="kmoderate-select" name="do">
				<option value="ModerateTopics">Moderate Topics</option>
				<option value="DeleteTopics">Delete Selected</option>
				<option value="MoveSelected">Move Selected</option>
				<option value="PermDelSel">Permanently Delete Selected</option>
				<option value="RestoreSel">Restore Selected</option>
			</select>
			<input type="checkbox" value="0" name="" class="kmoderate-topic-checkall">
		</div>