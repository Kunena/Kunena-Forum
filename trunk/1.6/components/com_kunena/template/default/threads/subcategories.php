<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 * Based on FireBoard Component
 * @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.bestofjoomla.com
 *
 * Based on Joomlaboard Component
 * @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @author TSMF & Jan de Graaff
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();

global $kunena_icons;
$kunena_emoticons = smile::getEmoticons ( 0 );
?>
<!-- B: List Cat -->
<div class="k_bt_cvr1">
<div class="k_bt_cvr2">
<div class="k_bt_cvr3">
<div class="k_bt_cvr4">
<div class="k_bt_cvr5">
<table
	class="kblocktable<?php
	echo isset ( $this->objCatInfo->class_sfx ) ? ' fb_blocktable' . $this->objCatInfo->class_sfx : '';
	?>"
	width="100%" id="kcat<?php
	echo $this->objCatInfo->id;
	?>" border="0"
	cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th colspan="5" align="left">
			<div class="ktitle_cover km"><?php
			echo CKunenaLink::GetCategoryLink ( 'showcat', $this->objCatInfo->id, stripslashes ( $this->objCatInfo->name ), $rel = 'follow', $class = 'fb_title fbl' );
			?>

	<?php
	echo $this->forumdesc;
	?>
			</div>

			<div class="fltrt"><span id="subcat_list"><a class="ktoggler close"
				rel="catid_<?php
				echo $this->objCatInfo->id;
				?>"></a></span></div>

			<!-- <img
				id="BoxSwitch_<?php
				echo $this->objCatInfo->id;
				?>__catid_<?php
				echo $this->objCatInfo->id;
				?>"
				class="hideshow"
				src="<?php
				echo KUNENA_URLIMAGESPATH . 'shrink.gif';
				?>" alt="" /> --></th>
		</tr>
	</thead>

	<tbody id="catid_<?php
	echo $this->objCatInfo->id;
	?>">
		<tr class="ksth ks">
			<th class="th-1 ksectiontableheader" width="1%">&nbsp;</th>
			<th class="th-2 ksectiontableheader" align="left"><?php
			echo _GEN_FORUM;
			?></th>
			<th class="th-3 ksectiontableheader" align="center" width="5%"><?php
			echo _GEN_TOPICS;
			?></th>

			<th class="th-4 ksectiontableheader" align="center" width="5%"><?php
			echo _GEN_REPLIES;
			?></th>

			<th class="th-5 ksectiontableheader" align="left" width="25%"><?php
			echo _GEN_LAST_POST;
			?></th>
		</tr>

		<?php
		$k = 0;
		foreach ( $this->subcats as $subcat ) {
			?>
		<tr
			class="k<?php
			echo $this->tabclass [$k ^= 1];
			echo isset ( $subcat->class_sfx ) ? ' k' . $this->tabclass [$k] . $subcat->class_sfx : '';
			?>"
			id="kcat<?
			echo $subcat->id;
			?>">
			<td class="td-1" align="center"><?php
			echo CKunenaLink::GetCategoryLink ( 'listcat', $subcat->id, $subcat->categoryicon, 'follow' );
			echo '<td class="td-2"  align="left"><div class="kthead-title kl">' . CKunenaLink::GetCategoryLink ( 'showcat', $subcat->id, stripslashes ( $subcat->name ), 'follow' );

			//new posts available
			if ($subcat->new && $this->my->id > 0) {
				echo '<sup><span class="newchar">&nbsp;(' . $subcat->new . ' ' . stripslashes ( $this->config->newchar ) . ")</span></sup>";
			}

			if ($subcat->locked) {
				echo isset ( $kunena_icons ['forumlocked'] ) ? '&nbsp;&nbsp;<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['forumlocked'] . '" border="0" alt="' . _GEN_LOCKED_FORUM . '" title="' . _GEN_LOCKED_FORUM . '"/>' : '&nbsp;&nbsp;<img src="' . KUNENA_URLEMOTIONSPATH . 'lock.gif"  border="0" alt="' . _GEN_LOCKED_FORUM . '">';
			}

			if ($subcat->review) {
				echo isset ( $kunena_icons ['forummoderated'] ) ? '&nbsp;&nbsp;<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['forummoderated'] . '" border="0" alt="' . _GEN_MODERATED . '" title="' . _GEN_MODERATED . '"/>' : '&nbsp;&nbsp;<img src="' . KUNENA_URLEMOTIONSPATH . 'review.gif" border="0"  alt="' . _GEN_MODERATED . '"/>';
			}

			echo '</div>';

			if ($subcat->forumdesc != "") {

				echo '<div class="kthead-desc  km">' . $subcat->forumdesc . ' </div>';
			}

			if (isset ( $this->childforums [$subcat->id] )) {
				if (count ( $this->childforums [$subcat->id] ) == 1) {
					echo '<div class="kthead-child  ks"><b>' . _KUNENA_CHILD_BOARD . ' </b>';
				} else {
					echo '<div class="kthead-child  ks"><b>' . _KUNENA_CHILD_BOARDS . ' </b>';
				}
				;

				foreach ( $this->childforums [$subcat->id] as $childforum ) {
					// start: parent read unread iconset
					if ($this->config->showchildcaticon) {
						//
						if ($this->config->shownew && $this->my->id != 0) {
							if ($childforum->new) {

								// Check Unread    Cat Images
								if (is_file ( KUNENA_ABSCATIMAGESPATH . $childforum->id . "_on_childsmall.gif" )) {
									echo "<img src=\"" . KUNENA_URLCATIMAGES . $childforum->id . "_on_childsmall.gif\" border=\"0\" class='forum-cat-image' alt=\" \" />";
								} else {
									echo isset ( $kunena_icons ['unreadforum'] ) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['unreadforum_childsmall'] . '" border="0" alt="' . _GEN_FORUM_NEWPOST . '" title="' . _GEN_FORUM_NEWPOST . '" />' : stripslashes ( $this->config->newchar );
								}
							} else {
								// Check Read Cat Images
								if (is_file ( KUNENA_ABSCATIMAGESPATH . $childforum->id . "_off_childsmall.gif" )) {
									echo "<img src=\"" . KUNENA_URLCATIMAGES . $childforum->id . "_off_childsmall.gif\" border=\"0\" class='forum-cat-image' alt=\" \" />";
								} else {
									echo isset ( $kunena_icons ['readforum'] ) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['readforum_childsmall'] . '" border="0" alt="' . _GEN_FORUM_NOTNEW . '" title="' . _GEN_FORUM_NOTNEW . '" />' : stripslashes ( $this->config->newchar );
								}
							}
						}

						// Not Login Cat Images
						else {
							if (is_file ( KUNENA_ABSCATIMAGESPATH . $childforum->id . "_notlogin_childsmall.gif" )) {
								echo "<img src=\"" . KUNENA_URLCATIMAGES . $childforum->id . "_notlogin_childsmall.gif\" border=\"0\" class='forum-cat-image' alt=\" \" />";
							} else {
								echo isset ( $kunena_icons ['notloginforum'] ) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['notloginforum_childsmall'] . '" border="0" alt="' . _GEN_FORUM_NOTNEW . '" title="' . _GEN_FORUM_NOTNEW . '" />' : stripslashes ( $this->config->newchar );
							}
						}
						//
					}
					// end: parent read unread iconset
					echo CKunenaLink::GetCategoryLink ( 'showcat', $childforum->id, stripslashes ( $childforum->name ), 'follow' ) . ' &nbsp;';
				}

				echo "</div>";
			}

			// moderator list
			if (! empty ( $this->modlist [$subcat->id] )) {
				echo '<div class="kthead-moderators  ks">' . _GEN_MODERATORS . ": ";

				$mod_cnt = 0;
				foreach ( $this->modlist [$subcat->id] as $mod ) {
					if ($mod_cnt)
						echo ', ';
					$mod_cnt ++;
					echo CKunenaLink::GetProfileLink ( $this->config, $mod->userid, ($this->config->username ? $mod->username : $mod->name) );
				}

				echo '</div>';
			}

			if (isset ( $this->pending [$subcat->id] )) {
				echo '<div class="ks"><font color="red">';
				echo CKunenaLink::GetCategoryReviewListLink ( $subcat->id, $this->pending [$subcat->id] . ' ' . _SHOWCAT_PENDING, 'nofollow' );
				echo '</font></div>';
			}
			?>
			</td>
			<td class="td-3 km" align="center"><?php
			echo $subcat->numTopics;
			?></td>
			<td class="td-4 km" align="center"><?php
			echo $subcat->numPosts;
			?></td>
			<?php
			if ($subcat->numTopics != 0) {
				?>

			<td class="td-5" align="left">
			<div class="klatest-subject km">
			<?php
				echo CKunenaLink::GetThreadLink ( 'view', $subcat->catid, $subcat->thread, kunena_htmlspecialchars ( stripslashes ( $subcat->subject ) ), kunena_htmlspecialchars ( stripslashes ( $subcat->subject ) ), $rel = 'nofollow' );
				?>
			</div>

			<div class="klatest-subject-by  ks">
			<?php
				echo _GEN_BY . ' ' . CKunenaLink::GetProfileLink ( $this->config, $subcat->userid, $subcat->username, $rel = 'nofollow' );
				?>

			| <span
				title="<?php
				echo CKunenaTimeformat::showDate ( $subcat->time_last_msg, 'config_post_dateformat_hover' );
				?>"><?php
				echo CKunenaTimeformat::showDate ( $subcat->time_last_msg, 'config_post_dateformat' );
				?></span> <?php
				echo CKunenaLink::GetThreadPageLink ( $this->config, 'view', $subcat->catid, $subcat->thread, $subcat->page, $this->config->messages_per_page, (isset ( $kunena_icons ['latestpost'] ) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['latestpost'] . '" border="0" alt="' . _SHOW_LAST . '" title="' . _SHOW_LAST . '" />' : '  <img src="' . KUNENA_URLEMOTIONSPATH . 'icon_newest_reply.gif" border="0"   alt="' . _SHOW_LAST . '" />'), $subcat->msgid, $rel = 'nofollow' );
				?>
			</div>
			</td>

		</tr>

		<?php
			} else {
				echo ' <td class="td-5"  align="left">' . _NO_POSTS . '</td></tr>';
			}
		}
		?>
	</tbody>
</table>
</div>
</div>
</div>
</div>
</div>
<!-- F: List Cat -->
