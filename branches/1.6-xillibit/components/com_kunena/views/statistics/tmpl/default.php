<?php
/**
 * @version		$Id: default.php 1084 2009-09-25 15:20:43Z mahagr $
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;
JHtml::stylesheet('default.css', 'components/com_kunena/media/css/');
?>
<div id="kunena">
<?php echo $this->loadCommonTemplate('header'); ?>
<?php if (isset($this->summary)): ?>	
<table class="forum_body stats_box">
	<thead>
		<tr>
			<th>
				<h3><a href="/forum/stats" title="<?php echo JText::_('K_KUNENA_FORUM_STATS'); ?>"><?php echo JText::_('K_KUNENA_FORUM_STATS'); ?></a></h3>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="fcol">
				<div class="stats_items">	
					<p><?php echo JText::_('K_TOTAL_USERS'); ?>: <span><a href="/component/community/search/browse"><?php echo $this->escape($this->summary['users']->users); ?></a></span> | <?php echo JText::_('K_NEWEST_MEMBER'); ?>: <span><a href="/community/profile?userid=<?php echo $this->escape($this->summary['users']->last_userid); ?>" title="<?php echo $this->escape($this->summary['users']->last_username); ?>"><?php echo $this->escape($this->summary['users']->last_username); ?></a></span></p>
					<p><?php echo JText::_('K_TOTAL_MESSAGES'); ?>: <span><?php echo intval($this->summary['forum']->messages); ?></span> | <?php echo JText::_('K_TOTAL_SUBJECTS'); ?>: <span><?php echo intval($this->summary['forum']->threads); ?></span> | <?php echo JText::_('K_TOTAL_SECTIONS'); ?>: <span><?php echo intval($this->summary['forum']->sections); ?></span> | <?php echo JText::_('K_TOTAL_CATEGORIES'); ?>: <span><?php echo intval($this->summary['forum']->categories); ?></span></p>
					<p><?php echo JText::_('K_TODAY_OPEN'); ?>: <span><?php echo intval($this->summary['recent']->todayopen); ?></span> | <?php echo JText::_('K_YESTERDAY_OPEN'); ?>: <span><?php echo intval($this->summary['recent']->yesterdayopen); ?></span> | <?php echo JText::_('K_TODAY_TOTAL_ANSWERED'); ?>: <span><?php echo intval($this->summary['recent']->todayanswered); ?></span> | <?php echo JText::_('K_YESTERDAY_TOTAL_ANSWERED'); ?>: <span><?php echo intval($this->summary['recent']->yesterdayanswered); ?></span></p>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<?php endif; ?>
<?php echo $this->loadCommonTemplate('footer'); ?>
</div>