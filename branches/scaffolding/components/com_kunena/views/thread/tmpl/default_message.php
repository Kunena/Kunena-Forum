<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	(C) 2008 Kunena. All rights reserved, see COPYRIGHT.php
 * @license		GNU General Public License, see LICENSE.php
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die('Invalid Request.');
?>
<table width="100%" border="1" cellspacing="0" cellpadding="2">
	<caption><a name="<?php echo $msg_id;?>"/></caption>

	<tbody>
		<tr class="fb_sth">
			<th colspan="2" class="view-th <?php echo $boardclass; ?>sectiontableheader">
				<a href="<?php echo JRequest::getURI(); ?>#<?php echo $msg_id; ?>">#<?php echo $msg_id; ?></a>
			</th>
		</tr>
		<tr>
			<td class="fb-msgview-left">
				<div class="fb-msgview-l-cover">
					<span class="view-username"><a href="<?php echo $msg_prflink ; ?>"><?php echo $msg_username; ?></a> </span> <span class="msgusertype">(<?php echo $msg_usertype; ?>)</span>
					<br />
					<?php if (@$msg_avatar) : ?>
					<a href="<?php echo $msg_prflink ; ?>"><?php echo $msg_avatar; ?></a>
					<?php endif; ?>
					<?php if ($gr_title = getFBGroupName($lists["userid"])) : ?>
					<span class="view-group_<?php echo $gr_title->id;?>"><?php echo $gr_title->title; ?></span>
					<?php endif; ?>

					<?php if (@$msg_personal) : ?>
					<div class="viewcover">
						<?php echo $msg_personal; ?>
					</div>
					<?php endif; ?>

					<?php if (@$msg_userrank) : ?>
					<div class="viewcover">
						<?php echo $msg_userrank; ?>
					</div>
					<?php endif; ?>

					<?php if (@$msg_userrankimg) : ?>
					<div class="viewcover">
						<?php echo $msg_userrankimg; ?>
					</div>
					<?php endif; ?>
<?php
// TODO: Clear @
if (@$msg_posts) :
	echo $msg_posts;
endif; ?>

<?php
if (@$useGraph) {
	$myGraph->BarGraphHoriz();
}

if (@$msg_online) :
	echo $msg_online;
endif;

if (@$msg_pms) {
	echo $msg_pms;
}

if (@$msg_profile) {
	echo $msg_profile;
}
?>
					<br />
<?php
if (@$msg_icq) {
	echo $msg_icq;
}

if (@$msg_gender) {
	echo $msg_gender;
}

if (@$msg_skype) {
	echo $msg_skype;
}

if (@$msg_website) {
	echo $msg_website;
}

if (@$msg_gtalk) {
	echo $msg_gtalk;
}

if (@$msg_yim) {
	echo $msg_yim;
}

if (@$msg_msn) {
	echo $msg_msn;
}

if (@$msg_aim) {
	echo $msg_aim;
}

if (@$msg_location) {
	echo $msg_location;
}

if (@$msg_birthdate) {
	echo $msg_birthdate;
}
?>
				</div>
			</td>
			<td class="fb-msgview-right">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td align="left">
<?php
$msg_time_since = _KUNENA_TIME_SINCE;
$msg_time_since = str_replace('%time%', time_since($fmessage->time, FBTools :: fbGetInternalTime()), $msg_time_since);
?>
							<span class="msgtitle"><?php echo $msg_subject; ?> </span> <span class="msgdate" title="<?php echo $msg_date; ?>"><?php echo $msg_time_since; ?></span>
						</td>
						<td align="right">
							<span class="msgkarma">
<?php
if (@$msg_karma) {
	echo $msg_karma . '&nbsp;&nbsp;' . $msg_karmaplus . ' ' . $msg_karmaminus;
} else {
	echo '&nbsp;';
}
?>
							</span>
						</td>
					</tr>
					<tr>
						<td colspan="2" valign="top">
							<div class="msgtext"><?php echo stripslashes($msg_text); ?></div>
