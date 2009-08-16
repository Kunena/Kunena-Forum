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
		<div class="bottom_info_box">
			<div class="pagination_box">
				<?php echo JText::_('K_PAGE'); ?>: <span>1</span>
				<a href="/forum/latest/page-2/sel-720" title="<?php echo JText::_('K_PAGE'); ?> 2">2</a>
				<a href="/forum/latest/page-3/sel-720" title="<?php echo JText::_('K_PAGE'); ?> 3">3</a>
				<a href="/forum/latest/page-4/sel-720" title="<?php echo JText::_('K_PAGE'); ?> 4">4</a>...
				<a href="/forum/latest/page-22/sel-720" title="<?php echo JText::_('K_PAGE'); ?> 22">22</a>
			</div>
			<div class="discussions"><span><?php echo $this->total; ?></span> <?php echo JText::_('K_DISCUSSIONS'); ?> <p><a href="/forum/fb_rss?no_html=1" title="" target="_blank"><img class="rsslink" src="images/emoticons/rss.gif" alt="<?php echo JText::_('K_SUBSCRIBE'); ?>" title="<?php echo JText::_('K_SUBSCRIBE'); ?>" /></a></p></div>
		</div>