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
			<h2 class="kheader"><a title="Recent Posts" rel="krecposts-detailsbox">Recent Posts</a></h2>
			<div class="kdetailsbox krec-posts" id="krecposts-detailsbox">
				<ul class="kposts">
					<?php include 'posts_row.php' ?>
				</ul>
			</div>
			<div class="clr"></div>
		</div>
		<div id="ksection-modbox">
			<select size="1" class="kinputbox" id="krecposts-select" name="do">
				<option value="ModeratePosts">Moderate Posts</option>
				<option value="DeleteTopics">Delete Selected</option>
				<option value="MoveSelected">Move Selected</option>
				<option value="PermDelSel">Permanently Delete Selected</option>
				<option value="RestoreSel">Restore Selected</option>
			</select>
			<input type="checkbox" value="0" name="" class="kmoderate-topic-checkall" />
		</div>