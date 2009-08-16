<?php
/**
 * @version		$Id:$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;
?>

		<div class="top_info_box">
			<div class="discussions"><span>647</span> Discussions</div>
			<div class="choose_hours">
				<select class="input_hours" onchange="document.location.href=this.options[this.selectedIndex].value;" name="select">
					<option value="/forum/latest/show/sel-4">4 Hours</option>
					<option value="/forum/latest/show/sel-8">8 Hours</option>
					<option value="/forum/latest/show/sel-12">12 Hours</option>
					<option value="/forum/latest/show/sel-24">24 Hours</option>
					<option value="/forum/latest/show/sel-48">48 Hours</option>
					<option value="/forum/latest/show/sel-168">Week</option>
					<option selected="selected" value="/forum/latest/show/sel-720">Month</option>
					<option value="/forum/latest/show/sel-8760">Year</option>
				</select>
			</div>
			<div class="jump_box">
				<form id="jump_forum" name="jumpto" method="post" target="_self" action="/forum">
					<input type="hidden" name="func" value="showcat"/>
					<select name="catid" class="choose_forum" onchange="if(this.options[this.selectedIndex].value> 0){ this.form.submit() }">
						<option value="0" selected="selected">Board Categories&#32;</option>
						<option value="94">Kunena - To Speak!</option>
						<option value="77">...&nbsp;General Talk about Kunena</option>
						<option value="119">...&nbsp;Feature Requests</option>
						<option value="136">...&nbsp;Templates and Design</option>
					</select>
					<input type="submit" name="Go" class="go_forum" value="Go"/>
				</form>
			</div>
			<div class="pagination_box">
				Page: <span>1</span>
				<a href="/forum/latest/page-2/sel-720" title="Page 2">2</a>
				<a href="/forum/latest/page-3/sel-720" title="Page 3">3</a>
				<a href="/forum/latest/page-4/sel-720" title="Page 4">4</a>...
				<a href="/forum/latest/page-22/sel-720" title="Page 22">22</a>
			</div>
		</div>
