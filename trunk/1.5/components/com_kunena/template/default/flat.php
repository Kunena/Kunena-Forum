<?php
/**
 * @version $Id: flat.php 1173 2009-10-27 22:51:09Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
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
defined ( '_JEXEC' ) or die ( 'Restricted access' );

$kunena_db = &JFactory::getDBO ();
$kunena_config = & CKunenaConfig::getInstance ();

$func = JString::strtolower ( JRequest::getCmd ( 'func', '' ) );
$id = JRequest::getInt ( 'id', 0 );

global $kunena_emoticons;
global $kunena_is_moderator;
$kunena_my = &JFactory::getUser ();
// Func Check
if (JString::strtolower ( $func ) == 'latest' || JString::strtolower ( $func ) == '') {
	$funclatest = 1;
} else {
	$funclatest = 0;
}

if (JString::strtolower ( $func ) == 'mylatest') {
	$funcmylatest = 1;
} else {
	$funcmylatest = 0;
}

// topic emoticons
$topic_emoticons = array ();

$topic_emoticons [0] = KUNENA_URLEMOTIONSPATH . 'default.gif';
$topic_emoticons [1] = KUNENA_URLEMOTIONSPATH . 'exclam.gif';
$topic_emoticons [2] = KUNENA_URLEMOTIONSPATH . 'question.gif';
$topic_emoticons [3] = KUNENA_URLEMOTIONSPATH . 'arrow.gif';
$topic_emoticons [4] = KUNENA_URLEMOTIONSPATH . 'love.gif';
$topic_emoticons [5] = KUNENA_URLEMOTIONSPATH . 'grin.gif';
$topic_emoticons [6] = KUNENA_URLEMOTIONSPATH . 'shock.gif';
$topic_emoticons [7] = KUNENA_URLEMOTIONSPATH . 'smile.gif';

// url of current page that user will be returned to after bulk operation
$kuri = JURI::getInstance ();
$Breturn = $kuri->toString ( array ('path' ) );

$tabclass = array ("sectiontableentry1", "sectiontableentry2" );

$st_count = 0;

if (count ( $this->messages [0] ) > 0) {
	foreach ( $this->messages [0] as $leafa ) {
		if (($leafa->ordering > 0 && ! $funcmylatest) || ($leafa->myfavorite && $funcmylatest)) {
			$st_count ++;
		}
	}
}

if (count ( $this->messages [0] ) > 0) {
	?>
<div class="<?php
	echo KUNENA_BOARD_CLASS;
	?>_bt_cvr1">
<div class="<?php
	echo KUNENA_BOARD_CLASS;
	?>_bt_cvr2">
<div class="<?php
	echo KUNENA_BOARD_CLASS;
	?>_bt_cvr3">
<div class="<?php
	echo KUNENA_BOARD_CLASS;
	?>_bt_cvr4">
<div class="<?php
	echo KUNENA_BOARD_CLASS;
	?>_bt_cvr5">
<form action="index.php" method="post" name="fbBulkActionForm">

<table
	class="fb_blocktable<?php
	echo isset ( $objCatInfo->class_sfx ) ? $objCatInfo->class_sfx : '';
	?>"
	id="fb_flattable" border="0" cellspacing="0" cellpadding="0"
	width="100%">

	<?php
	if ($funclatest || $funcmylatest) {

	} else {
		?>
	<thead>
		<tr>
			<th
				colspan="<?php
		echo ($kunena_is_moderator ? "5" : "4");
		?>">
			<div class="fb_title_cover fbm"><span class="fb_title fbl"><b><?php
		echo _KUNENA_THREADS_IN_FORUM;
		?>:</b>
			<?php
		echo '' . kunena_htmlspecialchars ( stripslashes ( $objCatInfo->name ) ) . '';
		?></span></div>
			<!-- FORUM TOOLS --> <?php
		//(JJ) BEGIN: RECENT POSTS
		if (file_exists ( KUNENA_ABSTMPLTPATH . '/plugin/forumtools/forumtools.php' )) {
			include (KUNENA_ABSTMPLTPATH . '/plugin/forumtools/forumtools.php');
		} else {
			include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'plugin/forumtools/forumtools.php');
		}
		//(JJ) FINISH: RECENT POSTS
		?>
			<!-- /FORUM TOOLS --></th>
		</tr>
	</thead>

	<?php
	}
	?>

	<tbody>
		<tr class="fb_sth fbs ">
			<th
				class="th-0 <?php
	echo KUNENA_BOARD_CLASS;
	?>sectiontableheader"
				width="5%" align="center"><?php
	echo _GEN_REPLIES;
	?></th>

			<th
				class="th-2 <?php
	echo KUNENA_BOARD_CLASS;
	?>sectiontableheader"
				width="1%">&nbsp;</th>
			<th
				class="th-3 <?php
	echo KUNENA_BOARD_CLASS;
	?>sectiontableheader"
				align="left"><?php
	echo _GEN_TOPICS;
	?></th>

			<th
				class="th-6 <?php
	echo KUNENA_BOARD_CLASS;
	?>sectiontableheader"
				width="27.5%" align="left"><?php
	echo _GEN_LAST_POST;
	?></th>

			<?php
	if ($kunena_is_moderator) {
		?>
			<th
				class="th-7 <?php
		echo KUNENA_BOARD_CLASS;
		?>sectiontableheader"
				width="1%" align="center">[X]</th>
			<?php
	}
	?>
		</tr>

		<?php
	$k = 0;
	$st_c = 0;

	$st_occured = 0;
	foreach ( $this->messages [0] as $leaf ) {
		$k = 1 - $k; //used for alternating colours
		$leaf->name = kunena_htmlspecialchars ( stripslashes ( $leaf->name ) );
		$leaf->email = kunena_htmlspecialchars ( stripslashes ( $leaf->email ) );
		?>

		<?php
		if ($st_c == 0 && $st_occured != 1 && $st_count != 0 && $funclatest == 0) {
			?>

		<tr>
			<td class="<?php
			echo KUNENA_BOARD_CLASS;
			?>contentheading fbm"
				id="fb_spot"
				colspan="<?php
			echo ($kunena_is_moderator ? "5" : "4");
			?>"
				align="left"><span><?php
			if (! $funcmylatest) {
				echo _KUNENA_SPOTS;
			} else {
				echo _USER_FAVORITES;
			}
			?></span></td>
		</tr>

		<?php
		}

		if ($st_c == $st_count && $st_occured != 1 && $st_count != 0 && $funclatest == 0) {
			$st_occured = 1;
			$k = 0;
			?>

		<tr>
			<td class="<?php
			echo KUNENA_BOARD_CLASS;
			?>contentheading fbm"
				id="fb_fspot"
				colspan="<?php
			echo ($kunena_is_moderator ? "5" : "4");
			?>"
				align="left"><span><?php
			if (! $funcmylatest) {
				echo _KUNENA_FORUM;
			} else {
				echo _KUNENA_MY_DISCUSSIONS_DETAIL;
			}
			?></span></td>
		</tr>

		<?php
		}
		?>

		<tr
			class="<?php
		echo KUNENA_BOARD_CLASS . $tabclass [$k];
		if ($leaf->ordering != 0 || ($leaf->myfavorite && $funcmylatest)) {
			echo '_stickymsg';
			$topicSticky = 1;
		}
		?>">
			<td class="td-0 fbm" align="center"><strong> <?php
		echo ( int ) $this->thread_counts [$leaf->id];
		?>
			</strong><?php
		echo _GEN_REPLIES;
		?></td>


			<?php
		if ($leaf->moved == 0) {
			// Need to add +1 as we only have the replies in the buffer
			$totalMessages = $this->thread_counts [$leaf->id] + 1;
			$curMessageNo = $totalMessages - ($last_read [$leaf->id]->unread ? $last_read [$leaf->id]->unread - 1 : 0);
			$threadPages = ceil ( $totalMessages / $kunena_config->messages_per_page );
			$unreadPage = ceil ( $curMessageNo / $kunena_config->messages_per_page );
			?>

			<td class="td-2" align="center"><?php
			echo $leaf->topic_emoticon == 0 ? '<img src="' . KUNENA_URLEMOTIONSPATH . 'default.gif" border="0"  alt="" />' : "<img src=\"" . $topic_emoticons [$leaf->topic_emoticon] . "\" alt=\"emo\" border=\"0\" />";
			?>
			</td>

			<td class="td-3"><?php
			//(JJ) ATTACHMENTS ICON
			if ($leaf->attachmesid > 0) {
				echo isset ( $kunena_emoticons ['topicattach'] ) ? '<img  class="attachicon" src="' . KUNENA_URLICONSPATH . $kunena_emoticons ['topicattach'] . '" border="0" alt="' . _KUNENA_ATTACH . '" />' : '<img class="attachicon" src="' . KUNENA_URLEMOTIONSPATH . 'attachment.gif"  alt="' . _KUNENA_ATTACH . '" title="' . _KUNENA_ATTACH . '" />';
			}
			?>

			<div class="fb-topic-title-cover"><?php
			echo CKunenaLink::GetThreadLink ( 'view', $leaf->catid, $leaf->id, kunena_htmlspecialchars ( stripslashes ( $leaf->subject ) ), kunena_htmlspecialchars ( stripslashes ( $this->messagetext [$leaf->id] ) ), 'follow', 'fb-topic-title fbm' );
			?>
			<!--            Favorite       --> <?php
			if ($kunena_config->allowfavorites && array_key_exists ( $leaf->id, $this->favthread )) {
				if ($leaf->myfavorite) {
					echo isset ( $kunena_emoticons ['favoritestar'] ) ? '<img  class="favoritestar" src="' . KUNENA_URLICONSPATH . $kunena_emoticons ['favoritestar'] . '" border="0" alt="' . _KUNENA_FAVORITE . '" />' : '<img class="favoritestar" src="' . KUNENA_URLEMOTIONSPATH . 'favoritestar.gif"  alt="' . _KUNENA_FAVORITE . '" title="' . _KUNENA_FAVORITE . '" />';
				} else if (array_key_exists ( 'favoritestar_grey', $kunena_emoticons )) {
					echo isset ( $kunena_emoticons ['favoritestar_grey'] ) ? '<img  class="favoritestar" src="' . KUNENA_URLICONSPATH . $kunena_emoticons ['favoritestar_grey'] . '" border="0" alt="' . _KUNENA_FAVORITE . '" />' : '<img class="favoritestar" src="' . KUNENA_URLEMOTIONSPATH . 'favoritestar.gif"  alt="' . _KUNENA_FAVORITE . '" title="' . _KUNENA_FAVORITE . '" />';
				}
			}
			?>
			<!--            /Favorite       --> <?php
			if ($kunena_config->shownew && $kunena_my->id != 0) {
				if (($this->prevCheck < $last_reply [$leaf->id]->time) && ! in_array ( $last_reply [$leaf->id]->thread, $this->read_topics )) {
					//new post(s) in topic
					echo CKunenaLink::GetThreadPageLink ( $kunena_config, 'view', $leaf->catid, $leaf->id, $unreadPage, $kunena_config->messages_per_page, '<sup><span class="newchar">&nbsp;(' . $last_read [$leaf->id]->unread . ' ' . stripslashes ( $kunena_config->newchar ) . ')</span></sup>', $last_read [$leaf->id]->lastread );
				}
			}

			if ($totalMessages > $kunena_config->messages_per_page) {
				echo ("<span class=\"jr-showcat-perpage\">[");
				echo _PAGE . ' ' . CKunenaLink::GetThreadPageLink ( $kunena_config, 'view', $leaf->catid, $leaf->id, 1, $kunena_config->messages_per_page, 1 );

				if ($threadPages > 3) {
					echo ("...");
					$startPage = $threadPages - 2;
				} else {
					echo (",");
					$startPage = 2;
				}

				$noComma = true;

				for($hopPage = $startPage; $hopPage <= $threadPages; $hopPage ++) {
					if ($noComma) {
						$noComma = false;
					} else {
						echo (",");
					}

					echo CKunenaLink::GetThreadPageLink ( $kunena_config, 'view', $leaf->catid, $leaf->thread, $hopPage, $kunena_config->messages_per_page, $hopPage );
				}

				echo ("]</span>");
			}
			?>
			</div>
			</td>
			<?php
		} else {
			$threadPages = 0;
			$unreadPage = 0;
			//this thread has been moved, get the new location
			$kunena_db->setQuery ( "SELECT message FROM #__fb_messages_text WHERE mesid='{$leaf->id}'" );
			$newURL = $kunena_db->loadResult ();
			// split the string and separate catid and id for proper link assembly
			parse_str ( $newURL, $newURLParams );
			?>

			<td class="td-2"><?php
			echo CKunenaLink::GetSimpleLink ( $id );
			?>
			<img src="<?php
			echo KUNENA_URLEMOTIONSPATH;
			?>arrow.gif"
				alt="emo" /></td>

			<td class="td-3">
			<div class="fb-topic-title-cover"><?php
			echo CKunenaLink::GetThreadLink ( 'view', $newURLParams ['catid'], $newURLParams ['id'], kunena_htmlspecialchars ( stripslashes ( $leaf->subject ) ), kunena_htmlspecialchars ( stripslashes ( $leaf->subject ) ), 'follow', 'fb-topic-title fbm' );
			?>
			</div>
			<?php
		}
		?>
			<div class="fbs"><!-- By --> <span class="topic_posted_time"><?php
		echo _KUNENA_POSTED_AT?>
			<?php
		echo time_since ( $leaf->time, time () + ($kunena_config->board_ofset * 3600) );
		?>
			<?php
		echo _KUNENA_AGO?> </span> <?php
		if ($leaf->name) {
			echo '<span class="topic_by">';
			echo _GEN_BY . ' ' . CKunenaLink::GetProfileLink ( $kunena_config, $leaf->userid, $leaf->name );
			echo '</span>';
		}
		?>
			<!-- /By --> <?php
		if (JString::strtolower ( $func ) != 'showcat') {
			?>
			<!-- Category --> <span class="topic_category"> <?php
			echo _KUNENA_CATEGORY . ' ' . CKunenaLink::GetCategoryLink ( 'showcat', $leaf->catid, kunena_htmlspecialchars ( stripslashes ( $leaf->catname ) ) );
			?>
			</span> <!-- /Category --> <?php
		}
		?> <!-- Views --> <span
				class="topic_views"> <?php
		echo _GEN_HITS;
		?>: <?php
		echo ( int ) $this->hits [$leaf->id];
		?>
			</span> <!-- /Views --> <?php
		if ($leaf->locked != 0) {
			?> <!-- Locked -->
			<span class="topic_locked"> <?php
			echo isset ( $kunena_emoticons ['topiclocked'] ) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_emoticons ['topiclocked'] . '" border="0" alt="' . _GEN_LOCKED_TOPIC . '" />' : '<img src="' . KUNENA_URLEMOTIONSPATH . 'lock.gif"  alt="' . _GEN_LOCKED_TOPIC . '" title="' . _GEN_LOCKED_TOPIC . '" />';
			$topicLocked = 1;
			?>
			</span> <!-- /Locked --> <?php
		}
		?></div>
			</td>
			<td class="td-6 fbs">
			<div style="position: relative"><!--  Sticky   --> <?php
		if ($leaf->ordering != 0) {
			?>
			<span class="topic_sticky"> <?php
			echo isset ( $kunena_emoticons ['topicsticky'] ) ? '<img  src="' . KUNENA_URLICONSPATH . $kunena_emoticons ['topicsticky'] . '" border="0" alt="' . _GEN_ISSTICKY . '" />' : '<img class="stickyicon" src="' . KUNENA_URLEMOTIONSPATH . 'pushpin.gif"  alt="' . _GEN_ISSTICKY . '" title="' . _GEN_ISSTICKY . '" />';
			$topicSticky = 1;
			?>
			</span> <?php
		}
		?> <!--  /Sticky   --> <!-- Avatar --> <?php // (JJ) AVATAR
		if ($kunena_config->avataroncat > 0) {
			?>
			<span class="topic_latest_post_avatar"> <?php
			if ($kunena_config->avatar_src == "jomsocial" && $leaf->userid) {
				// Get CUser object
				$jsuser = & CFactory::getUser ( $last_reply [$leaf->id]->userid );
				$useravatar = '<img class="fb_list_avatar" src="' . $jsuser->getThumbAvatar () . '" alt=" " />';
				echo CKunenaLink::GetProfileLink ( $kunena_config, $last_reply [$leaf->id]->userid, $useravatar );
			} else if ($kunena_config->avatar_src == "cb") {
				$kunenaProfile = & CkunenaCBProfile::getInstance ();
				$useravatar = $kunenaProfile->showAvatar ( $last_reply [$leaf->id]->userid, 'fb_list_avatar' );
				echo CKunenaLink::GetProfileLink ( $kunena_config, $last_reply [$leaf->id]->userid, $useravatar );
			} else if ($kunena_config->avatar_src == "aup") {
				// integration AlphaUserPoints
				$api_AUP = JPATH_SITE . DS . 'components' . DS . 'com_alphauserpoints' . DS . 'helper.php';
				if (file_exists ( $api_AUP )) {
					($kunena_config->fb_profile == 'aup') ? $showlink = 1 : $showlink = 0;
					echo AlphaUserPointsHelper::getAupAvatar ( $last_reply [$leaf->id]->userid, $showlink, 40, 40 );
				} // end integration AlphaUserPoints
			} else {
				$javatar = $last_reply [$leaf->id]->avatar;
				if ($javatar != '') {
					echo CKunenaLink::GetProfileLink ( $kunena_config, $last_reply [$leaf->id]->userid, '<img class="fb_list_avatar" src="' . (! file_exists ( KUNENA_PATH_UPLOADED . DS . 'avatars/s_' . $javatar ) ? KUNENA_LIVEUPLOADEDPATH . '/avatars/' . $javatar : KUNENA_LIVEUPLOADEDPATH . '/avatars/s_' . $javatar) . '" alt="" />' );
				} else {
					echo CKunenaLink::GetProfileLink ( $kunena_config, $last_reply [$leaf->id]->userid, '<img class="fb_list_avatar" src="' . KUNENA_LIVEUPLOADEDPATH . '/avatars/s_nophoto.jpg" alt="" />' );
				}
			}
			?>
			</span> <?php
		}
		?> <!-- /Avatar --> <!-- Latest Post --> <span
				class="topic_latest_post"> <?php
		if ($kunena_config->default_sort == 'asc') {
			if ($leaf->moved == 0)
				echo CKunenaLink::GetThreadPageLink ( $kunena_config, 'view', $leaf->catid, $leaf->thread, $threadPages, $kunena_config->messages_per_page, _GEN_LAST_POST, $last_reply [$leaf->id]->id );
			else
				echo _KUNENA_MOVED . ' ';
		} else {
			echo CKunenaLink::GetThreadPageLink ( $kunena_config, 'view', $leaf->catid, $leaf->thread, 1, $kunena_config->messages_per_page, _GEN_LAST_POST, $last_reply [$leaf->id]->id );
		}

		if ($leaf->name)
			echo ' ' . _GEN_BY . ' ' . CKunenaLink::GetProfileLink ( $kunena_config, $last_reply [$leaf->id]->userid, stripslashes ( $last_reply [$leaf->id]->name ), 'nofollow', 'topic_latest_post_user' );
		?>
			</span> <!-- /Latest Post --> <br />
			<!-- Latest Post Date --> <span class="topic_date"> <?php
		echo time_since ( $last_reply [$leaf->id]->time, time () + ($kunena_config->board_ofset * 3600) );
		?>
			<?php
		echo _KUNENA_AGO?> </span> <!-- /Latest Post Date --></div>

			</td>

			<?php
		if ($kunena_is_moderator) {
			?>

			<td class="td-7" align="center"><input type="checkbox"
				name="fbDelete[<?php
			echo $leaf->id?>]" value="1" /></td>

			<?php
		}
		?>
		</tr>

		<?php
		$st_c ++;
	}
	if ($kunena_is_moderator) {
		?>
		<tr class="<?php
		echo KUNENA_BOARD_CLASS?>sectiontableentry1">
			<td colspan="7" align="right" class="td-1 fbs"><script
				type="text/javascript">
                            jQuery(document).ready(function()
                            {
                                jQuery('#fbBulkActions').change(function()
                                {
                                    var myList = jQuery(this);

                                    if (jQuery(myList).val() == "bulkMove")
                                    {
                                        jQuery("#KUNENA_AvailableForums").removeAttr('disabled');
                                    }
                                    else
                                    {
                                        jQuery("#KUNENA_AvailableForums").attr('disabled', 'disabled');
                                    }
                                });
                            });
                        </script> <select name="do" id="fbBulkActions"
				class="inputbox fbs">
				<option value="">&nbsp;</option>
				<option value="bulkDel"><?php
		echo _KUNENA_DELETE_SELECTED;
		?></option>
				<option value="bulkMove"><?php
		echo _KUNENA_MOVE_SELECTED;
		?></option>
			</select> <?php
		CKunenaTools::showBulkActionCats ();
		?> <input
				type="submit" name="fbBulkActionsGo" class="fb_button fbs"
				value="<?php
		echo _KUNENA_GO;
		?>" /></td>
		</tr>
		<?php
	}
	?>
	</tbody>
</table>

<input type="hidden" name="Itemid"
	value="<?php
	echo KUNENA_COMPONENT_ITEMID;
	?>" /> <input
	type="hidden" name="option" value="com_kunena" /> <input type="hidden"
	name="func" value="bulkactions" /> <input type="hidden" name="return"
	value="<?php
	echo JRoute::_ ( $Breturn );
	?>" /></form>
</div>
</div>
</div>
</div>
</div>
<?php
} else {
	echo "<p align=\"center\">" . _VIEW_NO_POSTS . "</p>";
}
?>
