<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;
?>
	<div id="kunena">
		<div class="topnav">
			<ul>
				<li class="active"><a href="/forum/latest" title="Recent Discussions"><span>Recent Discussions</span></a></li>
				<li><a href="/forum/listcat" title="Categories"><span>Categories</span></a></li>
				<li><a href="/forum/faq" title="Help"><span>Help</span></a></li>
			</ul>
		</div>
		<div class="search">
			<form action="/forum/search" name="Search" method="post">
				<input class="inputbox" type="text" name="q" size="15" value="Search Forum" onblur="if(this.value=='') this.value='Search Forum';" onfocus="if(this.value=='Search Forum') this.value='';" />
				<input type="submit" value="Go" name="submit" class="submit_btn"/>
			</form>
		</div>
		<div class="show_hide">
			<img class="show_hide" src="images/icons/shrink.gif" alt="Show/Hide" />
		</div>
					
		<div class="profile_box">
			<p class="welcome">Welcome, <span>Guest</span></p>
			<p class="register_login">Please <a href="/component/user/login">Log in</a> or <a href="/component/user/register">Register</a>. <a href="/component/user/reset">Lost Password?</a></p>												
		</div>								
											
		<div class="corner1">
			<div class="corner2">
				<div class="corner3">
					<div class="corner4">
						<table class="announcements" summary="Announcements">
							<thead>
								<tr>
									<h3>Kunena 1.0.11 & 1.5.4 Released</h3>	
									<div><img class="show_hide" src="images/icons/shrink.gif" alt="Show/Hide"/></div>
								</tr>
							</thead>
							<tbody>
								<tr class="row_odd">
									<td class="col2">
										<div class="announce_create">07/11/2009 00:45</div>
										<div class="announce_summary">The Kunena Team announces the immediate availability of Kunena 1.5.4 and 1.0.11. These are important
														security releases and users are urged to update immediately. These releases also fix a number of other minor and major issues.
														<a href="/forum/announcement/read/id-8">Read More...</a></div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
