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
				<li class="active"><a href="/forum/latest" title="<?php echo JText::_('K_RECENT_DISCUSSIONS'); ?>"><span><?php echo JText::_('K_RECENT_DISCUSSIONS'); ?></span></a></li>
				<li><a href="/forum/listcat" title="<?php echo JText::_('K_CATEGORIES'); ?>"><span><?php echo JText::_('K_CATEGORIES'); ?></span></a></li>
				<li><a href="/forum/faq" title="<?php echo JText::_('K_HELP'); ?>"><span><?php echo JText::_('K_HELP'); ?></span></a></li>
			</ul>
		</div>
		<div class="search">
			<form action="/forum/search" name="Search" method="post">
				<input class="inputbox" type="text" name="q" size="15" value="<?php echo JText::_('K_SEARCH_FORUM'); ?>"  onblur="if(this.value=='') this.value='<?php echo JText::_('K_SEARCH_FORUM'); ?>';" onfocus="if(this.value=='<?php echo JText::_('K_SEARCH_FORUM'); ?>') this.value='';" />
				<input type="submit" value="<?php echo JText::_('K_GO'); ?>" name="submit" class="submit_btn"/>
			</form>
		</div>
					
		<div class="profile_box">
			<p class="welcome"><?php echo JText::_('K_WELCOME'); ?>, <span><?php echo JText::_('K_GUEST'); ?></span></p>
			<p class="register_login"><?php echo JText::_('K_PLEASE'); ?> <a href="/component/user/login"><?php echo JText::_('K_LOG_IN'); ?></a> <?php echo JText::_('K_OR'); ?> <a href="/component/user/register"><?php echo JText::_('K_REGISTER'); ?></a>. <a href="/component/user/reset"><?php echo JText::_('K_LOST_PASSWORD'); ?></a></p>												
		</div>								
											
		<div class="corner1">
			<div class="corner2">
				<div class="corner3">
					<div class="corner4">
						<table class="announcements" summary="<?php echo JText::_('K_ANNOUNCEMENTS_TABLE_SUMMARY'); ?>">
							<thead>
								<tr>
									<h3>Kunena 1.0.11 & 1.5.4 Released</h3>	
								</tr>
							</thead>
							<tbody>
								<tr class="row_odd">
									<td class="col2">
										<div class="announce_create">07/11/2009 00:45</div>
										<div class="announce_summary">The Kunena Team announces the immediate availability of Kunena 1.5.4 and 1.0.11. These are important
														security releases and users are urged to update immediately. These releases also fix a number of other minor and major issues.
														<a href="/forum/announcement/read/id-8"><?php echo JText::_('K_READ_MORE'); ?></a></div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