<?php
// TODO: Clear @
if (!@$msg_closed) {
?>
							<div id="sc<?php echo $msg_id; ?>" class="switchcontent">
								<!-- make this div distinct from others on this page -->
<?php
	//see if we need the users realname or his loginname
	if ($fbConfig['username']) {
		$authorName = $my->username;
	} else {
		$authorName = $u->name;
	}

	//contruct the reply subject
	$table = array_flip(get_html_translation_table(HTML_ENTITIES));
	$resubject = htmlspecialchars(strtr($msg_subject, $table));
	$resubject = strtolower(substr($resubject, 0, strlen(_POST_RE))) == strtolower(_POST_RE) ? stripslashes($resubject) : _POST_RE . stripslashes($resubject);
?>
								<form action="<?php echo JRoute::_(JB_LIVEURLREL. '&func=post'); ?>" method="post" name="postform" enctype="multipart/form-data">
									<input type="hidden" name="parentid" value="<?php echo $msg_id;?>"/>
									<input type="hidden" name="catid" value="<?php echo $catid;?>"/>
									<input type="hidden" name="action" value="post"/>
									<input type="hidden" name="contentURL" value="empty"/>
									<input type="hidden" name="fb_authorname" size="35" class="inputbox" maxlength="35" value="<?php echo $authorName;?>"/>
									<input type="hidden" name="email" size="35" class="inputbox" maxlength="35" value="<?php echo $u->email;?>"/>
									<input type="hidden" name="subject" size="35" class="inputbox" maxlength="<?php echo $fbConfig['maxSubject'];?>" value="<?php echo $resubject;?>"/>
									<textarea class="inputbox" name="message" id="message" style="height: 100px; width: 100%; overflow:auto;"></textarea>
<?php
	// Begin captcha . Thanks Adeptus
	if ($fbConfig['captcha'] == 1 && $my->id < 1) {
?>
									<?php echo _KUNENA_CAPDESC.'&nbsp;'?>
									<input name="txtNumber" type="text" id="txtNumber" value="" style="vertical-align:middle" size="10">&nbsp;
									<img src="<?php echo JB_DIRECTURL ;?>/template/default/plugin/captcha/randomImage.php" alt="" /><br />
<?php
	}
	// Finish captcha
?>
									<input type="submit" class="fb_qm_btn" name="submit" value="<?php echo _GEN_CONTINUE;?>"/>
									<input type="button" class="fb_qm_btn fb_qm_cncl_btn" id="cancel__<?php echo $msg_id; ?>" name="cancel" value="<?php echo _KUNENA_CANCEL;?>"/>
									<small><em><?php echo _KUNENA_QMESSAGE_NOTE?></em></small>
								</form>
							</div>
<?php
}
?>

						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="fb-msgview-left-c">&nbsp;</td>
			<td class="fb-msgview-right-c"><div class="fb_smalltext">
<?php
if ($fbConfig['reportmsg']) {
	$link = JRoute :: _('index.php?option=com_kunena&func=report&Itemid=' . KUNENA_KUNENA_ITEMID . '&msg_id=' . $msg_id . '&catid=' . $catid);
?>
				<a href="<?php echo $link;?>" title="#<?php echo $msg_id;?>"><?php echo _KUNENA_REPORT;?></a> &nbsp;
<?php  } ?>

				<?php echo $fbIcons['msgip'] ? '<img src="' . JB_URLICONSPATH . '' . $fbIcons['msgip'] . '" border="0" alt="'. _KUNENA_REPORT_LOGGED.'" />' . '' : '  <img src="' . JB_URLEMOTIONSPATH . 'ip.gif" border="0"	alt="'. _KUNENA_REPORT_LOGGED.'" />'; ?> <span class="fb_smalltext"> <?php echo _KUNENA_REPORT_LOGGED;?></span>
				<?php if($msg_ip) echo ''.$msg_ip_link.''.$msg_ip.'</a>'; else echo '&nbsp;';?>
				</div>
			</td>
		</tr>
<?php
	if ($fmessage->modified_by) {
?>
		<tr>
			<td class="fb-msgview-left-c">&nbsp;</td>
			<td class="fb-msgview-right-c">
				<div class="fb_message_editMarkUp_cover">
					<span class="fb_message_editMarkUp" ><?php echo _KUNENA_EDITING_LASTEDIT;?>: <?php echo date(_DATETIME, $fmessage->modified_time);?> <?php echo _KUNENA_BY; ?> <?php echo FBTools::whoisID($fmessage->modified_by)?>.
<?php
		if ($fmessage->modified_reason) {
			echo _KUNENA_REASON . ": " . $fmessage->modified_reason;
		}
?></span>
				</div>
			</td>
		</tr>
<?php
	}

	// TODO: Clear @
	if (@$msg_signature) {
?>
		<tr>
			<td class="fb-msgview-left-c">&nbsp;</td>
			<td class="fb-msgview-right-c" >
				<div class="msgsignature" >
					<?php	echo $msg_signature; ?>
				</div>
			</td>
		</tr>
<?php
	}
