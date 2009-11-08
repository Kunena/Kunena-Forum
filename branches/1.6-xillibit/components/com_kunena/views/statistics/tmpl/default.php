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
				<h3><?php echo JText::_('K_KUNENA_FORUM_STATS'); ?></h3>
			</th>
		</tr>
	</thead>
	<tbody>
	<tr>
			<th class="lcol col_last" colspan="2">
				<?php echo JText::_('K_KUNENA_FORUM_STATS_GENERAL'); ?>
			</th>
		</tr>
		<tr>
			<td class="fcol">
				<div class="stats_items">
					<p><?php echo JText::_('K_TOTAL_USERS'); ?>: <span><?php echo JHtml::_('klink.userlist', 'atag', $this->escape($this->summary['users']->users), $this->escape($this->summary['users']->users));?></span> | <?php echo JText::_('K_NEWEST_MEMBER'); ?>: <span><?php echo JHtml::_('klink.user', 'atag', $this->summary['users']->last_userid, $this->escape($this->summary['users']->last_username), $this->escape($this->summary['users']->last_username)); ?></span></p>
					<p><?php echo JText::_('K_TOTAL_MESSAGES'); ?>: <span><?php echo intval($this->summary['forum']->messages); ?></span> | <?php echo JText::_('K_TOTAL_SUBJECTS'); ?>: <span><?php echo intval($this->summary['forum']->threads); ?></span> | <?php echo JText::_('K_TOTAL_SECTIONS'); ?>: <span><?php echo intval($this->summary['forum']->sections); ?></span> | <?php echo JText::_('K_TOTAL_CATEGORIES'); ?>: <span><?php echo intval($this->summary['forum']->categories); ?></span></p>
					<p><?php echo JText::_('K_TODAY_OPEN'); ?>: <span><?php echo intval($this->summary['recent']->todayopen); ?></span> | <?php echo JText::_('K_YESTERDAY_OPEN'); ?>: <span><?php echo intval($this->summary['recent']->yesterdayopen); ?></span> | <?php echo JText::_('K_TODAY_TOTAL_ANSWERED'); ?>: <span><?php echo intval($this->summary['recent']->todayanswered); ?></span> | <?php echo JText::_('K_YESTERDAY_TOTAL_ANSWERED'); ?>: <span><?php echo intval($this->summary['recent']->yesterdayanswered); ?></span></p>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<table class="forum_body stats_box">
	<thead>
		<tr>
			<th>
				<h3><?php echo JText::_('K_KUNENA_FORUM_STATS_POPULAR_FIVE_THREADS'); ?></h3>
			</th>
		</tr>
	</thead>
	<tbody>
	<tr>			
			<th class="mcol col_content">Forum</th>
			<th class="lcol col_emoticon">&nbsp;</th>	
      <th class="mcol col_topics">Hits</th>		
		</tr>							
					<?php $i="0";
           foreach($this->summary['popularthreads'] as $row) 
          { echo "<tr><td class=\"fcol\">
					".JHtml::_('klink.thread', 'atag', $row->thread, $row->subject,$row->subject)."
			</td>	<td class=\"fcol\">";
					 if($i == "0") {
              echo "<img src = \"".KURL_COMPONENT_MEDIA . "images/bar.gif\" alt = \"\" height = \"10\" width = \"100\"/>";
              $maxvalue = $row->hits;
            } else {
              echo "<img src = \"".KURL_COMPONENT_MEDIA . "images/bar.gif\" alt = \"\" height = \"10\" width = \"".round(($row->hits * 100) / $maxvalue)."\"/>";
            }
            $i++;                                   
			echo  "</td><td class=\"fcol\">
					".$row->hits."
			</td></tr>"; }   ?>
			</tbody>
</table>
<table class="forum_body stats_box">
	<thead>
		<tr>
			<th>
				<h3><?php echo JText::_('K_KUNENA_FORUM_STATS_POPULAR_FIVE_USERS_MES'); ?></h3>
			</th>
		</tr>
	</thead>
	<tbody>
	<tr>			
			<th class="mcol col_content">Username</th>
			<th class="lcol col_emoticon">&nbsp;</th>	
      <th class="mcol col_topics">Posts</th>		
		</tr>		
			<?php $i="0"; foreach($this->summary['popularusers'] as $row) 
          { echo "<tr><td class=\"fcol\">
					".JHtml::_('klink.user', 'atag', $row->userid, $row->username, $row->username)."
			</td><td class=\"fcol\">";
					if($i == "0") {
              echo "<img src = \"".KURL_COMPONENT_MEDIA . "images/bar.gif\" alt = \"\" height = \"10\" width = \"100\"/>";
              $maxvalue = $row->posts;
            } else {
              echo "<img src = \"".KURL_COMPONENT_MEDIA . "images/bar.gif\" alt = \"\" height = \"10\" width = \"".round(($row->posts * 100) / $maxvalue)."\"/>";
            }
            $i++; 
			echo "</td><td class=\"fcol\">
					".$row->posts."
			</td>	</tr>"; }   ?>	
	</tbody>
</table>
<table class="forum_body stats_box">
	<thead>
		<tr>
			<th>
				<h3><?php echo JText::_('K_KUNENA_FORUM_STATS_POPULAR_IVE_USERS_PROF'); ?></h3>
			</th>
		</tr>
	</thead>
	<tbody>
	<tr>			
			<th class="mcol col_content">Username</th>
			<th class="lcol col_emoticon">&nbsp;</th>	
      <th class="mcol col_topics">Hits</th>		
		</tr>		
					<?php foreach($this->summary['popularuserprofile'] as $row) 
          { echo "<tr><td class=\"fcol\">
					".JHtml::_('klink.user', 'atag', $row->userid, $row->username, $row->username)."
			</td><td class=\"fcol\">";
					if($i == "0") {
              echo "<img src = \"".KURL_COMPONENT_MEDIA . "images/bar.gif\" alt = \"\" height = \"10\" width = \"100\"/>";
              $maxvalue = $row->uhits;
            } else {
              echo "<img src = \"".KURL_COMPONENT_MEDIA . "images/bar.gif\" alt = \"\" height = \"10\" width = \"".round(($row->uhits * 100) / $maxvalue)."\"/>";
            }
            $i++; 
			echo "</td><td class=\"fcol\">
					".$row->uhits."
			</td>	</tr>"; }   ?>				
	</tbody>
</table>
<?php endif; ?>
<?php echo $this->loadCommonTemplate('footer'); ?>
</div>