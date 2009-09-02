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

        <script type="text/javascript">
        jQuery(function()
        {
            jQuery(".fb_qr_fire").click(function()
            {
                jQuery("#sc" + (jQuery(this).attr("id").split("__")[1])).toggle();
            });
            jQuery(".fb_qm_cncl_btn").click(function()
            {
                jQuery("#sc" + (jQuery(this).attr("id").split("__")[1])).toggle();
            });

        });
        </script>

<div class="fb_forum-headerdesc">
<b>Welcome to the Kunena forum!</b><br />
		<br />
		Tell us and our members who you are, what you like and why you became
		a member of this site.<br />
		We welcome all new members and hope to see you around a lot!<br />
</div>

<!-- B: List Actions -->
<div class="fb_list_actions">
<div class="fb_list_actions_goto">
<a name="forumtop" />
<a href="/index.php/kunena/2-welcome-mat/1-welcome-to-kunena#forumbottom" title="" rel="nofollow"><img src="http://kunena15/components/com_kunena/template/default_ex/images/english/icons/bottom_arrow.gif" border="0" alt="Go to bottom" title="Go to bottom" /></a>
</div>
<div class="fb_list_actions_forum"></div>
<div class="fb_list_pages_all"><span class="fb_pagination">Page:&#32;<strong>1</strong></span></div>
</div>
<!-- F: List Actions -->

<table class="fb_blocktable" id="fb_views" cellpadding="0"
	cellspacing="0" border="0" width="100%">
	<thead>
		<tr>
			<th align="left">
			<div class="fb_title_cover  fbm"><span class="fb_title fbl"><b>TOPIC:&#32;</b>
			Welcome to Kunena!</span></div>
			<!-- B: FORUM TOOLS --> <script type="text/javascript">
    jQuery(document).ready(function()
    {
        jQuery("#jrftsw").click(function()
        {
            jQuery(".forumtools_contentBox").slideToggle("fast");
            return false;
        });
    });
