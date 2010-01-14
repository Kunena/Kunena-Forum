<?php
/**
 * @version $Id: listcat.php 1210 2009-11-23 06:51:41Z mahagr $
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
defined( '_JEXEC' ) or die();


global $kunena_icons;

$kunena_db = &JFactory::getDBO ();
$kunena_app = & JFactory::getApplication ();
$kunena_config = & CKunenaConfig::getInstance ();
$kunena_session = & CKunenaSession::getInstance ();
$kunena_my = & JFactory::getUser ();

$func = JString::strtolower ( JRequest::getCmd ( 'func', 'listcat' ) );

if (JString::strtolower ( $func ) == '') {
	//	include (KUNENA_ABSTMPLTPATH . '/latestx.php');
} else {

	//securing passed form elements
	$catid = ( int ) $catid;

	$kunena_emoticons = smile::getEmoticons ( 0 );

	//resetting some things:
	$moderatedForum = 0;
	$lockedForum = 0;
	// Start getting the categories
	$kunena_db->setQuery ( "SELECT * FROM #__fb_categories WHERE parent='0' AND published='1' ORDER BY ordering" );
	$allCat = $kunena_db->loadObjectList ();
	check_dberror ( "Unable to load categories." );

	$threadids = array ();
	$categories = array ();

	//meta description and keywords
	$metaDesc = (_KUNENA_CATEGORIES . ' - ' . stripslashes ( $kunena_config->board_title ));
	$metaKeys = (_KUNENA_CATEGORIES . ', ' . stripslashes ( $kunena_config->board_title ) . ', ' . $kunena_app->getCfg ( 'sitename' ));

	$document = & JFactory::getDocument ();
	$cur = $document->get ( 'description' );
	$metaDesc = $cur . '. ' . $metaDesc;
	$document = & JFactory::getDocument ();
	$document->setMetadata ( 'keywords', $metaKeys );
	$document->setDescription ( $metaDesc );

	if (count ( $allCat ) > 0) {
		foreach ( $allCat as $category ) {
			$threadids [] = $category->id;
			$categories [$category->parent] [] = $category;
		}
	}

	//Let's check if the only thing we need to show is 1 category
	if (in_array ( $catid, $threadids )) {
		//Yes, so now $threadids should contain only the current $catid:
		$threadids [] = $catid;
		//get new categories list for this category only:
		$kunena_db->setQuery ( "SELECT * FROM #__fb_categories WHERE parent='0' and published='1' and id='{$catid}' ORDER BY ordering" );
		$categories [$category->parent] = $kunena_db->loadObjectList ();
		check_dberror ( "Unable to load categories." );
	}

	//get the allowed forums and turn it into an array


	$allow_forum = ($kunena_session->allowed != '') ? explode ( ',', $kunena_session->allowed ) : array ();
	$kunena_is_admin = CKunenaTools::isAdmin ();

	// (JJ) BEGIN: ANNOUNCEMENT BOX
	if ($kunena_config->showannouncement > 0) {
		?>
<!-- B: announcementBox -->
<?php
		if (file_exists ( KUNENA_ABSTMPLTPATH . '/plugin/announcement/announcementbox.php' )) {
			require_once (KUNENA_ABSTMPLTPATH . '/plugin/announcement/announcementbox.php');
		} else {
			require_once (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'plugin/announcement/announcementbox.php');
		}
		?>
<!-- F: announcementBox -->
<?php
	}
	// (JJ) FINISH: ANNOUNCEMENT BOX

	CKunenaTools::showModulePosition( 'kunena_announcement' );
	?>
<!-- B: Pathway -->
<?php
	if (file_exists ( KUNENA_ABSTMPLTPATH . '/pathway.php' )) {
		require_once (KUNENA_ABSTMPLTPATH . '/pathway.php');
	} else {
		require_once (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'pathway.php');
	}
	?>
<!-- F: Pathway -->
<!-- B: Cat list Top -->
<table class="klist_top" border="0" cellspacing="0" cellpadding="0"
	width="100%">
	<tr>
		<td class="klist_markallcatsread"><?php
	if ($kunena_my->id != 0) {
		?>

		<form action="<?php
		echo KUNENA_LIVEURLREL;
		?>"
			name="markAllForumsRead" method="post"><input type="hidden"
			name="markaction" value="allread" /> <input type="submit"
			class="kbutton button ks"
			value="<?php
		echo _GEN_MARK_ALL_FORUMS_READ;
		?>" /></form>

		<?php
	}
	?></td>
		<td class="klist_categories"><?php
	if ($kunena_config->enableforumjump)
		require (KUNENA_PATH_LIB . DS . 'kunena.forumjump.php');
	?>
		</td>
	</tr>
</table>
<!-- F: Cat list Top -->


<?php
	if (count ( $categories [0] ) > 0) {
		foreach ( $categories [0] as $cat ) {
			if (in_array ( $cat->id, $allow_forum )) {
				?>
<!-- B: List Cat -->
<div class="kbt_cvr1" id="kblock<?php
				echo $cat->id;
				?>">
<div class="kbt_cvr2">
<div class="kbt_cvr3">
<div class="kbt_cvr4">
<div class="kbt_cvr5">
<table
	class="kblocktable<?php
				echo isset ( $cat->class_sfx ) ? ' kblocktable' . $cat->class_sfx : '';
				?>"
	width="100%" id="kcat<?php
				echo $cat->id;
				?>" border="0"
	cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th colspan="5">
			<div
				class="ktitle_cover<?php
				echo isset ( $cat->class_sfx ) ? ' ktitle_cover' . $cat->class_sfx : '';
				?> km"><?php
				echo CKunenaLink::GetCategoryLink ( 'listcat', $cat->id, kunena_htmlspecialchars ( stripslashes ( $cat->name ) ), 'follow', $class = 'ktitle kl' );

				if ($cat->description != "") {
					$tmpforumdesc = stripslashes ( smile::smileReplace ( $cat->description, 0, $kunena_config->disemoticons, $kunena_emoticons ) );
					$tmpforumdesc = nl2br ( $tmpforumdesc );

					?>
			<div class="ktitle_desc km"><?php
					echo $tmpforumdesc;
					?>
			</div>
			<?php
				}
				?></div>

           <div class="fltrt">
				<span id="cat_list"><a class="ktoggler close" rel="catid_<?php echo $cat->id; ?>"></a></span>
			</div>

			<!-- <img id="BoxSwitch_<?php echo $cat->id; ?>__catid_<?php echo $cat->id; ?>" class="hideshow" src="<?php echo KUNENA_URLIMAGESPATH . 'shrink.gif'; ?>" alt="" /> -->
			</th>
		</tr>
	</thead>
	<tbody id="catid_<?php
				echo $cat->id;
				?>">

		<?php
				//    show forums within the categories
				$kunena_db->setQuery ( "SELECT c.*, m.id AS mesid, m.subject, mm.catid, m.name AS mname,
                    						u.id AS userid, u.username, u.name AS uname, c.class_sfx
                    FROM #__fb_categories AS c
                    LEFT JOIN #__fb_messages AS m ON c.id_last_msg=m.id
                    LEFT JOIN #__users AS u ON u.id=m.userid
                    LEFT JOIN #__fb_messages AS mm ON mm.id=c.id_last_msg
                    WHERE c.parent='{$cat->id}' AND c.published='1' ORDER BY ordering" );
				$rows = $kunena_db->loadObjectList ();
				check_dberror ( "Unable to load categories." );

				$tabclass = array ("sectiontableentry1", "sectiontableentry2" );

				$k = 0;

				if (sizeof ( $rows ) == 0) {
					echo '' . _GEN_NOFORUMS . '';
				} else {
					foreach ( $rows as $singlerow ) {
						if (in_array ( $singlerow->id, $allow_forum )) {
							//    $k=for alternating row colors:
							$k = 1 - $k;

							$numtopics = $singlerow->numTopics;
							$numreplies = $singlerow->numPosts;
							$lastPosttime = $singlerow->time_last_msg;

							$forumDesc = stripslashes ( smile::smileReplace ( $singlerow->description, 0, $kunena_config->disemoticons, $kunena_emoticons ) );
							$forumDesc = nl2br ( $forumDesc );

							//    Get the forumsubparent categories :: get the subcategories here
							$kunena_db->setQuery ( "SELECT id, name, numTopics, numPosts FROM #__fb_categories WHERE parent='{$singlerow->id}' AND published='1' ORDER BY ordering" );
							$forumparents = $kunena_db->loadObjectList ();
							check_dberror ( "Unable to load categories." );

							foreach ( $forumparents as $childnum => $childforum ) {
								if (! in_array ( $childforum->id, $allow_forum ))
									unset ( $forumparents [$childnum] );
							}
							$forumparents = array_values ( $forumparents );

							if ($kunena_my->id) {
								//    get all threads with posts after the users last visit; don't bother for guests
								$kunena_db->setQuery ( "SELECT DISTINCT thread FROM #__fb_messages WHERE catid='{$singlerow->id}' AND hold='0' AND moved='0' AND time>'{$this->prevCheck}' GROUP BY thread" );
								$newThreadsAll = $kunena_db->loadObjectList ();
								check_dberror ( "Unable to load messages." );

								if (count ( $newThreadsAll ) == 0) {
									$newThreadsAll = array ();
								}
							}

							//    get latest post info
							$kunena_db->setQuery ( "SELECT m.thread, COUNT(*) AS totalmessages
                                FROM #__fb_messages AS m
                                LEFT JOIN #__fb_messages AS mm ON m.thread=mm.thread
                                WHERE m.id='{$singlerow->id_last_msg}'
                                GROUP BY m.thread" );
							$thisThread = $kunena_db->loadObject ();
							if (! is_object ( $thisThread )) {
								$thisThread = new stdClass ( );
								$thisThread->totalmessages = 0;
								$thisThread->thread = 0;
							}
							$latestthreadpages = ceil ( $thisThread->totalmessages / $kunena_config->messages_per_page );
							$latestthread = $thisThread->thread;
							$latestname = kunena_htmlspecialchars ( stripslashes ( $singlerow->mname ) );
							$latestcatid = $singlerow->catid;
							$latestid = $singlerow->id_last_msg;
							$latestsubject = kunena_htmlspecialchars ( stripslashes ( $singlerow->subject ) );
							$latestuserid = $singlerow->userid;
							?>

		<tr
			class="k<?php
							echo $tabclass [$k];
							echo isset ( $singlerow->class_sfx ) ? ' k' . $tabclass [$k] . $singlerow->class_sfx : '';
							?>"
			id="kcat<?php
							echo $singlerow->id?>">
			<td class="td-1" align="center" width="1%"><?php
							$tmpIcon = '';
							$cxThereisNewInForum = 0;
							if ($kunena_config->shownew && $kunena_my->id != 0) {
								//Check if unread threads are in any of the forums topics
								$newPostsAvailable = 0;

								foreach ( $newThreadsAll as $nta ) {
									if (! in_array ( $nta->thread, $this->read_topics )) {
										$newPostsAvailable ++;
									}
								}

								if ($newPostsAvailable > 0 && count ( $newThreadsAll ) != 0) {
									$cxThereisNewInForum = 1;

									// Check Unread    Cat Images
									if (is_file ( KUNENA_ABSCATIMAGESPATH . $singlerow->id . "_on.gif" )) {
										$tmpIcon = '<img src="' . KUNENA_URLCATIMAGES . $singlerow->id . '_on.gif" border="0" class="forum-cat-image"alt=" " />';
									} else {
										$tmpIcon = isset ( $kunena_icons ['unreadforum'] ) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['unreadforum'] . '" border="0" alt="' . _GEN_FORUM_NEWPOST . '" title="' . _GEN_FORUM_NEWPOST . '" />' : stripslashes ( $kunena_config->newchar );
									}
								} else {
									// Check Read Cat Images
									if (is_file ( KUNENA_ABSCATIMAGESPATH . $singlerow->id . "_off.gif" )) {
										$tmpIcon = '<img src="' . KUNENA_URLCATIMAGES . $singlerow->id . '_off.gif" border="0" class="forum-cat-image" alt=" " />';
									} else {
										$tmpIcon = isset ( $kunena_icons ['readforum'] ) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['readforum'] . '" border="0" alt="' . _GEN_FORUM_NOTNEW . '" title="' . _GEN_FORUM_NOTNEW . '" />' : stripslashes ( $kunena_config->newchar );
									}
								}
							} else {
								// Not Login Cat Images
								if (is_file ( KUNENA_ABSCATIMAGESPATH . $singlerow->id . "_notlogin.gif" )) {
									$tmpIcon = '<img src="' . KUNENA_URLCATIMAGES . $singlerow->id . '_notlogin.gif" border="0" class="forum-cat-image" alt=" " />';
								} else {
									$tmpIcon = isset ( $kunena_icons ['notloginforum'] ) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['notloginforum'] . '" border="0" alt="' . _GEN_FORUM_NOTNEW . '" title="' . _GEN_FORUM_NOTNEW . '" />' : stripslashes ( $kunena_config->newchar );
								}
							}
							echo CKunenaLink::GetCategoryLink ( 'showcat', $singlerow->id, $tmpIcon );
							?>
			</td>

			<td class="td-2" align="left">
			<div class="kthead-title kl"><?php //new posts available
							echo CKunenaLink::GetCategoryLink ( 'showcat', $singlerow->id, kunena_htmlspecialchars ( stripslashes ( $singlerow->name ) ) );

							if ($cxThereisNewInForum == 1 && $kunena_my->id > 0) {
								echo '<sup><span class="newchar">&nbsp;(' . $newPostsAvailable . ' ' . stripslashes ( $kunena_config->newchar ) . ")</span></sup>";
							}

							$cxThereisNewInForum = 0;

							if ($singlerow->locked) {
								echo isset ( $kunena_icons ['forumlocked'] ) ? '&nbsp;&nbsp;<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['forumlocked'] . '" border="0" alt="' . _GEN_LOCKED_FORUM . '" title="' . _GEN_LOCKED_FORUM . '"/>' : '&nbsp;&nbsp;<img src="' . KUNENA_URLEMOTIONSPATH . 'lock.gif"  border="0" alt="' . _GEN_LOCKED_FORUM . '">';
								$lockedForum = 1;
							}

							if ($singlerow->review) {
								echo isset ( $kunena_icons ['forummoderated'] ) ? '&nbsp;&nbsp;<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['forummoderated'] . '" border="0" alt="' . _GEN_MODERATED . '" title="' . _GEN_MODERATED . '"/>' : '&nbsp;&nbsp;<img src="' . KUNENA_URLEMOTIONSPATH . 'review.gif" border="0"  alt="' . _GEN_MODERATED . '">';
								$moderatedForum = 1;
							}
							?>
			</div>

			<?php
							if ($forumDesc != "") {
								?>

			<div class="kthead-desc km"><?php
								echo $forumDesc?>
			</div>

			<?php
							}

							// loop over subcategories to show them under
							if (count ( $forumparents ) > 0) {
								if ($kunena_config->numchildcolumn > 0) {
									$subtopicwidth = ' style="width: 99%;"';
									$subwidth = ' style="width: ' . floor ( 99 / $kunena_config->numchildcolumn ) . '%"';
								} else {
									$subtopicwidth = ' style="display: inline-block;"';
									$subwidth = '';
								}

								?>

			<div class="kthead-child">

			<div class="kcc-table">
			<div <?php
								echo $subtopicwidth?>
				class="kcc-childcat-title"><?php
								if (count ( $forumparents ) == 1) {
									echo _KUNENA_CHILD_BOARD;
								} else {
									echo _KUNENA_CHILD_BOARDS;
								}
								?>:
			</div>
			<?php

								for($row_count = 0; $row_count < count ( $forumparents ); $row_count ++) {
									echo "<div{$subwidth} class=\"kcc-subcat km\">";

									$forumparent = $forumparents [$row_count];

									if ($forumparent) {

										//Begin: parent read unread iconset
										if ($kunena_config->showchildcaticon) {
											//
											if ($kunena_config->shownew && $kunena_my->id != 0) {
												//    get all threads with posts after the users last visit; don't bother for guests
												$kunena_db->setQuery ( "SELECT thread FROM #__fb_messages WHERE catid='{$forumparent->id}' AND hold='0' AND time>'{$this->prevCheck}' GROUP BY thread" );
												$newPThreadsAll = $kunena_db->loadObjectList ();
												check_dberror ( "Unable to load messages." );

												if (count ( $newPThreadsAll ) == 0) {
													$newPThreadsAll = array ();
												}
												?>

			<?php
												//Check if unread threads are in any of the forums topics
												$newPPostsAvailable = 0;

												foreach ( $newPThreadsAll as $npta ) {
													if (! in_array ( $npta->thread, $this->read_topics )) {
														$newPPostsAvailable ++;
													}
												}

												if ($newPPostsAvailable > 0 && count ( $newPThreadsAll ) != 0) {
													// Check Unread    Cat Images
													if (is_file ( KUNENA_ABSCATIMAGESPATH . $forumparent->id . "_on_childsmall.gif" )) {
														echo "<img src=\"" . KUNENA_URLCATIMAGES . $forumparent->id . "_on_childsmall.gif\" border=\"0\" class='forum-cat-image' alt=\" \" />";
													} else {
														echo isset ( $kunena_icons ['unreadforum'] ) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['unreadforum_childsmall'] . '" border="0" alt="' . _GEN_FORUM_NEWPOST . '" title="' . _GEN_FORUM_NEWPOST . '" />' : stripslashes ( $kunena_config->newchar );
													}
												} else {
													// Check Read Cat Images
													if (is_file ( KUNENA_ABSCATIMAGESPATH . $forumparent->id . "_off_childsmall.gif" )) {
														echo "<img src=\"" . KUNENA_URLCATIMAGES . $forumparent->id . "_off_childsmall.gif\" border=\"0\" class='forum-cat-image' alt=\" \" />";
													} else {
														echo isset ( $kunena_icons ['readforum'] ) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['readforum_childsmall'] . '" border="0" alt="' . _GEN_FORUM_NOTNEW . '" title="' . _GEN_FORUM_NOTNEW . '" />' : stripslashes ( $kunena_config->newchar );
													}
												}
											} // Not Login Cat Images
else {
												if (is_file ( KUNENA_ABSCATIMAGESPATH . $forumparent->id . "_notlogin_childsmall.gif" )) {
													echo "<img src=\"" . KUNENA_URLCATIMAGES . $forumparent->id . "_notlogin_childsmall.gif\" border=\"0\" class='forum-cat-image' alt=\" \" />";
												} else {
													echo isset ( $kunena_icons ['notloginforum'] ) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['notloginforum_childsmall'] . '" border="0" alt="' . _GEN_FORUM_NOTNEW . '" title="' . _GEN_FORUM_NOTNEW . '" />' : stripslashes ( $kunena_config->newchar );
												}
												?>

			<?php
											}
											//
										}
										// end: parent read unread iconset
										?>

			<?php
										echo CKunenaLink::GetCategoryLink ( 'showcat', $forumparent->id, kunena_htmlspecialchars ( stripslashes ( $forumparent->name ) ) );
										echo '<span class="kchildcount ks">(' . $forumparent->numTopics . "/" . $forumparent->numPosts . ')</span>';
									}
									echo "</div>";
								}
								?>
			</div>
			</div>

			<?php
							}

							//get the Moderator list for display
							$kunena_db->setQuery ( "SELECT * FROM #__fb_moderation AS m LEFT JOIN #__users AS u ON u.id=m.userid WHERE m.catid='{$singlerow->id}'" );
							$modslist = $kunena_db->loadObjectList ();
							check_dberror ( "Unable to load moderators." );

							// moderator list
							if (count ( $modslist ) > 0) {
								?>

			<div
				class="kthead-moderators ks">
			<?php
								echo _GEN_MODERATORS;
								?>: <?php
								$mod_cnt = 0;
								foreach ( $modslist as $mod ) {
									if ($mod_cnt)
										echo ', ';
									$mod_cnt ++;
									echo CKunenaLink::GetProfileLink ( $kunena_config, $mod->userid, ($kunena_config->username ? $mod->username : $mod->name) );
								}
								?>
			</div>

			<?php
							}

							if (CKunenaTools::isModerator ( $kunena_my->id, $singlerow->id )) {
								$kunena_db->setQuery ( "SELECT COUNT(*) FROM #__fb_messages WHERE catid='{$singlerow->id}' AND hold='1'" );
								$numPending = $kunena_db->loadResult ();
								if ($numPending > 0) {
									echo '<div class="ks"><font color="red"> ';
									echo CKunenaLink::GetPendingMessagesLink ( $singlerow->id, $numPending . ' ' . _SHOWCAT_PENDING );
									echo '</font></div>';
								}
							}
							?>
			</td>

			<td class="td-3 km" align="center" width="5%"><!-- Number of Topics -->
			<span class="cat_topics_number"><?php
							echo CKunenaTools::formatLargeNumber ( $numtopics );
							?>
			</span> <span class="cat_topics"> <?php
							echo _GEN_TOPICS;
							?> </span> <!-- /Number of Replies --></td>

			<td class="td-4 km" align="center" width="5%"><!-- Number of Topics -->
			<span class="cat_replies_number"><?php
							echo CKunenaTools::formatLargeNumber ( $numreplies );
							?>
			</span> <span class="cat_replies"> <?php
							echo _GEN_REPLIES;
							?> </span> <!-- /Number of Replies --></td>

			<?php
							if ($numtopics != 0) {
								?>

			<td class="td-5" align="left" width="25%">
			<div
				class="klatest-subject km">
			<?php
								echo _GEN_LAST_POST;
								?>: <?php
								echo CKunenaLink::GetThreadPageLink ( $kunena_config, 'view', $singlerow->catid, $latestthread, $latestthreadpages, $kunena_config->messages_per_page, $latestsubject, $latestid );
								?>
			</div>

			<div
				class="klatest-subject-by ks">
			<?php
								echo _GEN_POSTEDBY . ' ';
								echo CKunenaLink::GetProfileLink ( $kunena_config, $latestuserid, $latestname );
								echo ' ';
								echo _GEN_ON;
								echo ' <span title="'.CKunenaTimeformat::showDate($singlerow->time_last_msg, 'config_post_dateformat_hover').'">' . CKunenaTimeformat::showDate($singlerow->time_last_msg, 'config_post_dateformat').'</span>';

								// echo CKunenaLink::GetThreadPageLink ( $kunena_config, 'view', $singlerow->catid, $latestthread, $latestthreadpages, $kunena_config->messages_per_page, isset ( $kunena_icons ['latestpost'] ) ? '<img src="' . KUNENA_URLICONSPATH . $kunena_icons ['latestpost'] . '" border="0" alt="' . _SHOW_LAST . '" title="' . _SHOW_LAST . '"/>' : '<img src="' . KUNENA_URLEMOTIONSPATH . 'icon_newest_reply.gif" border="0"  alt="' . _SHOW_LAST . '"/>', $latestid );
								?>
			</div>
			</td>

			<?php
							} else {
								?>

			<td class="td-5" align="left"><?php
								echo _NO_POSTS;
								?></td>

			<?php
							}
							?>
		</tr>
		<?php
						}
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
		}

		//(JJ) BEGIN: WHOISONLINE
		if ($kunena_config->showwhoisonline > 0) {
			if (file_exists ( KUNENA_ABSTMPLTPATH . '/plugin/who/whoisonline.php' )) {
				include (KUNENA_ABSTMPLTPATH . '/plugin/who/whoisonline.php');
			} else {
				include (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'plugin/who/whoisonline.php');
			}
		}
		//(JJ) FINISH: WHOISONLINE


		//(JJ) BEGIN: STATS
		if ($kunena_config->showstats > 0) {
			if (file_exists ( KUNENA_ABSTMPLTPATH . '/plugin/stats/stats.class.php' )) {
				include_once (KUNENA_ABSTMPLTPATH . '/plugin/stats/stats.class.php');
			} else {
				include_once (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'plugin/stats/stats.class.php');
			}

			$kunena_stats = new CKunenaStats ( );
			$kunena_stats->showFrontStats ();
		}
		//(JJ) FINISH: STATS
		?>

<?php
	} else {
		?>

<div><?php
		echo _LISTCAT_NO_CATS . '<br />';
		echo _LISTCAT_ADMIN . '<br />';
		echo _LISTCAT_PANEL . '<br /><br />';
		echo _LISTCAT_INFORM . '<br /><br />';
		echo _LISTCAT_DO . ' <img src="' . KUNENA_URLEMOTIONSPATH . 'wink.png"  alt="" border="0" />';
		?>
</div>

<?php
	}

} // <<< -- first latest if close //
?>
