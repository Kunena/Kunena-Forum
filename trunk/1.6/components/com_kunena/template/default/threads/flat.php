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
defined( '_JEXEC' ) or die();


$kunena_db = &JFactory::getDBO ();
$kunena_config = & CKunenaConfig::getInstance ();

$id = JRequest::getInt ( 'id', 0 );
$catid = JRequest::getInt ( 'catid', 0 );

global $kunena_icons;
$kunena_my = &JFactory::getUser ();

// Func Check


$funclatest = false;
$funcmylatest = false;
$funcnoreplies = false;

switch (JString::strtolower ( $this->func )) {
	case 'mylatest' :
		$funcmylatest = true;
		break;
	case 'noreplies' :
		$funcnoreplies = true;
		break;
	case 'latest' :
		$funclatest = true;
		break;
	default :
		break;
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
$Breturn = $kuri->toString ( array ('path', 'query', 'fragment' ) );

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
<div class="k_bt_cvr1">
<div class="k_bt_cvr2">
<div class="k_bt_cvr3">
<div class="k_bt_cvr4">
<div class="k_bt_cvr5">
<form action="index.php" method="post" name="kBulkActionForm">

<table
	class="kblocktable<?php
	echo isset ( $this->objCatInfo->class_sfx ) ? ' kblocktable' . $this->objCatInfo->class_sfx : '';
	?>"
	id="kflattable" border="0" cellspacing="0" cellpadding="0"
	width="100%">

	<thead>
		<tr>
			<th
				colspan="<?php
	echo (CKunenaTools::isModerator ( $kunena_my->id, $catid ) ? "6" : "5");
	?>">

			<?php
	if ($funclatest || $funcmylatest || $funcnoreplies) {
		?>
			<div class="ktitle_cover km"><span class="ktitle kl"><?php

		switch (JString::strtolower ( $this->func )) {
			case 'mylatest' :
				echo _KUNENA_MENU_MYLATEST_DESC;
				break;
			case 'noreplies' :
				echo _KUNENA_MENU_NOREPLIES_DESC;
				break;
			case 'latest' :
				echo _KUNENA_MENU_LATEST_DESC;
				break;
			default :
				break;
		}
		?></span></div>
			<?php
	} else {
		?>
			<div class="ktitle_cover km"><span class="ktitle kl"><?php
		echo _KUNENA_THREADS_IN_FORUM;
		?>:
			<?php
		echo kunena_htmlspecialchars ( stripslashes ( $this->objCatInfo->name ) );
		?></span></div>
		</th>
			<?php
	}
	?>
		</tr>


	</thead>
	<tbody>
		<?php
	$k = 0;
	$st_c = 0;

	$st_occured = 0;
	foreach ( $this->messages [0] as $leaf ) {
		$k = 1 - $k; //used for alternating colours
		$leaf->name = kunena_htmlspecialchars ( stripslashes ( $leaf->name ) );
		$leaf->email = kunena_htmlspecialchars ( stripslashes ( $leaf->email ) );

		if ($st_c == $st_count && $st_occured != 1 && $st_count != 0 && $funclatest == 0) {
			$st_occured = 1;
			$k = 0;
			?>

		<tr>
			<td class="kcontenttablespacer"
				colspan="<?php
			echo (CKunenaTools::isModerator ( $kunena_my->id, $catid ) ? "6" : "5");
			?>">&nbsp;
			</td>
		</tr>

		<?php
		}
		?>

		<tr
			class="k<?php
		echo $tabclass [$k];
		if ($leaf->ordering != 0 || ($leaf->myfavorite && $funcmylatest)) {
			echo '_stickymsg';
			$topicSticky = 1;
		}

		if ($leaf->class_sfx) {
			echo ' k' . $tabclass [$k];
			if ($leaf->ordering != 0 || ($leaf->myfavorite && $funcmylatest)) {
				echo '_stickymsg';
				$topicSticky = 1;
			}
			echo $leaf->class_sfx;
		}
		?>">
			<td class="td-0 km" align="center"><strong> <?php
		echo CKunenaTools::formatLargeNumber ( $this->thread_counts [$leaf->id] );
		?>
			</strong><?php
		echo _GEN_REPLIES;
		?></td>

			<td class="td-2" align="center"><?php
		if ($leaf->moved == 0) {
			echo $leaf->topic_emoticon == 0 ? '<img src="' . KUNENA_URLEMOTIONSPATH . 'default.gif" border="0"  alt="" />' : "<img src=\"" . $topic_emoticons [$leaf->topic_emoticon] . "\" alt=\"emo\" border=\"0\" />";
		} else {
			echo CKunenaLink::GetSimpleLink ( $id );
			?>
			<img src="<?php
			echo KUNENA_URLEMOTIONSPATH;
			?>arrow.gif"
				alt="emo" /> <?php
		}
		?></td>

			<td class="td-3"><?php
		if ($leaf->moved == 0) {
			// Need to add +1 as we only have the replies in the buffer
			$totalMessages = $this->thread_counts [$leaf->id] + 1;
			$curMessageNo = $totalMessages - ($this->last_read [$leaf->id]->unread ? $this->last_read [$leaf->id]->unread - 1 : 0);
			$threadPages = ceil ( $totalMessages / $kunena_config->messages_per_page );
			$unreadPage = ceil ( $curMessageNo / $kunena_config->messages_per_page );

			//(JJ) ATTACHMENTS ICON
			if ($leaf->attachmesid > 0) {
				echo isset ( $kunena_icons ['topicattach'] ) ? '<img  class="attachicon" src="' . KUNENA_URLICONSPATH . $kunena_icons ['topicattach'] . '" border="0" alt="' . _KUNENA_ATTACH . '" />' : '<img class="attachicon" src="' . KUNENA_URLEMOTIONSPATH . 'attachment.gif"  alt="' . _KUNENA_ATTACH . '" title="' . _KUNENA_ATTACH . '" />';
			}
			?>

			<div class="k-topic-title-cover"><?php
			echo CKunenaLink::GetThreadLink ( 'view', $leaf->catid, $leaf->id, kunena_htmlspecialchars ( stripslashes ( $leaf->subject ) ), kunena_htmlspecialchars ( stripslashes ( $this->messagetext [$leaf->id] ) ), 'follow', 'k-topic-title km' );
			?>
			<!--            Favorite       --> <?php
			if ($kunena_config->allowfavorites && array_key_exists ( $leaf->id, $this->favthread )) {
				if ($leaf->myfavorite) {
					echo isset ( $kunena_icons ['favoritestar'] ) ? '<img  class="favoritestar" src="' . KUNENA_URLICONSPATH . $kunena_icons ['favoritestar'] . '" border="0" alt="' . _KUNENA_FAVORITE . '" />' : '<img class="favoritestar" src="' . KUNENA_URLEMOTIONSPATH . 'favoritestar.gif"  alt="' . _KUNENA_FAVORITE . '" title="' . _KUNENA_FAVORITE . '" />';
				} else if (array_key_exists ( 'favoritestar_grey', $kunena_icons )) {
					echo isset ( $kunena_icons ['favoritestar_grey'] ) ? '<img  class="favoritestar" src="' . KUNENA_URLICONSPATH . $kunena_icons ['favoritestar_grey'] . '" border="0" alt="' . _KUNENA_FAVORITE . '" />' : '<img class="favoritestar" src="' . KUNENA_URLEMOTIONSPATH . 'favoritestar.gif"  alt="' . _KUNENA_FAVORITE . '" title="' . _KUNENA_FAVORITE . '" />';
				}
			}
			?>
			<!--            /Favorite       --> <?php
			if ($kunena_config->shownew && $kunena_my->id != 0) {
				if (($this->prevCheck < $this->last_reply [$leaf->id]->time) && ! in_array ( $this->last_reply [$leaf->id]->thread, $this->read_topics )) {
					//new post(s) in topic
					echo CKunenaLink::GetThreadPageLink ( $kunena_config, 'view', $leaf->catid, $leaf->id, $unreadPage, $kunena_config->messages_per_page, '<sup><span class="newchar">&nbsp;(' . $this->last_read [$leaf->id]->unread . ' ' . stripslashes ( $kunena_config->newchar ) . ')</span></sup>', $this->last_read [$leaf->id]->lastread );
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
			<div class="k-topic-title-cover"><?php
			echo CKunenaLink::GetThreadLink ( 'view', $newURLParams ['catid'], $newURLParams ['id'], kunena_htmlspecialchars ( stripslashes ( $leaf->subject ) ), kunena_htmlspecialchars ( stripslashes ( $leaf->subject ) ), 'follow', 'k-topic-title km' );
			?>
			</div>
			<?php
		}
		?>
			<div class="ks">
			<!-- By -->
				<?php
		if (JString::strtolower ( $this->func ) != 'showcat') {
			?>
			<!-- Category --> <span class="topic_category"> <?php
			echo _KUNENA_CATEGORY . ' ' . CKunenaLink::GetCategoryLink ( 'showcat', $leaf->catid, kunena_htmlspecialchars ( stripslashes ( $leaf->catname ) ) );
			?>
			</span> <!-- /Category -->
			<span class="divider">|</span>
<?php 	} ?>
			<span class="topic_posted_time" title="<?php echo CKunenaTimeformat::showDate($leaf->time, 'config_post_dateformat_hover'); ?>"><?php
		echo _KUNENA_POSTED_AT?>
			<?php
			echo CKunenaTimeformat::showDate($leaf->time, 'config_post_dateformat');
		?></span>

		<?php
		if ($leaf->name) {
			echo '<span class="topic_by">';
			echo _GEN_BY . ' ' . CKunenaLink::GetProfileLink ( $kunena_config, $leaf->userid, $leaf->name );
			echo '</span>';
		}
		?>
			<!-- /By -->



		<?php
		if ($leaf->locked != 0) {
			?> <!-- Locked --> <span class="topic_locked"> <?php
			echo isset ( $kunena_icons ['topiclocked'] ) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['topiclocked'] . '" border="0" alt="' . _GEN_LOCKED_TOPIC . '" />' : '<img src="' . KUNENA_URLEMOTIONSPATH . 'lock.gif"  alt="' . _GEN_LOCKED_TOPIC . '" title="' . _GEN_LOCKED_TOPIC . '" />';
			$topicLocked = 1;
			?>
			</span> <!-- /Locked --> <?php
		}
		?></div>
			</td>
			<td class="td-4" align="center"><!-- Views --> <span
				class="topic_views_number"><?php
		echo CKunenaTools::formatLargeNumber ( ( int ) $this->hits [$leaf->id] );
		?>
			</span> <span class="topic_views"> <?php
		echo _GEN_HITS;
		?> </span> <!-- /Views --></td>
			<td class="td-6 ks">
			<div style="position: relative"><!--  Sticky   --> <?php
		if ($leaf->ordering != 0) {
			?>
			<span class="topic_sticky"> <?php
			echo isset ( $kunena_icons ['topicsticky'] ) ? '<img  src="' . KUNENA_URLICONSPATH . $kunena_icons ['topicsticky'] . '" border="0" alt="' . _GEN_ISSTICKY . '" />' : '<img class="stickyicon" src="' . KUNENA_URLICONSPATH . 'tsticky.gif"  alt="' . _GEN_ISSTICKY . '" title="' . _GEN_ISSTICKY . '" />';
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
				$jsuser = & CFactory::getUser ( $this->last_reply [$leaf->id]->userid );
				$useravatar = '<img class="klist_avatar" src="' . $jsuser->getThumbAvatar () . '" alt=" " />';
				echo CKunenaLink::GetProfileLink ( $kunena_config, $this->last_reply [$leaf->id]->userid, $useravatar );
			} else if ($kunena_config->avatar_src == "cb") {
				$kunenaProfile = & CkunenaCBProfile::getInstance ();
				$useravatar = $kunenaProfile->showAvatar ( $this->last_reply [$leaf->id]->userid, 'fb_list_avatar' );
				echo CKunenaLink::GetProfileLink ( $kunena_config, $this->last_reply [$leaf->id]->userid, $useravatar );
			} else if ($kunena_config->avatar_src == "aup") {
				// integration AlphaUserPoints
				$api_AUP = JPATH_SITE . DS . 'components' . DS . 'com_alphauserpoints' . DS . 'helper.php';
				if (file_exists ( $api_AUP )) {
					($kunena_config->fb_profile == 'aup') ? $showlink = 1 : $showlink = 0;
					echo AlphaUserPointsHelper::getAupAvatar ( $this->last_reply [$leaf->id]->userid, $showlink, 40, 40 );
				} // end integration AlphaUserPoints
			} else {
				$javatar = $this->last_reply [$leaf->id]->avatar;
				if ($javatar != '') {
					echo CKunenaLink::GetProfileLink ( $kunena_config, $this->last_reply [$leaf->id]->userid, '<img class="klist_avatar" src="' . (! file_exists ( KUNENA_PATH_UPLOADED . DS . 'avatars/s_' . $javatar ) ? KUNENA_LIVEUPLOADEDPATH . '/avatars/' . $javatar : KUNENA_LIVEUPLOADEDPATH . '/avatars/s_' . $javatar) . '" alt="" />' );
				} else {
					echo CKunenaLink::GetProfileLink ( $kunena_config, $this->last_reply [$leaf->id]->userid, '<img class="klist_avatar" src="' . KUNENA_LIVEUPLOADEDPATH . '/avatars/s_nophoto.jpg" alt="" />' );
				}
			}
			?>
			</span> <?php
		}
		?> <!-- /Avatar --> <!-- Latest Post --> <span
				class="topic_latest_post"> <?php
		if ($kunena_config->default_sort == 'asc') {
			if ($leaf->moved == 0)
				echo CKunenaLink::GetThreadPageLink ( $kunena_config, 'view', $leaf->catid, $leaf->thread, $threadPages, $kunena_config->messages_per_page, _GEN_LAST_POST, $this->last_reply [$leaf->id]->id );
			else
				echo _KUNENA_MOVED . ' ';
		} else {
			echo CKunenaLink::GetThreadPageLink ( $kunena_config, 'view', $leaf->catid, $leaf->thread, 1, $kunena_config->messages_per_page, _GEN_LAST_POST, $this->last_reply [$leaf->id]->id );
		}

		if ($leaf->name)
			echo ' ' . _GEN_BY . ' ' . CKunenaLink::GetProfileLink ( $kunena_config, $this->last_reply [$leaf->id]->userid, stripslashes ( $this->last_reply [$leaf->id]->name ), 'nofollow', 'topic_latest_post_user' );
		?>
			</span> <!-- /Latest Post --> <br />
			<!-- Latest Post Date --> <span class="topic_date" title="<?php echo CKunenaTimeformat::showDate($this->last_reply[$leaf->id]->time, 'config_post_dateformat_hover'); ?>"> <?php
			echo CKunenaTimeformat::showDate($this->last_reply[$leaf->id]->time, 'config_post_dateformat');
		?> </span> <!-- /Latest Post Date --></div>

			</td>

			<?php
		if (CKunenaTools::isModerator ( $kunena_my->id, $catid )) {
			?>

			<td class="td-7" align="center"><input type="checkbox"
				name="kDelete[<?php
			echo $leaf->id?>]" value="1" /></td>

			<?php
		}
		?>
		</tr>

		<?php
		$st_c ++;
	}
	if (CKunenaTools::isModerator ( $kunena_my->id, $catid )) {
		?>
		<!-- Moderator Bulk Actions -->
		<tr class="ksectiontableentry1">
			<td colspan="7" align="right" class="td-1 ks"><script
				type="text/javascript">
                            jQuery(document).ready(function()
                            {
                                jQuery('#kBulkActions').change(function()
                                {
                                    var myList = jQuery(this);

                                    if (jQuery(myList).val() == "bulkMove")
                                    {
                                        jQuery("#bulkactions").removeAttr('disabled');
                                    }
                                    else
                                    {
                                        jQuery("#bulkactions").attr('disabled', 'disabled');
                                    }
                                });
                            });
                        </script> <select name="do" id="kBulkActions"
				class="inputbox ks">
				<option value="">&nbsp;</option>
				<option value="bulkDel"><?php
		echo _KUNENA_DELETE_SELECTED;
		?></option>
				<option value="bulkMove"><?php
		echo _KUNENA_MOVE_SELECTED;
		?></option>
			</select> <?php
		CKunenaTools::showBulkActionCats ();
		?> <input type="submit" name="kBulkActionsGo" class="kbutton ks"
				value="<?php
		echo _KUNENA_GO;
		?>" /></td>
		</tr>
		<!-- /Moderator Bulk Actions -->
		<?php
	}
	?>
	</tbody>
</table>

<input type="hidden" name="Itemid"
	value="<?php
	echo KUNENA_COMPONENT_ITEMID;
	?>" /> <input type="hidden" name="option" value="com_kunena" /> <input
	type="hidden" name="func" value="bulkactions" /> <input type="hidden"
	name="return" value="<?php
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