</script>

			<div id="fb_ft-cover">
			<div id="forumtools_control"><a href="#" id="jrftsw"
				class="forumtools">Forum Tools</a></div>

			<div class="forumtools_contentBox" id="box1">
			<div class="forumtools_content" id="subBox1">
			<ul>
				<li><a href="/index.php/kunena/2-welcome-mat/post/reply">Post New
				Topic</a></li>


				<li><a
					href="/index.php/kunena/2-welcome-mat/1-welcome-to-kunena/fb_pdf"
					rel="nofollow">Pdf</a></li>


				<li></li>


				<li><a href="/index.php/kunena/latest">Show latest posts</a></li>

				<li><a href="/index.php/kunena/rules">Rules</a></li>
				<li><a href="/index.php/kunena/faq">Help</a></li>
			</ul>
			</div>
			</div>
			</div>
			<!-- F: FORUM TOOLS --> <!-- Begin: Total Favorite -->
			<div class="fb_totalfavorite"></div>
			<!-- Finish: Total Favorite --></th>
		</tr>
	</thead>

	<tr>
		<td>

		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tbody>
				<tr class="fb_sth">

					<th colspan="2" class="view-th fb_sectiontableheader"><a name="1"></a>
					<a href="/index.php/kunena/2-welcome-mat/1-welcome-to-kunena#1"
						title="" rel="nofollow">#1</a></th>
				</tr>

				<tr>
					<!-- -->

					<td class="fb-msgview-right">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">

						<tr>
							<td align="left"><span class="msgtitle">Welcome to Kunena! </span>
							<span class="msgdate" title="2009/09/01 08:12">1 Day, 10 Hours
							ago</span></td>

							<td align="right"><span class="msgkarma"> <strong>Karma:</strong>
							0 </span></td>
						</tr>

						<tr>
							<td colspan="2" valign="top">
							<div class="msgtext"><span class="fbl"><b>Welcome to Kunena!</b></span><br />

							<br />
							Thank you for choosing Kunena for your community forum needs in
							Joomla.<br />
							<br />
							Kunena, translated from Swahili meaning &quot;to speak&quot;, is
							built by a team of open source professionals with the goal of
							providing a top-quality, tightly unified forum solution for
							Joomla. Kunena even supports social networking components like
							Community Builder and JomSocial.<br />
							<br />
							<br />
							<span class="fbl"><b>Additional Kunena Resources</b></span><br />
							<br />
							<b>Kunena Documentation:</b> <a href='http://www.kunena.com/docs'
								rel="nofollow" target="_blank">http://www.kunena.com/docs</a><br />

							<br />
							<b>Kunena Support Forum</b>: <a
								href='http://www.kunena.com/forum' rel="nofollow"
								target="_blank">http://www.kunena.com/forum</a><br />
							<br />
							<b>Kunena Downloads:</b> <a
								href='http://www.kunena.com/downloads' rel="nofollow"
								target="_blank">http://www.kunena.com/downloads</a><br />
							<br />
							<b>Kunena Blog:</b> <a href='http://www.kunena.com/blog'
								rel="nofollow" target="_blank">http://www.kunena.com/blog</a><br />
							<br />
							<b>Submit your feature ideas:</b> <a
								href='http://www.kunena.com/uservoice' rel="nofollow"
								target="_blank">http://www.kunena.com/uservoice</a><br />

							<br />
							<b>Follow Kunena on Twitter:</b> <a
								href='http://www.kunena.com/twitter' rel="nofollow"
								target="_blank">http://www.kunena.com/twitter</a><br />
							</div>



							</td>
						</tr>


					</table>
					</td>

					<td class="fb-msgview-left">
					<div class="fb-msgview-l-cover"><span class="view-username"> <a
						href="/index.php/kunena/fbprofile/userid-62" title=""
						rel="nofollow">admin</a> </span> <br />
					<a href="/index.php/kunena/fbprofile/userid-62" title=""
						rel="nofollow"><span class="fb_avatar"><img border="0"
						src="http://kunena15/images/fbfiles/avatars/nophoto.jpg" alt="" /></span></a>

					<div class="viewcover">Admin</div>

					<div class="viewcover"><img
						src="http://kunena15/components/com_kunena/template/default_ex/images/english/ranks/rankadmin.gif"
						alt="" /></div>


					<div class="viewcover"><strong>Posts: 0</strong></div>
					<div class="viewcover">
					<table border="0" cellspacing="0" cellpadding="0" align="center">
						<tr>
							<td width="64"><img
								src="http://kunena15/components/com_kunena/template/default_ex/images/english/graph/col9m.png"
								height="4" width="0" alt="graph"
								style="border: 0px solid #333333" /><img
								src="http://kunena15/components/com_kunena/template/default_ex/images/english/emoticons/graph.gif"
								height="4" width="60" alt="graph"
								style="border: 0px solid #333333" /></td>
						</tr>
					</table>
					</div>
					<img
						src="http://kunena15/components/com_kunena/template/default_ex/images/english/icons/offline.gif"
						border="0" alt="User Offline" /> <a
						href="/index.php/kunena/fbprofile/userid-62" title=""
						rel="nofollow"><img
						src="http://kunena15/components/com_kunena/template/default_ex/images/english/icons/profile.gif"
						alt="Click here to see the profile of this user" border="0"
						title="Click here to see the profile of this user" /></a> <br />

					</div>

					</td>
					<!-- -->

				</tr>

				<tr>
					<td class="fb-msgview-right-b">
					<div class="fb_message_editMarkUp_cover"></div>
					<div class="fb_message_buttons_cover">
					<div class="fb_message_buttons_row">The administrator has disabled
					public write access.</div>
					</div>

					</td>
					<td class="fb-msgview-left-b">&nbsp;</td>

				</tr>
			</tbody>

		</table>
		<!-- Begin: Message Module Positions --> <!-- Finish: Message Module Positions -->
		</td>
	</tr>

</table>


<!-- B: List Actions Bottom -->
<div class="fb_list_actions_bottom">
		<div class="fb_list_actions_goto">
		<a name="forumbottom" /> 
		<a
			href="/index.php/kunena/2-welcome-mat/1-welcome-to-kunena#forumtop"
			title="" rel="nofollow"><img
			src="http://kunena15/components/com_kunena/template/default_ex/images/english/icons/top_arrow.gif"
			border="0" alt="Go to top" title="Go to top" /></a>
		</div>
		<div class="fb_list_actions_forum"></div>
		<div class="fb_list_pages_all"><span class="fb_pagination">Page:&#32;<strong>1</strong></span></div>
</div>
<?php echo $this->loadCommonTemplate('pathway'); ?>
<!-- F: List Actions Bottom --> <!-- B: Category List Bottom -->

<table class="fb_list_bottom" border="0" cellspacing="0" cellpadding="0"
	width="100%">
	<tr>

		<td class="fb_list_moderators"><!-- Mod List --> <!-- /Mod List --></td>
		<td class="fb_list_categories">
		<form id="jumpto" name="jumpto" method="post" target="_self"
			action="/index.php/kunena"><span
			style="width: 100%; text-align: right;"> <input type="hidden"
			name="func" value="showcat" /> <select name="catid" id="catid"
			class="inputbox fbs" size="1"
			onchange="if(this.options[this.selectedIndex].value > 0){ this.form.submit() }">
			<option value="0">Board Categories&#32;</option>
			<option value="1">Main Forum</option>
			<option value="3">...&nbsp;Suggestion Box</option>
		</select> <input type="submit" name="Go" class="fb_button fbs"
			value="Go" /> </span></form>
		</td>
	</tr>
</table>
<!-- F: Category List Bottom -->

<?php
echo $this->loadCommonTemplate ( 'footer' );
?>
	</div>