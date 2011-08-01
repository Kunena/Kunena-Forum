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
<div class="forumlist tk-clear tk-latestx">
	<div class="catinner">
		<span class="corners-top"><span></span></span>
			<ul class="topiclist forums">
				<li class="rowfull tk-latestx">
					<dl class="icon">
						<dt style="">
							<?php echo JText::_('Filter users by:'); ?>
						</dt>
						<dd class="tk-selecttime">

					<select size="1" onchange="this.form.submit()" class="kinputbox" id="kfilter-select-attr" name="do">
						<option selected="selected" value="0">Last Registered</option>
						<option value="4">Logged-in</option>
						<option value="4">Rank</option>
						<option value="8">Number of Posts</option>
						<option value="12">Number of Thank Yous</option>
						<option value="24">Moderators</option>
						<option value="48">Administrators</option>
					</select>

					<button class="tk-submit-button">Go</button>
						</dd>
						<dd class="tk-pagination">
							<?php echo $this->getPagination(7) ?>
						</dd>
					</dl>
				</li>
			</ul>
		<span class="corners-bottom"><span></span></span>
	</div>
</div>