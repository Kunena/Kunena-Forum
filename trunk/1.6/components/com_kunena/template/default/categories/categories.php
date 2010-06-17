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
?>
<?php
	foreach ( $this->categories [0] as $cat ) {
		?>
<!-- B: List Cat -->
<div class="k-bt-cvr1" id="kblock<?php
		echo $cat->id;
		?>">
<div class="k-bt-cvr2">
<div class="k-bt-cvr3">
<div class="k-bt-cvr4">
<div class="k-bt-cvr5">
<table
	class="kblocktable<?php
		echo isset ( $cat->class_sfx ) ? ' kblocktable' . $cat->class_sfx : '';
		?>" id="kcat<?php
		echo $cat->id;
		?>" >
	<thead>
		<tr>
			<th colspan="5">
			<div class="ktitle-cover<?php
		echo isset ( $cat->class_sfx ) ? ' ktitle-cover' . $cat->class_sfx : '';
		?> km"><?php
		echo CKunenaLink::GetCategoryLink ( 'listcat', $cat->id, kunena_htmlspecialchars ( $cat->name ), 'follow', $class = 'ktitle kl' );

		if ($cat->description != "") {
			?>
			<div class="ktitle-desc km"><?php
			echo KunenaParser::parseBBCode ( $cat->description );
			?>
			</div>
			<?php
		}
		?></div>

			<div class="fltrt"><span id="cat_list"><a class="ktoggler close"
				rel="catid_<?php
		echo $cat->id;
		?>"></a></span></div>
			</th>
		</tr>
	</thead>
	<tbody id="catid_<?php
		echo $cat->id;
		?>">

		<?php
		if (empty ( $this->categories [$cat->id] )) {
			echo '' . JText::_('COM_KUNENA_GEN_NOFORUMS') . '';
		} else {
			$k = 0;
			foreach ( $this->categories [$cat->id] as $subcat ) {
				?>
		<tr
			class="k<?php
				echo $this->tabclass [$k ^= 1], isset ( $subcat->class_sfx ) ? ' k' . $this->tabclass [$k] . $subcat->class_sfx : '';
				?>"
			id="kcat<?php
				echo $subcat->id?>">
			<td class="td-1 kcenter" width="1%"><?php
				$tmpIcon = '';
				if ($this->config->shownew && $this->my->id != 0) {
					if ($subcat->new) {
						// Check Unread    Cat Images
						if (is_file ( KUNENA_ABSCATIMAGESPATH . $subcat->id . "_on.gif" )) {
							$tmpIcon = '<img src="' . KUNENA_URLCATIMAGES . $subcat->id . '_on.gif" border="0" class="kforum-cat-image"alt=" " />';
						} else {
							$tmpIcon = isset ( $kunena_icons ['unreadforum'] ) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['unreadforum'] . '" border="0" alt="' . JText::_('COM_KUNENA_GEN_FORUM_NEWPOST') . '" title="' . JText::_('COM_KUNENA_GEN_FORUM_NEWPOST') . '" />' : $this->config->newchar;
						}
					} else {
						// Check Read Cat Images
						if (is_file ( KUNENA_ABSCATIMAGESPATH . $subcat->id . "_off.gif" )) {
							$tmpIcon = '<img src="' . KUNENA_URLCATIMAGES . $subcat->id . '_off.gif" border="0" class="kforum-cat-image" alt=" " />';
						} else {
							$tmpIcon = isset ( $kunena_icons ['readforum'] ) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['readforum'] . '" border="0" alt="' . JText::_('COM_KUNENA_GEN_FORUM_NOTNEW') . '" title="' . JText::_('COM_KUNENA_GEN_FORUM_NOTNEW') . '" />' : $this->config->newchar;
						}
					}
				} else {
					// Not Login Cat Images
					if (is_file ( KUNENA_ABSCATIMAGESPATH . $subcat->id . "_notlogin.gif" )) {
						$tmpIcon = '<img src="' . KUNENA_URLCATIMAGES . $subcat->id . '_notlogin.gif" border="0" class="kforum-cat-image" alt=" " />';
					} else {
						$tmpIcon = isset ( $kunena_icons ['notloginforum'] ) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['notloginforum'] . '" border="0" alt="' . JText::_('COM_KUNENA_GEN_FORUM_NOTNEW') . '" title="' . JText::_('COM_KUNENA_GEN_FORUM_NOTNEW') . '" />' : $this->config->newchar;
					}
				}
				echo CKunenaLink::GetCategoryLink ( 'showcat', $subcat->id, $tmpIcon );
				?>
			</td>

			<td class="td-2 kleft">
			<div class="kthead-title kl"><?php
				//new posts available
				echo CKunenaLink::GetCategoryLink ( 'showcat', $subcat->id, kunena_htmlspecialchars ( $subcat->name ) );

				if ($subcat->new && $this->my->id > 0) {
					echo '<sup class="knewchar">(' . $subcat->new . ' ' . $this->config->newchar . ")</sup>";
				}

				if ($subcat->locked) {
					echo isset ( $kunena_icons ['forumlocked'] ) ? '&nbsp;&nbsp;<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['forumlocked'] . '" border="0" alt="' . JText::_('COM_KUNENA_GEN_LOCKED_FORUM') . '" title="' . JText::_('COM_KUNENA_GEN_LOCKED_FORUM') . '"/>' : '&nbsp;&nbsp;<img src="' . KUNENA_URLEMOTIONSPATH . 'lock.gif"  border="0" alt="' . JText::_('COM_KUNENA_GEN_LOCKED_FORUM') . '">';
				}

				if ($subcat->review) {
					echo isset ( $kunena_icons ['forummoderated'] ) ? '&nbsp;&nbsp;<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['forummoderated'] . '" border="0" alt="' . JText::_('COM_KUNENA_GEN_MODERATED') . '" title="' . JText::_('COM_KUNENA_GEN_MODERATED') . '"/>' : '&nbsp;&nbsp;<img src="' . KUNENA_URLEMOTIONSPATH . 'review.gif" border="0"  alt="' . JText::_('COM_KUNENA_GEN_MODERATED') . '">';
				}
				?>
			</div>

			<?php
				if ($subcat->forumdesc != "") {
					?>

			<div class="kthead-desc km"><?php
					echo $subcat->forumdesc?>
			</div>

			<?php
				}

				// loop over subcategories to show them under
				if (! empty ( $this->childforums [$subcat->id] )) {
					?>

			<div class="kthead-child">

			<div class="kcc-table">
			<div class="kcc-childcat-title"><?php
					if (count ( $this->childforums [$subcat->id] ) == 1) {
						echo JText::_('COM_KUNENA_CHILD_BOARD');
					} else {
						echo JText::_('COM_KUNENA_CHILD_BOARDS');
					}
					?>:
			</div>
			<?php

					foreach ( $this->childforums [$subcat->id] as $childforum ) {
						echo "<div class=\"kcc-subcat km\">";

						//Begin: parent read unread iconset
						if ($this->config->showchildcaticon) {
							if ($this->config->shownew && $this->my->id != 0) {
								if ($childforum->new) {
									// Check Unread    Cat Images
									if (is_file ( KUNENA_ABSCATIMAGESPATH . $childforum->id . "_on_childsmall.gif" )) {
										echo "<img src=\"" . KUNENA_URLCATIMAGES . $childforum->id . "_on_childsmall.gif\" border=\"0\" class='kforum-cat-image' alt=\" \" />";
									} else {
										echo isset ( $kunena_icons ['unreadforum'] ) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['unreadforum_childsmall'] . '" border="0" alt="' . JText::_('COM_KUNENA_GEN_FORUM_NEWPOST') . '" title="' . JText::_('COM_KUNENA_GEN_FORUM_NEWPOST') . '" />' : $this->config->newchar;
									}
								} else {
									// Check Read Cat Images
									if (is_file ( KUNENA_ABSCATIMAGESPATH . $childforum->id . "_off_childsmall.gif" )) {
										echo "<img src=\"" . KUNENA_URLCATIMAGES . $childforum->id . "_off_childsmall.gif\" border=\"0\" class='kforum-cat-image' alt=\" \" />";
									} else {
										echo isset ( $kunena_icons ['readforum'] ) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['readforum_childsmall'] . '" border="0" alt="' . JText::_('COM_KUNENA_GEN_FORUM_NOTNEW') . '" title="' . JText::_('COM_KUNENA_GEN_FORUM_NOTNEW') . '" />' : $this->config->newchar;
									}
								}
							} // Not Login Cat Images
else {
								if (is_file ( KUNENA_ABSCATIMAGESPATH . $childforum->id . "_notlogin_childsmall.gif" )) {
									echo "<img src=\"" . KUNENA_URLCATIMAGES . $childforum->id . "_notlogin_childsmall.gif\" border=\"0\" class='kforum-cat-image' alt=\" \" />";
								} else {
									echo isset ( $kunena_icons ['notloginforum'] ) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['notloginforum_childsmall'] . '" border="0" alt="' . JText::_('COM_KUNENA_GEN_FORUM_NOTNEW') . '" title="' . JText::_('COM_KUNENA_GEN_FORUM_NOTNEW') . '" />' : $this->config->newchar;
								}
								?>

			<?php
							}
							//
						}
						// end: parent read unread iconset
						?>

			<?php
						echo CKunenaLink::GetCategoryLink ( 'showcat', $childforum->id, kunena_htmlspecialchars ( $childforum->name ), '','', KunenaParser::stripBBCode ( $childforum->description ) );
						echo '<span class="kchildcount ks">(' . $childforum->numTopics . "/" . $childforum->numPosts . ')</span>';
						echo "</div>";
					}
					?>
			</div>
			</div>

			<?php
				}

				// get the Moderator list for display
				if (! empty ( $this->modlist [$subcat->id] )) {
					echo '<div class="kthead-moderators ks">' . JText::_('COM_KUNENA_GEN_MODERATORS') . ": ";

					$mod_cnt = 0;
					foreach ( $this->modlist [$subcat->id] as $mod ) {
						if ($mod_cnt)
							echo ', ';
						$mod_cnt ++;
						echo CKunenaLink::GetProfileLink ( $mod->userid, ($this->config->username ? $mod->username : $mod->name) );
					}

					echo '</div>';
				}

				if (isset ( $this->pending [$subcat->id] )) {
					echo '<div class="ks kalert">';
					echo CKunenaLink::GetCategoryReviewListLink ( $subcat->id, $this->pending [$subcat->id] . ' ' . JText::_('COM_KUNENA_SHOWCAT_PENDING'), 'nofollow' );
					echo '</div>';
				}
				?>
			</td>

			<td class="td-3 km kcenter" width="5%"><!-- Number of Topics -->
			<span class="kcat-topics-number"><?php
				echo CKunenaTools::formatLargeNumber ( $subcat->numTopics );
				?>
			</span> <span class="kcat-topics"> <?php
				echo JText::_('COM_KUNENA_GEN_TOPICS');
				?> </span> <!-- /Number of Replies --></td>

			<td class="td-4 km kcenter" width="5%"><!-- Number of Topics -->
			<span class="kcat-replies-number"><?php
				echo CKunenaTools::formatLargeNumber ( $subcat->numPosts );
				?>
			</span> <span class="kcat-replies"> <?php
				echo JText::_('COM_KUNENA_GEN_REPLIES');
				?> </span> <!-- /Number of Replies --></td>

			<?php
				if ($subcat->numTopics != 0) {
					?>

			<td class="td-5 kleft" width="25%">
			<!-- Avatar --> <?php
			if ($this->config->avataroncat > 0) :
				$profile = KunenaFactory::getUser((int)$subcat->userid);
				$useravatar = $profile->getAvatarLink('klist-avatar', 'lastpost');
				if ($useravatar) :
				?>
				<span class="klatest-avatar"> <?php
				echo CKunenaLink::GetProfileLink ( $subcat->userid, $useravatar );
				?>
				</span> <?php
				endif;
			endif;
			?> <!-- /Avatar -->
			<div class="klatest-subject ks">
					<?php
					echo JText::_('COM_KUNENA_GEN_LAST_POST');
					?>: <?php
					echo CKunenaLink::GetThreadPageLink ( 'view', $subcat->catid, $subcat->thread, $subcat->page, $this->config->messages_per_page, kunena_htmlspecialchars ( $subcat->subject ), $subcat->id_last_msg );
					?>
			</div>

			<div class="klatest-subject-by ks">
			<?php
					echo JText::_('COM_KUNENA_BY') . ' ';
					echo CKunenaLink::GetProfileLink ( $subcat->userid, kunena_htmlspecialchars ( $subcat->mname ) );
					echo ' ';
					//echo JText::_('COM_KUNENA_GEN_ON');
					echo '<br /><span class="nowrap" title="' . CKunenaTimeformat::showDate ( $subcat->time_last_msg, 'config_post_dateformat_hover' ) . '">' . CKunenaTimeformat::showDate ( $subcat->time_last_msg, 'config_post_dateformat' ) . '</span>';

					// echo CKunenaLink::GetThreadPageLink ( 'view', $subcat->catid, $subcat->thread, $subcat->page, $this->config->messages_per_page, isset ( $kunena_icons ['latestpost'] ) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['latestpost'] . '" border="0" alt="' . JText::_('COM_KUNENA_SHOW_LAST') . '" title="' . JText::_('COM_KUNENA_SHOW_LAST') . '"/>' : '<img src="' . KUNENA_URLEMOTIONSPATH . 'icon_newest_reply.gif" border="0"  alt="' . JText::_('COM_KUNENA_SHOW_LAST') . '"/>', $subcat->id_last_msg );
					?>
			</div>
			</td>

			<?php
				} else {
					?>

			<td class="td-5 kcenter" width="25%"><?php
					echo JText::_('COM_KUNENA_NO_POSTS');
					?></td>

			<?php
				}
				?>
		</tr>
		<?php
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

<?php
	}

