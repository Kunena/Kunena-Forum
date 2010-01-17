<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();
$kunena_config = & CKunenaConfig::getInstance ();
$catid = JRequest::getInt ( 'catid', 0 );
?>
<div align="left" class="kunena-msgtitle"><?php
					if ($this->prevCheck < $this->kunena_message->time && ! in_array ( $this->kunena_message->thread, $this->read_topics )) {
						$msgtitle = 'msgtitle_new';
					} else {
						$msgtitle = 'msgtitle';
					} ?>
	<span class="<?php echo $msgtitle; ?>">
		<?php echo $this->msg_html->subject; ?>
	</span>
	<span class="msgdate" title="<?php echo CKunenaTimeformat::showDate($this->kunena_message->time, 'config_post_dateformat_hover'); ?>">
		<?php echo CKunenaTimeformat::showDate($this->kunena_message->time, 'config_post_dateformat'); ?>
	</span>
	<span class="msgkarma">
		<?php if (isset ( $this->msg_html->karma )) { echo $this->msg_html->karma;
			if (isset ( $this->msg_html->karmaplus )) echo '&nbsp;&nbsp;' . $this->msg_html->karmaplus . ' ' . $this->msg_html->karmaminus;
			} else { echo '&nbsp;'; } ?>
	</span>
</div>
<div>
	<div class="msgtext">
		<?php echo $this->msg_html->text; ?>
	</div>
		<?php if (! isset ( $this->msg_html->closed )) { ?>
	<div id="sc<?php echo $this->msg_html->id; ?>" class="switchcontent"><!-- make this div distinct from others on this page -->
		<?php //see if we need the users realname or his loginname
			if ($kunena_config->username) { $authorName = $this->my->username;
			} else { $authorName = $this->my->name; }
			//contruct the reply subject
			$resubject = kunena_htmlspecialchars ( JString::strtolower ( JString::substr ( $this->msg_html->subject, 0, JString::strlen ( _POST_RE ) ) ) == JString::strtolower ( _POST_RE ) ? $this->msg_html->subject : _POST_RE . ' ' . $this->msg_html->subject );
		?>
		<form action="<?php echo JRoute::_ ( KUNENA_LIVEURLREL . '&amp;func=post' ); ?>" method="post" name="postform" enctype="multipart/form-data">
			<input type="hidden" name="parentid" value="<?php echo $this->msg_html->id; ?>" />
			<input type="hidden" name="catid" value="<?php echo $catid; ?>" />
			<input type="hidden" name="action" value="post" />
			<input type="text" name="subject" size="35" class="inputbox" maxlength="<?php echo $kunena_config->maxsubject; ?>" value="<?php echo html_entity_decode ( $resubject ); ?>" />
			<textarea class="inputbox" name="message" rows="6" cols="60" style="height: 100px; width: 100%; overflow: auto;">
			</textarea>
				<?php // Begin captcha . Thanks Adeptus
					if ($kunena_config->captcha && $this->my->id < 1) { ?>
				<?php echo _KUNENA_CAPDESC . '&nbsp;'?>
				<input name="txtNumber" type="text" id="txtNumber" value="" style="vertical-align: middle" size="10">&nbsp; <img src="?option=com_kunena&func=showcaptcha" alt="" /><br />
				<?php } // Finish captcha
				?>
			<input type="submit" class="kbutton kqr_fire" name="submit" value="<?php @print (_GEN_CONTINUE) ; ?>" />
			<input type="button" class="kbutton kqm_cncl_btn" id="cancel__<?php echo $this->msg_html->id; ?>" name="cancel" value="<?php @print (_KUNENA_CANCEL) ; ?>" />
			<small><em><?php echo _KUNENA_QMESSAGE_NOTE?></em></small>
		</form>
	</div>
		<?php } ?>
</div>
<div>
	<span><?php if (isset ( $this->msg_html->signature )) {
		echo '<div class="msgsignature">';
		echo $this->msg_html->signature;
		echo '</div>'; } ?>
	</span>
</div>
<div class="kmessage_editMarkUp_cover"><?php
	if ($this->kunena_message->modified_by) {
		echo '<span class="kmessage_editMarkUp" title="'.CKunenaTimeformat::showDate($this->kunena_message->modified_time, 'config_post_dateformat_hover').'">' . _KUNENA_EDITING_LASTEDIT . ': ' .
					CKunenaTimeformat::showDate($this->kunena_message->modified_time, 'config_post_dateformat' ) . ' ' . _KUNENA_BY . ' ' .
					($kunena_config->username ? $this->kunena_message->modified_username : $this->kunena_message->modified_name) . '.';
	if ($this->kunena_message->modified_reason) {
		echo _KUNENA_REASON . ': ' . kunena_htmlspecialchars ( stripslashes ( $this->kunena_message->modified_reason ) ); }
	echo '</span>'; }
	if ($kunena_config->reportmsg && $this->my->id > 1) {
	echo '<span class="kmessage_informMarkUp">' . CKunenaLink::GetReportMessageLink ( $catid, $this->msg_html->id, _KUNENA_REPORT ) . '</span>'; }
	//Check that the user is an admin to display the ip in messages
	$kunena_is_admin = CKunenaTools::isAdmin ();
	if (isset ( $this->msg_html->ip ) && $kunena_is_admin) {
	echo '<span class="kmessage_informMarkUp">' . CKunenaLink::GetMessageIPLink ( $this->msg_html->ip ) . '</span>'; } ?>
</div>