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

global $topic_emoticons;

$catid = JRequest::getInt ( 'catid', 0 );
$id = JRequest::getInt ( 'id', 0 );

$kunena_db = &JFactory::getDBO ();
$kunena_config = & CKunenaConfig::getInstance ();
$kunena_my = &JFactory::getUser ();

//Some initial thingies needed anyway:
if (! isset ( $this->kunena_set_focus )) $this->kunena_set_focus = 0;

$authorName = stripslashes ( $this->authorName );
CKunenaPolls::call_javascript_form();

include_once (KUNENA_PATH_LIB . DS . 'kunena.bbcode.js.php');

//keep session alive while editing
JHTML::_ ( 'behavior.keepalive' );

$document =& JFactory::getDocument();

if ($this->kunena_editmode) $this->title = _KUNENA_POST_EDIT . ' ' . $this->resubject;
else if ($this->parentid) $this->title = _KUNENA_POST_REPLY_TOPIC . ' ' . $this->subject;
else $this->title = _KUNENA_POST_NEW_TOPIC;
$document->setTitle ( $this->title . ' - ' . stripslashes ( $kunena_config->board_title ) );

$cap[0] = explode('-',$document->getLanguage());
JApplication::addCustomHeadTag('
      <script type="text/javascript">
	   <!--
var RecaptchaOptions = {
   lang : "'.$cap[0].'"
};
//-->
     </script>
		');
?>

<form class="postform" id="postform"
	action="<?php
	echo CKunenaLink::GetPostURL();
	?>"
	method="post" name="postform" enctype="multipart/form-data">
	<?php if (!isset($this->selectcatlist)): ?>
	<input
	type="hidden" name="catid" value="<?php
	echo $this->catid;
	?>" />
	<?php endif; ?>
<?php
if (! empty ( $this->kunena_editmode )) :
	?>
<input type="hidden" name="do" value="editpostnow" /> <input
	type="hidden" name="id" value="<?php
	echo $this->id;
	?>" />
<?php else: ?>
<input type="hidden" name="action" value="post" /> <input type="hidden"
	name="parentid" value="<?php
	echo $this->parentid;
	?>" />
<?php endif; ?>
	<input type="hidden"
	value="<?php
	echo JURI::base ( true ) . '/components/com_kunena/template/default';
	?>"
	name="templatePath" /> <input type="hidden"
	value="<?php
	echo JURI::base ( true );
	?>/" name="kunenaPath" />
<?php if (! empty ( $this->contentURL )) :?>
<input type="hidden" name="contentURL" value="<?php echo $this->contentURL; ?>" />
<?php endif; ?>

<div class="k_bt_cvr1">
<div class="k_bt_cvr2">
<div class="k_bt_cvr3">
<div class="k_bt_cvr4">
<div class="k_bt_cvr5">
	<table class="kblocktable<?php
	echo isset ( $msg_cat->class_sfx ) ? ' kblocktable' . $msg_cat->class_sfx : '';
	?>"
		id="kpostmessage">
		<thead>
			<tr>
				<th colspan="2">
					<div class="ktitle_cover km">
						<span class="ktitle kl">
							<?php
							echo $this->title;
							?>
						</span>
					</div>
				</th>
			</tr>
		</thead>
		<tbody id="kpost_message">
		<?php if (isset($this->selectcatlist)): ?>
			<tr class="ksectiontableentry2">
				<td class="kleftcolumn"><strong><?php
				echo _KUNENA_POST_IN_CATEGORY;
				?></strong></td>
	
				<td class="k-topicicons"><?php
				echo $this->selectcatlist;
				?>
				</td>
			</tr>
			<?php endif; ?>
	
			<tr class="ksectiontableentry1">
				<td class="kleftcolumn"><strong><?php
				echo _GEN_NAME;
				?></strong></td>
	
				<?php
				if (($kunena_config->regonly == "1" || $kunena_config->changename == '0') && $kunena_my->id != "" && ! CKunenaTools::isModerator ( $kunena_my->id, $this->catid )) {
					?>
				<td><input type="hidden" name="authorname" size="35"
					class="kinputbox postinput"
					maxlength="35" value="<?php
					echo $this->authorName;
					?>"><b><?php
					echo $this->authorName;
					?></b></td>
				<?php
				} else {
					if ($this->kunena_registered_user == 1) {
						echo "<td><input type=\"text\" name=\"authorname\" size=\"35\"  class=\"kinputbox postinput\"  maxlength=\"35\" value=\"$authorName\" /></td>";
					} else {
						echo "<td><input type=\"text\" name=\"authorname\" size=\"35\"  class=\"kinputbox postinput\"  maxlength=\"35\" value=\"\" />";
						echo "<script type=\"text/javascript\">document.postform.authorname.focus();</script></td>";
						$this->kunena_set_focus = 1;
					}
				}
				?>
			</tr>
	
			<?php
			if (($kunena_config->askemail && !$kunena_my->id) || $kunena_config->changename == 1 || CKunenaTools::isModerator ( $kunena_my->id, $this->catid )) {
			?>
			<tr class = "ksectiontableentry2">
				<td class = "kleftcolumn"><strong><?php echo _GEN_EMAIL;?></strong></td>
				<td><input type="text" name="email"  size="35" class="kinputbox postinput" maxlength="35" value="<?php echo $this->email;?>" /></td>
			</tr>
			<?php
			}
			?>
	
			<tr class="ksectiontableentry1">
				<?php
				if (! $this->kunena_from_bot) {
					?>
				<td class="kleftcolumn"><strong><?php
					echo _GEN_SUBJECT;
					?></strong></td>
	
				<td><input type="text"
					class="kinputbox postinput"
					name="subject" id="subject" size="35"
					maxlength="<?php
					echo $kunena_config->maxsubject;
					?>"
					value="<?php
					echo $this->resubject;
					?>" /></td>
	
				<?php
				} else {
					?>
	
				<td class="kleftcolumn"><strong><?php
					echo _GEN_SUBJECT;
					?></strong>:</td>
	
				<td><input type="hidden" class="inputbox" name="subject" size="35"
					maxlength="<?php
					echo $kunena_config->maxsubject;
					?>"
					value="<?php
					echo $this->resubject;
					?>" /><?php
					echo $this->resubject;
					?>
				</td>
	
				<?php
				}
				?>
	
				<?php
				if ($this->kunena_set_focus == 0 && $this->id == 0 && ! $this->kunena_from_bot) {
					echo "<script type=\"text/javascript\">document.postform.subject.focus();</script>";
					$this->kunena_set_focus = 1;
				}
				?>
			</tr>
	
			<?php
			if ($this->parentid == 0) {
				?>
			<tr class="ksectiontableentry2">
				<td class="kleftcolumn"><strong><?php
				echo _GEN_TOPIC_ICON;
				?></strong></td>
	
				<td class="k-topicicons">
					<table class="kflat">
						<tr>
							<td>
							<?php
							foreach ($topic_emoticons as $emoid=>$emoimg):
							?>
							<input type="radio" name="topic_emoticon" value="<?php echo $emoid; ?>"
								<?php echo $this->emoid==$emoid?" checked=\"checked\" ":"";?> /> <img
								src="<?php echo $emoimg;?>" alt=""
								border="0" />
							<?php endforeach; ?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<?php
			}
			?>
	
			<?php
			if ($kunena_config->rtewidth == 0) {
				$useRte = 0;
			} else {
				$useRte = 1;
			}
	
			// No show bbcode editor
			if (file_exists ( KUNENA_ABSTMPLTPATH . DS . 'editor' . DS . 'bbcode.php' )) {
				require_once (KUNENA_ABSTMPLTPATH . DS . 'editor' . DS . 'bbcode.php');
			} else {
				require_once (KUNENA_PATH_TEMPLATE_DEFAULT . DS . 'editor' . DS . 'bbcode.php');
			}
	
			if ($this->kunena_set_focus == 0) {
				echo '<tr><td style="display:none;"><script type="text/javascript">document.postform.message.focus();</script></td></tr>';
			}
	
			//check if this user is already subscribed to this topic but only if subscriptions are allowed
	
			if ($kunena_config->allowsubscriptions == 1) {
				if ($this->id == 0) {
					$fb_thread = - 1;
				} else {
					$kunena_db->setQuery ( "SELECT thread FROM #__fb_messages WHERE id='{$this->id}'" );
					$fb_thread = $kunena_db->loadResult ();
					check_dberror ( "Unable to load thread." );
				}
	
				$kunena_db->setQuery ( "SELECT thread FROM #__fb_subscriptions WHERE userid='{$kunena_my->id}' AND thread='{$fb_thread}'" );
				$fb_subscribed = $kunena_db->loadResult ();
				check_dberror ( "Unable to load subscriptions." );
	
				if ($fb_subscribed == "" || $this->id == 0) {
					$fb_cansubscribe = 1;
				} else {
					$fb_cansubscribe = 0;
				}
			}
			?>
			<?php
			if (($kunena_config->allowimageupload || ($kunena_config->allowimageregupload && $kunena_my->id != 0) || CKunenaTools::isModerator ( $kunena_my->id, $this->catid ))) {
				?>
			<?php
			}
			?>
			<?php
			if (($kunena_config->allowfileupload || ($kunena_config->allowfileregupload && $kunena_my->id != 0) || CKunenaTools::isModerator ( $kunena_my->id, $this->catid ))) {
				?>
			<?php
			}
	
			if ($kunena_my->id != 0 && $kunena_config->allowsubscriptions == 1 && $fb_cansubscribe == 1 && ! $this->kunena_editmode) {
				?>
	
			<tr class="ksectiontableentry1">
				<td class="kleftcolumn"><strong><?php
				echo _POST_SUBSCRIBE;
				?></strong></td>
				<td><?php
				if ($kunena_config->subscriptionschecked == 1) {
					?>
				<input type="checkbox" name="subscribeMe" value="1" checked /> <em><?php
					echo _POST_NOTIFIED;
					?></em>
	
				<?php
				} else {
					?> <input type="checkbox" name="subscribeMe" value="1" /> <em><?php
					echo _POST_NOTIFIED;
					?></em> <?php
				}
				?></td>
			</tr>
			<?php
			}
			//Begin captcha
			if ($kunena_config->captcha == 1 && $kunena_my->id < 1) {
				$enabled = JPluginHelper::isEnabled('system', 'jezReCaptcha');
				if($enabled){
				?>
			<tr class="ksectiontableentry1">
				<td class="kleftcolumn">&nbsp;<strong><?php
				echo _KUNENA_CAPDESC;
				?></strong>&nbsp;</td>
				<td class="kcaptcha>
				<?php global $mainframe;
	$mainframe->triggerEvent('onCaptchaDisplay'); ?>
				 </td>
			</tr>
			<?php
				}
			}
			// Finish captcha
			?>
			<tr id="kpost_buttons_tr" class="ksectiontableentry1">
				<td id="kpost_buttons" colspan="2" class="center">
					<input type="button" name="cancel" class="kbutton"
					value="<?php echo (' ' . _GEN_CANCEL . ' ');?>"
					onclick="javascript:window.history.back();"
					title="<?php echo (_KUNENA_EDITOR_HELPLINE_CANCEL);?>" />
					<input type="submit" name="submit" class="kbutton"
					value="<?php echo (' ' . _GEN_CONTINUE . ' ');?>"
					onclick="return submitForm()"
					title="<?php echo (_KUNENA_EDITOR_HELPLINE_SUBMIT);?>" />
				</td>
			</tr>
			<tr class="ksectiontableentry1">
				<td colspan="2"><?php
					if ($kunena_config->askemail) {
						echo $kunena_config->showemail == '0' ? "<em>* - " . _POST_EMAIL_NEVER . "</em>" : "<em>* - " . _POST_EMAIL_REGISTERED . "</em>";
					}
					?>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<?php
					$no_upload = "0"; //reset the value.. you just never know..
					if ($kunena_config->showhistory == 1) {
						listThreadHistory ( $this->id, $kunena_config, $kunena_db );
					}
					?>
				</td>
			</tr>
		</tbody>
	</table>
</div>
</div>
</div>
</div>
</div>
</form>