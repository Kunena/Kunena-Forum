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
<div class="fb_list_top">
	<span class="fb_list_markallcatsread"></span>
<?php echo $this->loadCommonTemplate('forumcat'); ?>
</div>
<!-- F: Cat list Top -->


<!-- B: List Cat -->
<table class="fb_blocktable">
	<thead>
		<tr>
			<th colspan="5">
				<h1><a class="fb_title" href="/index.php/kunena/1-main-forum" title="" rel="follow">Main Forum</a></h1>
				<div>This is the main forum category. As a level one category it serves as a container for individual boards or forums. It is also referred to as a level 1 category and is a must have for any Kunena Forum setup.</div>
				<img id="BoxSwitch_1__catid_1" class="hideshow" src="http://kunena15/components/com_kunena/template/default_ex/images/english/shrink.gif" alt="" />
			</th>
		</tr>
	</thead>
	<tbody id="catid_1">
		<tr class = "fb_sth">
			<th class="th-1 fb_sectiontableheader">&nbsp;</th>
			<th class="th-2 fb_sectiontableheader">Forum</th>
			<th class="th-3 fb_sectiontableheader">Topics</th>
			<th class="th-4 fb_sectiontableheader">Replies</th>
			<th class="th-5 fb_sectiontableheader">Last Post</th>
		</tr>
		
		<tr class="fb_sectiontableentry2" id="fb_cat2">
			<td class="td-1">
				<a href="/index.php/kunena/2-welcome-mat" title="" rel="follow"><img src="http://kunena15/components/com_kunena/template/default_ex/images/english/icons/folder_nonew.gif" border="0" alt="No New Posts" title="No New Posts" /></a>
			</td>
			<td class="td-2">
				<h2><a href="/index.php/kunena/2-welcome-mat" title="" rel="follow">Welcome Mat</a></h2>
				<div class = "fb_thead-desc">
					We encourage new members to post a short introduction of themselves in this forum category. Get to know each other and share you common interests.
				</div>
			</td>
			<td class="td-3">1</td>
			<td class="td-4">0</td>
			<td class="td-5">
				<div class="fb_latest-subject">
					<a href="/index.php/kunena/2-welcome-mat/1-welcome-to-kunena#1" title="" rel="follow">Welcome to Kunena!</a>
				</div>
				<div class="fb_latest-subject-by">
					by <a href="/index.php/kunena/fbprofile/userid-62" title="" rel="nofollow">Kunena</a> | <b>Yesterday</b>&#32;08:12 <a href="/index.php/kunena/2-welcome-mat/1-welcome-to-kunena#1" title="" rel="follow"><img src="http://kunena15/components/com_kunena/template/default_ex/images/english/icons/tlatest.gif" border="0" alt="Show most recent message" title="Show most recent message"/></a>
				</div>
			</td>
		</tr>
		
		<tr class = "fb_sectiontableentry1" id="fb_cat3">
			<td class="td-1">
				<a href="/index.php/kunena/3-suggestion-box" title="" rel="follow"><img src="http://kunena15/components/com_kunena/template/default_ex/images/english/icons/folder_nonew.gif" border="0" alt="No New Posts" title="No New Posts" /></a>
			</td>
			<td class="td-2">
				<h2><a href="/index.php/kunena/3-suggestion-box" title="" rel="follow">Suggestion Box</a></h2>
				<div class="fb_thead-desc">
					Have some feedback and input to share?<br />
					Don&#039;t be shy and drop us a note. We want to hear from you and strive to make our site better and more user friendly for our guests and members a like.
				</div>
			</td>
			<td class="td-3">0</td>
			<td class="td-4">0</td>
			<td class="td-5">No Posts</td>
		</tr>
	</tbody>
</table>
<!-- F: List Cat -->

<!-- B: Cat list Bottom -->
<div>
	<span class="fb_list_markallcatsread"></span>
<?php echo $this->loadCommonTemplate('forumcat'); ?>
</div>
<!-- F: Cat list Bottom -->

<!-- WHOIS ONLINE -->
<table class="fb_whoisonline">
	<thead>
		<tr>
			<th>
				<div>
					<a class="fb_title fbl" href = "/index.php/kunena/who">
						Online <b>0</b> Members and <b>1</b> Guest</a>
				</div>
				<img id = "BoxSwitch_whoisonline__whoisonline_tbody" class = "hideshow" src = "http://kunena15/components/com_kunena/template/default_ex/images/english/shrink.gif" alt = ""/>
			</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td class = "td-1 fbm" align="left">
				<!-- groups -->
			</td>
		</tr>
	</tbody>
</table>
<!-- /WHOIS ONLINE -->


<?php echo $this->loadCommonTemplate('footer'); ?>
	</div>