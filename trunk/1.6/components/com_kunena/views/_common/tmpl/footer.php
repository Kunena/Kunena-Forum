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
		
		
		<div class="corner1">
			<div class="corner2">
				<div class="corner3">
					<div class="corner4">
						<div class="stats_box">
								<h3><a href="/forum/stats" title="<?php echo JText::_('K_KUNENA_FORUM_STATS'); ?>"><?php echo JText::_('K_KUNENA_FORUM_STATS'); ?></a></h3>
								<div class="stats_items">	
									<p><?php echo JText::_('K_TOTAL_USERS'); ?>: <span><a href="/component/community/search/browse">6545</a></span> <?php echo JText::_('K_NEWEST_MEMBER'); ?>: <span><a href="/community/profile?userid=6611" title="tennshadow">tennshadow</a></span></p>
									<p><?php echo JText::_('K_TOTAL_MESSAGES'); ?>: <span>26093</span> <?php echo JText::_('K_TOTAL_SUBJECTS'); ?>: <span>4466</span> <?php echo JText::_('K_TOTAL_SECTIONS'); ?>: <span>8</span> <?php echo JText::_('K_TOTAL_CATEGORIES'); ?>: <span>109</span></p>
									<p><?php echo JText::_('K_TODAY_OPEN'); ?>: <span>13</span> Yesterday Open: <span>22</span> <?php echo JText::_('K_TODAY_TOTAL_ANSWERED'); ?>: <span>15</span> <?php echo JText::_('K_YESTERDAY_TOTAL_ANSWERED'); ?>: <span>47</span></p>
									<p><a href="/forum/latest"><?php echo JText::_('K_VIEW_RECENT_POSTS'); ?></a> <a href="/forum/stats"><?php echo JText::_('K_MORE_ABOUT_STATS'); ?></a> <a href="/component/community/search/browse"><?php echo JText::_('K_USER_LIST'); ?></a></p>
								</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="credits">
			<p><?php echo JText::_('K_POWERED_BY'); ?> <a href="http://www.kunena.com" title="Visit Kunena.com" target="_blank">Kunena</a></p>
		</div>
</div>
