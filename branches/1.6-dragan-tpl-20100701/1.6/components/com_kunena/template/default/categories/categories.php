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
foreach ( $this->categories [0] as $cat ) :
	$htmlClassBlockTable = !empty ( $cat->class_sfx ) ? ' kblocktable' . $cat->class_sfx : '';
	$htmlClassTitleCover = !empty ( $cat->class_sfx ) ? ' ktitle-cover' . $cat->class_sfx : '';
?>
<div class="kblock">
	<div class="kheader">
		<span class="ktoggler"><a class="ktoggler close"  rel="catid_<?php echo intval($cat->id) ?>"></a></span>
		<h2><span><?php echo CKunenaLink::GetCategoryLink ( 'listcat', intval($cat->id), $cat->name, 'follow', $class = '' ); ?></span></h2>
		<?php if (!empty($cat->description)) : ?>
		<div class="ktitle-desc km">
			<?php echo KunenaParser::parseBBCode ( $cat->description ); ?>
		</div>
		<?php endif; ?>
	</div>
	<div class="kcontainer" id="catid_<?php echo intval($cat->id) ?>">
		<div class="kbody">
<table class="kblocktable<?php echo $htmlClassBlockTable ?>" id="kcat<?php echo intval($cat->id) ?>">
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
			<td class="kcaticon kfirst kcenter" width="1%"><?php
				$tmpIcon = '';
				if ($this->config->shownew && $this->my->id != 0) {
					if ($subcat->new) {
						// Check Unread    Cat Images
						if (is_file ( KUNENA_ABSCATIMAGESPATH . $subcat->id . "_on.gif" )) {
							$tmpIcon = '<img src="' . KUNENA_URLCATIMAGES . $subcat->id . '_on.gif" border="0" class="kforum-cat-image"alt=" " />';
						} else {
							$tmpIcon = CKunenaTools::showIcon ( 'kunreadforum', JText::_('COM_KUNENA_GEN_FORUM_NEWPOST') );
						}
					} else {
						// Check Read Cat Images
						if (is_file ( KUNENA_ABSCATIMAGESPATH . $subcat->id . "_off.gif" )) {
							$tmpIcon = '<img src="' . KUNENA_URLCATIMAGES . $subcat->id . '_off.gif" border="0" class="kforum-cat-image" alt=" " />';
						} else {
							$tmpIcon = CKunenaTools::showIcon ( 'kreadforum', JText::_('COM_KUNENA_GEN_FORUM_NOTNEW') );
						}
					}
				} else {
					// Not Login Cat Images
					if (is_file ( KUNENA_ABSCATIMAGESPATH . $subcat->id . "_notlogin.gif" )) {
						$tmpIcon = '<img src="' . KUNENA_URLCATIMAGES . $subcat->id . '_notlogin.gif" border="0" class="kforum-cat-image" alt=" " />';
					} else {
						$tmpIcon = CKunenaTools::showIcon ( 'knotloginforum', JText::_('COM_KUNENA_GEN_FORUM_NOTNEW') );
					}
				}
				echo CKunenaLink::GetCategoryLink ( 'showcat', $subcat->id, $tmpIcon );
				?>
			</td>

			<td class="kcattitle kmiddle kleft">
			<div class="kthead-title kl"><?php
				//new posts available
				echo CKunenaLink::GetCategoryLink ( 'showcat', $subcat->id, kunena_htmlspecialchars ( $subcat->name ) );

				if ($subcat->new && $this->my->id > 0) {
					echo '<sup class="knewchar">(' . $subcat->new . ' ' . JText::_('COM_KUNENA_A_GEN_NEWCHAR') . ")</sup>";
				}

				if ($subcat->locked) {
					echo CKunenaTools::showIcon ( 'kforumlocked', JText::_('COM_KUNENA_GEN_LOCKED_FORUM') );
				}

				if ($subcat->review) {
					echo CKunenaTools::showIcon ( 'kforummoderated', JText::_('COM_KUNENA_GEN_MODERATED') );
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
										echo CKunenaTools::showIcon ( 'kunreadforum-sm', JText::_('COM_KUNENA_GEN_FORUM_NEWPOST') );
									}
								} else {
									// Check Read Cat Images
									if (is_file ( KUNENA_ABSCATIMAGESPATH . $childforum->id . "_off_childsmall.gif" )) {
										echo "<img src=\"" . KUNENA_URLCATIMAGES . $childforum->id . "_off_childsmall.gif\" border=\"0\" class='kforum-cat-image' alt=\" \" />";
									} else {
										echo CKunenaTools::showIcon ( 'kreadforum-sm', JText::_('COM_KUNENA_GEN_FORUM_NOTNEW') );
									}
								}
							} // Not Login Cat Images
else {
								if (is_file ( KUNENA_ABSCATIMAGESPATH . $childforum->id . "_notlogin_childsmall.gif" )) {
									echo "<img src=\"" . KUNENA_URLCATIMAGES . $childforum->id . "_notlogin_childsmall.gif\" border=\"0\" class='kforum-cat-image' alt=\" \" />";
								} else {
									echo CKunenaTools::showIcon ( 'knotloginforum-sm', JText::_('COM_KUNENA_GEN_FORUM_NOTNEW') );
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

			<td class="ktopics kmiddle km kcenter" width="5%"><!-- Number of Topics -->
			<span class="kcat-topics-number"><?php
				echo CKunenaTools::formatLargeNumber ( $subcat->numTopics );
				?>
			</span> <span class="kcat-topics"> <?php
				echo JText::_('COM_KUNENA_GEN_TOPICS');
				?> </span> <!-- /Number of Replies --></td>

			<td class="kreplies kmiddle km kcenter" width="5%"><!-- Number of Topics -->
			<span class="kcat-replies-number"><?php
				echo CKunenaTools::formatLargeNumber ( $subcat->numPosts );
				?>
			</span> <span class="kcat-replies"> <?php
				echo JText::_('COM_KUNENA_GEN_REPLIES');
				?> </span> <!-- /Number of Replies --></td>

			<?php
				if ($subcat->numTopics != 0) {
					?>

			<td class="klastpost kmiddle kleft" width="25%">
			<!-- Avatar --> <?php
			if ($this->config->avataroncat > 0) :
				$profile = KunenaFactory::getUser((int)$subcat->userid);
				$useravatar = $profile->getAvatarLink('klist-avatar', 'list');
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
					?>
			</div>
			</td>

			<?php
				} else {
					?>

			<td class="knoposts kmiddle kcenter" width="25%"><?php
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
</table>
		</div>
	</div>
</div>

<?php
	endforeach; ?>

