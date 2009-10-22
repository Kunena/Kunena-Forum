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

<?php if (isset($this->statistics)): ?>
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
					<p><?php echo JText::_('K_TOTAL_USERS'); ?>: <span><?php echo JHtml::_('klink.userlist', 'atag', $this->escape($this->statistics['users']->users), $this->escape($this->statistics['users']->users));?></span> | <?php echo JText::_('K_NEWEST_MEMBER'); ?>: <span><?php echo JHtml::_('klink.user', 'atag', $this->statistics['users']->last_userid, $this->escape($this->statistics['users']->last_username), $this->escape($this->statistics['users']->last_username)); ?></span></p>
					<p><?php echo JText::_('K_TOTAL_MESSAGES'); ?>: <span><?php echo intval($this->statistics['forum']->messages); ?></span> | <?php echo JText::_('K_TOTAL_SUBJECTS'); ?>: <span><?php echo intval($this->statistics['forum']->threads); ?></span> | <?php echo JText::_('K_TOTAL_SECTIONS'); ?>: <span><?php echo intval($this->statistics['forum']->sections); ?></span> | <?php echo JText::_('K_TOTAL_CATEGORIES'); ?>: <span><?php echo intval($this->statistics['forum']->categories); ?></span></p>
					<p><?php echo JText::_('K_TODAY_OPEN'); ?>: <span><?php echo intval($this->statistics['recent']->todayopen); ?></span> | <?php echo JText::_('K_YESTERDAY_OPEN'); ?>: <span><?php echo intval($this->statistics['recent']->yesterdayopen); ?></span> | <?php echo JText::_('K_TODAY_TOTAL_ANSWERED'); ?>: <span><?php echo intval($this->statistics['recent']->todayanswered); ?></span> | <?php echo JText::_('K_YESTERDAY_TOTAL_ANSWERED'); ?>: <span><?php echo intval($this->statistics['recent']->yesterdayanswered); ?></span></p>
					<p><?php echo JText::_('K_NUMBER_BIRTHDAY_TODAY'); ?>: <span><?php echo intval($this->statistics['birthdaynb']);  ?></span> | <?php echo JText::_('K_USERS_NAMES_BIRTHDAY'); ?>: <span><?php echo $this->statistics['birthdayname']; ?></span></p>
					<p><?php echo JHtml::_('klink.recent', 'atag', '<span>'.JText::_('K_VIEW_RECENT_POSTS').'</span>', JText::_('K_VIEW_RECENT_POSTS')); ?> | <?php echo JHtml::_('klink.statistics', 'atag', '<span>'.JText::_('K_MORE_ABOUT_STATS').'</span>', JText::_('K_MORE_ABOUT_STATS')); ?> | <?php echo JHtml::_('klink.userlist', 'atag', JText::_('K_USER_LIST'), JText::_('K_USER_LIST'));?></p>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<?php endif; ?>

<?php if (isset($this->whoisonline)): ?>
<!-- WHOIS ONLINE -->
<table class="forum_body whoisonline">
	<thead>
		<tr>
			<th>
				<h3><a class="fb_title fbl" href="">
					Online <b>0</b> Members and <b>1</b> Guest</a>
				</h3>
<?php //				<img id = "BoxSwitch_whoisonline__whoisonline_tbody" class = "hideshow" src = "http://kunena15/components/com_kunena/template/default_ex/images/english/shrink.gif" alt = ""/> ?>
			</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td class = "fcol">
				&nbsp;<!-- groups -->
			</td>
		</tr>
	</tbody>
</table>
<!-- /WHOIS ONLINE -->
<?php endif; ?>

<div id="kunena_footer">
<div class="credits">
	<p><?php echo JHtml::_('klink.teamCredits', 'atag', JText::_('K_POWERED_BY')); ?> <?php echo JHtml::_('klink.credits', 'atag', JText::_('Visit Kunena.com'), 'Kunena'); ?></p>
</div>
<div class="rsslink" >
	<a href="/forum/fb_rss?no_html=1" title="" target="_blank">
	<img src="<?php echo KURL_COMPONENT_MEDIA ?>images/rss.png" alt="<?php echo JText::_('K_SUBSCRIBE'); ?>" title="<?php echo JText::_('K_SUBSCRIBE'); ?>" /></a>
</div>
</div>