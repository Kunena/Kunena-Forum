<?php
/**
 * @version		$Id: default.php 1024 2009-08-19 06:18:15Z fxstein $
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
<?php echo $this->loadCommonTemplate('pathway'); ?>

<!-- B: Cat list Top -->
<div class="top_info_box">
	<span class="fb_list_markallcatsread"></span>
<?php echo $this->loadCommonTemplate('forumcat'); ?>
</div>
<!-- F: Cat list Top -->

<?php foreach ($this->categories['root'] as $section): ?>
<!-- B: List Cat -->
<table class="forum_body">
	<thead>
		<tr>
			<th colspan="5">
				<h1><?php echo JHtml::_('klink.categories', 'atag', $section->id, $this->escape($section->name), $this->escape($section->name)); ?></h1>
				<div><?php echo $section->description; ?></div>
<?php //				<img id="BoxSwitch__catid_<?php echo $section->id; ?/>" class="hideshow" src="http://kunena15/components/com_kunena/template/default_ex/images/english/shrink.gif" alt="" /> ?>
			</th>
		</tr>
	</thead>
	<tbody id="catid_<?php echo $section->id; ?>">
<?php 
if (empty($this->categories[$section->id])):
?>
		<tr class="row_odd" id="fb_cat0">
			<td class="fcol" colspan="4">
				<b>There are no forums in this section.</b>
			</td>
		</tr>
<?php 	
else:
?>
		<tr>
			<th class="lcol col_emoticon">&nbsp;</th>
			<th class="mcol col_content">Forum</th>
			<th class="mcol col_topics">Topics</th>
			<th class="mcol col_posts">Posts</th>
			<th class="lcol col_last">Last Post</th>
		</tr>
<?php 	
foreach ($this->categories[$section->id] as $current=>$category): 
?>
		<tr class="<?php echo ($current%2) ? 'row_even' : 'row_odd'; ?>" id="fb_cat<?php echo $category->id; ?>">
			<td class="lcol col_emoticon">
				<?php echo JHtml::_('klink.categories', 'atag', $category->id, '<img src="http://kunena15/components/com_kunena/template/default_ex/images/english/icons/folder_nonew.gif" border="0" alt="No New Posts" title="No New Posts" />', 'No New Posts'); ?>
			</td>
			<td class="mcol col_content">
				<h2>
					<?php echo JHtml::_('klink.categories', 'atag', $category->id, $this->escape($category->name), $this->escape($category->name)); ?>
				</h2>
				<div class = "fb_thead-desc">
					<?php echo $category->description; ?>
				</div>
			</td>
			<td class="mcol col_topics"><?php echo $category->numTopics; ?></td>
			<td class="mcol col_posts"><?php echo $category->numPosts; ?></td>
			<td class="rcol col_last">
<?php if ($category->id_last_msg): ?>
				<div class="fb_latest-subject">
					<?php echo JHtml::_('klink.thread', 'atag', $category->thread, $this->escape($category->subject), $this->escape($category->subject)); ?>
				</div>
				<div class="fb_latest-subject-by">
					by 
					<?php echo JHtml::_('klink.user', 'atag', $category->userid, $this->escape($category->username), 'Show User: '.$this->escape($category->username)); ?> | 
					<?php echo JHTML::_('date', $category->time_last_msg); ?> <a href="/index.php/kunena/2-welcome-mat/1-welcome-to-kunena#1" title="" rel="follow"><img src="http://kunena15/components/com_kunena/template/default_ex/images/english/icons/tlatest.gif" border="0" alt="Show most recent message" title="Show most recent message"/></a>
				</div>
<?php else: ?>
				<div>No posts</div>
<?php endif; ?>
			</td>
		</tr>
<?php
endforeach; 
endif;
?>
	</tbody>
</table>
<!-- F: List Cat -->
<?php endforeach; ?>

<!-- B: Cat list Bottom -->
<div class="bottom_info_box">
	<span class="fb_list_markallcatsread"></span>
<?php echo $this->loadCommonTemplate('forumcat'); ?>
</div>
<!-- F: Cat list Bottom -->

<?php echo $this->loadCommonTemplate('footer'); ?>
	</div>