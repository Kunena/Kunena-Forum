<?php
/**
 * @version $Id: message.php 1210 2009-11-23 06:51:41Z mahagr $
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

$page = JRequest::getInt ( 'page', 0 );
$limitstart = JRequest::getInt ( 'limitstart', 0 );

$kunena_my = &JFactory::getUser ();
$kunena_config = & CKunenaConfig::getInstance ();
$kunena_db = &JFactory::getDBO ();

global $kunena_icons;

$catid = JRequest::getInt ( 'catid', 0 );

if ($kunena_config->fb_profile == 'cb') {
	$msg_params = array ('username' => &$msg_html->username, 'messageobject' => &$this->kunena_message, 'subject' => &$msg_html->subject, 'messagetext' => &$msg_html->text, 'signature' => &$msg_html->signature, 'karma' => &$msg_html->karma, 'karmaplus' => &$msg_html->karmaplus, 'karmaminus' => &$msg_html->karmaminus );
	$kunenaProfile = & CkunenaCBProfile::getInstance ();
	$profileHtml = $kunenaProfile->showProfile ( $this->kunena_message->userid, $msg_params );
} else {
	$profileHtml = null;
}

?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tbody>
		<tr class="fb_sth">
			<th colspan="2"
				class="view-th fb_sectiontableheader"><a name="<?php
				echo $msg_html->id;
				?>"></a> <?php
				if ($kunena_config->ordering_system == 'old_ord') {
					echo CKunenaLink::GetSamePageAnkerLink ( $msg_html->id, '#' . $msg_html->id );
				} else {
					if ($kunena_config->default_sort == 'desc') {
						if ( $page == '1') {
							$numb = $this->total_messages--;
							echo CKunenaLink::GetSamePageAnkerLink($msg_html->id,'#'.$numb);
						} else {
							$nums = $this->total_messages - $limitstart;
							$numb = $nums;
							echo CKunenaLink::GetSamePageAnkerLink($msg_html->id,'#'.$numb);
							$this->total_messages--;
						}
					} else {
						if ( $page == '1') {
							echo CKunenaLink::GetSamePageAnkerLink($msg_html->id,'#'.$this->mmm);
						}else {
							$nums = $this->mmm + $limitstart;
							echo CKunenaLink::GetSamePageAnkerLink($msg_html->id,'#'.$nums);
						}
					}
				}
				?>
			</th>
		</tr>

		<tr>
			<td class="fb-msgview-right">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td align="left"><?php

					if ($this->prevCheck < $this->kunena_message->time && ! in_array ( $this->kunena_message->thread, $this->read_topics )) {
						$msgtitle = 'msgtitle_new';
					} else {
						$msgtitle = 'msgtitle';
					}
					?>
					<span class="<?php
					echo $msgtitle;
					?>"><?php
						echo $msg_html->subject;
						?>
					</span> <span class="msgdate"
						title="<?php
						echo CKunenaTimeformat::showDate($this->kunena_message->time, 'config_post_dateformat_hover');
						?>"><?php
						echo CKunenaTimeformat::showDate($this->kunena_message->time, 'config_post_dateformat');
						?></span></td>

					<td align="right"><span class="msgkarma"> <?php
					if (isset ( $msg_html->karma )) {
						echo $msg_html->karma;
						if (isset ( $msg_html->karmaplus ))
							echo '&nbsp;&nbsp;' . $msg_html->karmaplus . ' ' . $msg_html->karmaminus;
					} else {
						echo '&nbsp;';
					}
					?>

					</span></td>
				</tr>

				<tr>
					<td colspan="2" valign="top">
					<div class="msgtext"><?php
					echo $msg_html->text;
					?></div>

					<?php
					if (! isset ( $msg_html->closed )) {
						?>

					<div id="sc<?php
						echo $msg_html->id;
						?>"
						class="switchcontent"><!-- make this div distinct from others on this page -->
					<?php
						//see if we need the users realname or his loginname
						if ($kunena_config->username) {
							$authorName = $kunena_my->username;
						} else {
							$authorName = $kunena_my->name;
						}

						//contruct the reply subject
						$resubject = kunena_htmlspecialchars ( JString::strtolower ( JString::substr ( $msg_html->subject, 0, JString::strlen ( _POST_RE ) ) ) == JString::strtolower ( _POST_RE ) ? $msg_html->subject : _POST_RE . ' ' . $msg_html->subject );
						?>

					<form
						action="<?php
						echo JRoute::_ ( KUNENA_LIVEURLREL . '&amp;func=post' );
						?>"
						method="post" name="postform" enctype="multipart/form-data"><input
						type="hidden" name="parentid"
						value="<?php
						echo $msg_html->id;
						?>" /> <input type="hidden" name="catid"
						value="<?php
						echo $catid;
						?>" /> <input type="hidden" name="action" value="post" /> <input
						type="text" name="subject" size="35" class="inputbox"
						maxlength="<?php
						echo $kunena_config->maxsubject;
						?>"
						value="<?php
						echo html_entity_decode ( $resubject );
						?>" /> <textarea class="inputbox" name="message" rows="6"
						cols="60" style="height: 100px; width: 100%; overflow: auto;"></textarea> <?php
						// Begin captcha . Thanks Adeptus
						if ($kunena_config->captcha && $kunena_my->id < 1) {
							?>
					<?php
							echo _KUNENA_CAPDESC . '&nbsp;'?>
					<input name="txtNumber" type="text" id="txtNumber" value=""
						style="vertical-align: middle" size="10">&nbsp; <img
						src="?option=com_kunena&func=showcaptcha" alt="" /><br />
					<?php
						}
						// Finish captcha
						?>

					<input type="submit" class="fb_button fb_qr_fire" name="submit"
						value="<?php
						@print (_GEN_CONTINUE) ;
						?>" /> <input type="button" class="fb_button fb_qm_cncl_btn"
						id="cancel__<?php
						echo $msg_html->id;
						?>"
						name="cancel"
						value="<?php
						@print (_KUNENA_CANCEL) ;
						?>" /> <small><em><?php
						echo _KUNENA_QMESSAGE_NOTE?></em></small></form>
					</div>
					<?php
					}
					?>
					</td>
				</tr>
			</table>
			</td>

			<td class="fb-msgview-left">
				<div class="fb-msgview-l-cover">
					<?php
					if ($profileHtml) {
						echo $profileHtml;
					} else {
					?>
					<span class="view-username">
						<?php
						if ($userinfo->userid) {
							echo CKunenaLink::GetProfileLink ( $kunena_config, $this->kunena_message->userid, $msg_html->username );
						} else {
							echo $msg_html->username;
						}
						?>
					</span>
					<?php
						if ($kunena_config->userlist_usertype)
							echo '<span class = "msgusertype">(' . $msg_html->usertype . ')</span>';
						?>
					<br />
					<?php
						if ($this->kunena_message->userid > 0) {
							echo CKunenaLink::GetProfileLink ( $kunena_config, $this->kunena_message->userid, $msg_html->avatar );
						} else {
							echo $msg_html->avatar;
						}
					?>
					<div class="viewcover"><?php
						if (isset ( $msg_html->userrank )) {
							echo $msg_html->userrank;
						}
						?>
					</div>
					<div class="viewcover"><?php
						if (isset ( $msg_html->userrankimg )) {
							echo $msg_html->userrankimg;
						}
						?>
					</div>
				<?php
					if (isset ( $msg_html->posts )) {
						echo $msg_html->posts;
					}
					?>
				<?php
					if (isset ( $msg_html->points )) {
						echo $msg_html->points;;
					}
					?>
				<div class="onlineimg">
				<?php
					if (isset ( $msg_html->online )) {
						echo $msg_html->online;
					}
					?>
				</div>
				<?php
					if (isset ( $msg_html->pms )) {
						echo $msg_html->pms;
					}
					?>
				<div class="smallicons">
				<?php
					if (isset ( $msg_html->icq )) {
						echo $msg_html->icq;
					}
					?>
				<?php
					if (isset ( $msg_html->gender )) {
						echo $msg_html->gender;
					}
					?>
				<?php
					if (isset ( $msg_html->skype )) {
						echo $msg_html->skype;
					}
					?>
				<?php
					if (isset ( $msg_html->twitter )) {
						echo $msg_html->twitter;
					}
					?>
				<?php
					if (isset ( $msg_html->facebook )) {
						echo $msg_html->facebook;
					}
					?>
                     <?php
					if (isset ( $msg_html->aim )) {
						echo $msg_html->aim;
					}
					?>
                     <?php
					if (isset ( $msg_html->bebo )) {
						echo $msg_html->bebo;
					}
					?>
                     <?php
					if (isset ( $msg_html->blogger )) {
						echo $msg_html->blogger;
					}
					?>
                     <?php
					if (isset ( $msg_html->delicious )) {
						echo $msg_html->delicious;
					}
					?>
                     <?php
					if (isset ( $msg_html->digg )) {
						echo $msg_html->digg;
					}
					?>
                     <?php
					if (isset ( $msg_html->flickr )) {
						echo $msg_html->flickr;
					}
					?>
                     <?php
					if (isset ( $msg_html->friendfeed )) {
						echo $msg_html->friendfeed;
					}
					?>
                     <?php
					if (isset ( $msg_html->linkedin )) {
						echo $msg_html->linkedin;
					}
					?>
                    <?php
					if (isset ( $msg_html->myspace )) {
						echo $msg_html->myspace;
					}
					?>
                    <?php
					if (isset ( $msg_html->yahoo )) {
						echo $msg_html->yahoo;
					}
					?>
				<?php
					if (isset ( $msg_html->website )) {
						echo $msg_html->website;
					}
					?>
				<?php
					if (isset ( $msg_html->gtalk )) {
						echo $msg_html->gtalk;
					}
					?>
				<?php
					if (isset ( $msg_html->yim )) {
						echo $msg_html->yim;
					}
					?>
				<?php
					if (isset ( $msg_html->msn )) {
						echo $msg_html->msn;
					}
					?>
				<?php
					if (isset ( $msg_html->location )) {
						echo $msg_html->location;
					}
					?>
				<?php
					if (isset ( $msg_html->birthdate )) {
						echo $msg_html->birthdate;
					}
				}
				?>
				</div>
					<?php
						if (isset ( $msg_html->personal )) {
						?>
						<div class="viewcover"><?php echo $msg_html->personal;?></div>
					<?php
						}
					?>

			</div>
			</td>
			<!-- -->

		</tr>

		<tr>
			<td class="fb-msgview-right-b">
			<div class="fb_message_editMarkUp_cover"><?php
			if ($this->kunena_message->modified_by) {

				echo '<span class="fb_message_editMarkUp" title="'.CKunenaTimeformat::showDate($this->kunena_message->modified_time, 'config_post_dateformat_hover').'">' . _KUNENA_EDITING_LASTEDIT . ': ' .
					CKunenaTimeformat::showDate($this->kunena_message->modified_time, 'config_post_dateformat' ) . ' ' . _KUNENA_BY . ' ' .
					($kunena_config->username ? $this->kunena_message->modified_username : $this->kunena_message->modified_name) . '.';
				if ($this->kunena_message->modified_reason) {
					echo _KUNENA_REASON . ': ' . kunena_htmlspecialchars ( stripslashes ( $this->kunena_message->modified_reason ) );
				}
				echo '</span>';
			}

			if ($kunena_config->reportmsg && $kunena_my->id > 1) {
				echo '<span class="fb_message_informMarkUp">' . CKunenaLink::GetReportMessageLink ( $catid, $msg_html->id, _KUNENA_REPORT ) . '</span>';
			}
			//Check that the user is an admin to display the ip in messages
			$kunena_is_admin = CKunenaTools::isAdmin ();
			if (isset ( $msg_html->ip ) && $kunena_is_admin) {
				echo '<span class="fb_message_informMarkUp">' . CKunenaLink::GetMessageIPLink ( $msg_html->ip ) . '</span>';
			}
			?>
			</div>
			<div class="fb_message_buttons_cover">
			<div class="fb_message_buttons_row"><?php
				if (! isset ( $msg_html->closed )) {
					echo " " . $msg_html->quickreply;
					echo " " . $msg_html->reply;
					echo " " . $msg_html->quote;

					if (CKunenaTools::isModerator ( $kunena_my->id, $catid ))
						echo ' </div><div class="fb_message_buttons_row">';

					if (isset ( $msg_html->merge )) {
						echo " " . $msg_html->merge;
					}

					if (isset ( $msg_html->split )) {
						echo " " . $msg_html->split;
					}
					if (isset ( $msg_html->delete )) {
						echo " " . $msg_html->delete;
					}
					if (isset ( $msg_html->edit )) {
						echo " " . $msg_html->edit;
					}

				} else {
					echo $msg_html->closed;
				}
			?>
			</div>
			</div>
			<?php
			if (isset ( $msg_html->signature )) {
				echo '<div class="msgsignature">';
				echo $msg_html->signature;
				echo '</div>';
			}
			?>

			</td>
			<td class="fb-msgview-left-b">&nbsp;</td>

		</tr>
	</tbody>
</table>
<!-- Begin: Message Module Position -->
<?php
CKunenaTools::showModulePosition('kunena_msg_' . $this->mmm);
?>
<!-- Finish: Message Module Position -->