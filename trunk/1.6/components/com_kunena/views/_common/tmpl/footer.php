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
									<p><?php echo JText::_('K_TOTAL_USERS'); ?>: <span><a href="/component/community/search/browse"><?php echo $this->escape($this->statistics['users']->users); ?></a></span> | <?php echo JText::_('K_NEWEST_MEMBER'); ?>: <span><a href="/community/profile?userid=<?php echo $this->escape($this->statistics['users']->last_userid); ?>" title="<?php echo $this->escape($this->statistics['users']->last_username); ?>"><?php echo $this->escape($this->statistics['users']->last_username); ?></a></span></p>
									<p><?php echo JText::_('K_TOTAL_MESSAGES'); ?>: <span><?php echo intval($this->statistics['forum']->messages); ?></span> | <?php echo JText::_('K_TOTAL_SUBJECTS'); ?>: <span><?php echo intval($this->statistics['forum']->threads); ?></span> | <?php echo JText::_('K_TOTAL_SECTIONS'); ?>: <span><?php echo intval($this->statistics['forum']->sections); ?></span> | <?php echo JText::_('K_TOTAL_CATEGORIES'); ?>: <span><?php echo intval($this->statistics['forum']->categories); ?></span></p>
									<p><?php echo JText::_('K_TODAY_OPEN'); ?>: <span><?php echo intval($this->statistics['recent']->todayopen); ?></span> | <?php echo JText::_('K_YESTERDAY_OPEN'); ?>: <span><?php echo intval($this->statistics['recent']->yesterdayopen); ?></span> | <?php echo JText::_('K_TODAY_TOTAL_ANSWERED'); ?>: <span><?php echo intval($this->statistics['recent']->todayanswered); ?></span> | <?php echo JText::_('K_YESTERDAY_TOTAL_ANSWERED'); ?>: <span><?php echo intval($this->statistics['recent']->yesterdayanswered); ?></span></p>
									<p><a href="/forum/latest"><?php echo JText::_('K_VIEW_RECENT_POSTS'); ?></a> | <a href="/forum/stats"><?php echo JText::_('K_MORE_ABOUT_STATS'); ?></a> | <a href="/component/community/search/browse"><?php echo JText::_('K_USER_LIST'); ?></a></p>
								</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="credits">
			<p><?php echo JText::_('K_POWERED_BY'); ?> <a href="http://www.kunena.com" title="Visit Kunena.com" target="_blank">Kunena</a></p>
		</div>
		<div class="rsslink" >
				<a href="/forum/fb_rss?no_html=1" title="" target="_blank">
				<img src="components/com_kunena/media/images/icons/rss.gif" alt="<?php echo JText::_('K_SUBSCRIBE'); ?>" title="<?php echo JText::_('K_SUBSCRIBE'); ?>" /></a>
		</div>