?>
		<tr>
			<td class="fb-msgview-left-b">&nbsp;</td>
			<td class="fb-msgview-right-b" align="right">
				<span id="fb_qr_sc__<?php echo $msg_id;?>" class="fb_qr_fire" style="cursor:hand; cursor:pointer"><?php
	//we should only show the Quick Reply section to registered users. otherwise we are missing too much information!!
	/*	onClick="expandcontent(this, 'sc<?php echo $msg_id;?>')" */
	if ($my->id > 0 && !@$msg_closed) {
		echo $msg_quickmsg = JHTML::_( 'fb.icon', $fbIcons, 'quickmsg', _KUNENA_QUICKMSG );
	}
?></span>
<?php
	if (@$fbIcons['reply']) {
		if ($msg_closed == "") {
			echo $msg_reply;
			echo " " . $msg_quote;

			if (@$msg_delete) {
				echo " " . $msg_delete;
			}

			if (@$msg_move) {
				echo " " . $msg_move;
			}

			if (@$msg_edit) {
				echo " " . $msg_edit;
			}

			if (@$msg_sticky) {
				echo " " . $msg_sticky;
			}

			if (@$msg_lock) {
				echo " " . $msg_lock;
			}
		} else {
			echo $msg_closed;
		}
	} else {
		if (@$msg_closed == '') {
			echo ' | '.$msg_reply;
			echo ' | '.$msg_quote;

			if (@$msg_delete) {
				echo ' | ' . $msg_delete;
			}

			if (@$msg_move) {
				echo ' | ' . $msg_move;
			}

			if (@$msg_edit) {
				echo ' | ' . $msg_edit;
			}

			if (@$msg_sticky) {
				echo ' | ' . $msg_sticky;
			}

			if (@$msg_lock) {
				echo ' | ' . $msg_lock;
			}
		} else {
			echo $msg_closed;
		}
	}
?>
			</td>
		</tr>
	</tbody>
</table>
<!-- Begin: Message Module Positions -->
<?php
	if (mosCountModules('fb_msg_t')) {
?>
<div class="fb_msg_t">
	<?php mosLoadModules('fb_msg_t', -2); ?>
</div>
<?php
	}

	if (mosCountModules('fb_msg_1')) {
		if ($mmm == 1) {
?>
<div class="fb_msg_1">
	<?php mosLoadModules('fb_msg_1', -2); ?>
</div>
<?php
		}
	}

	if (mosCountModules('fb_msg_2')) {
		if ($mmm == 2) {
?>
<div class="fb_msg_2">
	<?php mosLoadModules('fb_msg_2', -2); ?>
</div>
<?php
		}
	}

	if (mosCountModules('fb_msg_b')) {
?>
<div class="fb_msg_b">
	<?php mosLoadModules('fb_msg_b', -2); ?>
</div>
<?php
	}
?>
<!-- Finish: Message Module Positions -->

<?php

	// --------------------------------------------------------------
	//  Legend to the variables used
	// --------------------------------------------------------------
	// $msg_id		  = Message ID#
	// $msg_username	= Username (with email link if enabled)
	// $msg_avatar	  = User Avatar
	// $msg_usertype	= User Type (Visitor/Member/moderator/Admin)
	// $msg_userrank	= User Rank
	// $msg_userrankimg = User Rank Image
	// $msg_posts		= Post Count
	// $msg_karma		= Karma Points
	// $msg_karmaplus	= Linked Image for Karma+
	// $msg_karmaminus  = Linked Image for Karma-
	// $msg_ip		  = IP of Poster
	// $msg_ip_link	 = Link to look up IP of Poster
	// $msg_date		= Date of Post
	// $msg_subject	 = Post Subject
	// $msg_text		= Post Text
	// $msg_signature	= User's Signature
	// $msg_reply		= Reply Option
	// $msg_quote		= Quote Option
	// $msg_edit		= Edit Option
	// $msg_closed	  = Locked/Diabled message
	// $msg_delete	  = Delete Option
	// $msg_sticky	  = Sticky/Unsticky Option
	// $msg_lock		= Lock/Unlock Option
	// $msg_aim		 = User's AIM
	// $msg_icq		 = User's ICQ#
	// $msg_msn		 = User's MSN
	// $msg_yahoo		= User's Yahoo
	// $msg_profile	 = Image link to user's Profile Page
	// $msg_pms		 = Linked image for PMS2
	// $msg_buddy		= Add buddy image link
	// $msg_loc		 = User's Location
	// $msg_regdate	 = User's Registration Date
	// $tabclass[$k]	= CSS Class for TD (use to alternate colors)
	// fb_messagebody	= CSS Class for post text
	// fb_signature	 = CSS Class for signature
